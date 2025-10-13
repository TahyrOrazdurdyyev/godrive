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
                <li class="breadcrumb-item active">{{trans('lang.on_board_table')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="admin-top-section">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex top-title-section pb-4 justify-content-between">
                        <div class="d-flex top-title-left align-self-center">
                            <span class="icon mr-3"><img src="{{ asset('images/onboarding.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.on_board_plural')}}</h3>
                            <span class="counter ml-3">{{ $screens->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card border">
                    <div class="card-header d-flex justify-content-between align-items-center border-0">
                        <div class="card-header-title">
                            <h3 class="text-dark-2 mb-2 h4">{{trans('lang.on_board_plural')}}</h3>
                            <p class="mb-0 text-dark-2">{{trans('lang.on_board_plural_text')}}</p>
                        </div>
                        <div class="card-header-right d-flex align-items-center">
                            <div class="card-header-btn mr-3">
                                <a class="btn-primary btn rounded-full" href="{{ route('on-board.save', '0') }}">
                                    <i class="mdi mdi-plus mr-2"></i>{{trans('lang.on_board_create')}}
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="table-responsive m-t-10">
                            <table id="onboardTable" class="display table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{trans('lang.image')}}</th>
                                        <th>{{trans('lang.title')}}</th>
                                        <th>{{trans('lang.description')}}</th>
                                        <th>{{trans('lang.app_screen')}}</th>
                                        <th>Display Order</th>
                                        <th>{{trans('lang.active')}}</th>
                                        <th>{{trans('lang.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($screens as $screen)
                                    <tr>
                                        <td>
                                            @if($screen->image)
                                                <img src="{{ $screen->image }}" width="60" height="60" alt="image">
                                            @else
                                                <img src="{{ asset('images/default_image.png') }}" width="60" height="60" alt="image">
                                            @endif
                                        </td>
                                        <td>{{ $screen->title }}</td>
                                        <td>{{ Str::limit($screen->description, 50) }}</td>
                                        <td>
                                            @if($screen->app_type == 'customer')
                                                <span class="badge badge-info">Customer</span>
                                            @elseif($screen->app_type == 'driver')
                                                <span class="badge badge-success">Driver</span>
                                            @else
                                                <span class="badge badge-primary">Both</span>
                                            @endif
                                        </td>
                                        <td>{{ $screen->display_order }}</td>
                                        <td>
                                            <label class="switch">
                                                <input type="checkbox" class="status-toggle" data-id="{{ $screen->id }}" {{ $screen->is_active ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <span class="action-btn">
                                                <a href="{{ route('on-board.save', $screen->id) }}">
                                                    <i class="mdi mdi-lead-pencil"></i>
                                                </a>
                                                <a href="javascript:void(0)" class="delete-screen" data-id="{{ $screen->id }}">
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
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#onboardTable').DataTable({
            order: [[4, 'asc']],
            columnDefs: [
                {orderable: false, targets: [0, 5, 6]}
            ],
            language: {
                zeroRecords: "{{trans('lang.no_record_found')}}",
                emptyTable: "{{trans('lang.no_record_found')}}"
            }
        });

        // Toggle status
        $(document).on('change', '.status-toggle', function() {
            var screenId = $(this).data('id');
            var isActive = $(this).is(':checked');
            
            $.ajax({
                url: '/on-board/' + screenId + '/toggle-status',
                method: 'POST',
                data: {
                    is_active: isActive,
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

        // Delete screen
        $(document).on('click', '.delete-screen', function() {
            if (confirm("{{trans('lang.delete_alert')}}")) {
                var screenId = $(this).data('id');
                
                $.ajax({
                    url: '/on-board/' + screenId,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error deleting screen');
                        }
                    },
                    error: function() {
                        alert('Error deleting screen');
                    }
                });
            }
        });
    });
</script>
@endsection
