<?php

/**
 * Resumen: Política de autorización del KYC (Laravel Gate). Decide quién puede ver solicitudes y documentos:
 *  - El USUARIO propietario puede ver sus propias solicitudes y documentos (no eliminados).
 *  - El ADMINISTRADOR puede verlos y gestionarlos solo si está activo y su rol tiene el permiso manage_kyc
 *    (el mismo mecanismo de permisos del panel actual: UserPermission). Un administrador sin ese permiso no
 *    puede consultar documentos sensibles aunque tenga otros permisos.
 * La propiedad se comprueba con el user_id guardado en la solicitud y el documento, nunca con parámetros de la
 * petición, lo que evita IDOR. Se registra en RedaAlojamientoServiceProvider para DocumentoKyc y SolicitudKyc.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Policies
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Policies;

use App\Http\Helpers\UserPermission;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Reda\RedaAlojamiento\Models\Kyc\DocumentoKyc;
use Reda\RedaAlojamiento\Models\Kyc\SolicitudKyc;

class KycPolicy
{
    /**
     * Permiso administrativo que habilita la gestión del KYC y de sus documentos.
     */
    public const PERMISO_ADMIN = 'manage_kyc';

    /**
     * Indica si el administrador está activo y su rol tiene el permiso manage_kyc.
     */
    public function gestionar(Authenticatable $actor): bool
    {
        if (!$actor instanceof Admin) {
            return false;
        }

        return ($actor->status ?? 'Active') === 'Active'
            && (int) UserPermission::has_permission($actor->getAuthIdentifier(), self::PERMISO_ADMIN) > 0;
    }

    /**
     * Quién puede ver una solicitud KYC: su propietario o un administrador con manage_kyc.
     */
    public function verSolicitud(Authenticatable $actor, SolicitudKyc $solicitud): bool
    {
        if ($actor instanceof Admin) {
            return $this->gestionar($actor);
        }

        return $actor instanceof User && (int) $solicitud->user_id === (int) $actor->getAuthIdentifier();
    }

    /**
     * Quién puede ver o descargar un documento: su propietario (mientras no esté eliminado) o un
     * administrador con manage_kyc. Los dos vínculos (documento y solicitud) deben corresponder al mismo usuario.
     */
    public function verDocumento(Authenticatable $actor, DocumentoKyc $documento): bool
    {
        if ($documento->estado === DocumentoKyc::ESTADO_ELIMINADO) {
            return false;
        }

        if ($actor instanceof Admin) {
            return $this->gestionar($actor);
        }

        if (!$actor instanceof User) {
            return false;
        }

        $id = (int) $actor->getAuthIdentifier();

        return (int) $documento->user_id === $id
            && (int) ($documento->solicitud?->user_id ?? 0) === $id;
    }
}
