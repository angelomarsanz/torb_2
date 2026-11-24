<?php

/**
 * Resumen: Descarga protegida de documentos de identidad para el usuario propietario (GET kyc/documentos/{id}).
 * Nunca existe un enlace público: el archivo se lee del disco privado, se descifra, se verifica su integridad
 * y se entrega con cabeceras seguras (sin caché, nosniff, CSP restrictiva). La autorización la decide KycPolicy
 * con el usuario de la sesión; si el documento no existe o no es suyo responde 404 (sin distinguir ambos casos,
 * para no revelar qué identificadores existen). Cada consulta queda en la auditoría (eventos_kyc).
 * Las imágenes se muestran en línea; los PDF siempre se descargan como adjunto.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Http\Controllers\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Http\Controllers\Kyc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Reda\RedaAlojamiento\Models\Kyc\DocumentoKyc;
use Reda\RedaAlojamiento\Models\Kyc\EventoKyc;
use Reda\RedaAlojamiento\Services\Kyc\DocumentoKycInvalidoException;
use Reda\RedaAlojamiento\Services\Kyc\KycDocumentoService;
use Symfony\Component\HttpFoundation\Response;

class KycDocumentoController extends Controller
{
    /**
     * Entrega un documento al usuario que lo subió.
     */
    public function descargar(Request $peticion, int $documento, KycDocumentoService $servicio): Response
    {
        $usuario   = Auth::user();
        $registro  = DocumentoKyc::with('solicitud')->find($documento);

        if (!$usuario || !$registro || !Gate::forUser($usuario)->allows('verDocumento', $registro)) {
            abort(404);
        }

        try {
            $archivo = $servicio->leer($registro);
        } catch (DocumentoKycInvalidoException $e) {
            abort(404);
        }

        $servicio->registrarAcceso($registro, EventoKyc::ORIGEN_USUARIO, null, $peticion->ip());

        return response($archivo['contenido'], 200, [
            'Content-Type'            => $archivo['mime'],
            'Content-Length'          => (string) strlen($archivo['contenido']),
            'Content-Disposition'     => ($archivo['mime'] === 'application/pdf' ? 'attachment' : 'inline')
                . '; filename="kyc-' . $archivo['tipo'] . '.' . $archivo['extension'] . '"',
            'X-Content-Type-Options'  => 'nosniff',
            'Cache-Control'           => 'no-store, private, max-age=0',
            'Pragma'                  => 'no-cache',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            'Referrer-Policy'         => 'no-referrer',
            'X-Robots-Tag'            => 'noindex, nofollow',
        ]);
    }
}
