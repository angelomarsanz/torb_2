<?php

/**
 * Resumen: Controlador para la gestión de Alertas del Sistema en el panel administrativo.
 * Proporciona endpoints para listar alertas paginadas (de 10 en 10), consultar el conteo
 * de alertas no leídas en tiempo real para insignias (badges) y campanas, y marcar
 * alertas como leídas de forma individual o masiva.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Http\Controllers\Admin\Alerta
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Http\Controllers\Admin\Alerta;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Reda\RedaAlojamiento\Models\Alerta\AlertaAdmin;

class AlertaController extends Controller
{
    /**
     * Muestra la vista principal del listado de alertas en el panel administrativo.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('reda-alojamiento::admin.alerta.index');
    }

    /**
     * Obtiene el listado de alertas paginadas (de 10 en 10) vía AJAX para el panel de administración.
     * Soporta filtrado por estado ('todos', 'no_leidas', 'leidas') y retorna los controles
     * de paginación renderizados con la vista general de paginación del plugin.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerAlertasPaginadas(Request $request)
    {
        try {
            $adminActual = Auth::guard('admin')->user();
            $adminId = $adminActual ? $adminActual->id : null;

            $estado = $request->get('estado', 'todos');

            $query = AlertaAdmin::with(['usuario', 'disputa.booking.properties'])
                ->where(function ($q) use ($adminId) {
                    $q->where('admin_id', $adminId)
                      ->orWhereNull('admin_id');
                });

            if ($estado === 'no_leidas') {
                $query->where('leido', 0);
            } elseif ($estado === 'leidas') {
                $query->where('leido', 1);
            }

            $alertasPaginadas = $query->orderBy('created_at', 'desc')->paginate(10);

            // Mapear datos para presentación amigable en el frontend
            $items = $alertasPaginadas->getCollection()->map(function ($alerta) {
                return [
                    'id'            => $alerta->id,
                    'titulo'        => $alerta->titulo,
                    'mensaje'       => $alerta->mensaje,
                    'tipo'          => $alerta->tipo,
                    'leido'         => (bool) $alerta->leido,
                    'fecha'         => $alerta->created_at ? $alerta->created_at->format('d/m/Y H:i') : '',
                    'fecha_humana'  => $alerta->created_at ? $alerta->created_at->diffForHumans() : '',
                    'disputa_id'    => $alerta->disputa_id,
                    'usuario_id'    => $alerta->user_id,
                    'usuario_nombre'=> $alerta->usuario ? ($alerta->usuario->first_name . ' ' . $alerta->usuario->last_name) : null,
                    'usuario_email' => $alerta->usuario ? $alerta->usuario->email : null,
                ];
            });

            // Conteo total de no leídas para mantener actualizados los contadores globales
            $conteoNoLeidas = AlertaAdmin::where(function ($q) use ($adminId) {
                $q->where('admin_id', $adminId)
                  ->orWhereNull('admin_id');
            })->where('leido', 0)->count();

            // Renderizar la barra de paginación estándar de REDA
            $htmlPaginacion = view('reda-alojamiento::admin.general.paginacion', [
                'paginator' => $alertasPaginadas,
                'elements'  => $alertasPaginadas->links()->elements ?? []
            ])->render();

            return response()->json([
                'success' => true,
                'message' => __('Alertas obtenidas con éxito'),
                'mensaje_usuario' => '',
                'respuesta' => [
                    'alertas'        => $items,
                    'html_paginacion'=> $htmlPaginacion,
                    'total'          => $alertasPaginadas->total(),
                    'conteo_no_leidas'=> $conteoNoLeidas,
                    'pagina_actual'  => $alertasPaginadas->currentPage(),
                    'ultima_pagina'  => $alertasPaginadas->lastPage(),
                ],
                'code' => 200
            ], 200);

        } catch (Exception $e) {
            Log::error("Error en AlertaController@obtenerAlertasPaginadas: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'mensaje_usuario' => __('Error al cargar el listado de alertas del sistema.'),
                'respuesta' => '',
                'code' => 500
            ], 500);
        }
    }

    /**
     * Obtiene el conteo de alertas no leídas del administrador actualmente autenticado.
     * Utilizado para alimentar la campana del header y el badge del submenú lateral.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerConteoNoLeidas()
    {
        try {
            $adminActual = Auth::guard('admin')->user();
            $adminId = $adminActual ? $adminActual->id : null;

            $conteo = AlertaAdmin::where(function ($q) use ($adminId) {
                $q->where('admin_id', $adminId)
                  ->orWhereNull('admin_id');
            })->where('leido', 0)->count();

            return response()->json([
                'success' => true,
                'message' => __('Conteo de alertas no leídas recuperado'),
                'mensaje_usuario' => '',
                'respuesta' => [
                    'count' => $conteo
                ],
                'code' => 200
            ], 200);

        } catch (Exception $e) {
            Log::error("Error en AlertaController@obtenerConteoNoLeidas: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'mensaje_usuario' => __('Error al obtener el conteo de alertas no leídas.'),
                'respuesta' => ['count' => 0],
                'code' => 500
            ], 500);
        }
    }

    /**
     * Marca una alerta específica como leída.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function marcarLeida($id)
    {
        try {
            $adminActual = Auth::guard('admin')->user();
            $adminId = $adminActual ? $adminActual->id : null;

            $alerta = AlertaAdmin::where('id', $id)
                ->where(function ($q) use ($adminId) {
                    $q->where('admin_id', $adminId)
                      ->orWhereNull('admin_id');
                })->firstOrFail();

            $alerta->leido = 1;
            $alerta->fecha_lectura = Carbon::now();
            $alerta->save();

            // Nuevo conteo para actualizar badges en la interfaz
            $nuevoConteo = AlertaAdmin::where(function ($q) use ($adminId) {
                $q->where('admin_id', $adminId)
                  ->orWhereNull('admin_id');
            })->where('leido', 0)->count();

            return response()->json([
                'success' => true,
                'message' => __('Alerta marcada como leída'),
                'mensaje_usuario' => __('Alerta marcada como leída con éxito.'),
                'respuesta' => [
                    'conteo_no_leidas' => $nuevoConteo
                ],
                'code' => 200
            ], 200);

        } catch (Exception $e) {
            Log::error("Error en AlertaController@marcarLeida: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'mensaje_usuario' => __('No se pudo marcar la alerta como leída.'),
                'respuesta' => '',
                'code' => 500
            ], 500);
        }
    }

    /**
     * Marca todas las alertas no leídas del administrador actual como leídas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function marcarTodasLeidas()
    {
        try {
            $adminActual = Auth::guard('admin')->user();
            $adminId = $adminActual ? $adminActual->id : null;

            AlertaAdmin::where(function ($q) use ($adminId) {
                $q->where('admin_id', $adminId)
                  ->orWhereNull('admin_id');
            })
            ->where('leido', 0)
            ->update([
                'leido'         => 1,
                'fecha_lectura' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Todas las alertas han sido marcadas como leídas'),
                'mensaje_usuario' => __('Todas las alertas fueron marcadas como leídas.'),
                'respuesta' => [
                    'conteo_no_leidas' => 0
                ],
                'code' => 200
            ], 200);

        } catch (Exception $e) {
            Log::error("Error en AlertaController@marcarTodasLeidas: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'mensaje_usuario' => __('Error al marcar todas las alertas como leídas.'),
                'respuesta' => '',
                'code' => 500
            ], 500);
        }
    }
}
