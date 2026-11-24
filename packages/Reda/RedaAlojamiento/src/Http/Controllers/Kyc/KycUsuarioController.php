<?php

/**
 * Resumen: Endpoints del usuario autenticado para la verificación de identidad.
 *  - POST kyc/didit/iniciar : crea (o retoma) la sesión y devuelve la URL de Didit.
 *  - GET  kyc/retorno       : destino al que Didit devuelve al usuario. NO aprueba nada: ignora los
 *                             parámetros de la URL (status, verificationSessionId), pide a KycService
 *                             una consulta de respaldo, muestra un mensaje según el estado real guardado y
 *                             redirige a la pantalla de verificaciones (config kyc.url_seccion).
 *  - GET  kyc/estado        : estado KYC actual en JSON (con consulta de respaldo limitada por intervalo).
 *  - GET  kyc/manual/opciones   : tipos de documento, países y límites del formulario manual.
 *  - POST kyc/manual            : envía la solicitud manual (datos personales y documentos).
 *  - GET  kyc/manual/solicitud  : datos propios para completar una corrección pendiente.
 *  - POST kyc/manual/correccion : reenvía datos y/o documentos tras una corrección solicitada.
 * El usuario se toma siempre de la sesión (Auth::id()), nunca de parámetros de la petición. Toda la
 * validación y las reglas de negocio están en KycService. La interfaz del usuario (Fase 7) vive en
 * resources/js/vistas/frontend/kyc y consume estos endpoints.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Http\Controllers\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Http\Controllers\Kyc;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Reda\RedaAlojamiento\Enums\EstadoKyc;
use Reda\RedaAlojamiento\Models\Kyc\DocumentoKyc;
use Reda\RedaAlojamiento\Services\Kyc\KycService;
use Reda\RedaAlojamiento\Services\Kyc\ValidadorKycManual;
use Reda\RedaAlojamiento\Support\PaisesIso;

class KycUsuarioController extends Controller
{
    /**
     * Inicia la verificación con Didit. Con AJAX devuelve JSON con la URL en "respuesta.url";
     * sin AJAX redirige directamente a Didit.
     */
    public function iniciarDidit(Request $peticion, KycService $kyc): JsonResponse|RedirectResponse
    {
        $resultado = $kyc->iniciarDidit((int) Auth::id(), $peticion->ip());

        if ($peticion->expectsJson() || $peticion->ajax()) {
            return response()->json($resultado, $resultado['code']);
        }

        if ($resultado['success']) {
            return redirect()->away($resultado['respuesta']['url']);
        }

        Common::one_time_message('error', $resultado['mensaje_usuario']);

        return redirect((string) config('kyc.url_seccion', 'users/edit-verification'));
    }

    /**
     * Regreso desde Didit. El resultado se decide por el webhook o la consulta a la API, nunca por la URL.
     */
    public function retorno(Request $peticion, KycService $kyc): RedirectResponse
    {
        $usuarioId = (int) Auth::id();
        $solicitud = $kyc->solicitudActiva($usuarioId);

        if ($solicitud) {
            $kyc->sincronizarConDidit($solicitud);
        }

        $estado = $kyc->estadoDe($usuarioId);

        if ($estado === EstadoKyc::Verificado && $kyc->estaVerificado($usuarioId)) {
            Common::one_time_message('success', __('Tu identidad ha sido verificada.'));
        } elseif ($estado === EstadoKyc::Fallido) {
            Common::one_time_message('error', __('La verificación automática no pudo completarse. Puedes intentarlo de nuevo o solicitar la revisión manual.'));
        } else {
            Common::one_time_message('info', __('Estamos procesando tu verificación. Te avisaremos cuando tengamos el resultado.'));
        }

        return redirect((string) config('kyc.url_seccion', 'users/edit-verification'));
    }

    /**
     * Estado KYC del usuario autenticado (para que la interfaz consulte el avance sin recargar).
     */
    public function estado(KycService $kyc): JsonResponse
    {
        $usuarioId = (int) Auth::id();
        $solicitud = $kyc->solicitudActiva($usuarioId);

        if ($solicitud) {
            $kyc->sincronizarConDidit($solicitud);
        }

        return response()->json([
            'success'         => true,
            'message'         => __('Estado KYC consultado'),
            'mensaje_usuario' => '',
            'respuesta'       => $kyc->resumenParaUsuario($usuarioId),
            'code'            => 200,
        ])->header('Cache-Control', 'no-store, private');
    }

    /**
     * Opciones del formulario manual: tipos de documento (con los documentos que exige cada uno), país
     * predeterminado (Venezuela, sin impedir otros países), países admitidos y límites de archivos.
     */
    public function opcionesManual(): JsonResponse
    {
        $tipos = [];
        foreach ((array) config('kyc.tipos_documento', []) as $clave => $tipo) {
            $tipos[] = [
                'clave'            => $clave,
                'etiqueta'         => __($tipo['etiqueta']),
                'requiere_reverso' => (bool) $tipo['requiere_reverso'],
                'requiere_selfie'  => (bool) $tipo['requiere_selfie'],
            ];
        }

        return response()->json([
            'success'         => true,
            'message'         => __('Opciones del formulario manual'),
            'mensaje_usuario' => '',
            'respuesta'       => [
                'tipos_documento'     => $tipos,
                'pais_predeterminado' => config('kyc.pais_predeterminado', 'VE'),
                'paises'              => PaisesIso::codigos(),
                'edad_minima'         => (int) config('kyc.manual.edad_minima', 18),
                'archivos'            => [
                    'extensiones'      => config('kyc.documentos.extensiones_permitidas'),
                    'tamano_maximo_kb' => (int) config('kyc.documentos.tamano_maximo_kb'),
                    'solo_imagen'      => config('kyc.documentos.tipos_solo_imagen'),
                ],
            ],
            'code'            => 200,
        ])->header('Cache-Control', 'no-store, private');
    }

    /**
     * Envía la solicitud de verificación manual (multipart: datos personales y documentos).
     */
    public function enviarManual(Request $peticion, KycService $kyc): JsonResponse
    {
        if ($this->superaTamanoDePost($peticion)) {
            return $this->tamanoExcedido();
        }

        $resultado = $kyc->enviarManual(
            (int) Auth::id(),
            $peticion->only(array_merge(ValidadorKycManual::CAMPOS, ['acepta_tratamiento'])),
            $this->archivosDe($peticion),
            $peticion->ip()
        );

        return response()->json($resultado, $resultado['code']);
    }

    /**
     * Reenvía datos y documentos cuando un administrador pidió una corrección.
     */
    public function reenviarCorreccion(Request $peticion, KycService $kyc): JsonResponse
    {
        if ($this->superaTamanoDePost($peticion)) {
            return $this->tamanoExcedido();
        }

        $resultado = $kyc->reenviarCorreccion(
            (int) Auth::id(),
            $peticion->only(ValidadorKycManual::CAMPOS),
            $this->archivosDe($peticion),
            $peticion->ip()
        );

        return response()->json($resultado, $resultado['code']);
    }

    /**
     * Datos de la solicitud manual del propio usuario para completar una corrección (solo si hay una pendiente).
     */
    public function solicitudManual(KycService $kyc): JsonResponse
    {
        $datos = $kyc->solicitudParaCorreccion((int) Auth::id());

        return response()->json([
            'success'         => $datos !== null,
            'message'         => $datos !== null ? __('Solicitud con corrección pendiente') : __('Sin corrección pendiente'),
            'mensaje_usuario' => $datos !== null ? '' : __('No tienes ninguna corrección pendiente en tu solicitud.'),
            'respuesta'       => $datos ?? '',
            'code'            => $datos !== null ? 200 : 404,
        ], $datos !== null ? 200 : 404)->header('Cache-Control', 'no-store, private');
    }

    /**
     * Archivos de la petición por tipo (documento_frente, documento_reverso, documento_selfie).
     *
     * @return array<string,mixed>
     */
    private function archivosDe(Request $peticion): array
    {
        $archivos = [];

        foreach (DocumentoKyc::tipos() as $tipo) {
            $archivo = $peticion->file('documento_' . $tipo);

            if ($archivo !== null) {
                $archivos[$tipo] = $archivo;
            }
        }

        return $archivos;
    }

    /**
     * Detecta que PHP descartó el cuerpo por superar post_max_size (llegaría vacío y confundiría al usuario).
     */
    private function superaTamanoDePost(Request $peticion): bool
    {
        $largo  = (int) $peticion->server('CONTENT_LENGTH', 0);
        $limite = trim((string) ini_get('post_max_size'));
        $bytes  = (int) $limite * match (strtolower(substr($limite, -1))) { 'g' => 1073741824, 'm' => 1048576, 'k' => 1024, default => 1 };

        return $bytes > 0 && $largo > $bytes;
    }

    /**
     * Respuesta 413 estándar cuando los archivos superan el límite del servidor.
     */
    private function tamanoExcedido(): JsonResponse
    {
        return response()->json([
            'success'         => false,
            'message'         => __('Archivos demasiado grandes'),
            'mensaje_usuario' => __('Los archivos superan el tamaño permitido por el servidor. Reduce su tamaño e inténtalo de nuevo.'),
            'respuesta'       => 'tamano_excedido',
            'code'            => 413,
        ], 413);
    }
}
