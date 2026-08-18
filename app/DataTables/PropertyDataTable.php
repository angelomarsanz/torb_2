<?php

namespace App\DataTables;

use App\Models\Properties;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;
use Request, Common;

class PropertyDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        $properties = $this->query();

        return datatables()
            ->of($properties)
            ->addColumn('action', function ($properties) {
                $edit = $delete = '';
                if (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_properties')) {
                    $edit = '<a href="' . url('admin/listing/' . $properties->id) . '/basics" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';
                }
                if (Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_property')) {
                    $delete = '<a href="' . url('admin/delete-property/' . $properties->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>';
                }
                return $edit . $delete;
            })
            ->addColumn('id', function ($properties) {
                return $properties->id;
            })
            ->addColumn('host_name', function ($properties) {
                return '<a href="' . url('admin/edit-customer/' . optional($properties->users)->id) . '"><span class="status-badge status-host"><i class="fa fa-user-circle-o"></i> ' . ucfirst(optional($properties->users)->first_name) . '</span></a>';
            })
            ->addColumn('name', function ($properties) {
                return '<a href="' . url('admin/listing/' . $properties->id . '/basics') . '">' . ucfirst($properties->name) . '</a>';
            })
            ->addColumn('created_at', function ($properties) {
                return dateFormat($properties->created_at);
            })
            ->addColumn('recomended', function ($properties) {

                if ($properties->recomended == 1) {
                    return 'Yes';
                }
                return 'No';

            })
            ->addColumn('status', function ($properties) {
                $status = $properties->status;
                $badgeClass = ($status == 'Listed') ? 'status-accepted' : 'status-expired';
                $icon = ($status == 'Listed') ? 'fa-check-circle' : 'fa-info-circle';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
            })
            ->addColumn('verified', function ($properties) {
                $status = ($properties->is_verified == 'Approved' || $properties->is_verified == '') ? 'Approved' : 'Pending';
                $badgeClass = ($status == 'Approved') ? 'status-accepted' : 'status-pending';
                $icon = ($status == 'Approved') ? 'fa-check-circle' : 'fa-clock-o';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
            })
            ->rawColumns(['host_name', 'name', 'status', 'verified', 'action'])
            ->toJson();
    }

    public function query()
    {
        $user_id    = Request::segment(4);
        $status     = isset(request()->status) ? request()->status : null;
        $from = isset(request()->from) ? setDateForDb(request()->from) : null;
        $to = isset(request()->to) ? setDateForDb(request()->to) : null;
        $space_type = isset(request()->space_type) ? request()->space_type : null;

        $query = Properties::with(['users:id,first_name,profile_image']);
        if (isset($user_id)) {
            $query->where('host_id', '=', $user_id);
        }


        if ($from) {
             $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
             $query->whereDate('created_at', '<=', $to);
        }
        if ($status) {
            $query->where('status', '=', $status);
        }
        if ($space_type) {
            $query->where('space_type', '=', $space_type);
        }
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'properties.id', 'title' => 'Id', 'width' => '5%', 'className' => 'text-center'])
            ->addColumn(['data' => 'name', 'name' => 'properties.name', 'title' => 'Name', 'className' => 'bold-column'])
            ->addColumn(['data' => 'host_name', 'name' => 'users.first_name', 'title' => 'Host Name'])
            ->addColumn(['data' => 'space_type_name', 'name' => 'space_type', 'title' => 'Space Type'])
            ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
            ->addColumn(['data' => 'recomended', 'name' => 'recomended', 'title' => 'Recomended'])
            ->addColumn(['data' => 'verified', 'name' => 'verified', 'title' => 'Verified'])
            ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => 'Date'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
            ->parameters(dataTableOptions());
    }


    protected function filename(): string
    {
        return 'propertydatatables_' . time();
    }
}
