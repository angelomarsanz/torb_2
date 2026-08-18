<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Http\Controllers\Controller;
use App\Models\{
    User,
    Properties,
    Bookings,
    Reports,
    Currency,
    Messages,
    Reviews
};
use Carbon\Carbon;
use Common;

class DashboardController extends Controller
{
    protected $report;

    public function __construct(Reports $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        $data['total_users_count']        = User::count();
        $data['total_property_count']     = Properties::count();
        $data['total_reservations_count'] = Bookings::count();

        $data['today_users_count']        = User::whereDate('created_at', DB::raw('CURDATE()'))->count();
        $data['today_property_count']     = Properties::whereDate('created_at', DB::raw('CURDATE()'))->count();
        $data['today_reservations_count'] = Bookings::whereDate('created_at', DB::raw('CURDATE()'))->count();

        // Dashboard widget counts (matching standard dashboard design)
        $data['active_properties_count']  = Properties::where('status', 'Listed')->count();
        $data['pending_properties_count'] = Properties::where('status', 'Unlisted')->count();
        $data['expired_properties_count'] = 0;
        $data['agents_count']            = (int) \DB::table('properties')->selectRaw('count(distinct host_id) as c')->value('c');

        $properties = new Properties;
        $data['propertiesList'] = $properties->getLatestProperties();

        $bookings = new Bookings;
        $data['bookingList'] = $bookings->getBookingLists();
        return view('admin.dashboard', $data);
    }

}
