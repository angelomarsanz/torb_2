<?php

namespace App\DataTables;

use App\Models\BedType;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;

class BedTypeDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())

            ->addColumn('status', function($bedType){
                $status = $bedType->status == null ? 'Active' : $bedType->status;
                $badgeClass = ($status == 'Active') ? 'status-accepted' : 'status-expired';
                $icon = ($status == 'Active') ? 'fa-check-circle' : 'fa-info-circle';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
            })
            ->addColumn('action', function ($bedType) {

                $edit = '<a href="' . url('admin/settings/edit-bed-type/' . $bedType->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';
                $delete = '<a href="' . url('admin/settings/delete-bed-type/' . $bedType->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>';

                return $edit . ' ' . $delete;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function query()
    {
        $query = BedType::query();

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'name', 'name' => 'bed_type.name', 'title' => 'Name', 'className' => 'bold-column'])
            ->addColumn(['data' => 'status', 'name' => 'bed_type.status', 'title' => 'Status'])
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
        return 'spacetypedatatables_' . time();
    }
}
