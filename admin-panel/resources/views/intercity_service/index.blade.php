@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.intercity_service_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.intercity_service_table')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="admin-top-section">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex top-title-section pb-4 justify-content-between">
                        <div class="d-flex top-title-left align-self-center">
                            <span class="icon mr-3"><img src="{{ asset('images/category.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.intercity_service_plural')}}</h3>
                            <span class="counter ml-3">{{ $services->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <strong>Note:</strong> Freight km charge will be calculated based on freight vehicle km charge
        </div>

        <div class="table-list">
            <div class="row">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-0">
                            <div class="card-header-title">
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.intercity_service_table')}}</h3>
                                <p class="mb-0 text-dark-2">View and manage all the intercity service</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a class="btn-primary btn rounded-full" href="{{ route('intercity-service.create') }}">
                                        <i class="mdi mdi-plus mr-2"></i>{{trans('lang.intercity_service_create')}}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="intercityTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{trans('lang.intercity_service_name')}}</th>
                                            <th>{{trans('lang.image')}}</th>
                                            <th>Price per Seat</th>
                                            <th>Price Full Vehicle</th>
                                            <th>{{trans('lang.enable')}}</th>
                                            <th>{{trans('lang.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($services as $service)
                                        <tr>
                                            <td>{{ $service->title }}</td>
                                            <td>
                                                @if($service->image)
                                                    <img src="{{ $service->image }}" width="60" height="60" alt="image">
                                                @else
                                                    <img src="{{ asset('images/default_service.png') }}" width="60" height="60" alt="image">
                                                @endif
                                            </td>
                                            <td>${{ number_format($service->price_per_seat, 2) }}</td>
                                            <td>${{ number_format($service->price_full_vehicle, 2) }}</td>
                                            <td>
                                                <label class="switch">
                                                    <input type="checkbox" class="status-toggle" data-id="{{ $service->id }}" {{ $service->enable ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <span class="action-btn">
                                                    <a href="{{ route('intercity-service.edit', $service->id) }}">
                                                        <i class="mdi mdi-lead-pencil"></i>
                                                    </a>
                                                    <a href="javascript:void(0)" class="delete-service" data-id="{{ $service->id }}">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
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
    $(document).ready(function() {
        $('#intercityTable').DataTable({
            order: [[0, 'asc']],
            columnDefs: [
                {orderable: false, targets: [1, 4, 5]}
            ],
            language: {
                zeroRecords: "{{trans('lang.no_record_found')}}",
                emptyTable: "{{trans('lang.no_record_found')}}"
            }
        });

        // Toggle status
        $(document).on('change', '.status-toggle', function() {
            var serviceId = $(this).data('id');
            var isEnabled = $(this).is(':checked');
            
            $.ajax({
                url: '/intercity-service/' + serviceId + '/toggle-status',
                method: 'POST',
                data: {
                    enable: isEnabled,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (!response.success) {
                        alert('Error updating status');
                        location.reload();
                    }
                },
                error: function() {
                    alert('Error updating status');
                    location.reload();
                }
            });
        });

        // Delete service
        $(document).on('click', '.delete-service', function() {
            if (confirm("{{trans('lang.delete_alert')}}")) {
                var serviceId = $(this).data('id');
                
                $.ajax({
                    url: '/intercity-service/' + serviceId,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error deleting service');
                        }
                    },
                    error: function() {
                        alert('Error deleting service');
                    }
                });
            }
        });
    });
</script>
@endsection
