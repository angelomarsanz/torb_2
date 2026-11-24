<?php

/**
 * Resumen: Estados posibles de la verificación de identidad (KYC) de un usuario o solicitud.
 * Los valores almacenados están en inglés (not_started, verified, ...) y los nombres de los casos en español.
 * Es la única definición de estados y de transiciones válidas; KycService la consulta antes de
 * cambiar cualquier estado. Solo el estado "verified" autoriza operaciones restringidas.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Enums
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Enums;

enum EstadoKyc: string
{
    case SinIniciar     = 'not_started';
    case Pendiente      = 'pending';
    case EnProceso      = 'in_progress';
    case RevisionManual = 'manual_review';
    case Verificado     = 'verified';
    case Rechazado      = 'rejected';
    case Fallido        = 'failed';
    case Expirado       = 'expired';

    /**
     * Indica si el estado autoriza publicar alojamientos, negocios y crear reservas.
     */
    public function autorizaOperaciones(): bool
    {
        return $this === self::Verificado;
    }

    /**
     * Indica si la solicitud está abierta (no tiene una decisión final).
     * Una sola solicitud abierta por usuario puede existir a la vez.
     */
    public function esAbierto(): bool
    {
        return in_array($this, self::abiertos(), true);
    }

    /**
     * Estados de una solicitud que todavía no tiene una decisión final.
     *
     * @return array<int, self>
     */
    public static function abiertos(): array
    {
        return [self::EnProceso, self::Pendiente, self::RevisionManual];
    }

    /**
     * Estados que permiten iniciar un nuevo intento de verificación (automático o manual).
     *
     * @return array<int, self>
     */
    public static function permitenNuevoIntento(): array
    {
        return [self::SinIniciar, self::Fallido, self::Rechazado, self::Expirado];
    }

    /**
     * Estados válidos que puede alcanzar una solicitud o usuario desde el estado actual.
     * El cambio al mismo estado no se considera una transición (se trata como "sin cambio").
     *
     * Didit permite que un revisor cambie una decisión ya tomada (Declined -> Approved y
     * Approved -> Declined, Resubmitted o Kyc Expired), por eso un fallo automático puede pasar a
     * verified y una verificación puede perderse (failed, in_progress o expired). Estas reglas
     * generales no autorizan por sí solas las acciones administrativas: KycService restringe cada
     * flujo (Didit, manual) al método de la solicitud.
     *
     * @return array<int, self>
     */
    public function transicionesPermitidas(): array
    {
        return match ($this) {
            self::SinIniciar     => [self::EnProceso, self::RevisionManual],
            self::EnProceso      => [self::Pendiente, self::Verificado, self::Fallido, self::Expirado],
            self::Pendiente      => [self::EnProceso, self::Verificado, self::Fallido, self::Expirado],
            self::RevisionManual => [self::Verificado, self::Rechazado],
            self::Fallido        => [self::EnProceso, self::RevisionManual, self::Verificado],
            self::Rechazado,
            self::Expirado       => [self::EnProceso, self::RevisionManual],
            self::Verificado     => [self::Expirado, self::Fallido, self::EnProceso],
        };
    }

    /**
     * Indica si el estado actual puede pasar al estado destino.
     */
    public function puedePasarA(self $destino): bool
    {
        return in_array($destino, $this->transicionesPermitidas(), true);
    }

    /**
     * Texto legible para el usuario, traducido con el archivo JSON del plugin.
     */
    public function etiqueta(): string
    {
        return match ($this) {
            self::SinIniciar     => __('Sin verificar'),
            self::Pendiente      => __('Verificación pendiente de revisión'),
            self::EnProceso      => __('Verificación en proceso'),
            self::RevisionManual => __('Solicitud pendiente de revisión'),
            self::Verificado     => __('Identidad verificada'),
            self::Rechazado      => __('Solicitud rechazada'),
            self::Fallido        => __('Verificación automática fallida'),
            self::Expirado       => __('Verificación expirada'),
        };
    }

    /**
     * Lista de valores de texto de todos los estados (útil para validaciones y filtros).
     *
     * @return array<int, string>
     */
    public static function valores(): array
    {
        return array_map(static fn (self $estado) => $estado->value, self::cases());
    }
}
