<?php

namespace App\DataTables;

use App\Models\StartingCities;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;

class StartingCitiesDataTable extends DataTable
{
    protected array|string $exportColumns = ['name', 'image'];

    public function ajax(): JsonResponse
    {
        $startingCities = $this->query();

        return datatables()
            ->of($startingCities)
            ->addColumn('image', function ($startingCities) {
                return '<img src="' . $startingCities->image_url . '" width="200" height="100">';
            })
            ->addColumn('action', function ($startingCities) {
                return '<a href="' . url('admin/settings/edit-starting-cities/' . $startingCities->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;<a href="' . url('admin/settings/delete-starting-cities/' . $startingCities->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>';
            })
            ->addColumn('name', function ($startingCities) {
                return '<a href="' . url('admin/settings/edit-starting-cities/' . $startingCities->id) . '">' . $startingCities->name . '</a>';
            })
            ->addColumn('status', function ($startingCities) {
                $status = $startingCities->status;
                $badgeClass = ($status == 'Active') ? 'status-accepted' : 'status-expired';
                $icon = ($status == 'Active') ? 'fa-check-circle' : 'fa-info-circle';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
            })
            ->rawColumns(['action','image','name','status'])
            ->toJson();
    }

    public function query()
    {
        $startingCities = StartingCities::select();
        return $this->applyScopes($startingCities);
    }

    public function html()
    {
        return $this->builder()
        ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Name', 'className' => 'bold-column'])
        ->addColumn(['data' => 'image', 'name' => 'image', 'title' => 'Image'])
        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
        ->parameters(dataTableOptions());
    }

    protected function filename(): string
    {
        return 'spacetypedatatables_' . time();
    }
}
