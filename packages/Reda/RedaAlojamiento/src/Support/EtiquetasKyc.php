<?php

/**
 * Resumen: Etiquetas legibles y traducidas para las pantallas del KYC (tipos de evento de auditoría, resultados,
 * orígenes, tipos de documento y de archivo, y clases de insignia por estado). Centraliza los textos para que las
 * vistas no repitan tablas de equivalencia. Cualquier valor desconocido se devuelve tal cual (nunca rompe la vista).
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Support
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Support;

use Reda\RedaAlojamiento\Enums\EstadoKyc;

class EtiquetasKyc
{
    /**
     * Descripción de un tipo de evento de auditoría.
     */
    public static function evento(?string $tipo): string
    {
        return match ($tipo) {
            'envio_manual'                 => __('Solicitud manual enviada'),
            'correccion_solicitada'        => __('Corrección solicitada por un administrador'),
            'correccion_atendida'          => __('Corrección enviada por el usuario'),
            'aprobacion_manual'            => __('Solicitud aprobada'),
            'rechazo_manual'               => __('Solicitud rechazada'),
            'observacion_admin'            => __('Observación interna agregada'),
            'retencion_suspendida'         => __('Retención de documentos suspendida'),
            'retencion_reanudada'          => __('Retención de documentos reanudada'),
            'numero_documento_revelado'    => __('Número de documento mostrado a un administrador'),
            'solicitud_consultada'         => __('Detalle consultado por un administrador'),
            'documento_consultado'         => __('Documento consultado'),
            'documentos_eliminados'        => __('Documentos eliminados por retención'),
            'inicio_verificacion_didit'    => __('Verificación con Didit iniciada'),
            'sesion_didit_creada'          => __('Sesión de Didit creada'),
            'error_creacion_sesion_didit'  => __('Error al crear la sesión de Didit'),
            'didit_reemplazada_por_manual' => __('Verificación de Didit reemplazada por la manual'),
            'webhook:status.updated',
            'webhook:data.updated'         => __('Notificación de Didit'),
            'consulta_decision'            => __('Consulta del resultado a Didit'),
            default                        => (string) $tipo,
        };
    }

    /**
     * Resultado del procesamiento de un evento.
     */
    public static function resultado(?string $resultado): string
    {
        return match ($resultado) {
            'procesado'                   => __('Procesado'),
            'duplicado'                   => __('Duplicado'),
            'ignorado_sesion_antigua'     => __('Ignorado (sesión anterior)'),
            'ignorado_evento_antiguo'     => __('Ignorado (evento antiguo)'),
            'ignorado_sin_cambio'         => __('Sin cambio'),
            'ignorado_transicion_invalida' => __('Ignorado (transición no válida)'),
            'ignorado_estado_desconocido' => __('Ignorado (estado desconocido)'),
            'ignorado_entorno'            => __('Ignorado (otro entorno)'),
            'ignorado_sesion_desconocida' => __('Ignorado (sesión desconocida)'),
            'rechazado_vendor_data'       => __('Rechazado (usuario no coincide)'),
            'confirmacion_fallida'        => __('Aprobación no confirmada'),
            'error'                       => __('Error'),
            default                       => (string) $resultado,
        };
    }

    /**
     * Origen de un evento.
     */
    public static function origen(?string $origen): string
    {
        return match ($origen) {
            'didit_webhook'  => __('Didit (notificación)'),
            'didit_consulta' => __('Didit (consulta)'),
            'usuario'        => __('Usuario'),
            'admin'          => __('Administrador'),
            'sistema'        => __('Sistema'),
            default          => (string) $origen,
        };
    }

    /**
     * Etiqueta de un tipo de documento de identidad (cédula, pasaporte...).
     */
    public static function tipoDocumento(?string $clave): string
    {
        $etiqueta = $clave ? config('kyc.tipos_documento.' . $clave . '.etiqueta') : null;

        return $etiqueta ? __($etiqueta) : (string) $clave;
    }

    /**
     * Etiqueta de un tipo de archivo (frente, reverso, selfie).
     */
    public static function tipoArchivo(?string $tipo): string
    {
        return match ($tipo) {
            'frente'  => __('Frente del documento'),
            'reverso' => __('Reverso del documento'),
            'selfie'  => __('Selfie con el documento'),
            default   => (string) $tipo,
        };
    }

    /**
     * Estado de un archivo.
     */
    public static function estadoArchivo(?string $estado): string
    {
        return match ($estado) {
            'vigente'     => __('Vigente'),
            'reemplazado' => __('Reemplazado'),
            'eliminado'   => __('Eliminado'),
            default       => (string) $estado,
        };
    }

    /**
     * Clase de Bootstrap para la insignia de un estado.
     */
    public static function claseEstado(?EstadoKyc $estado): string
    {
        return match ($estado) {
            EstadoKyc::Verificado                       => 'bg-success',
            EstadoKyc::RevisionManual, EstadoKyc::Pendiente => 'bg-warning text-dark',
            EstadoKyc::EnProceso                        => 'bg-info text-dark',
            EstadoKyc::Rechazado, EstadoKyc::Fallido     => 'bg-danger',
            EstadoKyc::Expirado                         => 'bg-secondary',
            default                                     => 'bg-light text-dark',
        };
    }
}
