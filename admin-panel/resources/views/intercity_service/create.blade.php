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
                <li class="breadcrumb-item"><a href="{!! route('intercity-service') !!}">{{trans('lang.intercity_service_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.intercity_service_create')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card pb-4">
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('intercity-service.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="0">

                    <fieldset>
                        <legend>{{trans('lang.intercity_service_details')}}</legend>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.intercity_service_name')}}<span class="required-field">*</span></label>
                            <div class="col-7">
                                <input type="text" class="form-control" name="title" required placeholder="{{trans('lang.intercity_service_name_help')}}">
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">Price per Seat<span class="required-field">*</span></label>
                            <div class="col-7">
                                <input type="number" step="0.01" class="form-control" name="price_per_seat" min="0" required placeholder="Enter price per seat">
                                <div class="form-text text-muted">Price charged per passenger seat</div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">Price Full Vehicle<span class="required-field">*</span></label>
                            <div class="col-7">
                                <input type="number" step="0.01" class="form-control" name="price_full_vehicle" min="0" required placeholder="Enter full vehicle price">
                                <div class="form-text text-muted">Price to book entire vehicle</div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.image')}}</label>
                            <div class="col-7">
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <div class="form-text text-muted">Upload service image (optional)</div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="enable" id="enable" checked>
                                <label class="form-check-label" for="enable">{{trans('lang.enable')}}</label>
                            </div>
                        </div>
                    </fieldset>

                    <div class="form-group col-12 text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> {{trans('lang.save')}}
                        </button>
                        <a href="{{ route('intercity-service') }}" class="btn btn-default">
                            <i class="fa fa-undo"></i> {{trans('lang.cancel')}}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
