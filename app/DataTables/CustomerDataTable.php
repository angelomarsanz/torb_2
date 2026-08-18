<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;
use Auth;

class CustomerDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('action', function ($users) {
                $edit = \Helpers::has_permission(Auth::guard('admin')->user()->id, 'edit_customer') ? '<a href="' . url('admin/edit-customer/' . $users->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;' : '';
                $delete = \Helpers::has_permission(Auth::guard('admin')->user()->id, 'delete_customer') ? '<a href="' . url('admin/delete-customer/' . $users->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>&nbsp;' : '';
                return $edit . $delete;
            })
            ->addColumn('first_name', function ($users) {
                return '<a href="' . url('admin/edit-customer/' . $users->id) . '">' . ucfirst($users->first_name) . '</a>';
            })
            ->addColumn('last_name', function ($users) {
                return '<a href="' . url('admin/edit-customer/' . $users->id) . '">' . ucfirst($users->last_name) . '</a>';
            })
            ->addColumn('formatted_phone', function ($users) {
                if ($users->formatted_phone == '') return '-';
                return '<a href="' . url('admin/edit-customer/' . $users->id) . '">' . $users->formatted_phone . '</a>';
            })
            ->addColumn('created_at', function ($users) {
                return dateFormat($users->created_at);
            })
            ->addColumn('status', function ($users) {
                $status = $users->status;
                $badgeClass = ($status == 'Active') ? 'status-accepted' : 'status-expired';
                $icon = ($status == 'Active') ? 'fa-check-circle' : 'fa-info-circle';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
            })
            ->rawColumns(['first_name', 'last_name', 'formatted_phone', 'status', 'action'])
            ->toJson();
    }

    public function query()
    {
        $status = isset(request()->status) ? request()->status : null;
        $from = isset(request()->from) ? setDateForDb(request()->from) : null;
        $to = isset(request()->to) ? setDateForDb(request()->to) : null;
        $customer = isset(request()->customer) ? request()->customer : null;

        $query = User::select();

        if (!empty($from)) {
            $query->whereDate('created_at', '>=', $from);
        }
        if (!empty($to)) {
            $query->whereDate('created_at', '<=', $to);
        }
        if (!empty($status)) {
            $query->where('status', '=', $status);
        }
        if (!empty($customer)) {
            $query->where('id', '=', $customer);
        }

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'ID', 'width' => '5%', 'className' => 'text-center'])

            ->addColumn(['data' => 'first_name', 'name' => 'users.first_name', 'title' => 'First Name', 'className' => 'bold-column'])
            ->addColumn(['data' => 'last_name', 'name' => 'users.last_name', 'title' => 'Last Name', 'className' => 'bold-column'])
            ->addColumn(['data' => 'formatted_phone', 'name' => 'formatted_phone', 'title' => 'Phone'])
            ->addColumn(['data' => 'email', 'name' => 'email', 'title' => 'Email'])
            ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status', 'orderable' => false, 'searchable' => false])
            ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
            ->parameters(dataTableOptions());
    }


    protected function filename(): string
    {
        return 'customersdatatables_' . time();
    }
}
