<?php

/**
 * Controlador de Mediaciones (Disputas) para el Panel Administrativo.
 * 
 * Gestiona el listado, filtrado, conteo y detalle de casos de mediación entre
 * turistas y anfitriones en el backend de Torbian. Permite acceso y gestión tanto
 * a administradores con Rol 1 (Admin) como con Rol 2 (Atención al usuario),
 * y proporciona la gestión de la configuración de límites de mediaciones
 * (primer aviso y segundo aviso/suspensión) exclusivamente para Rol 1.
 * 
 * @package Reda\RedaAlojamiento\Http\Controllers\Admin\Disputa
 */

namespace Reda\RedaAlojamiento\Http\Controllers\Admin\Disputa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Reda\RedaAlojamiento\Models\Disputa\Disputa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DisputaController extends Controller
{
    /**
     * Muestra la vista principal de mediaciones para el administrador.
     */
    public function index()
    {
        return view('reda-alojamiento::admin.disputa.index');
    }

    /**
     * Verifica si el administrador actual tiene permisos completos para gestionar mediaciones.
     * Tienen acceso tanto el Rol 1 (Admin) como el Rol 2 (Atención al usuario).
     *
     * @param int|null $adminId
     * @return bool
     */
    private function tieneAccesoCompletoMediaciones($adminId): bool
    {
        if (!$adminId) {
            return false;
        }

        return \DB::table('role_admin')
            ->leftJoin('roles', 'role_admin.role_id', '=', 'roles.id')
            ->where('role_admin.admin_id', $adminId)
            ->where(function ($q) {
                $q->whereIn('role_admin.role_id', [1, 2])
                  ->orWhereIn(\DB::raw('LOWER(roles.name)'), ['admin', 'atención al usuario', 'atencion al usuario', 'atención a usuario', 'atencion a usuario'])
                  ->orWhereIn(\DB::raw('LOWER(roles.display_name)'), ['admin', 'atención al usuario', 'atencion al usuario', 'atención a usuario', 'atencion a usuario']);
            })
            ->exists();
    }

    /**
     * Obtiene los datos del rol del administrador conectado.
     *
     * @param int|null $adminId
     * @return object|null Objeto con role_id, role_name, display_name
     */
    private function obtenerRolAdmin($adminId)
    {
        if (!$adminId) {
            return null;
        }

        return \DB::table('role_admin')
            ->leftJoin('roles', 'role_admin.role_id', '=', 'roles.id')
            ->where('role_admin.admin_id', $adminId)
            ->select('roles.id as role_id', 'roles.name as role_name', 'roles.display_name')
            ->first();
    }

    /**
     * Obtiene la lista de agentes elegibles para asignación (Rol 2 y Rol 1).
     *
     * @return \Illuminate\Support\Collection
     */
    private function obtenerAgentesDisponibles()
    {
        return \DB::table('admin')
            ->join('role_admin', 'admin.id', '=', 'role_admin.admin_id')
            ->join('roles', 'role_admin.role_id', '=', 'roles.id')
            ->where('admin.status', 'Active')
            ->whereIn('role_admin.role_id', [1, 2])
            ->select(
                'admin.id',
                'admin.username',
                'admin.profile_image',
                'role_admin.role_id',
                'roles.display_name as rol_display_name',
                'roles.name as rol_name'
            )
            ->orderBy('role_admin.role_id', 'desc') // Agentes (rol 2) primero, luego Admin (rol 1)
            ->orderBy('admin.username', 'asc')
            ->get()
            ->map(function ($a) {
                $adminModel = new \App\Models\Admin();
                $adminModel->id = $a->id;
                $adminModel->profile_image = $a->profile_image;

                return [
                    'id' => (int) $a->id,
                    'nombre' => $a->username,
                    'foto' => reda_get_profile_src($adminModel, 'admin'),
                    'role_id' => (int) $a->role_id,
                    'rol_nombre' => $a->rol_display_name ?: ($a->role_id == 1 ? __('Admin') : __('Atención al usuario'))
                ];
            });
    }

    /**
     * Obtiene el listado de mediaciones paginado para el administrador.
     * Permite visualización completa a rol 1 (Admin) y filtrada para rol 2 (Atención al usuario).
     */
    public function obtenerDisputasPaginadas(Request $request)
    {
        $estatus = $request->get('status', 'todos');
        
        // Obtenemos el ID del administrador activo y su rol
        $adminId = auth()->guard('admin')->id();
        $rolData = $this->obtenerRolAdmin($adminId);
        $roleId = $rolData ? (int) $rolData->role_id : null;

        $tieneAcceso = $this->tieneAccesoCompletoMediaciones($adminId);
        if (!$tieneAcceso) {
            return response()->json([
                'success' => false,
                'message' => __('Acceso denegado'),
                'mensaje_usuario' => __('No tiene permisos para acceder a las mediaciones.'),
                'respuesta' => [
                    'data' => [],
                    'pagination' => ''
                ],
                'code' => 403
            ], 403);
        }

        $consulta = Disputa::query();

        // REQUERIMIENTO: Si es Rol 2 (Atención al usuario / Agente), solo puede ver
        // las mediaciones no asignadas o tomadas, y las que le fueron asignadas a él.
        // NO debe ver mediaciones asignadas a otro agente.
        if ($roleId === 2) {
            $consulta->where(function ($q) use ($adminId) {
                $q->whereNull('id_usuario_agente_asignado')
                  ->orWhere('id_usuario_agente_asignado', 0)
                  ->orWhere('id_usuario_agente_asignado', $adminId);
            });
        }

        if ($estatus !== 'todos') {
            // Mapeo de estados del frontend a los valores en la base de datos (traducidos)
            $mapeo = [
                'abiertos' => __('Abierto'),
                'revision' => __('En revisión'),
                'espera'   => __('Esperando respuesta'),
                'resueltos' => __('Resuelto'),
                'cerrados' => __('Cerrado')
            ];
            
            if (isset($mapeo[$estatus])) {
                $consulta->where('estado', $mapeo[$estatus]);
            }
        }

        $disputas = $consulta->with(['booking.properties.property_address', 'agente', 'turista', 'anfitrion'])->orderBy('updated_at', 'desc')->paginate(10);

        // Formatear los datos para el consumo del frontend via Javascript
        $elementos = $disputas->getCollection()->map(function($d) use ($adminId) {
            
            // Lógica para contar mensajes no leídos (consistente con MensajeController)
            $usuarioA = $d->id_usuario_turista;
            $usuarioB = $d->id_usuario_anfitrion;
            $propertyId = $d->booking ? $d->booking->property_id : null;
            
            $nuevosMensajes = 0;
            if ($propertyId) {
                $sharedBookingIds = \DB::table('bookings')
                    ->where('property_id', $propertyId)
                    ->where(function($q) use ($usuarioA, $usuarioB) {
                        $q->where(function($q2) use ($usuarioA, $usuarioB) {
                            $q2->where('user_id', $usuarioA)->where('host_id', $usuarioB);
                        })->orWhere(function($q2) use ($usuarioA, $usuarioB) {
                            $q2->where('user_id', $usuarioB)->where('host_id', $usuarioA);
                        });
                    })->pluck('id')->toArray();

                $nuevosMensajes = \App\Models\Messages::where('property_id', $propertyId)
                    ->whereIn('booking_id', $sharedBookingIds)
                    ->where('read', 0)
                    ->where(function($q) use ($adminId) {
                        $q->whereNotExists(function ($query) {
                              $query->select(\DB::raw(1))
                                    ->from('reda_mensajes_metadata')
                                    ->whereColumn('reda_mensajes_metadata.message_id', 'messages.id')
                                    ->where('reda_mensajes_metadata.sender_type', 'admin');
                          })
                          ->orWhere('sender_id', '!=', $adminId);
                    })
                    ->count();
            }

            // Adjuntos del Turista
            $adjuntosTurista = [];
            if ($d->documentos_turista) {
                $rutas = json_decode($d->documentos_turista, true);
                if (is_array($rutas)) {
                    foreach ($rutas as $ruta) {
                        $webPath = (strpos($ruta, 'public/') === 0) ? '/' . $ruta : '/public/' . $ruta;
                        $adjuntosTurista[] = [
                            'nombre' => basename($ruta),
                            'url' => $webPath,
                            'es_imagen' => in_array(strtolower(pathinfo($ruta, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])
                        ];
                    }
                }
            }

            // Adjuntos del Anfitrión
            $adjuntosAnfitrion = [];
            if ($d->documentos_anfitrion) {
                $rutas = json_decode($d->documentos_anfitrion, true);
                if (is_array($rutas)) {
                    foreach ($rutas as $ruta) {
                        $webPath = (strpos($ruta, 'public/') === 0) ? '/' . $ruta : '/public/' . $ruta;
                        $adjuntosAnfitrion[] = [
                            'nombre' => basename($ruta),
                            'url' => $webPath,
                            'es_imagen' => in_array(strtolower(pathinfo($ruta, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])
                        ];
                    }
                }
            }

            // Combinar adjuntos para el resumen general del admin (o mostrar por separado)
            $adjuntos = array_merge($adjuntosTurista, $adjuntosAnfitrion);

            // Asegurar prefijo /public/ para la foto de la propiedad si es ruta relativa
            $propiedadFoto = $d->booking && $d->booking->properties ? $d->booking->properties->cover_photo : '/public/img/unnamed.png';
            if ($d->booking && $d->booking->properties && strpos($propiedadFoto, 'http') === false) {
                $propiedadFoto = (strpos($propiedadFoto, 'public/') === 0) ? '/' . $propiedadFoto : '/public/' . $propiedadFoto;
            }

            // Datos de ubicación
            $ubicacion = '';
            if ($d->booking && $d->booking->properties && $d->booking->properties->property_address) {
                $direccion = $d->booking->properties->property_address;
                $ubicacion = trim(($direccion->city ?? '') . ', ' . ($direccion->state ?? ''), ', ');
            }

            return [
                'id' => $d->id,
                'estado' => $d->estado,
                'paso_actual' => $d->paso_actual,
                'motivo' => $d->motivo,
                'prioridad' => $d->prioridad,
                'descripcion' => $d->descripcion,
                'booking_id' => $d->booking_id,
                'booking_start_date' => $d->booking ? date('d/m/Y', strtotime($d->booking->start_date)) : '',
                'booking_end_date' => $d->booking ? date('d/m/Y', strtotime($d->booking->end_date)) : '',
                'booking_guest' => $d->booking ? $d->booking->guest : 0,
                'propiedad_nombre' => $d->booking && $d->booking->properties ? $d->booking->properties->name : '',
                'propiedad_ubicacion' => $ubicacion,
                'adjuntos' => $adjuntos,
                'adjuntos_turista' => $adjuntosTurista,
                'adjuntos_anfitrion' => $adjuntosAnfitrion,
                'fecha_apertura' => $d->fecha_apertura ? $d->fecha_apertura->format('d/m/Y H:i') : '',
                'actualizado_hace' => $d->updated_at->diffForHumans(),
                'id_usuario_agente_asignado' => $d->id_usuario_agente_asignado ? (int) $d->id_usuario_agente_asignado : null,
                'agente' => $d->agente ? [
                    'id' => (int) $d->agente->id,
                    'nombre' => $d->agente->username,
                    'foto' => reda_get_profile_src($d->agente, 'admin')
                ] : null,
                'turista_nombre' => $d->turista ? $d->turista->first_name . ' ' . $d->turista->last_name : '',
                'turista_foto' => reda_get_profile_src($d->turista),
                'anfitrion_nombre' => $d->anfitrion ? $d->anfitrion->first_name . ' ' . $d->anfitrion->last_name : '',
                'anfitrion_foto' => reda_get_profile_src($d->anfitrion),
                'propiedad_foto' => $propiedadFoto,
                'id_usuario_inicial' => $d->id_usuario_inicial,
                'rol_usuario_inicial' => $d->rol_usuario_inicial,
                'id_usuario_turista' => $d->id_usuario_turista,
                'id_usuario_anfitrion' => $d->id_usuario_anfitrion,
                'conteo_mensajes_nuevos' => $nuevosMensajes,
            ];
        });

        $agentesDisponibles = ($roleId === 1) ? $this->obtenerAgentesDisponibles() : [];

        $respuesta = [
            'success' => true,
            'message' => __('Listado de mediaciones (Admin)'),
            'debug' => [
                'admin_id' => $adminId,
                'role_id' => $roleId
            ],
            'mensaje_usuario' => __('Listado recuperado con éxito'),
            'respuesta' => [
                'data' => $elementos,
                'pagination' => (string) $disputas->appends(request()->except('page'))->links('reda-alojamiento::admin.general.paginacion'),
                'rol_admin' => $roleId,
                'admin_id' => $adminId,
                'agentes' => $agentesDisponibles
            ],
            'code' => 200
        ];

        return response()->json($respuesta, $respuesta['code']);
    }

    /**
     * Obtiene el conteo de mediaciones activas para el administrador.
     * Se consideran activas aquellas cuyo estado es diferente a 'Cerrado' o 'Cerrada'.
     * Para rol 2, filtra solo las no asignadas y las asignadas a él.
     */
    public function obtenerConteoDisputasActivas()
    {
        try {
            $adminId = auth()->guard('admin')->id();
            $rolData = $this->obtenerRolAdmin($adminId);
            $roleId = $rolData ? (int) $rolData->role_id : null;

            $query = Disputa::query();

            // Para agentes (Rol 2), solo contar las no asignadas y las asignadas a él
            if ($roleId === 2) {
                $query->where(function ($q) use ($adminId) {
                    $q->whereNull('id_usuario_agente_asignado')
                      ->orWhere('id_usuario_agente_asignado', 0)
                      ->orWhere('id_usuario_agente_asignado', $adminId);
                });
            }

            // Filtramos por estados que NO sean 'Cerrado' o 'Cerrada' (y sus versiones traducidas)
            $estadosCerrados = [
                'Cerrado', 
                'Cerrada', 
                __('Cerrado'), 
                __('Cerrada')
            ];

            $conteo = $query->whereNotIn('estado', array_unique($estadosCerrados))->count();

            return response()->json([
                'success' => true,
                'message' => __('Conteo de mediaciones activas (Admin)'),
                'mensaje_usuario' => __('Conteo recuperado con éxito'),
                'respuesta' => $conteo,
                'code' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al obtener conteo de mediaciones (Admin): " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'mensaje_usuario' => __('Error al obtener el conteo de mediaciones'),
                'respuesta' => 0,
                'code' => 500
            ], 500);
        }
    }

    /**
     * Retorna el HTML del modal de detalle de mediación para el administrador.
     * Permite acceso tanto a rol 1 (Admin) como a rol 2 (Atención al usuario).
     */
    public function getDetailModal($id)
    {
        $adminId = auth()->guard('admin')->id();
        $rolData = $this->obtenerRolAdmin($adminId);
        $roleId = $rolData ? (int) $rolData->role_id : null;

        $disputa = Disputa::findOrFail($id);

        // Seguridad: Si es Rol 2 y la mediación ya está asignada a otro agente, denegar acceso
        if ($roleId === 2 && $disputa->id_usuario_agente_asignado && $disputa->id_usuario_agente_asignado != $adminId) {
            return response()->json([
                'success' => false,
                'message' => __('Acceso denegado'),
                'mensaje_usuario' => __('No tiene permisos para ver el detalle de esta mediación porque está asignada a otro agente.'),
                'code' => 403
            ], 403);
        }

        // Usamos la vista específica para admin adaptada a Bootstrap 5
        $html = view('reda-alojamiento::admin.disputa.modal_detalle', compact('disputa'))->render();
        
        $respuesta = [
            'success' => true,
            'message' => __('Carga de detalle'),
            'mensaje_usuario' => __('Cargado con éxito'),
            'respuesta' => $html,
            'code' => 200
        ];

        return response()->json($respuesta, $respuesta['code']);
    }

    /**
     * Asigna o toma una mediación por parte de un administrador o agente.
     * Si el usuario logueado tiene Rol 1, puede asignar a cualquier agente o a sí mismo.
     * Si el usuario logueado tiene Rol 2, toma la mediación para sí mismo ("Tomar mediación").
     * Actualiza la columna id_usuario_agente_asignado en la tabla disputas.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function asignarAgente(Request $request)
    {
        try {
            $adminId = auth()->guard('admin')->id();
            if (!$adminId) {
                return response()->json([
                    'success' => false,
                    'message' => __('No autenticado'),
                    'mensaje_usuario' => __('La sesión ha expirado. Por favor, inicie sesión nuevamente.'),
                    'respuesta' => '',
                    'code' => 401
                ], 401);
            }

            $rolData = $this->obtenerRolAdmin($adminId);
            $roleId = $rolData ? (int) $rolData->role_id : null;

            if (!in_array($roleId, [1, 2])) {
                return response()->json([
                    'success' => false,
                    'message' => __('Acceso denegado'),
                    'mensaje_usuario' => __('No tiene permisos para asignar mediaciones.'),
                    'respuesta' => '',
                    'code' => 403
                ], 403);
            }

            $disputaId = $request->input('disputa_id');
            if (!$disputaId) {
                return response()->json([
                    'success' => false,
                    'message' => __('ID de disputa no proporcionado'),
                    'mensaje_usuario' => __('Debe especificar una mediación válida.'),
                    'respuesta' => '',
                    'code' => 400
                ], 400);
            }

            $disputa = Disputa::find($disputaId);
            if (!$disputa) {
                return response()->json([
                    'success' => false,
                    'message' => __('Disputa no encontrada'),
                    'mensaje_usuario' => __('La mediación indicada no existe.'),
                    'respuesta' => '',
                    'code' => 404
                ], 404);
            }

            $agenteAsignarId = null;

            if ($roleId === 1) {
                // Rol 1 (Admin): Puede asignar a cualquier agente activo (Rol 2) o a sí mismo (Rol 1)
                $agenteAsignarId = $request->input('agente_id');
                if (!$agenteAsignarId) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Agente no especificado'),
                        'mensaje_usuario' => __('Por favor seleccione un agente válido.'),
                        'respuesta' => '',
                        'code' => 400
                    ], 400);
                }

                // Validar que el agente existe, esté activo y tenga rol 1 o 2
                $agenteValido = \DB::table('admin')
                    ->join('role_admin', 'admin.id', '=', 'role_admin.admin_id')
                    ->where('admin.id', $agenteAsignarId)
                    ->where('admin.status', 'Active')
                    ->whereIn('role_admin.role_id', [1, 2])
                    ->exists();

                if (!$agenteValido) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Agente inválido'),
                        'mensaje_usuario' => __('El agente seleccionado no es válido o no está activo.'),
                        'respuesta' => '',
                        'code' => 422
                    ], 422);
                }
            } else if ($roleId === 2) {
                // Rol 2 (Atención al usuario): Toma la mediación para sí mismo
                // Validar que no haya sido tomada previamente por otro agente
                if ($disputa->id_usuario_agente_asignado && $disputa->id_usuario_agente_asignado != $adminId) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Mediación ya asignada'),
                        'mensaje_usuario' => __('Esta mediación ya ha sido asignada a otro agente.'),
                        'respuesta' => '',
                        'code' => 409
                    ], 409);
                }

                $agenteAsignarId = $adminId;
            }

            // Actualizar la columna id_usuario_agente_asignado en la tabla disputas
            $disputa->id_usuario_agente_asignado = $agenteAsignarId;
            $disputa->save();

            // Cargar datos del agente asignado para devolverlos a la interfaz
            $agenteAdmin = \App\Models\Admin::find($agenteAsignarId);
            $agenteFoto = reda_get_profile_src($agenteAdmin, 'admin');

            $mensajeAccion = ($roleId === 2 || $agenteAsignarId == $adminId)
                ? __('Has tomado la mediación exitosamente.')
                : __('La mediación ha sido asignada a :nombre exitosamente.', ['nombre' => $agenteAdmin->username]);

            return response()->json([
                'success' => true,
                'message' => __('Mediación asignada'),
                'mensaje_usuario' => $mensajeAccion,
                'respuesta' => [
                    'disputa_id' => $disputa->id,
                    'agente_id' => (int) $agenteAsignarId,
                    'agente_nombre' => $agenteAdmin->username,
                    'agente_foto' => $agenteFoto
                ],
                'code' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al asignar agente a la disputa: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'mensaje_usuario' => __('Ocurrió un error al asignar la mediación. Por favor, intente nuevamente.'),
                'respuesta' => '',
                'code' => 500
            ], 500);
        }
    }

    /**
     * Muestra la vista de configuración de mediaciones para administradores con Rol 1.
     * Recupera de la tabla settings los umbrales de primer aviso y segundo aviso/suspensión.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function configuracion()
    {
        $adminActual = Auth::guard('admin')->user();
        $adminId = $adminActual ? $adminActual->id : null;
        $rolData = $this->obtenerRolAdmin($adminId);
        $esAdminRol1 = $rolData && ((int) $rolData->role_id === 1 || strtolower(trim($rolData->role_name ?? '')) === 'admin');

        if (!$esAdminRol1) {
            abort(403, __('No tienes permisos para acceder a la configuración de mediaciones.'));
        }

        // Recuperar configuraciones desde la tabla settings
        $settingPrimerAviso = \DB::table('settings')
            ->where('name', 'Cantidad mediaciones permitidas primer aviso')
            ->first();

        $settingSegundoAviso = \DB::table('settings')
            ->where('name', 'Cantidad mediaciones segundo aviso y suspensión')
            ->first();

        $primerAviso = $settingPrimerAviso ? $settingPrimerAviso->value : '';
        $segundoAviso = $settingSegundoAviso ? $settingSegundoAviso->value : '';

        return view('reda-alojamiento::admin.disputa.configuracion', compact('primerAviso', 'segundoAviso'));
    }

    /**
     * Guarda la configuración de mediaciones en la tabla settings.
     * Valida y actualiza los registros 'Cantidad mediaciones permitidas primer aviso'
     * y 'Cantidad mediaciones segundo aviso y suspensión' con type 'Mediaciones'.
     * Exclusivo para administradores con Rol 1.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardarConfiguracion(Request $request)
    {
        try {
            $adminActual = Auth::guard('admin')->user();
            $adminId = $adminActual ? $adminActual->id : null;
            $rolData = $this->obtenerRolAdmin($adminId);
            $esAdminRol1 = $rolData && ((int) $rolData->role_id === 1 || strtolower(trim($rolData->role_name ?? '')) === 'admin');

            if (!$esAdminRol1) {
                return response()->json([
                    'success' => false,
                    'message' => __('Acceso no autorizado'),
                    'mensaje_usuario' => __('No tienes permisos para modificar la configuración de mediaciones.'),
                    'respuesta' => '',
                    'code' => 403
                ], 403);
            }

            // Validación de los campos
            $request->validate([
                'primer_aviso' => 'required|integer|min:0',
                'segundo_aviso' => 'required|integer|min:0',
            ], [
                'primer_aviso.required' => __('La cantidad de mediaciones para primer aviso es obligatoria.'),
                'primer_aviso.integer' => __('La cantidad de mediaciones para primer aviso debe ser un número entero.'),
                'primer_aviso.min' => __('La cantidad de mediaciones para primer aviso no puede ser negativa.'),
                'segundo_aviso.required' => __('La cantidad de mediaciones para segundo aviso y suspensión es obligatoria.'),
                'segundo_aviso.integer' => __('La cantidad de mediaciones para segundo aviso y suspensión debe ser un número entero.'),
                'segundo_aviso.min' => __('La cantidad de mediaciones para segundo aviso y suspensión no puede ser negativa.'),
            ]);

            // Guardar o actualizar en la tabla settings con name, value y type 'Mediaciones'
            \DB::table('settings')->updateOrInsert(
                ['name' => 'Cantidad mediaciones permitidas primer aviso'],
                [
                    'value' => (string) $request->primer_aviso,
                    'type' => 'Mediaciones'
                ]
            );

            \DB::table('settings')->updateOrInsert(
                ['name' => 'Cantidad mediaciones segundo aviso y suspensión'],
                [
                    'value' => (string) $request->segundo_aviso,
                    'type' => 'Mediaciones'
                ]
            );

            // Limpiar caché de settings del core si existe
            try {
                \Illuminate\Support\Facades\Cache::forget(config('cache.prefix') . '.settings');
            } catch (\Exception $cacheEx) {
                // Silencioso si la clave o el driver de caché no aplican
            }

            return response()->json([
                'success' => true,
                'message' => __('Configuración de mediaciones guardada con éxito'),
                'mensaje_usuario' => __('Configuración de mediaciones guardada con éxito.'),
                'respuesta' => [
                    'primer_aviso' => (int) $request->primer_aviso,
                    'segundo_aviso' => (int) $request->segundo_aviso,
                ],
                'code' => 200
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            $primerError = collect($ve->errors())->flatten()->first();
            return response()->json([
                'success' => false,
                'message' => $primerError,
                'mensaje_usuario' => $primerError,
                'respuesta' => $ve->errors(),
                'code' => 422
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error al guardar la configuración de mediaciones: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'mensaje_usuario' => __('Ocurrió un error al guardar la configuración. Por favor, intente nuevamente.'),
                'respuesta' => '',
                'code' => 500
            ], 500);
        }
    }

}
