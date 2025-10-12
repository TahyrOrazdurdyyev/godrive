@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            @if ($id == '')
            <h3 class="text-themecolor">{{ trans('lang.create_subscription_plan') }}</h3>
            @else
            <h3 class="text-themecolor">{{ trans('lang.edit_subscription_plan') }}</h3>
            @endif
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('subscription-plans') }}">{{ trans('lang.subscription_plans') }}</a></li>
                @if ($id == '')
                <li class="breadcrumb-item active">{{ trans('lang.create_subscription_plan') }}</li>
                @else
                <li class="breadcrumb-item active">{{ trans('lang.edit_subscription_plan') }}</li>
                @endif
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ route('subscription-plans.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $id }}">

                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{ trans('lang.plan_details') }}</legend>
                            
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{ trans('lang.plan_name') }}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control" name="title" value="{{ old('title', $plan->title ?? '') }}" placeholder="{{ trans('lang.enter_plan_name') }}" required>
                                </div>
                            </div>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{ trans('lang.plan_type') }}</label>
                                <div class="form-check width-50">
                                    <input type="radio" id="free_type" name="type" value="free" {{ old('type', $plan->type ?? 'free') == 'free' ? 'checked' : '' }}>
                                    <label class="control-label" for="free_type">{{ trans('lang.free') }}</label>
                                </div>
                                <div class="form-check width-50">
                                    <input type="radio" id="paid_type" name="type" value="paid" {{ old('type', $plan->type ?? '') == 'paid' ? 'checked' : '' }}>
                                    <label class="control-label" for="paid_type">{{ trans('lang.paid') }}</label>
                                </div>
                            </div>

                            <div class="form-group row width-100 {{ old('type', $plan->type ?? 'free') == 'paid' ? '' : 'd-none' }} plan_price_div">
                                <label class="col-3 control-label">{{ trans('lang.plan_price') }}</label>
                                <div class="col-7">
                                    <input type="number" step="0.01" class="form-control" name="amount" id="plan_price" value="{{ old('amount', $plan->amount ?? '') }}" placeholder="{{ trans('lang.enter_plan_price') }}">
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.plan_validity_days') }}</label>
                                <div class="form-check width-100">
                                    <input type="radio" id="unlimited_days" name="duration_type" value="unlimited" {{ old('duration_days', $plan->duration_days ?? -1) == -1 ? 'checked' : '' }}>
                                    <label class="control-label" for="unlimited_days">{{ trans('lang.unlimited') }}</label>
                                </div>
                                <div class="d-flex">
                                    <div class="form-check width-50">
                                        <input type="radio" id="limited_days" name="duration_type" value="limited" {{ old('duration_days', $plan->duration_days ?? -1) != -1 ? 'checked' : '' }}>
                                        <label class="control-label" for="limited_days">{{ trans('lang.limited') }}</label>
                                    </div>
                                    <div class="form-check width-50 {{ old('duration_days', $plan->duration_days ?? -1) != -1 ? '' : 'd-none' }} expiry-limit-div">
                                        <input type="number" name="duration_days" id="plan_validity" class="form-control" value="{{ old('duration_days', $plan->duration_days ?? '') }}" placeholder="{{ trans('lang.ex_365') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.description') }}</label>
                                <div class="col-7">
                                    <textarea class="form-control" name="description" rows="5">{{ old('description', $plan->description ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.order') }}</label>
                                <div class="col-7">
                                    <input type="number" class="form-control" name="display_order" value="{{ old('display_order', $plan->display_order ?? 0) }}" placeholder="{{ trans('lang.enter_display_order') }}" required>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <div class="form-check width-100">
                                    <input type="checkbox" name="enable" id="status" value="1" {{ old('enable', $plan->enable ?? false) ? 'checked' : '' }}>
                                    <label class="control-label" for="status">{{ trans('lang.status') }}</label>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{ trans('lang.image') }}</label>
                                <div class="col-7">
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <div class="form-text text-muted">{{ trans('lang.image') }}</div>
                                    @if(isset($plan) && $plan->image)
                                    <div class="mt-2">
                                        <img src="{{ asset($plan->image) }}" class="rounded" style="width:100px" alt="current image">
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend>{{ trans('lang.plan_points') }}</legend>
                            <div class="form-group row width-100">
                                <div id="options-container" class="col-12">
                                    @if(old('plan_points'))
                                        @foreach(old('plan_points') as $index => $point)
                                        <div class="form-group d-flex ml-1 option-row mt-1">
                                            <input type="text" class="form-control" name="plan_points[]" value="{{ $point }}" placeholder="Enter plan point">
                                            <button type="button" class="btn btn-danger ml-2 remove-point">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </div>
                                        @endforeach
                                    @elseif(isset($plan) && $plan->plan_points)
                                        @foreach($plan->plan_points as $index => $point)
                                        <div class="form-group d-flex ml-1 option-row mt-1">
                                            <input type="text" class="form-control" name="plan_points[]" value="{{ $point }}" placeholder="Enter plan point">
                                            <button type="button" class="btn btn-danger ml-2 remove-point">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" id="add-plan-point" class="btn btn-primary ml-3">{{ trans('lang.add_more') }}</button>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend>{{ trans('lang.maximum_booking_limit') }}</legend>
                            <div class="form-group row width-100">
                                <div class="form-check width-100">
                                    <input type="radio" id="unlimited_booking" name="booking_type" value="unlimited" {{ old('total_orders', $plan->total_orders ?? -1) == -1 ? 'checked' : '' }}>
                                    <label class="control-label" for="unlimited_booking">{{ trans('lang.unlimited') }}</label>
                                </div>
                                <div class="d-flex">
                                    <div class="form-check width-50">
                                        <input type="radio" id="limited_booking" name="booking_type" value="limited" {{ old('total_orders', $plan->total_orders ?? -1) != -1 ? 'checked' : '' }}>
                                        <label class="control-label" for="limited_booking">{{ trans('lang.limited') }}</label>
                                    </div>
                                    <div class="form-check width-50 {{ old('total_orders', $plan->total_orders ?? -1) != -1 ? '' : 'd-none' }} booking-limit-div">
                                        <input type="number" name="total_orders" id="booking_limit" class="form-control" value="{{ old('total_orders', $plan->total_orders ?? '') }}" placeholder="{{ trans('lang.ex_1000') }}">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="form-group col-12 text-center btm-btn">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> {{ trans('lang.save') }}
                    </button>
                    <a href="{{ url('subscription-plans') }}" class="btn btn-default">
                        <i class="fa fa-undo"></i>{{ trans('lang.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Plan type change handler
        $('input[name="type"]').on('change', function() {
            if($(this).val() == 'paid') {
                $('.plan_price_div').removeClass('d-none');
                $('#plan_price').attr('required', true);
            } else {
                $('.plan_price_div').addClass('d-none');
                $('#plan_price').removeAttr('required').val('0');
            }
        });

        // Duration type change handler
        $('input[name="duration_type"]').on('change', function() {
            if($(this).val() == 'limited') {
                $('.expiry-limit-div').removeClass('d-none');
                $('#plan_validity').attr('required', true);
            } else {
                $('.expiry-limit-div').addClass('d-none');
                $('#plan_validity').removeAttr('required').val('-1');
            }
        });

        // Booking type change handler
        $('input[name="booking_type"]').on('change', function() {
            if($(this).val() == 'limited') {
                $('.booking-limit-div').removeClass('d-none');
                $('#booking_limit').attr('required', true);
            } else {
                $('.booking-limit-div').addClass('d-none');
                $('#booking_limit').removeAttr('required').val('-1');
            }
        });

        // Add plan point
        $('#add-plan-point').on('click', function() {
            var html = '<div class="form-group d-flex ml-1 option-row mt-1">' +
                '<input type="text" class="form-control" name="plan_points[]" placeholder="Enter plan point" required>' +
                '<button type="button" class="btn btn-danger ml-2 remove-point">' +
                '<i class="mdi mdi-delete"></i>' +
                '</button>' +
                '</div>';
            $('#options-container').append(html);
        });

        // Remove plan point
        $(document).on('click', '.remove-point', function() {
            $(this).closest('.option-row').remove();
        });

        // Set hidden field values before submit based on radio selections
        $('form').on('submit', function() {
            if($('input[name="duration_type"]:checked').val() == 'unlimited') {
                $('#plan_validity').val('-1');
            }
            if($('input[name="booking_type"]:checked').val() == 'unlimited') {
                $('#booking_limit').val('-1');
            }
            
            // Remove empty plan points
            $('input[name="plan_points[]"]').each(function() {
                if($(this).val().trim() == '') {
                    $(this).closest('.option-row').remove();
                }
            });
        });
    });
</script>
@endsection
