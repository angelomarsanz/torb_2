<?php

namespace App\DataTables;

use App\Models\Roles;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;

class RoleDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        $role = $this->query();

        return datatables()
            ->of($role)
            ->addColumn('action', function ($role) {
                return '<a href="' . url('admin/settings/edit-role/' . $role->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;<a href="' . url('admin/settings/delete-role/' . $role->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>';
            })
            ->addColumn('name', function ($role) {
                return '<a href="' . url('admin/settings/edit-role/' . $role->id) . '">' . $role->name. '</a>';
            })
            ->rawColumns(['action','name'])
            ->toJson();
    }

    public function query()
    {
        $role = Roles::select();
        return $this->applyScopes($role);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Id', 'width' => '5%', 'className' => 'text-center'])
            ->columns([
                ['data' => 'name', 'name' => 'name', 'title' => 'Name', 'className' => 'bold-column'],
                'display_name',
                'description',
            ])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
            ->parameters(dataTableOptions());
    }
}
