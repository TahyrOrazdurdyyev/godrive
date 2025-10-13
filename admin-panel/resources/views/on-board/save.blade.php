@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.on_board_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('on-board') !!}">{{trans('lang.on_board_plural')}}</a></li>
                <li class="breadcrumb-item active">{{ $id == '0' ? trans('lang.on_board_create') : trans('lang.on_board_edit')}}</li>
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

                <form action="{{ route('on-board.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $id }}">

                    <fieldset>
                        <legend>{{trans('lang.on_board_details')}}</legend>

                        <div class="form-group row width-100">
                            <label class="col-3 control-label">{{trans('lang.title')}}<span class="required-field">*</span></label>
                            <div class="col-7">
                                <input type="text" class="form-control" name="title" value="{{ old('title', $screen->title ?? '') }}" required placeholder="Enter title">
                            </div>
                        </div>

                        <div class="form-group row width-100">
                            <label class="col-3 control-label">{{trans('lang.description')}}</label>
                            <div class="col-7">
                                <textarea class="form-control" name="description" rows="4" placeholder="Enter description">{{ old('description', $screen->description ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.app_screen')}}<span class="required-field">*</span></label>
                            <div class="col-7">
                                <select class="form-control" name="app_type" required>
                                    <option value="both" {{ old('app_type', $screen->app_type ?? 'both') == 'both' ? 'selected' : '' }}>Both Apps</option>
                                    <option value="customer" {{ old('app_type', $screen->app_type ?? '') == 'customer' ? 'selected' : '' }}>Customer App</option>
                                    <option value="driver" {{ old('app_type', $screen->app_type ?? '') == 'driver' ? 'selected' : '' }}>Driver App</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">Display Order<span class="required-field">*</span></label>
                            <div class="col-7">
                                <input type="number" class="form-control" name="display_order" value="{{ old('display_order', $screen->display_order ?? 0) }}" required min="0">
                                <div class="form-text text-muted">Order in which screens appear (0, 1, 2, ...)</div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.image')}}</label>
                            <div class="col-7">
                                @if(isset($screen) && $screen->image)
                                    <div class="mb-2">
                                        <img src="{{ $screen->image }}" width="150" height="150" alt="Current image">
                                    </div>
                                @endif
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <div class="form-text text-muted">Upload onboarding screen image (optional)</div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" {{ old('is_active', $screen->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{trans('lang.active')}}</label>
                            </div>
                        </div>
                    </fieldset>

                    <div class="form-group col-12 text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> {{trans('lang.save')}}
                        </button>
                        <a href="{{ route('on-board') }}" class="btn btn-default">
                            <i class="fa fa-undo"></i> {{trans('lang.cancel')}}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
