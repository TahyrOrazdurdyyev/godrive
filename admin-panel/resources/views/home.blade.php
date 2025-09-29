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
                    <div class="col-md-4">
                        <div class="card card-box-with-icon bg--24">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="card-box-with-content">
                                    <h4 class="text-dark-2 mb-1 h4" id="earnings_count_today">$0</h4>
                                    <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_earnings')}}</p>
                                </div>
                                <span class="box-icon ab"><img src="{{ asset('images/total_earning.png') }}"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-box-with-icon bg--14">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="card-box-with-content">
                                    <h4 class="text-dark-2 mb-1 h4" id="admincommission_count_today">$0</h4>
                                    <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_admin_commission')}}</p>
                                </div>
                                <span class="box-icon ab"><img src="{{ asset('images/total_payment.png') }}"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4 mb-3"></div>

                    <div class="col-sm-6 col-lg-3 mb-3">
                        <div class="card-box">
                            <h5>{{ trans('lang.dashboard_ride_placed') }}</h5>
                            <h2 id="placed_count_today">0</h2>
                            <i class="mdi mdi-check-circle"></i>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mb-3">
                        <div class="card-box">
                            <h5>{{ trans('lang.dashboard_ride_active') }}</h5>
                            <h2 id="active_count_today">0</h2>
                            <i class="mdi mdi-car-connected"></i>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mb-3">
                        <div class="card-box">
                            <h5>{{ trans('lang.dashboard_ride_completed') }}</h5>
                            <h2 id="completed_count_today">0</h2>
                            <i class="mdi mdi-check-circle-outline"></i>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mb-3">
                        <div class="card-box">
                            <h5>{{ trans('lang.dashboard_ride_canceled') }}</h5>
                            <h2 id="canceled_count_today">0</h2>
                            <i class="mdi mdi-window-close"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- НИЖНИЙ БЛОК - ОБЩАЯ СТАТИСТИКА -->
        <div class="card mb-3 business-analytics">
            <div class="card-body">
                <div class="row flex-between align-items-center g-2 mb-3 order_stats_header">
                    <div class="col-sm-6">
                        <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                            {{ trans('lang.dashboard_total_trip') }}</h4>
                    </div>
                </div>

                <div class="row business-analytics_list">
                    <div class="col-md-4">
                        <div class="card card-box-with-icon bg--15">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="card-box-with-content">
                                    <h4 class="text-dark-2 mb-1 h4" id="total_rides">00</h4>
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
                                    <h4 class="text-dark-2 mb-1 h4" id="users_count">00</h4>
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
                                    <h4 class="text-dark-2 mb-1 h4" id="driver_count">00</h4>
                                    <p class="mb-0 small text-dark-2">{{trans('lang.dashboard_total_drivers')}}</p>
                                </div>
                                <span class="box-icon ab"><img src="{{ asset('images/home_driver.png') }}"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mb-3">
                        <div class="card-box">
                            <h5>{{ trans('lang.dashboard_ride_placed') }}</h5>
                            <h2 id="placed_count">0</h2>
                            <i class="mdi mdi-check-circle"></i>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mb-3">
                        <div class="card-box">
                            <h5>{{ trans('lang.dashboard_ride_active') }}</h5>
                            <h2 id="active_count">0</h2>
                            <i class="mdi mdi-car-connected"></i>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mb-3">
                        <div class="card-box">
                            <h5>{{ trans('lang.dashboard_ride_completed') }}</h5>
                            <h2 id="completed_count">0</h2>
                            <i class="mdi mdi-check-circle-outline"></i>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mb-3">
                        <div class="card-box">
                            <h5>{{ trans('lang.dashboard_ride_canceled') }}</h5>
                            <h2 id="canceled_count">0</h2>
                            <i class="mdi mdi-window-close"></i>
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
        url: '/api/admin/stats/dashboard',
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if(response.success) {
                var data = response.data;
                
                // Обновляем счетчики TODAY
                $('#total_rides_today').text(data.today.total_rides || 0);
                $('#users_count_today').text(data.today.total_users || 0);
                $('#driver_count_today').text(data.today.total_drivers || 0);
                
                // Обновляем счетчики TOTAL
                $('#total_rides').text(data.total.total_rides || 0);
                $('#users_count').text(data.total.total_users || 0);
                $('#driver_count').text(data.total.total_drivers || 0);
                
                // Устанавливаем остальные в 0 пока нет данных
                $('#earnings_count_today').text('$0');
                $('#admincommission_count_today').text('$0');
                $('#placed_count_today').text('0');
                $('#active_count_today').text('0');
                $('#completed_count_today').text('0');
                $('#canceled_count_today').text('0');
                
                $('#placed_count').text('0');
                $('#active_count').text('0');
                $('#completed_count').text('0');
                $('#canceled_count').text('0');
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