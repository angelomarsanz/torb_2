<?php

namespace App\DataTables;

use App\Models\{Language, Settings};
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;

class LanguageDataTable extends DataTable
{

    protected array|string $exportColumns = ['name', 'value', 'status'];

    public function ajax(): JsonResponse
    {
        $language = $this->query();

        return datatables()
            ->of($language)
            ->addColumn('action', function ($language) {
                $defaultLanguage = Settings::getAll()->where('name', 'default_language')->first();
                $edit   = '<a href="' . url('admin/settings/edit-language/' . $language->id) . '" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';
                if ($defaultLanguage->value != $language->id) {
                    $delete = '<a href="' . url('admin/settings/delete-language/' . $language->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>';
                } else {
                    $delete = '';
                }
                return $edit . $delete;
            })
            ->addColumn('status', function ($language) {
                $status = $language->status;
                $badgeClass = ($status == 'Active') ? 'status-accepted' : 'status-expired';
                $icon = ($status == 'Active') ? 'fa-check-circle' : 'fa-info-circle';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function query()
    {
        $language = Language::select();
        return $this->applyScopes($language);
    }

    public function html()
    {
        return $this->builder()
        ->columns([
            ['data' => 'name', 'name' => 'name', 'title' => 'Name', 'className' => 'bold-column'],
            'short_name',
            'status'
        ])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
        ->parameters(dataTableOptions());
    }
}
