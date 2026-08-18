@extends('admin.template')
@section('main')
  <div class="content-wrapper dashboard-page-wrapper">
    <div class="dashboard-content-inner">
    <section class="content-header dashboard-page-header">
      <div class="dashboard-header-inner">
        <h1 class="dashboard-page-title">DASHBOARD</h1>
      </div>
    </section>

    <section class="content dashboard-content-section">
      <div class="container-fluid px-0">
    
        {{-- Widget cards: 4 boxes in a horizontal line, equal width --}}
        <div class="row dashboard-widgets-row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
          {{-- Total Customers --}}
          <div class="col">
            <a href="{{ url('admin/customers') }}" class="text-decoration-none d-block h-100">
              <div class="dashboard-widget-card dashboard-widget-light dashboard-widget-light-purple h-100">
                <span class="dashboard-widget-title">Total Customers</span>
                <h3 class="dashboard-widget-value">{{ $total_users_count ?? 0 }}</h3>
                <i class="fa fa-users dashboard-widget-bg-icon" aria-hidden="true"></i>
              </div>
            </a>
          </div>
          {{-- Total Properties --}}
          <div class="col">
            <a href="{{ url('admin/properties') }}" class="text-decoration-none d-block h-100">
              <div class="dashboard-widget-card dashboard-widget-light dashboard-widget-light-teal h-100">
                <span class="dashboard-widget-title">Total Properties</span>
                <h3 class="dashboard-widget-value">{{ $total_property_count ?? 0 }}</h3>
                <i class="fa fa-building dashboard-widget-bg-icon" aria-hidden="true"></i>
              </div>
            </a>
          </div>
          
          {{-- Todays Properties --}}
          <div class="col">
            <a href="{{ url('admin/properties') }}" class="text-decoration-none d-block h-100">
              <div class="dashboard-widget-card dashboard-widget-light dashboard-widget-light-amber h-100">
                <span class="dashboard-widget-title">Total Reservations</span>  
                <h3 class="dashboard-widget-value">{{ $total_reservations_count ?? 0 }}</h3>
                <i class="fa fa-home dashboard-widget-bg-icon" aria-hidden="true"></i>
              </div>
            </a>
          </div>
          
          {{-- Todays Reservations --}}
          <div class="col">
            <a href="{{ url('admin/bookings') }}" class="text-decoration-none d-block h-100">
              <div class="dashboard-widget-card dashboard-widget-light dashboard-widget-light-blue h-100">
                <span class="dashboard-widget-title">Todays Reservations</span>
                <h3 class="dashboard-widget-value">{{ $today_reservations_count ?? 0 }}</h3>
                <i class="fa fa-calendar dashboard-widget-bg-icon" aria-hidden="true"></i>
              </div>
            </a>
          </div>
        </div>

        {{-- Latest Property --}}
        <div class="row">
          <div class="col-12">
            <div class="card stunning-table-card dashboard-latest-card">
              <div class="card-header">
                <h3 class="card-title mb-0">Latest Property</h3>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive stunning-table-wrapper dashboard-latest-table">
                  <table class="table table-dashboard-latest mb-0">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Host Name</th>
                        <th>Space type</th>
                        <th width="15%">Created at</th>
                        <th width="10%">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if (!empty($propertiesList))
                        @foreach ($propertiesList as $property)
                          <tr>
                            <td><a href="{{ url('admin/listing/' . $property->properties_id) . '/basics' }}">{{ $property->property_name }}</a></td>
                            <td><a href="{{ url('admin/edit-customer/' . $property->host_id) }}">{{ $property->first_name . ' ' . $property->last_name }}</a></td>
                            <td class="text-muted">{{ $property->property_name }}</td>
                            <td class="text-muted">{{ dateFormat($property->property_created_at) }}</td>
                            <td>
                              @if($property->property_status == 'Listed')
                                <span class="status-badge status-accepted"><i class="fa fa-check-circle"></i> Listed</span>
                              @elseif($property->property_status == 'Unlisted')
                                <span class="status-badge status-expired"><i class="fa fa-info-circle"></i> Unlisted</span>
                              @else
                                <span class="status-badge status-pending"><i class="fa fa-dot-circle-o"></i> {{ $property->property_status }}</span>
                              @endif
                            </td>
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
            <div class="card stunning-table-card dashboard-latest-card">
              <div class="card-header">
                <h3 class="card-title mb-0">Latest Bookings</h3>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive stunning-table-wrapper dashboard-latest-table">
                  <table class="table table-dashboard-latest mb-0">
                    <thead>
                      <tr>
                        <th>Host Name</th>
                        <th>Guest Name</th>
                        <th>Property Name</th>
                        <th>Total Amount</th>
                        <th>Created at</th>
                        <th width="10%">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if (!empty($bookingList))
                        @foreach ($bookingList as $booking)
                          <tr>
                            <td><a href="{{ url('admin/bookings/detail/' . $booking->id) }}">{{ $booking->host_name }}</a></td>
                            <td><a href="{{ url('admin/edit-customer/' . $booking->user_id) }}">{{ $booking->guest_name }}</a></td>
                            <td><a href="{{ url('admin/listing/' . $booking->property_id) . '/basics' }}">{{ $booking->property_name }}</a></td>
                            <td class="text-muted">{!! moneyFormat($booking->symbol, $booking->total_amount) !!}</td>
                            <td class="text-muted">{{ dateFormat($booking->created_at) }}</td>
                            <td>
                              @php
                                $status = strtolower($booking->status);
                              @endphp
                              @if($status == 'accepted' || $status == 'completed')
                                <span class="status-badge status-accepted"><i class="fa fa-check-circle"></i> {{ ucfirst($status) }}</span>
                              @elseif($status == 'pending')
                                <span class="status-badge status-pending"><i class="fa fa-clock-o"></i> {{ ucfirst($status) }}</span>
                              @elseif($status == 'cancelled' || $status == 'declined')
                                <span class="status-badge status-cancelled"><i class="fa fa-times-circle"></i> {{ ucfirst($status) }}</span>
                              @elseif($status == 'expired')
                                <span class="status-badge status-expired"><i class="fa fa-exclamation-circle"></i> {{ ucfirst($status) }}</span>
                              @elseif($status == 'processing')
                                <span class="status-badge status-processing"><i class="fa fa-refresh fa-spin-hover"></i> {{ ucfirst($status) }}</span>
                              @else
                                <span class="badge bg-secondary">{{ $booking->status }}</span>
                              @endif
                            </td>
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
  </div>
@stop
