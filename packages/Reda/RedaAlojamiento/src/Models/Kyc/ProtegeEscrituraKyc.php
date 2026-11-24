<?php

/**
 * Resumen: Rasgo (trait) de los modelos KYC que bloquea toda escritura realizada fuera de la ventana
 * autorizada por GuardiaEscrituraKyc. Se engancha a los eventos creating, updating y deleting del modelo,
 * de modo que ni un controlador ni código externo puedan alterar estados, documentos o auditoría KYC
 * saltándose los servicios.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Models\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Models\Kyc;

use Reda\RedaAlojamiento\Services\Kyc\GuardiaEscrituraKyc;

trait ProtegeEscrituraKyc
{
    /**
     * Registra los eventos que verifican la ventana de escritura autorizada.
     */
    public static function bootProtegeEscrituraKyc(): void
    {
        static::creating(static function ($modelo) {
            GuardiaEscrituraKyc::exigirActiva(static::class, 'creating');
        });

        static::updating(static function ($modelo) {
            GuardiaEscrituraKyc::exigirActiva(static::class, 'updating');
        });

        static::deleting(static function ($modelo) {
            GuardiaEscrituraKyc::exigirActiva(static::class, 'deleting');
        });
    }
}
