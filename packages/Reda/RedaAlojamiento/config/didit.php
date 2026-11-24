<?php

/**
 * Resumen: Credenciales y parámetros de conexión con Didit. Se fusiona en config('services.didit')
 * desde RedaAlojamientoServiceProvider para no editar config/services.php. Los valores salen
 * únicamente de .env (nunca de la base de datos, JavaScript, HTML ni respuestas JSON).
 *
 * DIDIT_ENVIRONMENT admite "sandbox" o "production". Didit decide el entorno por la aplicación a la
 * que pertenece la API key; este valor se usa para rechazar webhooks de otro entorno.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Config
 * @author     REDA Tech Team
 * @version    1.0.0
 */

return [
    'api_url'        => rtrim((string) env('DIDIT_API_URL', 'https://verification.didit.me'), '/'),
    'api_key'        => env('DIDIT_API_KEY'),
    'workflow_id'    => env('DIDIT_WORKFLOW_ID'),
    'webhook_secret' => env('DIDIT_WEBHOOK_SECRET'),
    'environment'    => strtolower((string) env('DIDIT_ENVIRONMENT', 'sandbox')),
];
