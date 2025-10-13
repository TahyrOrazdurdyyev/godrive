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
                <li class="breadcrumb-item active">{{trans('lang.currency_table')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="admin-top-section">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex top-title-section pb-4 justify-content-between">
                        <div class="d-flex top-title-left align-self-center">
                            <span class="icon mr-3"><i class="mdi mdi-currency-usd" style="font-size: 40px;"></i></span>
                            <h3 class="mb-0">{{trans('lang.currency_plural')}}</h3>
                            <span class="counter ml-3">{{ $currencies->count() }}</span>
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.currency_table')}}</h3>
                                <p class="mb-0 text-dark-2">View and manage all the currency</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a class="btn-primary btn rounded-full" href="{{ route('currency.create') }}">
                                        <i class="mdi mdi-plus mr-2"></i>{{trans('lang.currency_create')}}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="currencyTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{trans('lang.currency_name')}}</th>
                                            <th>{{trans('lang.currency_symbol')}}</th>
                                            <th>{{trans('lang.currency_code')}}</th>
                                            <th>{{trans('lang.symbol_at_right')}}</th>
                                            <th>{{trans('lang.active')}}</th>
                                            <th>{{trans('lang.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($currencies as $currency)
                                        <tr>
                                            <td>{{ $currency->name }}</td>
                                            <td>{{ $currency->symbol }}</td>
                                            <td>{{ $currency->code }}</td>
                                            <td>
                                                @if($currency->symbol_at_right)
                                                    <span class="badge badge-success">Yes</span>
                                                @else
                                                    <span class="badge badge-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <label class="switch">
                                                    <input type="checkbox" class="status-toggle" data-id="{{ $currency->id }}" {{ $currency->is_active ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <span class="action-btn">
                                                    <a href="{{ route('currency.edit', $currency->id) }}">
                                                        <i class="mdi mdi-lead-pencil"></i>
                                                    </a>
                                                    <a href="javascript:void(0)" class="delete-currency" data-id="{{ $currency->id }}">
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
        $('#currencyTable').DataTable({
            order: [[0, 'asc']],
            columnDefs: [
                {orderable: false, targets: [4, 5]}
            ],
            language: {
                zeroRecords: "{{trans('lang.no_record_found')}}",
                emptyTable: "{{trans('lang.no_record_found')}}"
            }
        });

        // Toggle status
        $(document).on('change', '.status-toggle', function() {
            var currencyId = $(this).data('id');
            var isActive = $(this).is(':checked');
            
            $.ajax({
                url: '/currency/' + currencyId + '/toggle-status',
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

        // Delete currency
        $(document).on('click', '.delete-currency', function() {
            if (confirm("{{trans('lang.delete_alert')}}")) {
                var currencyId = $(this).data('id');
                
                $.ajax({
                    url: '/currency/' + currencyId,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error deleting currency');
                        }
                    },
                    error: function() {
                        alert('Error deleting currency');
                    }
                });
            }
        });
    });
</script>
@endsection
