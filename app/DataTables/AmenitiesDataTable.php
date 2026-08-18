<?php

/**
 * AmenitiesData Data Table
 *
 * AmenitiesData Data Table handles AmenitiesData datas.
 *
 * @category   AmenitiesData
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

use App\Models\Amenities;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Services\DataTable;

class AmenitiesDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('type_id', function ($amenity) {

                return optional($amenity->amenityType)->name;
            })
            ->addColumn('action', function ($amenity) {

                $edit = '<a href="' . url('admin/edit-amenities/' . $amenity->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';
                $delete = '<a href="' . url('admin/delete-amenities/' . $amenity->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>';
                return $edit . ' ' . $delete;
            })
            ->addColumn('status', function ($amenity) {
                $status = $amenity->status;
                $badgeClass = ($status == 'Active') ? 'status-accepted' : 'status-expired';
                $icon = ($status == 'Active') ? 'fa-check-circle' : 'fa-info-circle';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
            })
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    public function query()
    {
        $query = Amenities::with('amenityType')->select();
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'amenities.id', 'title' => 'Id', 'width' => '5%', 'className' => 'text-center'])
            ->addColumn(['data' => 'title', 'name' =>'amenities.title', 'title' => 'Name', 'className' => 'bold-column'])
            ->addColumn(['data' => 'description', 'name' =>'amenities.description', 'title' => 'Description'])
            ->addColumn(['data' => 'symbol', 'name' =>'amenities.symbol', 'title' => 'Symbol'])
            ->addColumn(['data' => 'type_id', 'name' =>'amenityType.name', 'title' => 'Type'])
            ->addColumn(['data' => 'status', 'name' =>'amenities.status', 'title' => 'Status'])
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
        return 'amenitiesdatatables_' . time();
    }
}
