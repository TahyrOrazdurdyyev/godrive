@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.all_document_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.document_table')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="admin-top-section">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex top-title-section pb-4 justify-content-between">
                        <div class="d-flex top-title-left align-self-center">
                            <span class="icon mr-3"><img src="{{ asset('images/document.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.document_plural')}}</h3>
                            <span class="counter ml-3 doc_count">{{ $documents->count() }}</span>
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.document_table')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.documents_table_text')}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a class="btn-primary btn rounded-full" href="{!! url('/documents/save/0') !!}">
                                        <i class="mdi mdi-plus mr-2"></i>{{trans('lang.document_create')}}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="documentTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{trans('lang.document_title')}}</th>
                                            <th>{{trans('lang.enable')}}</th>
                                            <th>{{trans('lang.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($documents as $document)
                                        <tr>
                                            <td>
                                                <a href="{{ route('documents.save', $document->id) }}">{{ $document->title }}</a>
                                            </td>
                                            <td>
                                                <label class="switch">
                                                    <input type="checkbox" class="status-toggle" data-id="{{ $document->id }}" {{ $document->is_enabled ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <span class="action-btn">
                                                    <a href="{{ route('documents.save', $document->id) }}">
                                                        <i class="mdi mdi-lead-pencil"></i>
                                                    </a>
                                                    @if(in_array('document.delete', json_decode(@session('user_permissions'), true)))
                                                    <a href="javascript:void(0)" class="delete-document" data-id="{{ $document->id }}">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>
                                                    @endif
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
        $('#documentTable').DataTable({
            order: [[0, 'asc']],
            columnDefs: [
                {orderable: false, targets: [1, 2]}
            ],
            language: {
                zeroRecords: "{{trans('lang.no_record_found')}}",
                emptyTable: "{{trans('lang.no_record_found')}}"
            }
        });

        // Toggle status
        $(document).on('change', '.status-toggle', function() {
            var documentId = $(this).data('id');
            var isEnabled = $(this).is(':checked');
            
            $.ajax({
                url: '/documents/' + documentId + '/toggle-status',
                method: 'POST',
                data: {
                    is_enabled: isEnabled,
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

        // Delete document
        $(document).on('click', '.delete-document', function() {
            if (confirm("{{trans('lang.delete_alert')}}")) {
                var documentId = $(this).data('id');
                
                $.ajax({
                    url: '/documents/' + documentId,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error deleting document');
                        }
                    },
                    error: function() {
                        alert('Error deleting document');
                    }
                });
            }
        });
    });
</script>
@endsection
