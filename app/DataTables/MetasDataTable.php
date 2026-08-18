<?php

namespace App\DataTables;

use App\Models\Meta;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;

class MetasDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('action', function ($seoMetas) {

                $edit = '<a href="' . url('admin/settings/edit_meta/' . $seoMetas->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';

                return $edit;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function query()
    {
        $query = Meta::select();
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'url', 'name' => 'seo_metas.url', 'title' => 'Url', 'className' => 'bold-column'])
            ->addColumn(['data' => 'title', 'name' => 'seo_metas.title', 'title' => 'Title'])
            ->addColumn(['data' => 'description', 'name' => 'seo_metas.description', 'title' => 'Description'])
            ->addColumn(['data' => 'keywords', 'name' => 'seo_metas.keywords', 'title' => 'Keywords'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
            ->parameters(dataTableOptions());
    }


    protected function filename(): string
    {
        return 'campaignsdatatables_' . time();
    }
}
