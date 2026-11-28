<?php

/**
 * Resumen: Controlador del panel administrativo KYC ("Verificación de identidad"), integrado en el panel actual
 * (guard "admin", layout admin.template y permisos del proyecto). No crea un panel aparte.
 *
 * Pantallas: listado paginado con filtros y búsqueda, detalle de una solicitud, listado de verificaciones eliminadas
 * y historial de auditoría de un usuario.
 * Acciones AJAX (estructura estándar): aprobar, rechazar (motivo obligatorio), solicitar corrección, agregar
 * observación interna, suspender/reanudar la retención de documentos, mostrar el número de documento completo y
 * eliminar la verificación de un usuario (motivo obligatorio) para que pueda volver a enviarla.
 * Descarga protegida de documentos para administradores.
 *
 * Seguridad: cada acción exige el permiso manage_kyc en tres capas: el middleware de la ruta, autorizar() en este
 * controlador (KycPolicy) y KycService (que lo vuelve a verificar antes de modificar nada). Un administrador sin
 * ese permiso no puede ver datos ni documentos. El administrador y la IP se registran en la auditoría.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Http\Controllers\Admin\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Http\Controllers\Admin\Kyc;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Reda\RedaAlojamiento\Models\Kyc\DocumentoKyc;
use Reda\RedaAlojamiento\Models\Kyc\EventoKyc;
use Reda\RedaAlojamiento\Models\Kyc\SolicitudKyc;
use Reda\RedaAlojamiento\Services\Kyc\ConsultaKycAdmin;
use Reda\RedaAlojamiento\Services\Kyc\DocumentoKycInvalidoException;
use Reda\RedaAlojamiento\Services\Kyc\KycDocumentoService;
use Reda\RedaAlojamiento\Services\Kyc\KycService;
use Symfony\Component\HttpFoundation\Response;

class KycAdminController extends Controller
{
    /**
     * Administrador autenticado, tras comprobar que tiene el permiso manage_kyc (403 si no).
     */
    private function autorizar(): Admin
    {
        $admin = Auth::guard('admin')->user();

        abort_unless($admin && Gate::forUser($admin)->allows('gestionar', SolicitudKyc::class), 403);

        return $admin;
    }

    /**
     * Listado paginado de solicitudes con filtros y búsqueda.
     */
    public function index(Request $peticion, ConsultaKycAdmin $consulta)
    {
        $this->autorizar();

        $filtros = [
            'grupo'  => $peticion->query('grupo', 'pendientes'),
            'metodo' => $peticion->query('metodo', ''),
            'buscar' => $peticion->query('buscar', ''),
        ];

        return view('reda-alojamiento::admin.kyc.index', [
            'solicitudes' => $consulta->listar($filtros),
            'contadores'  => $consulta->contadores(),
            'filtros'     => $filtros,
        ]);
    }

    /**
     * Detalle de una solicitud. La consulta de datos personales queda en la auditoría.
     */
    public function show(Request $peticion, int $id, ConsultaKycAdmin $consulta, KycService $kyc)
    {
        $admin = $this->autorizar();

        $datos = $consulta->detalle($id);
        abort_if($datos === null, 404);

        $kyc->registrarConsultaAdmin((int) $admin->id, $id, $peticion->ip());

        return response()->view('reda-alojamiento::admin.kyc.detalle', $datos)
            ->header('Cache-Control', 'no-store, private');
    }

    /**
     * Listado de verificaciones eliminadas por administradores (con su motivo).
     */
    public function eliminadas(ConsultaKycAdmin $consulta)
    {
        $this->autorizar();

        return view('reda-alojamiento::admin.kyc.eliminadas', [
            'eliminaciones' => $consulta->listarEliminaciones(),
            'contadores'    => $consulta->contadores(),
            'filtros'       => ['grupo' => 'eliminadas', 'metodo' => '', 'buscar' => ''],
        ]);
    }

    /**
     * Historial de auditoría de un usuario (incluye los eventos que se conservan tras eliminar su verificación).
     */
    public function historial(int $usuario, ConsultaKycAdmin $consulta)
    {
        $this->autorizar();

        $datos = $consulta->historialUsuario($usuario);
        abort_if($datos === null, 404);

        return response()->view('reda-alojamiento::admin.kyc.historial', $datos + [
            'contadores' => $consulta->contadores(),
            'filtros'    => ['grupo' => 'eliminadas', 'metodo' => '', 'buscar' => ''],
        ])->header('Cache-Control', 'no-store, private');
    }

    /**
     * Aprueba una solicitud manual.
     */
    public function aprobar(Request $peticion, int $id, KycService $kyc): JsonResponse
    {
        $admin = $this->autorizar();

        return $this->responder($kyc->aprobarManual((int) $admin->id, $id, $this->texto($peticion, 'observacion'), $peticion->ip()));
    }

    /**
     * Rechaza una solicitud manual (el motivo es obligatorio).
     */
    public function rechazar(Request $peticion, int $id, KycService $kyc): JsonResponse
    {
        $admin = $this->autorizar();

        return $this->responder($kyc->rechazarManual((int) $admin->id, $id, $this->texto($peticion, 'motivo'), $this->texto($peticion, 'observacion'), $peticion->ip()));
    }

    /**
     * Solicita al usuario que corrija datos o documentos.
     */
    public function solicitarCorreccion(Request $peticion, int $id, KycService $kyc): JsonResponse
    {
        $admin = $this->autorizar();

        return $this->responder($kyc->solicitarCorreccion((int) $admin->id, $id, $this->texto($peticion, 'correccion'), $peticion->ip()));
    }

    /**
     * Agrega una observación interna.
     */
    public function observacion(Request $peticion, int $id, KycService $kyc): JsonResponse
    {
        $admin = $this->autorizar();

        return $this->responder($kyc->agregarObservacion((int) $admin->id, $id, $this->texto($peticion, 'observacion'), $peticion->ip()));
    }

    /**
     * Suspende o reanuda la eliminación automática de los documentos.
     */
    public function retencion(Request $peticion, int $id, KycService $kyc): JsonResponse
    {
        $admin = $this->autorizar();

        return $this->responder($kyc->suspenderRetencion((int) $admin->id, $id, $peticion->boolean('suspender'), $peticion->ip()));
    }

    /**
     * Elimina la verificación del usuario dueño de la solicitud (el motivo es obligatorio y queda auditado).
     */
    public function eliminar(Request $peticion, int $id, KycService $kyc): JsonResponse
    {
        $admin = $this->autorizar();

        return $this->responder($kyc->eliminarVerificacion((int) $admin->id, $id, $this->texto($peticion, 'motivo'), $peticion->ip()));
    }

    /**
     * Muestra el número de documento completo (queda auditado).
     */
    public function revelarDocumento(Request $peticion, int $id, KycService $kyc): JsonResponse
    {
        $admin = $this->autorizar();

        return $this->responder($kyc->revelarNumeroDocumento((int) $admin->id, $id, $peticion->ip()));
    }

    /**
     * Descarga protegida de un documento para un administrador con manage_kyc.
     */
    public function documento(Request $peticion, int $documento, KycDocumentoService $servicio): Response
    {
        $admin    = $this->autorizar();
        $registro = DocumentoKyc::with('solicitud')->find($documento);

        if (!$registro || !Gate::forUser($admin)->allows('verDocumento', $registro)) {
            abort(404);
        }

        try {
            $archivo = $servicio->leer($registro);
        } catch (DocumentoKycInvalidoException $e) {
            abort(404);
        }

        $servicio->registrarAcceso($registro, EventoKyc::ORIGEN_ADMIN, (int) $admin->id, $peticion->ip());

        return response($archivo['contenido'], 200, [
            'Content-Type'            => $archivo['mime'],
            'Content-Length'          => (string) strlen($archivo['contenido']),
            'Content-Disposition'     => ($archivo['mime'] === 'application/pdf' ? 'attachment' : 'inline')
                . '; filename="kyc-' . $archivo['tipo'] . '.' . $archivo['extension'] . '"',
            'X-Content-Type-Options'  => 'nosniff',
            'Cache-Control'           => 'no-store, private, max-age=0',
            'Pragma'                  => 'no-cache',
            'Content-Security-Policy' => "default-src 'none'; img-src 'self' data:; sandbox",
            'Referrer-Policy'         => 'no-referrer',
            'X-Robots-Tag'            => 'noindex, nofollow',
        ]);
    }

    /**
     * Valor de texto de la petición (solo cadenas; cualquier otro tipo se descarta).
     */
    private function texto(Request $peticion, string $campo): ?string
    {
        $valor = $peticion->input($campo);

        return is_string($valor) ? $valor : null;
    }

    /**
     * Respuesta JSON estándar a partir del resultado del servicio.
     *
     * @param array{success:bool,message:string,mensaje_usuario:string,respuesta:mixed,code:int} $resultado
     */
    private function responder(array $resultado): JsonResponse
    {
        return response()->json($resultado, $resultado['code']);
    }
}
