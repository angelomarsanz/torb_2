<?php

namespace App\DataTables;

use App\Models\Messages;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;
use Auth;
class MessagesDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('action', function ($message) {
                $edit   = $delete = '';
                $edit   = \Helpers::has_permission(Auth::guard('admin')->user()->id, 'edit_messages') ?'<a href="'.url('admin/send-message-email/'.$message->id).'" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;':'';


                return $edit;
            })
            ->addColumn('sender_id', function ($message) {
                return ucfirst(optional($message->sender)->first_name). ' ' . ucfirst(optional($message->sender)->last_name);
            })
            ->addColumn('receiver_id', function ($message) {
                return ucfirst(optional($message->receiver)->first_name). ' ' . ucfirst(optional($message->receiver)->last_name);
            })
            ->addColumn('property_id', function ($message) {
                return ucfirst(optional($message->properties)->name);
            })

          ->addColumn('message', function ($message) {
                return '<a href="'.url('admin/messaging/host/'.optional($message->bookings)->id).'">'.$message->message.'</a>';
            })
                ->addColumn('created_at', function ($message) {
                return dateFormat($message->created_at);
            })
            ->rawColumns(['action','sender_id','receiver_id','property_id','message'])
            ->toJson();
    }

    public function query()
    {

        $query = Messages::with('properties','sender','receiver')->whereRaw('id in (select max(id) from messages group by (booking_id))');

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Message No&nbsp;&nbsp;', 'className' => 'text-start'])
            ->addColumn(['data' => 'booking_id', 'name' => 'booking_id', 'title' => 'Booking Id '])
            ->addColumn(['data' => 'sender_id', 'name' => 'sender_id', 'title' => 'Sender', 'className' => 'bold-column'])
            ->addColumn(['data' => 'sender_id', 'name' => 'sender.first_name', 'title' => 'Sender', 'visible' => false])
            ->addColumn(['data' => 'sender_id', 'name' => 'sender.last_name', 'title' => 'Sender', 'visible' => false])
            ->addColumn(['data' => 'receiver_id', 'name' => 'receiver_id', 'title' => 'Receiver '])
            ->addColumn(['data' => 'receiver_id', 'name' => 'receiver.first_name', 'title' => 'Receiver', 'visible' => false])
            ->addColumn(['data' => 'receiver_id', 'name' => 'receiver.last_name', 'title' => 'Receiver', 'visible' => false])
            ->addColumn(['data' => 'property_id', 'name' => 'property_id', 'title' => 'Property Name '])
            ->addColumn(['data' => 'message', 'name' => 'message', 'title' => 'Messages'])
            ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At', 'orderable' => false,])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'className' => 'text-center'])
             ->parameters(dataTableOptions());
    }


    protected function filename(): string
    {
        return 'messagesdatatables_' . time();
    }
}
