@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor restaurantTitle">{{ trans('lang.subscription_plans') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.subscription_plans') }}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="admin-top-section">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex top-title-section pb-4 justify-content-between">
                        <div class="d-flex top-title-left align-items-center">
                            <span class="icon mr-3"><img src="{{ asset('images/subscription.png') }}"></span>
                            <h3 class="mb-0">{{ trans('lang.subscription_plans') }}</h3>
                            <span class="counter ml-3 total_count">{{ $plans->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="overview-sec">
            <div class="row">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-0">
                            <div class="card-header-title">
                                <h3 class="text-dark-2 mb-2 h4">{{trans("lang.overview")}}</h3>
                                <p class="mb-0 text-dark-2">{{trans("lang.see_overview_of_package_earning")}}</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row subscription-list">
                                @foreach($plans as $plan)
                                <div class="col-md-4">
                                    <div class="card card-box-with-icon">
                                        <div class="card-body">
                                            <span class="box-icon">
                                                <img src="{{ $plan->image ? asset($plan->image) : asset('images/placeholder.png') }}">
                                            </span>
                                            <div class="card-box-with-content mt-3">
                                                <h4 class="text-dark-2 mb-1 h4">{{ $plan->title }}</h4>
                                                <p class="mb-0 text-dark-2">
                                                    @if($plan->type == 'free')
                                                        <span style="color:red;">Free</span>
                                                    @else
                                                        {{ number_format($plan->amount, 2) }}
                                                    @endif
                                                </p>
                                            </div>
                                            <span class="background-img">
                                                <img src="{{ $plan->image ? asset($plan->image) : asset('images/placeholder.png') }}">
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans("lang.subscription_package_list")}}</h3>
                                <p class="mb-0 text-dark-2">{{trans("lang.manage_all_package_in_single_click")}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a href="{!! route('subscription-plans.save') !!}"
                                        class="btn-primary btn rounded-full"><i
                                            class="mdi mdi-plus mr-2"></i>{{ trans('lang.create_subscription_plan') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif
                            
                            @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                            @endif

                            <div class="table-responsive m-t-10">
                                <table id="subscriptionPlansTable"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('lang.plan_name') }}</th>
                                            <th>{{ trans('lang.plan_price') }}</th>
                                            <th>{{ trans('lang.duration') }}</th>
                                            <th>{{ trans('lang.total_orders') }}</th>
                                            <th>{{ trans('lang.status') }}</th>
                                            <th>{{ trans('lang.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($plans as $plan)
                                        <tr>
                                            <td>
                                                @if($plan->image)
                                                <img src="{{ asset($plan->image) }}" alt="" style="width:50px;height:50px;border-radius:50%;" class="mr-2">
                                                @endif
                                                {{ $plan->title }}
                                            </td>
                                            <td>
                                                @if($plan->type == 'free')
                                                    <span style="color:red;">Free</span>
                                                @else
                                                    {{ number_format($plan->amount, 2) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($plan->duration_days == -1)
                                                    {{ trans('lang.unlimited') }}
                                                @else
                                                    {{ $plan->duration_days }} Days
                                                @endif
                                            </td>
                                            <td>
                                                @if($plan->total_orders == -1)
                                                    {{ trans('lang.unlimited') }}
                                                @else
                                                    {{ $plan->total_orders }}
                                                @endif
                                            </td>
                                            <td>
                                                <label class="switch">
                                                    <input type="checkbox" class="status-toggle" data-id="{{ $plan->id }}" {{ $plan->enable ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <span class="action-btn">
                                                    <a href="{{ route('subscription-plans.save', $plan->id) }}" class="link-td">
                                                        <i class="mdi mdi-lead-pencil"></i>
                                                    </a>
                                                    <a href="javascript:void(0)" class="link-td delete-plan" data-id="{{ $plan->id }}">
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
    // Initialize DataTable
    $('#subscriptionPlansTable').DataTable({
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [5] }
        ]
    });

    // Status toggle
    $('.status-toggle').on('change', function() {
        var planId = $(this).data('id');
        var isEnabled = $(this).is(':checked');
        var checkbox = $(this);

        $.ajax({
            url: '/subscription-plans/toggle-status/' + planId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                enable: isEnabled ? 1 : 0
            },
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message || 'Status updated successfully');
                } else {
                    toastr.error(response.message || 'Failed to update status');
                    checkbox.prop('checked', !isEnabled);
                }
            },
            error: function() {
                toastr.error('Failed to update status');
                checkbox.prop('checked', !isEnabled);
            }
        });
    });

    // Delete plan
    $('.delete-plan').on('click', function() {
        var planId = $(this).data('id');
        
        if(confirm('{{ trans("lang.are_you_sure_you_want_to_delete") }}')) {
            $.ajax({
                url: '/subscription-plans/delete/' + planId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message || 'Plan deleted successfully');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete plan');
                    }
                },
                error: function() {
                    toastr.error('Failed to delete plan');
                }
            });
        }
    });
});
</script>
@endsection
