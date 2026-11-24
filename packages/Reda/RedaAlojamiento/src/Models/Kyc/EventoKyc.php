<?php

/**
 * Resumen: Modelo Eloquent de la tabla eventos_kyc, el registro de auditoría del KYC. Es inmutable:
 * solo puede crearse (desde los servicios KYC) y jamás actualizarse ni eliminarse. La columna
 * clave_idempotencia (UNIQUE) permite descartar eventos repetidos de Didit. El detalle se guarda
 * saneado, sin números de documento completos, imágenes ni datos biométricos.
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
use LogicException;

class EventoKyc extends Model
{
    use ProtegeEscrituraKyc;

    public const ORIGEN_DIDIT_WEBHOOK  = 'didit_webhook';
    public const ORIGEN_DIDIT_CONSULTA = 'didit_consulta';
    public const ORIGEN_USUARIO        = 'usuario';
    public const ORIGEN_ADMIN          = 'admin';
    public const ORIGEN_SISTEMA        = 'sistema';

    public const RESULTADO_PROCESADO              = 'procesado';
    public const RESULTADO_DUPLICADO              = 'duplicado';
    public const RESULTADO_IGNORADO_SESION_ANTIGUA = 'ignorado_sesion_antigua';
    public const RESULTADO_IGNORADO_EVENTO_ANTIGUO = 'ignorado_evento_antiguo';
    public const RESULTADO_IGNORADO_SIN_CAMBIO    = 'ignorado_sin_cambio';
    public const RESULTADO_IGNORADO_TRANSICION    = 'ignorado_transicion_invalida';
    public const RESULTADO_IGNORADO_ESTADO        = 'ignorado_estado_desconocido';
    public const RESULTADO_IGNORADO_ENTORNO       = 'ignorado_entorno';
    public const RESULTADO_IGNORADO_SESION_DESCONOCIDA = 'ignorado_sesion_desconocida';
    public const RESULTADO_RECHAZADO_VENDOR       = 'rechazado_vendor_data';
    public const RESULTADO_CONFIRMACION_FALLIDA   = 'confirmacion_fallida';
    public const RESULTADO_ERROR                  = 'error';

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'eventos_kyc';

    /**
     * Atributos asignables de forma masiva.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'solicitud_id',
        'origen',
        'tipo',
        'clave_idempotencia',
        'didit_event_id',
        'didit_session_id',
        'didit_estado',
        'estado_anterior',
        'estado_nuevo',
        'resultado',
        'detalle',
        'payload_hash',
        'admin_id',
        'ip',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'detalle' => 'array',
    ];

    /**
     * Impide actualizar o eliminar eventos de auditoría, incluso desde los servicios.
     */
    protected static function booted(): void
    {
        static::updating(static function () {
            throw new LogicException('Los eventos de auditoría KYC son inmutables.');
        });

        static::deleting(static function () {
            throw new LogicException('Los eventos de auditoría KYC no pueden eliminarse.');
        });
    }

    /**
     * Solicitud asociada al evento.
     */
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(SolicitudKyc::class, 'solicitud_id');
    }

    /**
     * Usuario asociado al evento.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
