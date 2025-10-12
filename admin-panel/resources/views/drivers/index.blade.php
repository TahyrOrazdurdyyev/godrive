@extends('layouts.app')
@section('content')
@php
$type = 'all';
@endphp
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                @if(request()->is('drivers/approved'))
                @php $type = 'approved'; @endphp
                {{trans('lang.approved_drivers')}}
                @elseif(request()->is('drivers/pending'))
                @php $type = 'pending'; @endphp
                {{trans('lang.approval_pending_drivers')}}
                @else
                {{trans('lang.all_drivers')}}
                @endif
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.driver_table')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="admin-top-section">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex top-title-section pb-4 justify-content-between">
                        <div class="d-flex top-title-left align-self-center">
                            <span class="icon mr-3"><img src="{{ asset('images/driver.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.driver_table')}}</h3>
                            <span class="counter ml-3 total_count">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-list">
            <div class="row">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-0">
                            <div class="card-header-title">
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.driver_table')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.driver_table_text')}}</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="driverTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{trans('lang.user_info')}}</th>
                                            <th>{{trans('lang.email')}}</th>
                                            <th>{{trans('lang.phone')}}</th>
                                            <th>{{trans('lang.status')}}</th>
                                            <th>{{trans('lang.date')}}</th>
                                            <th>{{trans('lang.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="append_list1">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var type = "{{$type}}";
    var placeholderImage = "{{ asset('/images/default_user.png') }}";

    // Load drivers via AJAX from MySQL
    function loadDrivers(filterType) {
        jQuery("#overlay").show();
        
        $.ajax({
            url: '{{ route("drivers.api.list") }}',
            type: 'GET',
            data: {
                type: filterType
            },
            success: function(response) {
                jQuery("#overlay").hide();
                
                if (response.success) {
                    var html = '';
                    var drivers = response.drivers;
                    
                    $('.total_count').text(drivers.length);
                    
                    if (drivers.length === 0) {
                        html = '<tr><td colspan="6" class="text-center">No drivers found</td></tr>';
                    } else {
                        drivers.forEach(function(driver) {
                            var userImg = driver.profile_pic 
                                ? '<img width="70" height="70" src="' + driver.profile_pic + '" alt="image">'
                                : '<img width="70" height="70" src="' + placeholderImage + '" alt="image">';
                            
                            var statusBadge = driver.is_active == 1 
                                ? '<span class="badge badge-success">Active</span>' 
                                : '<span class="badge badge-warning">Pending</span>';
                            
                            var actionButtons = driver.is_active == 0 
                                ? '<button class="btn btn-success btn-sm" onclick="approveDriver(' + driver.id + ')">Approve</button> ' +
                                  '<button class="btn btn-danger btn-sm" onclick="rejectDriver(' + driver.id + ')">Reject</button>'
                                : '<a href="/drivers/view/' + driver.id + '" class="btn btn-primary btn-sm">View</a>';
                            
                            html += '<tr>' +
                                '<td>' + userImg + ' ' + driver.full_name + '</td>' +
                                '<td>' + driver.email + '</td>' +
                                '<td>' + driver.phone + '</td>' +
                                '<td>' + statusBadge + '</td>' +
                                '<td>' + (driver.created_at || 'N/A') + '</td>' +
                                '<td>' + actionButtons + '</td>' +
                                '</tr>';
                        });
                    }
                    
                    $('#append_list1').html(html);
                } else {
                    alert('Error loading drivers: ' + response.message);
                }
            },
            error: function(xhr) {
                jQuery("#overlay").hide();
                alert('Error loading drivers: ' + xhr.responseText);
            }
        });
    }

    // Approve driver
    function approveDriver(driverId) {
        if (!confirm('Are you sure you want to approve this driver?')) return;
        
        jQuery("#overlay").show();
        
        $.ajax({
            url: '/api/drivers/' + driverId + '/approve',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                jQuery("#overlay").hide();
                
                if (response.success) {
                    alert('Driver approved successfully!');
                    loadDrivers(type);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                jQuery("#overlay").hide();
                alert('Error approving driver: ' + xhr.responseText);
            }
        });
    }

    // Reject driver
    function rejectDriver(driverId) {
        if (!confirm('Are you sure you want to reject this driver?')) return;
        
        jQuery("#overlay").show();
        
        $.ajax({
            url: '/api/drivers/' + driverId + '/reject',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                jQuery("#overlay").hide();
                
                if (response.success) {
                    alert('Driver rejected successfully!');
                    loadDrivers(type);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                jQuery("#overlay").hide();
                alert('Error rejecting driver: ' + xhr.responseText);
            }
        });
    }

    // Load drivers when page loads
    $(document).ready(function() {
        loadDrivers(type);
    });
</script>
@endsection
