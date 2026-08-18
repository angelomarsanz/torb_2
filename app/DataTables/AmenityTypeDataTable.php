<?php

/**
 * AmenityTypeDataTable Data Table
 *
 * AmenityTypeDataTable Data Table handles AmenityTypeDataTable datas.
 *
 * @category   AmenityTypeDataTable
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

use App\Models\AmenityType;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;

class AmenityTypeDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('action', function ($amenityType) {

                $edit   = '<a href="' . url('admin/settings/edit-amenities-type/' . $amenityType->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';
                $delete = '<a href="' . url('admin/settings/delete-amenities-type/' . $amenityType->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>';

                return $edit . ' ' . $delete;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function query()
    {
        $query = AmenityType::select();

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'name', 'name' =>'amenity_type.name', 'title' => 'Name', 'className' => 'bold-column'])
            ->addColumn(['data' => 'description', 'name' =>'amenity_type.description', 'title' => 'Description'])
            ->addColumn(['data' => 'action', 'name' =>'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
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
        return 'amenitytypedatatables_' . time();
    }
}
