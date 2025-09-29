@extends('layouts.app')
@section('content')
<div id="main-wrapper" class="page-wrapper">
    <div class="container-fluid">
        <div class="card mb-3 business-analytics">
            <div class="card-body">
                <div class="row flex-between align-items-center g-2 mb-3 order_stats_header">
                    <div class="col-sm-6">
                        <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                            {{ trans('lang.dashboard_today_trip') }}</h4>
                    </div>
                </div>

                <div class="row business-analytics_list">
                    <div class="col-md-4">
                        <div class="card card-box-with-icon bg--15">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="card-box-with-content">
                                    <h4 class="text-dark-2 mb-1 h4" id="total_rides_today">00</h4>
                                    <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_orders')}}</p>
                                </div>
                                <span class="box-icon ab"><img src="{{ asset('images/total_rides.png') }}"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-box-with-icon bg--1">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="card-box-with-content">
                                    <h4 class="text-dark-2 mb-1 h4" id="users_count_today">00</h4>
                                    <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_clients')}}</p>
                                </div>
                                <span class="box-icon ab"><img src="{{ asset('images/home_users.png') }}"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-box-with-icon bg--5">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="card-box-with-content">
                                    <h4 class="text-dark-2 mb-1 h4" id="driver_count_today">00</h4>
                                    <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_drivers')}}</p>
                                </div>
                                <span class="box-icon ab"><img src="{{ asset('images/home_driver.png') }}"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    jQuery("#overlay").show();
    loadDashboardData();
});

function loadDashboardData() {
    $.ajax({
        url: '/test-stats',
        method: 'GET',
        success: function(response) {
            if(response.success) {
                var data = response.data;
                $('#total_rides_today').text(data.orders || 0);
                $('#users_count_today').text(data.users || 0);
                $('#driver_count_today').text(data.drivers || 0);
            }
        },
        error: function() {
            console.log('Error loading stats');
        },
        complete: function() {
            jQuery("#overlay").hide();
        }
    });
}
</script>
@endsection
