<?php

namespace App\DataTables;

use App\Models\Testimonials;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\JsonResponse;
use Common;

class TestimonialsDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        $testimonials = $this->query();

        return datatables()
            ->of($testimonials)

            ->addColumn('action', function ($testimonials) {
                $edit = $delete = '';
                if (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_testimonial')) {
                    $edit = '<a href="' . url('admin/edit-testimonials/' . $testimonials->id).'" class="btn-action btn-action-edit"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';
                }
                if (Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_testimonial')) {
                    $delete = '<a href="' . url('admin/delete-testimonials/' . $testimonials->id) . '" class="btn-action btn-action-delete delete-warning"><i class="fa fa-trash-o"></i></a>';
                }
                return $edit . $delete;
            })

            ->addColumn('review', function($testimonials)  {
                $options = '';
                for ($i = 1; $i <=5 ; $i++) {
                    $activeClass = ($i <= $testimonials->review) ? 'rating-star-active' : '';
                    $options .= ' <i class="fa fa-star rating-star-item ' . $activeClass . '" style="font-size: 1rem;"></i>';
                }
                return $options;
            })

            ->addColumn('created_at', function ($testimonials) {
                return dateFormat($testimonials->created_at);
            })

            ->addColumn('description', function ($testimonials) {
                return substr($testimonials->description, 0, 50);
            })
            ->addColumn('status', function ($testimonials) {
                $status = $testimonials->status;
                $badgeClass = ($status == 'Active') ? 'status-accepted' : 'status-expired';
                $icon = ($status == 'Active') ? 'fa-check-circle' : 'fa-info-circle';
                return '<span class="status-badge ' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . $status . '</span>';
            })
            ->rawColumns(['action', 'review', 'status'])
            ->make(true);
    }


    public function query()
    {
        $testimonials =Testimonials::select();
        return $this->applyScopes($testimonials);
    }

    public function html()
    {
        return $this->builder()
        ->columns([
            ['data' => 'id', 'name' => 'id', 'title' => 'Id', 'width' => '5%', 'className' => 'text-center'],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name', 'className' => 'bold-column'],
            'designation',
            'description',
            'review',
            'status',
            'created_at'

        ])
        ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center'])
        ->parameters(dataTableOptions());
    }
}
