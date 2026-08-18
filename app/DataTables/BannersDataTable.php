<?php

namespace App\DataTables;

use App\Models\Banners;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;

class BannersDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        $banners = $this->query();
        return datatables()
            ->of($banners)
            ->addColumn('image', function ($banners) {

                return '<img src="' . $banners->image_url . '" width="200" height="100">';
            })
            ->addColumn('default', function ($banners) {

                return $banners->default_banner ;
            })
            ->addColumn('status', function ($banners) {
                $status = $banners->status;
                $badgeClass = ($status == 'Active') ? 'status-accepted' : 'status-expired';
                $icon = ($status == 'Active') ? 'fa-check-circle' : 'fa-info-circle';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
            })
            ->addColumn('action', function ($banners) {

                $edit = '<a href="' . url('admin/settings/edit-banners/' . $banners->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';

                if ($banners->default_banner == 'Yes') {
                    $delete = '';
                } else {
                    $delete = '<a href="' . url('admin/settings/delete-banners/' . $banners->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>';
                }

                return $edit . ' ' . $delete;

            })
            ->rawColumns(['image','default_banner','action','status'])
            ->toJson();
    }


    public function query()
    {
        $banners = Banners::select();
        return $this->applyScopes($banners);
    }

    public function html()
    {
        return $this->builder()

        ->addColumn(['data' => 'heading', 'name' => 'heading', 'title' => 'Heading', 'className' => 'bold-column'])
        ->addColumn(['data' => 'subheading', 'name' => 'subheading', 'title' => 'Subheading'])
        ->addColumn(['data' => 'image', 'name' => 'image', 'title' => 'Image'])
        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
        ->addColumn(['data' => 'default', 'name' => 'default', 'title' => 'Default'])


        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
        ->parameters(dataTableOptions());
    }
}
