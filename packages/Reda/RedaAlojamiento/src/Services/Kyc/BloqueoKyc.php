<?php

/**
 * Resumen: Servicio CENTRAL del bloqueo de operaciones por KYC. Es el único lugar que decide si una operación
 * restringida debe impedirse; lo usan el middleware EnsureKycVerified (rutas) y BloqueoKycObserver (modelos),
 * de modo que la regla no se duplica en los controladores.
 *
 * Regla: la restricción se aplica al usuario autenticado (guard "users") en una petición web cuando
 *  - kyc.bloqueo_habilitado está activo (interruptor de emergencia),
 *  - no es consola/cola/cron ni una ruta del panel administrativo (los administradores tienen sus propios permisos),
 *  - y KycService::estaVerificado() es falso. Solo el estado verified (vigente) autoriza.
 * Los webhooks, retornos de pasarela sin sesión y procesos internos no tienen usuario autenticado y no se afectan.
 *
 * Ventana de reserva: cuando un usuario VERIFICADO inicia una reserva nueva se abre una ventana de unos minutos
 * (kyc.reservas.ventana_autorizacion_minutos) en su sesión. Si perdiera la verificación antes de terminar de
 * pagar, puede completar esa misma reserva, para no cobrar sin crearla. Un usuario no verificado jamás la obtiene.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Reda\RedaAlojamiento\Exceptions\KycNoVerificadoException;

class BloqueoKyc
{
    private const CLAVE_VENTANA = 'kyc_reserva_autorizada';

    public function __construct(private readonly KycService $kyc)
    {
    }

    /**
     * Indica si el bloqueo está activo (interruptor de emergencia KYC_ENFORCEMENT).
     */
    public function habilitado(): bool
    {
        return (bool) config('kyc.bloqueo_habilitado', true);
    }

    /**
     * Id del usuario al que se aplica la restricción en la petición actual, o null si no aplica
     * (bloqueo desactivado, consola, panel administrativo o sin usuario autenticado).
     */
    public function usuarioSujeto(): ?int
    {
        if (!$this->habilitado() || app()->runningInConsole()) {
            return null;
        }

        if (request()->is('admin', 'admin/*')) {
            return null;
        }

        $id = Auth::guard('users')->id();

        return $id ? (int) $id : null;
    }

    /**
     * Indica si el usuario tiene la identidad verificada y vigente.
     */
    public function estaAutorizado(int $usuarioId): bool
    {
        return $this->kyc->estaVerificado($usuarioId);
    }

    /**
     * Abre la ventana de reserva para un usuario verificado que inicia una reserva nueva.
     */
    public function abrirVentanaReserva(int $usuarioId): void
    {
        $minutos = (int) config('kyc.reservas.ventana_autorizacion_minutos', 45);

        if ($minutos > 0 && request()->hasSession()) {
            request()->session()->put(self::CLAVE_VENTANA, ['usuario' => $usuarioId, 'hasta' => time() + $minutos * 60]);
        }
    }

    /**
     * Indica si el usuario tiene una ventana de reserva vigente en su sesión.
     */
    public function ventanaReservaVigente(int $usuarioId): bool
    {
        if (!request()->hasSession()) {
            return false;
        }

        $ventana = request()->session()->get(self::CLAVE_VENTANA);

        return is_array($ventana)
            && (int) ($ventana['usuario'] ?? 0) === $usuarioId
            && (int) ($ventana['hasta'] ?? 0) >= time();
    }

    /**
     * Impide la operación si el usuario sujeto a la restricción no está verificado.
     *
     * @param string $operacion   Descripción interna (para el registro).
     * @param bool   $conVentana  true si la operación es crear/convertir una reserva y admite la ventana de reserva.
     *
     * @throws KycNoVerificadoException
     */
    public function exigir(string $operacion, bool $conVentana = false): void
    {
        $usuarioId = $this->usuarioSujeto();

        if ($usuarioId === null || $this->estaAutorizado($usuarioId)) {
            return;
        }

        if ($conVentana && $this->ventanaReservaVigente($usuarioId)) {
            return;
        }

        Log::info('KYC bloqueo: el usuario ' . $usuarioId . ' intentó ' . $operacion . ' sin identidad verificada.');

        throw new KycNoVerificadoException($operacion);
    }
}
