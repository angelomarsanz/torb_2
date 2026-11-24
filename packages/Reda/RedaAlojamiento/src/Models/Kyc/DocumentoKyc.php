<?php

/**
 * Resumen: Modelo Eloquent de la tabla documentos_kyc. Guarda los metadatos de cada archivo de identidad
 * (frente, reverso, selfie) de la verificación manual; el archivo vive cifrado en el disco privado y
 * nunca se expone con una URL pública. Los documentos reemplazados se conservan como historial.
 * Solo KycDocumentoService puede escribir en él. El campo user_id permite validar la propiedad (anti-IDOR).
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Models\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Models\Kyc;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoKyc extends Model
{
    use ProtegeEscrituraKyc;

    public const TIPO_FRENTE  = 'frente';
    public const TIPO_REVERSO = 'reverso';
    public const TIPO_SELFIE  = 'selfie';

    public const ESTADO_VIGENTE     = 'vigente';
    public const ESTADO_REEMPLAZADO = 'reemplazado';
    public const ESTADO_ELIMINADO   = 'eliminado';

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'documentos_kyc';

    /**
     * Atributos asignables de forma masiva.
     *
     * @var array
     */
    protected $fillable = [
        'solicitud_id',
        'user_id',
        'tipo',
        'disco',
        'ruta',
        'nombre_original',
        'mime',
        'tamano_bytes',
        'sha256',
        'cifrado',
        'estado',
        'reemplazado_por_id',
        'eliminado_en',
    ];

    /**
     * Atributos que no deben serializarse hacia vistas o respuestas JSON.
     *
     * @var array
     */
    protected $hidden = [
        'disco',
        'ruta',
        'sha256',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'cifrado'      => 'boolean',
        'tamano_bytes' => 'integer',
        'eliminado_en' => 'datetime',
    ];

    /**
     * Tipos de documento admitidos.
     *
     * @return array<int, string>
     */
    public static function tipos(): array
    {
        return [self::TIPO_FRENTE, self::TIPO_REVERSO, self::TIPO_SELFIE];
    }

    /**
     * Indica si el documento está vigente.
     */
    public function estaVigente(): bool
    {
        return $this->estado === self::ESTADO_VIGENTE;
    }

    /**
     * Solicitud a la que pertenece el documento.
     */
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(SolicitudKyc::class, 'solicitud_id');
    }

    /**
     * Usuario propietario del documento.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
