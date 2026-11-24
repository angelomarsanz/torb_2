<?php

/**
 * Resumen: Modelo Eloquent de la tabla solicitudes_kyc. Cada fila es un intento de verificación de
 * identidad (Didit o manual) y conserva el historial: iniciar un nuevo intento nunca modifica el anterior.
 * Los datos sensibles (número de documento, fecha de nacimiento y dirección) se guardan cifrados con la
 * clave de la aplicación; el número de documento además mantiene un hash HMAC para búsquedas exactas y
 * sus últimos 4 caracteres para mostrarlo enmascarado. Solo los servicios KYC pueden escribir en él.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Models\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Models\Kyc;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;
use Reda\RedaAlojamiento\Enums\EstadoKyc;
use Reda\RedaAlojamiento\Enums\MetodoKyc;

class SolicitudKyc extends Model
{
    use ProtegeEscrituraKyc;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'solicitudes_kyc';

    /**
     * Atributos asignables de forma masiva (los servicios nunca pasan datos crudos de la petición).
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'metodo',
        'estado',
        'numero_intento',
        'clave_abierta',
        'didit_session_id',
        'didit_vendor_data',
        'didit_estado_original',
        'didit_resultado',
        'didit_ultimo_timestamp',
        'didit_ultima_consulta_en',
        'nombres',
        'apellidos',
        'tipo_documento',
        'numero_documento',
        'pais_expedicion',
        'fecha_nacimiento',
        'pais_residencia',
        'ciudad_residencia',
        'direccion_residencia',
        'correo_contacto',
        'iniciada_en',
        'enviada_en',
        'aprobada_en',
        'rechazada_en',
        'cerrada_en',
        'revisado_por_admin_id',
        'motivo_rechazo',
        'mensaje_usuario',
        'observaciones_internas',
        'correccion_solicitada',
        'correccion_solicitada_en',
        'correccion_atendida_en',
        'eliminar_documentos_en',
        'documentos_eliminados_en',
        'retencion_suspendida',
    ];

    /**
     * Atributos que nunca deben serializarse hacia vistas o respuestas JSON.
     *
     * @var array
     */
    protected $hidden = [
        'numero_documento',
        'numero_documento_hash',
        'fecha_nacimiento',
        'direccion_residencia',
        'didit_resultado',
        'didit_vendor_data',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'metodo'                   => MetodoKyc::class,
        'estado'                   => EstadoKyc::class,
        'fecha_nacimiento'         => 'encrypted',
        'direccion_residencia'     => 'encrypted',
        'didit_resultado'          => 'array',
        'retencion_suspendida'     => 'boolean',
        'didit_ultima_consulta_en' => 'datetime',
        'iniciada_en'              => 'datetime',
        'enviada_en'               => 'datetime',
        'aprobada_en'              => 'datetime',
        'rechazada_en'             => 'datetime',
        'cerrada_en'               => 'datetime',
        'correccion_solicitada_en' => 'datetime',
        'correccion_atendida_en'   => 'datetime',
        'eliminar_documentos_en'   => 'datetime',
        'documentos_eliminados_en' => 'datetime',
    ];

    /**
     * Número de documento: se guarda cifrado, junto con su hash de búsqueda y sus últimos 4 caracteres.
     * Al leerlo devuelve el valor normalizado (mayúsculas, solo letras y números).
     */
    protected function numeroDocumento(): Attribute
    {
        return Attribute::make(
            get: static fn ($valor) => $valor === null ? null : Crypt::decryptString($valor),
            set: static function ($valor) {
                $normalizado = self::normalizarDocumento($valor);

                if ($normalizado === '') {
                    return [
                        'numero_documento'          => null,
                        'numero_documento_hash'     => null,
                        'numero_documento_ultimos4' => null,
                    ];
                }

                return [
                    'numero_documento'          => Crypt::encryptString($normalizado),
                    'numero_documento_hash'     => self::hashDocumento($normalizado),
                    'numero_documento_ultimos4' => substr($normalizado, -4),
                ];
            },
        );
    }

    /**
     * Número de documento enmascarado para listados y vistas (muestra solo los últimos 4 caracteres).
     */
    public function getNumeroDocumentoEnmascaradoAttribute(): ?string
    {
        $ultimos4 = $this->attributes['numero_documento_ultimos4'] ?? null;

        return $ultimos4 === null || $ultimos4 === '' ? null : '••••' . $ultimos4;
    }

    /**
     * Normaliza un número de documento: mayúsculas y solo letras y números (V-12.345.678 => V12345678).
     */
    public static function normalizarDocumento(?string $valor): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $valor));
    }

    /**
     * Calcula el hash HMAC-SHA256 de un documento normalizado con la clave de la aplicación.
     * Si APP_KEY cambia, los hashes almacenados dejan de coincidir (se debe conservar la clave).
     */
    public static function hashDocumento(string $normalizado): string
    {
        return hash_hmac('sha256', $normalizado, (string) config('app.key'));
    }

    /**
     * Hashes candidatos para buscar un documento tolerando la letra de nacionalidad de la cédula
     * venezolana: 12345678, V12345678 y E12345678 se consideran el mismo número.
     *
     * @return array<int, string>
     */
    public static function hashesCandidatosDocumento(?string $valor): array
    {
        $normalizado = self::normalizarDocumento($valor);

        if ($normalizado === '') {
            return [];
        }

        $soloNumero = preg_match('/^[VE](\d+)$/', $normalizado, $coincidencia) ? $coincidencia[1] : $normalizado;

        $candidatos = array_unique([$normalizado, $soloNumero, 'V' . $soloNumero, 'E' . $soloNumero]);

        return array_map(static fn (string $c) => self::hashDocumento($c), array_values($candidatos));
    }

    /**
     * Indica si la solicitud está abierta (sin decisión final).
     */
    public function estaAbierta(): bool
    {
        return $this->estado?->esAbierto() ?? false;
    }

    /**
     * Indica si el administrador pidió una corrección que el usuario aún no atendió.
     */
    public function tieneCorreccionPendiente(): bool
    {
        return $this->correccion_solicitada_en !== null && $this->correccion_atendida_en === null;
    }

    /**
     * Solicitudes sin decisión final.
     */
    public function scopeAbiertas($consulta)
    {
        return $consulta->whereIn('estado', array_map(static fn (EstadoKyc $e) => $e->value, EstadoKyc::abiertos()));
    }

    /**
     * Usuario que realizó la solicitud.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Administrador que aprobó o rechazó la solicitud.
     */
    public function administrador(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'revisado_por_admin_id');
    }

    /**
     * Todos los documentos de la solicitud, incluido el historial de reemplazos.
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoKyc::class, 'solicitud_id');
    }

    /**
     * Documentos vigentes (no reemplazados ni eliminados).
     */
    public function documentosVigentes(): HasMany
    {
        return $this->documentos()->where('estado', DocumentoKyc::ESTADO_VIGENTE);
    }

    /**
     * Eventos de auditoría de la solicitud.
     */
    public function eventos(): HasMany
    {
        return $this->hasMany(EventoKyc::class, 'solicitud_id');
    }
}
