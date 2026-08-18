<?php

namespace App\DataTables;

use App\Models\Page;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;

class PagesDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('action', function ($pages) {

                $edit = '<a href="' . url('admin/edit-page/' . $pages->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';
                $delete = '<a href="' . url('admin/delete-page/' . $pages->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>';

                return $edit . ' ' . $delete;
            })
            ->addColumn('status', function ($pages) {
                $status = $pages->status;
                $badgeClass = ($status == 'Active') ? 'status-accepted' : 'status-expired';
                $icon = ($status == 'Active') ? 'fa-check-circle' : 'fa-info-circle';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
            })
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    public function query()
    {
        $query = Page::select();
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id',     'name' => 'pages.id',     'title' => 'Id', 'width' => '5%', 'className' => 'text-center'])
            ->addColumn(['data' => 'name',   'name' => 'pages.name',   'title' => 'Name', 'className' => 'bold-column'])
            ->addColumn(['data' => 'url',    'name' => 'pages.url',    'title' => 'Url'])
            ->addColumn(['data' => 'status', 'name' => 'pages.status', 'title' => 'Status'])
            ->addColumn(['data' => 'action', 'name' => 'action',       'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
             ->parameters(dataTableOptions());
    }


    protected function filename(): string
    {
        return 'membersdatatables_' . time();
    }
}
