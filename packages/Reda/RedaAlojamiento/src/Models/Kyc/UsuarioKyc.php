<?php

/**
 * Resumen: Modelo Eloquent de la tabla usuarios_kyc: el estado efectivo de autorización KYC de cada usuario.
 * Es la única fuente de verdad que consulta el bloqueo de operaciones. Un usuario sin fila equivale a
 * not_started. Solo KycService escribe en este modelo, dentro de una transacción con bloqueo de fila.
 * esVerificado() exige además que la verificación no haya vencido (expira_en), de modo que una
 * verificación caducada deja de autorizar aunque el proceso de limpieza aún no la haya actualizado.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Models\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Models\Kyc;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Reda\RedaAlojamiento\Enums\EstadoKyc;
use Reda\RedaAlojamiento\Enums\MetodoKyc;

class UsuarioKyc extends Model
{
    use ProtegeEscrituraKyc;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'usuarios_kyc';

    /**
     * Atributos asignables de forma masiva.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'estado',
        'solicitud_activa_id',
        'metodo_verificacion',
        'verificado_en',
        'verificado_por_admin_id',
        'expira_en',
        'version',
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'estado'              => EstadoKyc::class,
        'metodo_verificacion' => MetodoKyc::class,
        'verificado_en'       => 'datetime',
        'expira_en'           => 'datetime',
        'version'             => 'integer',
    ];

    /**
     * Estado KYC efectivo, considerando que un registro ausente o sin estado equivale a not_started.
     */
    public function estadoEfectivo(): EstadoKyc
    {
        return $this->estado ?? EstadoKyc::SinIniciar;
    }

    /**
     * Indica si el usuario tiene la identidad verificada y vigente.
     */
    public function esVerificado(): bool
    {
        if ($this->estadoEfectivo() !== EstadoKyc::Verificado) {
            return false;
        }

        return $this->expira_en === null || $this->expira_en->isFuture();
    }

    /**
     * Usuario al que pertenece el estado.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Solicitud que originó el estado actual.
     */
    public function solicitudActiva(): BelongsTo
    {
        return $this->belongsTo(SolicitudKyc::class, 'solicitud_activa_id');
    }

    /**
     * Administrador que aprobó la verificación manual (nulo si fue automática).
     */
    public function administradorVerificador(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'verificado_por_admin_id');
    }
}
