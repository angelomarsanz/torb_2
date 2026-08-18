<?php

namespace App\DataTables;

use App\Models\{Currency, Settings};
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;

class CurrencyDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('action', function ($currency) {
                $homeCurrency = Settings::getAll()->where('name', 'default_currency')->first();
                $edit = '<a href="' . url('admin/settings/edit-currency/' . $currency->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';
                if ($currency->id != $homeCurrency->value) {
                    $delete = '<a href="' . url('admin/settings/delete-currency/' . $currency->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>';
                } else {
                    $delete = '';
                }

                return $edit . ' ' . $delete;
            })
            ->addColumn('org_symbol', function ($transaction) {
                return  $transaction->org_symbol ;

            })
            ->addColumn('status', function ($currency) {
                $status = $currency->status;
                $badgeClass = ($status == 'Active') ? 'status-accepted' : 'status-expired';
                $icon = ($status == 'Active') ? 'fa-check-circle' : 'fa-info-circle';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
            })

            ->rawColumns(['action', 'org_symbol', 'status'])
            ->toJson();
    }

    public function query()
    {
        $query = Currency::query();
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'name', 'name' => 'currency.name', 'title' => 'Name', 'className' => 'bold-column'])
            ->addColumn(['data' => 'code', 'name' => 'currency.code', 'title' => 'Code'])
            ->addColumn(['data' => 'org_symbol', 'name' => 'currency.symbol', 'title' => 'Symbol'])
            ->addColumn(['data' => 'rate', 'name' => 'currency.rate', 'title' => 'Rate'])
            ->addColumn(['data' => 'status', 'name' => 'currency.status', 'title' => 'Status'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
            ->parameters(dataTableOptions());
    }


    protected function filename(): string
    {
        return 'currencydatatables_' . time();
    }
}
