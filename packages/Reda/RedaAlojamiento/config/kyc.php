<?php

/**
 * Resumen: Configuración del sistema KYC (verificación de identidad) del plugin REDA.
 * Centraliza el interruptor de emergencia, la tabla de equivalencia entre los estados
 * de Didit y los estados internos, los documentos admitidos (Venezuela y extranjeros),
 * los límites de intentos, los parámetros de archivos y la política de retención.
 * Las credenciales de Didit NO se definen aquí: viven en .env y se exponen en services.didit.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Config
 * @author     REDA Tech Team
 * @version    1.0.0
 */

use Reda\RedaAlojamiento\Enums\EstadoKyc;

return [

    /*
    |--------------------------------------------------------------------------
    | Interruptor de emergencia
    |--------------------------------------------------------------------------
    | Si es false, el bloqueo de operaciones por KYC se desactiva (procedimiento de
    | rollback). No desactiva el registro de solicitudes ni el panel administrativo.
    */
    'bloqueo_habilitado' => env('KYC_ENFORCEMENT', true),

    /*
    |--------------------------------------------------------------------------
    | Edición de publicaciones existentes (decisión D1)
    |--------------------------------------------------------------------------
    | Siempre se bloquea crear, completar borradores y volver a activar publicaciones.
    | Si es true, además se bloquea editar alojamientos y negocios ya publicados.
    */
    'bloquear_edicion_publicados' => env('KYC_BLOQUEAR_EDICION_PUBLICADOS', false),

    /*
    |--------------------------------------------------------------------------
    | País predeterminado de los formularios (ISO 3166-1 alfa-2)
    |--------------------------------------------------------------------------
    | Venezuela es el valor inicial, pero el usuario puede elegir cualquier país.
    */
    'pais_predeterminado' => 'VE',

    /*
    |--------------------------------------------------------------------------
    | Integración con Didit (parámetros no secretos)
    |--------------------------------------------------------------------------
    */
    'didit' => [
        // Tolerancia entre X-Timestamp del webhook y la hora del servidor.
        'tolerancia_timestamp_segundos' => 300,

        // Confirmar por API (GET /v3/session/{id}/decision/) antes de marcar verified.
        'confirmar_aprobacion_por_api' => true,

        // false: vendor_data estable "kyc-{user_id}" y trazabilidad por metadata y session_id.
        // true: vendor_data único por intento "kyc-{user_id}-{solicitud_id}".
        // Con true, Didit trata cada intento como una persona distinta y pierde la detección
        // de duplicados entre intentos del mismo usuario; por eso el valor por defecto es false.
        'vendor_data_por_intento' => false,

        // Tiempo mínimo entre consultas de respaldo de una misma solicitud.
        'intervalo_consulta_respaldo_segundos' => 30,

        // Antigüedad a partir de la cual el comando programado consulta solicitudes abiertas.
        'minutos_para_consulta_programada' => 5,

        // Tabla de equivalencia única. Los estados de Didit se comparan con igualdad estricta
        // (distinguen mayúsculas y espacios). Un estado ausente de esta tabla no cambia nada.
        'mapa_estados' => [
            'Not Started'  => EstadoKyc::EnProceso->value,
            'In Progress'  => EstadoKyc::EnProceso->value,
            'Resubmitted'  => EstadoKyc::EnProceso->value,
            'In Review'    => EstadoKyc::Pendiente->value,
            'Approved'     => EstadoKyc::Verificado->value,
            'Declined'     => EstadoKyc::Fallido->value,
            'Abandoned'    => EstadoKyc::Fallido->value,
            'Expired'      => EstadoKyc::Expirado->value,
            'Kyc Expired'  => EstadoKyc::Expirado->value,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de documento de la verificación manual
    |--------------------------------------------------------------------------
    | codigo_didit: código de expected_document_types de Didit (P, ID, DL, RP).
    | requiere_reverso: si el documento tiene información en el reverso.
    | requiere_selfie: la fotografía sosteniendo el documento es obligatoria.
    */
    'tipos_documento' => [
        'cedula_identidad' => [
            'etiqueta'         => 'Cédula de identidad',
            'codigo_didit'     => 'ID',
            'requiere_reverso' => true,
            'requiere_selfie'  => true,
        ],
        'pasaporte' => [
            'etiqueta'         => 'Pasaporte',
            'codigo_didit'     => 'P',
            'requiere_reverso' => false,
            'requiere_selfie'  => true,
        ],
        'licencia_conducir' => [
            'etiqueta'         => 'Licencia de conducir',
            'codigo_didit'     => 'DL',
            'requiere_reverso' => true,
            'requiere_selfie'  => true,
        ],
        'permiso_residencia' => [
            'etiqueta'         => 'Permiso o carnet de residencia',
            'codigo_didit'     => 'RP',
            'requiere_reverso' => true,
            'requiere_selfie'  => true,
        ],
        'documento_extranjero' => [
            'etiqueta'         => 'Documento de identidad extranjero',
            'codigo_didit'     => 'ID',
            'requiere_reverso' => true,
            'requiere_selfie'  => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Documentos privados
    |--------------------------------------------------------------------------
    */
    'documentos' => [
        // Disco privado registrado por el plugin (sin URL pública).
        'disco' => 'kyc_privado',

        // Carpeta del disco privado. Vacío = storage/app/private/kyc. Como la raíz web de este proyecto es la
        // raíz del código, se recomienda una ruta FUERA de public_html (por ejemplo /home/appvac/kyc_privado).
        'ruta' => env('KYC_RUTA_PRIVADA'),

        'extensiones_permitidas' => ['jpg', 'jpeg', 'png', 'pdf'],
        'mimes_permitidos'       => ['image/jpeg', 'image/png', 'application/pdf'],
        'tamano_maximo_kb'       => 5120,
        'maximo_archivos'        => 3,

        // La selfie de validación solo admite imágenes; frente y reverso admiten también PDF.
        'tipos_solo_imagen' => ['selfie'],

        // Límites de las imágenes (se rechazan las ilegibles y las que pueden agotar la memoria al procesarlas).
        'lado_minimo_px'     => 300,
        'lado_maximo_px'     => 12000,
        'megapixeles_maximo' => 40,
        'calidad_jpeg'       => 90,

        // Cifrado en reposo con la clave de la aplicación (APP_KEY).
        'cifrar' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Datos personales de la verificación manual
    |--------------------------------------------------------------------------
    */
    'manual' => [
        // Edad mínima para verificarse (las operaciones comerciales exigen mayoría de edad).
        'edad_minima' => 18,
    ],

    /*
    |--------------------------------------------------------------------------
    | Límites de intentos
    |--------------------------------------------------------------------------
    */
    'limites' => [
        'intentos_didit_por_dia'    => 5,
        'envios_manuales_por_dia'   => 3,
        'reenvios_correccion_por_dia' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Política de retención de documentos
    |--------------------------------------------------------------------------
    | Es independiente de la lógica de aprobación del KYC: eliminar un archivo nunca
    | modifica el estado de verificación. Los valores se expresan en días y null
    | desactiva la eliminación automática para ese caso. Los plazos definitivos deben
    | validarse con la asesoría legal según la normativa de protección de datos aplicable.
    */
    'retencion' => [
        'automatica_habilitada'  => env('KYC_RETENCION_AUTOMATICA', true),
        'dias_tras_aprobacion'   => 90,
        'dias_tras_rechazo'      => 30,
        'dias_tras_fallo'        => 30,
        'dias_tras_expiracion'   => 30,

        // Nunca eliminar si hay una revisión, disputa, investigación o proceso pendiente.
        'respetar_procesos_pendientes' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Bloqueo de operaciones sin KYC (Fase 6)
    |--------------------------------------------------------------------------
    */

    // Sección a la que se redirige a un usuario no verificado (el módulo KYC del perfil).
    'url_seccion' => 'users/edit-verification',

    /*
    |--------------------------------------------------------------------------
    | Notificaciones del KYC (Fase 8)
    |--------------------------------------------------------------------------
    | habilitadas: interruptor general (KYC_NOTIFICACIONES=false apaga todos los avisos sin tocar código).
    | correo_usuario: correos al usuario cuando su verificación cambia o se le pide una corrección.
    | alerta_admin / correo_admin: alerta en el panel y correo a los administradores activos con manage_kyc
    | cuando llega una solicitud que requiere revisión.
    | Los correos nunca incluyen el texto libre del administrador (motivo del rechazo, corrección solicitada)
    | ni datos del documento: solo avisan y enlazan a la pantalla protegida por la sesión del usuario.
    */
    'notificaciones' => [
        'habilitadas'    => env('KYC_NOTIFICACIONES', true),
        'correo_usuario' => true,
        'alerta_admin'   => true,
        'correo_admin'   => true,
    ],

    // Reservas: estados de una reserva existente que espera el pago del huésped (el anfitrión ya la aceptó).
    // Pagar una reserva así NO es una reserva nueva y no se bloquea.
    'reservas' => [
        'estados_pago_pendiente' => ['Processing'],

        // Si un usuario verificado inicia una reserva y pierde la verificación antes de terminar el pago,
        // puede completarla durante esta ventana (evita cobrar sin crear la reserva). 0 = sin ventana.
        'ventana_autorizacion_minutos' => 45,
    ],

    // Controladores cuyas rutas deben estar clasificadas (restringidas o permitidas con justificación).
    // reda:kyc-auditar-rutas falla si una ruta de estos controladores no está clasificada.
    'controladores_vigilados' => [
        \App\Http\Controllers\PropertyController::class,
        \App\Http\Controllers\PaymentController::class,
        \App\Http\Controllers\BookingController::class,
        \App\Http\Controllers\TripsController::class,
        \App\Http\Controllers\CalendarController::class,
        \Modules\Gateway\Http\Controllers\GatewayController::class,
        \Reda\RedaAlojamiento\Http\Controllers\General\PasarelaPagoController::class,
        \Reda\RedaAlojamiento\Http\Controllers\General\RedaPaymentController::class,
        \Reda\RedaAlojamiento\Http\Controllers\General\RedaBookingController::class,
        \Reda\RedaAlojamiento\Http\Controllers\General\ChatController::class,
        \Reda\RedaAlojamiento\Http\Controllers\General\MediaController::class,
        \Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController::class,
    ],

    /*
    | Operaciones restringidas: "Controlador@método" => etiqueta de la regla.
    | El middleware kyc.verificado se añade a toda ruta cuya ACCIÓN coincida (sin depender de la URL, de modo que
    | las rutas duplicadas o renombradas por módulos u overrides del plugin quedan protegidas igual).
    |   null          siempre restringida.
    |   reserva_nueva restringida salvo que pague una reserva EXISTENTE del usuario que espera el pago.
    |   activacion    restringida al activar una publicación (no al desactivarla).
    |   borrador      restringida al modificar un alojamiento que aún no está publicado.
    |   edicion       restringida solo si bloquear_edicion_publicados=true (D1) y la petición modifica datos.
    | Además existen los observadores de modelo (BloqueoKycObserver) que impiden crear propiedades, reservas
    | reales, negocios y actividades aunque aparezca una ruta nueva sin clasificar.
    */
    'operaciones_restringidas' => [
        // Alojamientos
        'App\Http\Controllers\PropertyController@create'       => null,
        'App\Http\Controllers\PropertyController@updateStatus' => 'activacion',
        'App\Http\Controllers\PropertyController@listing'      => 'borrador',
        'App\Http\Controllers\PropertyController@photoMessage'      => 'edicion',
        'App\Http\Controllers\PropertyController@photoDelete'       => 'edicion',
        'App\Http\Controllers\PropertyController@makeDefaultPhoto'  => 'edicion',
        'App\Http\Controllers\PropertyController@makePhotoSerial'   => 'edicion',
        'App\Http\Controllers\CalendarController@calenderJson'      => 'edicion',
        'App\Http\Controllers\CalendarController@calenderPriceSet'  => 'edicion',
        'App\Http\Controllers\CalendarController@icalendarImport'   => 'edicion',

        // Reservas nuevas (el override del plugin sustituye PaymentController@index por RedaPaymentController@index)
        'App\Http\Controllers\PaymentController@index'         => 'reserva_nueva',
        'App\Http\Controllers\PaymentController@createBooking' => 'reserva_nueva',
        'Reda\RedaAlojamiento\Http\Controllers\General\RedaPaymentController@index' => 'reserva_nueva',
        'Reda\RedaAlojamiento\Http\Controllers\General\RedaPaymentController@redirectReservar'     => null,
        'Reda\RedaAlojamiento\Http\Controllers\General\RedaPaymentController@checkBookingRedirect' => null,
        'Modules\Gateway\Http\Controllers\GatewayController@pay'         => 'reserva_nueva',
        'Modules\Gateway\Http\Controllers\GatewayController@makePayment' => 'reserva_nueva',
        // El plugin sustituye makePayment por su propio controlador (mensaje y comprobantes de transferencias)
        'Reda\RedaAlojamiento\Http\Controllers\General\PasarelaPagoController@makePayment' => 'reserva_nueva',

        // Negocios
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@create'         => null,
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@agregarActividad' => null,
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@formularioDePasosExperiencias' => 'edicion',
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@guardarHorario'            => 'edicion',
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@eliminarHorario'           => 'edicion',
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@reordenarActividades'     => 'edicion',
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@actualizarPreciosLote'    => 'edicion',
        'Reda\RedaAlojamiento\Http\Controllers\General\MediaController@uploadPhoto'      => 'edicion',
        'Reda\RedaAlojamiento\Http\Controllers\General\MediaController@deletePhoto'      => 'edicion',
        'Reda\RedaAlojamiento\Http\Controllers\General\MediaController@makeDefaultPhoto' => 'edicion',
        'Reda\RedaAlojamiento\Http\Controllers\General\MediaController@cropPhoto'        => 'edicion',
    ],

    /*
    | Acciones originales que el plugin sustituye por una versión propia al arrancar (routes "booted" del
    | ServiceProvider). Se mantienen clasificadas por si el override se desactivara, pero no generan aviso de
    | "acción sin ruta" mientras el override esté activo.
    */
    'acciones_alternativas' => [
        'App\Http\Controllers\PaymentController@index',
        'Modules\Gateway\Http\Controllers\GatewayController@makePayment',
        'Modules\Gateway\Http\Controllers\GatewayController@paymentCallback',
    ],

    /*
    | Rutas de los controladores vigilados que NO se restringen, con su justificación. Cualquier ruta nueva de
    | esos controladores que no esté aquí ni en operaciones_restringidas hace fallar la auditoría.
    */
    'rutas_permitidas' => [
        // Reservas existentes, cancelaciones y consulta (se conservan las reglas actuales de reservas y pagos)
        'App\Http\Controllers\BookingController@index'          => 'Consulta de una reserva existente.',
        'App\Http\Controllers\BookingController@myBookings'     => 'Consulta de reservas existentes.',
        'App\Http\Controllers\BookingController@requested'      => 'Consulta de solicitudes de reserva existentes.',
        'App\Http\Controllers\BookingController@requestPayment' => 'Pago de una reserva ya creada y aceptada por el anfitrión (no es una reserva nueva).',
        'App\Http\Controllers\BookingController@accept'         => 'El anfitrión responde a una reserva existente (D2).',
        'App\Http\Controllers\BookingController@decline'        => 'El anfitrión responde a una reserva existente (D2).',
        'App\Http\Controllers\BookingController@expire'         => 'Vencimiento de una reserva existente.',
        'App\Http\Controllers\BookingController@hostCancel'     => 'Cancelación de una reserva existente.',
        'App\Http\Controllers\TripsController@guestCancel'      => 'Cancelación de una reserva existente.',
        'App\Http\Controllers\TripsController@myTrips'          => 'Consulta de viajes existentes.',
        'App\Http\Controllers\TripsController@receipt'          => 'Consulta del comprobante de una reserva existente.',
        'Reda\RedaAlojamiento\Http\Controllers\General\RedaBookingController@getActiveBookingPropertyIds' => 'Consulta de reservas existentes.',
        'Reda\RedaAlojamiento\Http\Controllers\General\RedaBookingController@getBookingDetails'           => 'Consulta de reservas existentes.',
        'Reda\RedaAlojamiento\Http\Controllers\General\RedaBookingController@getCountActiveBookings'      => 'Consulta de reservas existentes.',
        'Reda\RedaAlojamiento\Http\Controllers\General\RedaBookingController@getHuespedesInfo'            => 'Consulta de reservas existentes.',
        'Reda\RedaAlojamiento\Http\Controllers\General\RedaBookingController@getPaidBookingIds'           => 'Consulta de reservas existentes.',
        'Reda\RedaAlojamiento\Http\Controllers\General\RedaBookingController@obtenerFechasOcupadas'       => 'Consulta de disponibilidad (navegación).',

        // Chat
        'Reda\RedaAlojamiento\Http\Controllers\General\ChatController@iniciarChat' => 'Chat y mensajería no se bloquean: crea una consulta (reserva con estado vacío), que no es una reserva real.',

        // Pasarelas: páginas, retornos y webhooks de pagos ya iniciados
        'Modules\Gateway\Http\Controllers\GatewayController@paymentGateways'    => 'Listado de pasarelas (consulta).',
        'Modules\Gateway\Http\Controllers\GatewayController@paymentCallback'    => 'Retorno de un pago ya iniciado por un usuario autorizado.',
        'Reda\RedaAlojamiento\Http\Controllers\General\PasarelaPagoController@paymentCallback' => 'Retorno de un pago ya iniciado por un usuario autorizado (override del plugin).',
        'Modules\Gateway\Http\Controllers\GatewayController@paymentCancelled'   => 'Pago cancelado.',
        'Modules\Gateway\Http\Controllers\GatewayController@paymentConfirmation' => 'Confirmación de un pago ya realizado.',
        'Modules\Gateway\Http\Controllers\GatewayController@paymentFailed'      => 'Pago fallido.',
        'Modules\Gateway\Http\Controllers\GatewayController@paymentHook'        => 'Webhook de la pasarela de un pago ya iniciado por un usuario autorizado.',
        'Modules\Gateway\Http\Controllers\GatewayController@enableModule'       => 'Gestión del módulo (solo administrador, protegida por permisos).',
        'Modules\Gateway\Http\Controllers\GatewayController@disableModule'      => 'Gestión del módulo (solo administrador, protegida por permisos).',

        // Rutas huérfanas del script original: el controlador no define estos métodos (responden con error)
        'App\Http\Controllers\PaymentController@bankPayment'   => 'Ruta huérfana: el método no existe en el controlador.',
        'App\Http\Controllers\PaymentController@cancel'        => 'Ruta huérfana: el método no existe en el controlador.',
        'App\Http\Controllers\PaymentController@success'       => 'Ruta huérfana: el método no existe en el controlador.',
        'App\Http\Controllers\PaymentController@stripePayment' => 'Ruta huérfana: el método no existe en el controlador.',
        'App\Http\Controllers\PaymentController@stripeRequest' => 'Ruta huérfana: el método no existe en el controlador.',

        // Alojamientos: consulta y favoritos
        'App\Http\Controllers\PropertyController@single'                   => 'Consulta de alojamientos.',
        'App\Http\Controllers\PropertyController@userProperties'           => 'Consulta de los propios alojamientos.',
        'App\Http\Controllers\PropertyController@getPrice'                 => 'Consulta de precios.',
        'App\Http\Controllers\PropertyController@currencySymbol'           => 'Utilidad de moneda (consulta).',
        'App\Http\Controllers\PropertyController@set_slug'                 => 'Utilidad de enlaces (no publica nada).',
        'App\Http\Controllers\PropertyController@addEditBookMark'          => 'Favoritos.',
        'App\Http\Controllers\PropertyController@userBookmark'             => 'Favoritos.',
        'App\Http\Controllers\PropertyController@unauthenticationFavourite' => 'Favoritos.',
        'App\Http\Controllers\CalendarController@icalendarExport'          => 'Exportación de calendario (consulta).',
        'App\Http\Controllers\CalendarController@icalendarSynchronization' => 'Sincronización iCal de una publicación existente.',

        // Negocios: consulta y mantenimiento de lo ya publicado
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@index'                       => 'Consulta de negocios.',
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@listadoFrontend'             => 'Consulta de negocios.',
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@listadoProductosServicios'   => 'Consulta de negocios.',
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@obtenerActividadesPaginadas' => 'Consulta de negocios.',
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@obtenerNegociosPaginados'    => 'Consulta de negocios.',
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@productosServiciosEncontrados' => 'Consulta de negocios.',
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@getActividadDetalle'         => 'Consulta de una oferta existente.',
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@getActividadForm'            => 'Formulario de una oferta existente (consulta).',
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@deleteActividad'             => 'Eliminar una oferta propia no publica nada.',
        'Reda\RedaAlojamiento\Http\Controllers\Experiencia\ExperienciaController@destroy'                     => 'Eliminar un negocio propio no publica nada.',
    ],

];
