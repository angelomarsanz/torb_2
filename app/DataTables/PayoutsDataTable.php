<?php
namespace App\DataTables;

use App\Models\Withdrawal;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;
use Request;

class PayoutsDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        $payout = $this->query();

        return datatables()->of($payout)

        ->addColumn('p_method', function ($payout) {
            return $payout->payment_methods?->name;
        })

        ->addColumn('user_id', function ($payout) {
            return ucfirst($payout->user?->first_name ? $payout->user?->first_name : '') . ' ' . ucfirst($payout->user?->last_name ? $payout->user?->last_name : '');
        })

        ->addColumn('created_at', function ($payout) {
                return dateFormat($payout->created_at);
        })


        ->addColumn('subtotal', function ($payout) {
            return $payout->status == 'Success' ? $payout->amount : $payout->subtotal;
        })

        ->addColumn('currency_id', function ($payout) {
            return $payout->currency?->code;
        })

        ->addColumn('status', function ($payout) {
            $status = $payout->status;
            $badgeClass = ($status == 'Success') ? 'status-accepted' : (($status == 'Pending') ? 'status-pending' : 'status-expired');
            $icon = ($status == 'Success') ? 'fa-check-circle' : (($status == 'Pending') ? 'fa-clock-o' : 'fa-info-circle');
            
            return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
        })

        ->addColumn('action', function ($withDrawal) {
            [$url, $title, $icon] = ($withDrawal->status == "Pending") ? ['edit/', 'Edit payout', 'edit'] : ['details/', 'Details', 'tasks'];
            return '<a href="' . url('admin/payouts/' . $url . $withDrawal->id) . '" class="btn-action btn-action-edit" title="' . $title . '"><i class="fa fa-' . $icon . '"></i></a>&nbsp;';
        })

        ->rawColumns(['status', 'action'])
        ->toJson();
    }

    public function query()
    {
        $status   = isset(request()->status) ? request()->status : null;
        $from     = isset(request()->from) ? setDateForDb(request()->from) : null;
        $to       = isset(request()->to) ? setDateForDb(request()->to) : null;

        $user_id  = Request::segment(4);

        $query = Withdrawal::with('user', 'currency', 'payment_methods');
        
        if (isset($user_id)) {
            $query->where('withdrawals.user_id', '=', $user_id);
        }

        if (!empty($from)) {
         $query->whereDate('withdrawals.created_at', '>=', $from);
        }

        if (!empty($to)) {
            $query->whereDate('withdrawals.created_at', '<=', $to);
        }

        if (!empty($status)) {
            $query->where('withdrawals.status', '=', $status);
        }

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'ID', 'visible' => false, 'width' => '5%', 'className' => 'text-center'])
        ->addColumn(['data' => 'user_id', 'name' => 'user.first_name','user.last_name' , 'title' => 'User Name', 'className' => 'bold-column'])
        ->addColumn(['data' => 'currency_id', 'name' => 'currency.code', 'title' => 'Currency'])
        ->addColumn(['data' => 'p_method', 'name' => 'payment_methods.name', 'title' => 'Payment Method'])
        ->addColumn(['data' => 'account_number', 'name' => 'account_number', 'title' => 'Account Number'])
        ->addColumn(['data' => 'email', 'name' => 'user.email', 'title' => 'Email'])
        ->addColumn(['data' => 'subtotal', 'name' => 'amount', 'title' => 'Amount'])
        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
        ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])

        ->parameters(dataTableOptions());
    }


    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'payoutsdatatables_' . time();

    }
}
