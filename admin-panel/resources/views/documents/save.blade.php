@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.document_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('documents') !!}">{{trans('lang.document_plural')}}</a></li>
                <li class="breadcrumb-item active">{{ $id == '0' ? trans('lang.document_create') : trans('lang.document_edit')}}</li>
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

                <form action="{{ route('documents.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $id }}">

                    <fieldset>
                        <legend>{{trans('lang.document_details')}}</legend>

                        <div class="form-group row width-100">
                            <label class="col-3 control-label">{{trans('lang.document_title')}}<span class="required-field">*</span></label>
                            <div class="col-7">
                                <input type="text" class="form-control" name="title" id="document_title" 
                                       value="{{ old('title', $document->title ?? '') }}" 
                                       placeholder="{{trans('lang.document_title_help')}}" required>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="is_enabled" id="document_active" 
                                       {{ old('is_enabled', $document->is_enabled ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="document_active">{{trans('lang.enable')}}</label>
                            </div>
                        </div>
                    </fieldset>

                    <div class="form-group col-12 text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> {{trans('lang.save')}}
                        </button>
                        <a href="{{ route('documents') }}" class="btn btn-default">
                            <i class="fa fa-undo"></i> {{trans('lang.cancel')}}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
