<?php

namespace App\DataTables;

use App\Models\Reviews;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;

class ReviewsDataTable extends DataTable
{

    public function ajax(): JsonResponse
    {
        $reviews = $this->query();

        return datatables()
            ->of($reviews)
            ->addColumn('action', function ($reviews) {
                $edit = '<a href="' . url('admin/edit_review/' . $reviews->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';
                return $edit;
            })
            ->addColumn('property_name', function ($reviews) {
                return '<a href="' . url('admin/listing/'. $reviews->id .'/basics') .'">' . $reviews->property_name . '</a>';
            })
            ->addColumn('sender', function ($reviews) {
                return '<a href="' . url('admin/edit-customer/' . $reviews->sender_id) . '">' . $reviews->sender . '</a>';
            })
            ->addColumn('receiver', function ($reviews) {
                return '<a href="' . url('admin/edit-customer/' . $reviews->receiver_id) . '">' . $reviews->receiver . '</a>';
            })
            ->addColumn('created_at', function ($reviews) {
                return dateFormat($reviews->created_at);
            })
            ->addColumn('reviewer', function ($reviews) {
                $reviewer = strtolower($reviews->reviewer);
                $badgeClass = ($reviewer == 'host') ? 'status-accepted' : 'status-processing';
                $icon = ($reviewer == 'host') ? 'fa-user-circle' : 'fa-user';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . ucfirst($reviewer) . '</span>';
            })
            ->rawColumns(['action', 'property_name', 'sender', 'receiver', 'reviewer', 'created_at'])
            ->toJson();
    }


    public function query()
    {
        $from       = isset(request()->from) ? setDateForDb(request()->from) : null;
        $to         = isset(request()->to) ? setDateForDb(request()->to) : null;
        $property   = isset(request()->property) ? request()->property : null;
        $reviewer   = isset(request()->reviewer) ? request()->reviewer : null;
        $reviews    = Reviews::join('properties', function ($join) {
                                $join->on('properties.id', '=', 'reviews.property_id');
        })
                        ->join('users', function ($join) {
                                $join->on('users.id', '=', 'reviews.sender_id');
                        })
                        ->join('users as receiver', function ($join) {
                                $join->on('receiver.id', '=', 'reviews.receiver_id');
                        })
                        ->select(['reviews.id as id', 'sender_id', 'receiver_id', 'booking_id', 'properties.name as property_name', 'properties.id as property_id', 'users.first_name as sender', 'receiver.first_name as receiver', 'reviewer', 'message', 'reviews.created_at as created_at', 'reviews.updated_at as updated_at']);
        if (!empty($from)) {
            $reviews->whereDate('reviews.created_at', '>=', $from);
        }

        if (!empty($to)) {
            $reviews->whereDate('reviews.created_at', '<=', $to);
        }

        if (!empty($property)) {
            $reviews->where('properties.id', '=', $property);
        }
        if (!empty($reviewer)) {
            $reviews->where('reviews.reviewer', '=', $reviewer);
        }
        return $this->applyScopes($reviews);
    }

    public function html()
    {
        return $this->builder()
        ->addColumn(['data' => 'id', 'name' => 'reviews.id', 'title' => 'Id', 'visible' => true, 'searchable' => false, 'width' => '5%', 'className' => 'text-center'])

        ->addColumn(['data' => 'property_name', 'name' => 'properties.name', 'title' => 'Property Name', 'className' => 'bold-column'])
        ->addColumn(['data' => 'sender', 'name' => 'users.first_name', 'title' => 'Sender', 'className' => 'bold-column'])
        ->addColumn(['data' => 'receiver', 'name' => 'receiver.first_name', 'title' => 'Receiver', 'className' => 'bold-column'])
        ->addColumn(['data' => 'reviewer', 'name' => 'reviews.reviewer', 'title' => 'Reviewer'])
        ->addColumn(['data' => 'message', 'name' => 'message', 'title' => 'Message'])
        ->addColumn(['data' => 'created_at', 'name' => 'reviews.created_at', 'title' => 'Date'])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
        ->parameters(dataTableOptions());
    }


    protected function filename(): string
    {
        return 'reviewdatatables_' . time();
    }
}
