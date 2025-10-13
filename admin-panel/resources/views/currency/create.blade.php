@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.currency_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('currency') !!}">{{trans('lang.currency_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.currency_create')}}</li>
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

                <form action="{{ route('currency.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="0">

                    <fieldset>
                        <legend>{{trans('lang.currency_details')}}</legend>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.currency_name')}}<span class="required-field">*</span></label>
                            <div class="col-7">
                                <input type="text" class="form-control" name="name" required placeholder="Turkmenistan Manat">
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.currency_symbol')}}<span class="required-field">*</span></label>
                            <div class="col-7">
                                <input type="text" class="form-control" name="symbol" required placeholder="m">
                                <div class="form-text text-muted">Currency symbol (e.g., $, €, ₽, m)</div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.currency_code')}}<span class="required-field">*</span></label>
                            <div class="col-7">
                                <input type="text" class="form-control" name="code" required placeholder="TMT" maxlength="10">
                                <div class="form-text text-muted">ISO currency code (e.g., USD, EUR, TMT)</div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="symbol_at_right" id="symbol_at_right">
                                <label class="form-check-label" for="symbol_at_right">{{trans('lang.symbol_at_right')}}</label>
                                <div class="form-text text-muted">Display symbol after amount (e.g., 100m instead of m100)</div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" checked>
                                <label class="form-check-label" for="is_active">{{trans('lang.active')}}</label>
                            </div>
                        </div>
                    </fieldset>

                    <div class="form-group col-12 text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> {{trans('lang.save')}}
                        </button>
                        <a href="{{ route('currency') }}" class="btn btn-default">
                            <i class="fa fa-undo"></i> {{trans('lang.cancel')}}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
