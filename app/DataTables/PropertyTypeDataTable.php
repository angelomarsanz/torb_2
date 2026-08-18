<?php

namespace App\DataTables;

use App\Models\PropertyType;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;

class PropertyTypeDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('action', function ($propertyType) {

                $edit = '<a href="' . url('admin/settings/edit-property-type/' . $propertyType->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';
                $delete = '<a href="' . url('admin/settings/delete-property-type/' . $propertyType->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>';

                return $edit . ' ' . $delete;
            })
            ->addColumn('status', function ($propertyType) {
                $status = $propertyType->status;
                $badgeClass = ($status == 'Active') ? 'status-accepted' : 'status-expired';
                $icon = ($status == 'Active') ? 'fa-check-circle' : 'fa-info-circle';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function query()
    {
        $query = PropertyType::query();
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'name', 'name' => 'property_type.name', 'title' => 'Name', 'className' => 'bold-column'])
            ->addColumn(['data' => 'description', 'name' => 'property_type.description', 'title' => 'Description'])
            ->addColumn(['data' => 'status', 'name' => 'property_type.status', 'title' => 'Status'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
             ->parameters(dataTableOptions());
    }

    protected function filename(): string
    {
        return 'propertytypedatatables_' . time();
    }
}
