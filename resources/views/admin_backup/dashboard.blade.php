@extends('admin.template')
@section('main')
  <div class="content-wrapper">
    <section class="content-header py-3">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-sm-6">
            <h1 class="stunning-page-title mb-0">Dashboard</h1>
          </div>
          <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
             <a href="{{ url('admin/add-properties') }}" class="stunning-btn-setup">
                <i class="fa fa-plus"></i> Add Property
             </a>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        {{-- Small boxes (Stat box) - AdminLTE 4 / Bootstrap 5 --}}
        <div class="row">
          <div class="col-lg-4 col-6">
            <div class="small-box bg-gradient-indigo text-white">
              <div class="inner">
                <h3>{{ $total_users_count }}</h3>
                <p>Total Users</p>
              </div>
              <div class="icon">
                <i class="fa fa-user-plus"></i>
              </div>
              <a href="{{ url('admin/customers') }}" class="small-box-footer f-14">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-4 col-6">
            <div class="small-box bg-gradient-teal text-white">
              <div class="inner">
                <h3>{{ $total_property_count }}</h3>
                <p>Total Property</p>
              </div>
              <div class="icon">
                <i class="fa fa-building"></i>
              </div>
              <a href="{{ url('admin/properties') }}" class="small-box-footer f-14">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-4 col-6">
            <div class="small-box bg-gradient-cyan text-white">
              <div class="inner">
                <h3>{{ $total_reservations_count }}</h3>
                <p>Total Reservations</p>
              </div>
              <div class="icon">
                <i class="fa fa-plane"></i>
              </div>
              <a href="{{ url('admin/bookings') }}" class="small-box-footer f-14">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-4 col-6">
            <div class="small-box bg-gradient-purple text-white">
              <div class="inner">
                <h3>{{ $today_users_count }}</h3>
                <p>Today Users</p>
              </div>
              <div class="icon">
                <i class="fa fa-user-plus"></i>
              </div>
              <a href="{{ url('admin/customers') }}" class="small-box-footer f-14">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-4 col-6">
            <div class="small-box bg-gradient-orange text-white">
              <div class="inner">
                <h3>{{ $today_property_count }}</h3>
                <p>Today Property</p>
              </div>
              <div class="icon">
                <i class="fa fa-building"></i>
              </div>
              <a href="{{ url('admin/properties') }}" class="small-box-footer f-14">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-4 col-6">
            <div class="small-box bg-gradient-rose text-white">
              <div class="inner">
                <h3>{{ $today_reservations_count }}</h3>
                <p>Today Reservations</p>
              </div>
              <div class="icon">
                <i class="fa fa-plane"></i>
              </div>
              <a href="{{ url('admin/bookings') }}" class="small-box-footer f-14">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        {{-- Latest Property --}}
        <div class="row">
          <div class="col-12">
            <div class="card stunning-table-card">
              <div class="card-header">
                <h3 class="card-title mb-0">Latest Property</h3>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive stunning-table-wrapper">
                  <table class="table mb-0">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Host Name</th>
                        <th>Space type</th>
                        <th width="15%">Date</th>
                        <th width="5%">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if (!empty($propertiesList))
                        @foreach ($propertiesList as $property)
                          <tr>
                            <td><a href="{{ url('admin/listing/' . $property->properties_id) . '/basics' }}">{{ $property->property_name }}</a></td>
                            <td><a href="{{ url('admin/edit-customer/' . $property->host_id) }}">{{ $property->first_name . ' ' . $property->last_name }}</a></td>
                            <td>{{ $property->property_name }}</td>
                            <td>{{ dateFormat($property->property_created_at) }}</td>
                            <td>{{ $property->property_status }}</td>
                          </tr>
                        @endforeach
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Latest Bookings --}}
        <div class="row">
          <div class="col-12">
            <div class="card stunning-table-card">
              <div class="card-header">
                <h3 class="card-title mb-0">Latest Bookings</h3>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive stunning-table-wrapper">
                  <table class="table mb-0">
                    <thead>
                      <tr>
                        <th>Host Name</th>
                        <th>Guest Name</th>
                        <th>Property Name</th>
                        <th>Total Amount</th>
                        <th>Date</th>
                        <th width="5%">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if (!empty($bookingList))
                        @foreach ($bookingList as $booking)
                          <tr>
                            <td><a href="{{ url('admin/bookings/detail/' . $booking->id) }}">{{ $booking->host_name }}</a></td>
                            <td><a href="{{ url('admin/edit-customer/' . $booking->user_id) }}">{{ $booking->guest_name }}</a></td>
                            <td><a href="{{ url('admin/listing/' . $booking->property_id) . '/basics' }}">{{ $booking->property_name }}</a></td>
                            <td>{!! moneyFormat($booking->symbol, $booking->total_amount) !!}</td>
                            <td>{{ dateFormat($booking->created_at) }}</td>
                            <td>{{ $booking->status }}</td>
                          </tr>
                        @endforeach
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
@stop
