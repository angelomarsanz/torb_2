<?php

/**
 * AdminuserDataTable Data Table
 *
 * AdminuserDataTable Data Table handles AdminuserDataTable datas.
 *
 * @category   AdminuserDataTable
 * @package    vRent
 * @author     Techvillage Dev Team
 * @copyright  2020 Techvillage
 * @license
 * @version    2.7
 * @link       http://techvill.net
 * @since      Version 1.3
 * @deprecated None
 */

namespace App\DataTables;

use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Services\DataTable;

class AdminuserDataTable extends DataTable
{
    public function ajax() : JsonResponse
    {
        $admin = $this->query();

        return datatables()
            ->of($admin)
            ->addColumn('action', function ($admin) {
                $edit = '<a href="' . url('admin/edit-admin/' . $admin->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';
                $delete = '<a href="' . url('admin/delete-admin/' . $admin->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>';
                return $edit . $delete;
            })
            ->addColumn('username', function ($admin) {
                return '<a href="' . url('admin/edit-admin/' . $admin->id) . '">' . $admin->username . '</a>';
            })
            ->addColumn('status', function ($admin) {
                $status = $admin->status;
                $badgeClass = ($status == 'Active') ? 'status-accepted' : 'status-expired';
                $icon = ($status == 'Active') ? 'fa-check-circle' : 'fa-info-circle';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
            })
            ->rawColumns(['username', 'status', 'action'])
            ->toJson();
    }

    public function query()
    {
        $admin = Admin::join('role_admin', function ($join) {
                                $join->on('role_admin.admin_id', '=', 'admin.id');
        })
                        ->join('roles', function ($join) {
                                $join->on('roles.id', '=', 'role_admin.role_id');
                        })
                        ->select(['admin.id as id', 'username', 'email', 'roles.display_name as role_name', 'status']);

        return $this->applyScopes($admin);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'admin.id', 'title' => 'Id', 'width' => '5%', 'className' => 'text-center'])
            ->addColumn(['data' => 'username', 'name' => 'admin.username', 'title' => 'Username', 'className' => 'bold-column'])
            ->addColumn(['data' => 'email', 'name' => 'admin.email', 'title' => 'Email'])
            ->addColumn(['data' => 'role_name', 'name' => 'roles.display_name', 'title' => 'Role Name'])
            ->addColumn(['data' => 'status', 'name' => 'admin.status', 'title' => 'Status'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
            ->parameters(dataTableOptions());
    }

    protected function getColumns()
    {
        return [
            'id',
            'created_at',
            'updated_at',
        ];
    }

    protected function filename(): string
    {
        return 'admindatatables_' . time();
    }
}
