<?php

/**
 * Resumen: Consultas de solo lectura del panel administrativo KYC.
 *  - listar(): solicitudes paginadas de 10 en 10 con el usuario, el administrador responsable y filtros por estado
 *    (pendientes de revisión, en proceso, verificados, rechazados, fallidos, expirados), por método (didit/manual) y
 *    búsqueda por nombre, correo o número de documento. El documento se busca por su hash HMAC (coincidencia
 *    exacta, tolerando la letra V/E), nunca descifrando registros. Los comodines de LIKE se escapan.
 *  - contadores(): cantidad de solicitudes por grupo de estado (para las pestañas).
 *  - detalle(): todo lo necesario para la pantalla de una solicitud: datos, documentos con su historial, resultado
 *    de Didit, otras solicitudes del usuario, coincidencia de documento y el historial de cambios.
 * No escribe en la base de datos ni decide permisos: eso lo hacen KycService y KycPolicy.
 *
 * @package    Reda\RedaAlojamiento
 * @subpackage Services\Kyc
 * @author     REDA Tech Team
 * @version    1.0.0
 */

namespace Reda\RedaAlojamiento\Services\Kyc;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Reda\RedaAlojamiento\Enums\EstadoKyc;
use Reda\RedaAlojamiento\Models\Kyc\EventoKyc;
use Reda\RedaAlojamiento\Models\Kyc\SolicitudKyc;

class ConsultaKycAdmin
{
    /**
     * Grupos de estado de los filtros del listado.
     *
     * @return array<string,array<int,string>>
     */
    public static function grupos(): array
    {
        return [
            'pendientes'  => [EstadoKyc::RevisionManual->value, EstadoKyc::Pendiente->value],
            'en_proceso'  => [EstadoKyc::EnProceso->value],
            'verificados' => [EstadoKyc::Verificado->value],
            'rechazados'  => [EstadoKyc::Rechazado->value],
            'fallidos'    => [EstadoKyc::Fallido->value],
            'expirados'   => [EstadoKyc::Expirado->value],
        ];
    }

    public function __construct(private readonly KycService $kyc)
    {
    }

    /**
     * Solicitudes paginadas (10 por página) según los filtros: grupo (por defecto "pendientes"; "todos" no filtra),
     * metodo (didit|manual) y buscar (texto libre).
     *
     * @param  array<string,mixed> $filtros
     */
    public function listar(array $filtros): LengthAwarePaginator
    {
        $grupo  = is_string($filtros['grupo'] ?? null) ? $filtros['grupo'] : 'pendientes';
        $metodo = is_string($filtros['metodo'] ?? null) ? $filtros['metodo'] : '';
        $buscar = is_string($filtros['buscar'] ?? null) ? trim(mb_substr($filtros['buscar'], 0, 100)) : '';

        $consulta = SolicitudKyc::query()
            ->join('users', 'users.id', '=', 'solicitudes_kyc.user_id')
            ->leftJoin('admin', 'admin.id', '=', 'solicitudes_kyc.revisado_por_admin_id')
            ->select('solicitudes_kyc.*', 'users.first_name as usuario_nombre', 'users.last_name as usuario_apellido',
                'users.email as usuario_correo', 'admin.username as admin_nombre');

        $grupos = self::grupos();

        if (isset($grupos[$grupo])) {
            $consulta->whereIn('solicitudes_kyc.estado', $grupos[$grupo]);
        }

        if (in_array($metodo, ['didit', 'manual'], true)) {
            $consulta->where('solicitudes_kyc.metodo', $metodo);
        }

        if ($buscar !== '') {
            $this->aplicarBusqueda($consulta, $buscar);
        }

        // Pendientes: la más antigua primero (cola de revisión); el resto: la más reciente primero.
        if ($grupo === 'pendientes') {
            $consulta->orderBy('solicitudes_kyc.enviada_en')->orderBy('solicitudes_kyc.id');
        } else {
            $consulta->orderByDesc('solicitudes_kyc.id');
        }

        return $consulta->paginate(10)->withQueryString();
    }

    /**
     * Cantidad de solicitudes por grupo de estado.
     *
     * @return array<string,int>
     */
    public function contadores(): array
    {
        $porEstado = SolicitudKyc::query()->selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado');
        $resultado = ['todos' => (int) $porEstado->sum()];

        foreach (self::grupos() as $grupo => $estados) {
            $resultado[$grupo] = (int) collect($estados)->sum(fn ($e) => (int) ($porEstado[$e] ?? 0));
        }

        return $resultado;
    }

    /**
     * Datos completos de una solicitud para su pantalla de detalle, o null si no existe.
     *
     * @return array<string,mixed>|null
     */
    public function detalle(int $solicitudId): ?array
    {
        $solicitud = SolicitudKyc::find($solicitudId);

        if (!$solicitud) {
            return null;
        }

        $usuario = DB::table('users')->where('id', $solicitud->user_id)
            ->first(['id', 'first_name', 'last_name', 'email', 'phone', 'status', 'deleted_at']);

        $administrador = $solicitud->revisado_por_admin_id
            ? DB::table('admin')->where('id', $solicitud->revisado_por_admin_id)->value('username') : null;

        $documentos = $solicitud->documentos()->orderBy('tipo')->orderByDesc('id')->get();

        $eventos = EventoKyc::query()
            ->leftJoin('admin', 'admin.id', '=', 'eventos_kyc.admin_id')
            ->where('eventos_kyc.solicitud_id', $solicitud->id)
            ->select('eventos_kyc.*', 'admin.username as admin_nombre')
            ->orderByDesc('eventos_kyc.id')
            ->limit(200)
            ->get();

        $historial = SolicitudKyc::query()
            ->leftJoin('admin', 'admin.id', '=', 'solicitudes_kyc.revisado_por_admin_id')
            ->where('solicitudes_kyc.user_id', $solicitud->user_id)
            ->select('solicitudes_kyc.*', 'admin.username as admin_nombre')
            ->orderByDesc('solicitudes_kyc.id')
            ->get();

        return [
            'solicitud'     => $solicitud,
            'usuario'       => $usuario,
            'administrador' => $administrador,
            'documentos'    => $documentos,
            'eventos'       => $eventos,
            'historial'     => $historial,
            'coincidencias' => $this->kyc->coincidenciasDocumento($solicitud),
            'es_manual_abierta' => $solicitud->metodo?->value === 'manual' && $solicitud->estado === EstadoKyc::RevisionManual,
        ];
    }

    /**
     * Búsqueda por palabras (nombre, apellido, correo) y por número de documento exacto (hash).
     *
     * @param \Illuminate\Database\Eloquent\Builder $consulta
     */
    private function aplicarBusqueda($consulta, string $buscar): void
    {
        $palabras = array_slice(array_values(array_filter(preg_split('/\s+/u', $buscar) ?: [])), 0, 4);
        $hashes   = count($palabras) === 1 && strlen(SolicitudKyc::normalizarDocumento($palabras[0])) >= 4
            ? SolicitudKyc::hashesCandidatosDocumento($palabras[0]) : [];

        $columnas = ['solicitudes_kyc.nombres', 'solicitudes_kyc.apellidos', 'users.first_name', 'users.last_name', 'users.email'];

        $consulta->where(function ($donde) use ($palabras, $hashes, $columnas) {
            $donde->where(function ($todas) use ($palabras, $columnas) {
                foreach ($palabras as $palabra) {
                    // El carácter | actúa como escape para no interpretar % ni _ del texto buscado como comodines.
                    $patron = '%' . str_replace(['|', '%', '_'], ['||', '|%', '|_'], $palabra) . '%';

                    $todas->where(function ($alguna) use ($columnas, $patron) {
                        foreach ($columnas as $columna) {
                            $alguna->orWhereRaw("{$columna} LIKE ? ESCAPE '|'", [$patron]);
                        }
                    });
                }
            });

            if (!empty($hashes)) {
                $donde->orWhereIn('solicitudes_kyc.numero_documento_hash', $hashes);
            }
        });
    }
}
