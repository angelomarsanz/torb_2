<?php

/**
 * Resumen: Receptor público de los webhooks de Didit (POST /webhooks/didit/kyc).
 * Verifica la firma HMAC-SHA256 sobre el cuerpo ORIGINAL de la petición (X-Signature-V2 o X-Signature,
 * con marca de tiempo vigente) antes de leer su contenido, rechaza con 401 las firmas inválidas y delega
 * el procesamiento en KycService. No usa sesión ni CSRF (la autenticidad la da la firma). Nunca registra
 * el cuerpo del webhook ni el secreto; solo el motivo del rechazo y la IP. Responde 2xx cuando el evento
 * se procesó o se descartó definitivamente, y 5xx cuando conviene que Didit reintente la entrega.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Http\Controllers\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Http\Controllers\Kyc;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Reda\RedaAlojamiento\Services\Kyc\DiditService;
use Reda\RedaAlojamiento\Services\Kyc\FirmaWebhookDidit;
use Reda\RedaAlojamiento\Services\Kyc\KycService;

class DiditWebhookController extends Controller
{
    /**
     * Tamaño máximo aceptado para el cuerpo del webhook (bytes).
     */
    private const TAMANO_MAXIMO = 2097152;

    /**
     * Recibe un webhook de Didit: autentica la firma y procesa el evento.
     */
    public function recibir(Request $peticion, DiditService $didit, KycService $kyc): JsonResponse
    {
        $secreto = $didit->secretoWebhook();

        if ($secreto === '') {
            Log::error('KYC webhook Didit: DIDIT_WEBHOOK_SECRET no está configurado; se solicita reintento.');

            return $this->responder(false, __('Webhook no configurado'), 'webhook_no_configurado', 503);
        }

        // Cuerpo exacto recibido: la firma se calcula sobre estos bytes (o su forma canónica), no sobre un JSON re-codificado.
        $cuerpo = $peticion->getContent();

        if (strlen($cuerpo) > self::TAMANO_MAXIMO) {
            return $this->responder(false, __('Cuerpo demasiado grande'), 'cuerpo_demasiado_grande', 413);
        }

        $firma  = new FirmaWebhookDidit($secreto, (int) config('kyc.didit.tolerancia_timestamp_segundos', 300));
        $motivo = $firma->motivoDeRechazo($cuerpo, static fn (string $nombre) => $peticion->header($nombre));

        if ($motivo !== null) {
            Log::warning('KYC webhook Didit rechazado (' . $motivo . ') desde la IP ' . $peticion->ip() . '.');

            return $this->responder(false, __('Firma inválida'), 'firma_invalida', 401);
        }

        $payload = json_decode($cuerpo, true);

        if (!is_array($payload)) {
            return $this->responder(false, __('Cuerpo inválido'), 'cuerpo_invalido', 400);
        }

        $resultado = $kyc->procesarWebhookDidit($payload, $cuerpo, $peticion->ip());

        return response()->json($resultado, $resultado['code']);
    }

    /**
     * Construye la respuesta estándar del plugin. El mensaje para el usuario va vacío: el receptor es
     * un servicio entre servidores y el detalle técnico queda en el log.
     */
    private function responder(bool $exito, string $mensaje, string $respuesta, int $codigo): JsonResponse
    {
        return response()->json([
            'success'         => $exito,
            'message'         => $mensaje,
            'mensaje_usuario' => '',
            'respuesta'       => $respuesta,
            'code'            => $codigo,
        ], $codigo);
    }
}
