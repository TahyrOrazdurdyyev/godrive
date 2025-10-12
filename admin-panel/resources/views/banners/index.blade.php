@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.all_banner_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.banner_table')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="admin-top-section">
            <div class="col-12">
                <div class="d-flex top-title-section pb-4 justify-content-between">
                    <div class="d-flex top-title-left align-self-center">
                        <span class="icon mr-3"><img src="{{ asset('images/banner.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.all_banner_plural')}}</h3>
                        <span class="counter ml-3 total_count">{{ count($banners) }}</span>
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.banner_table')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.banner_table_text')}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a class="btn-primary btn rounded-full" href="{!! url('/banners/save/0') !!}"><i
                                            class="mdi mdi-plus mr-2"></i>{{trans('lang.banner_create')}}</a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="taxTable"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <?php if (in_array('banners.delete', json_decode(@session('user_permissions')))) { ?>
                                                <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                        class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                            class="do_not_delete" href="javascript:void(0)"><i
                                                                class="mdi mdi-delete"></i>
                                                            {{trans('lang.all')}}</a></label>
                                                </th>
                                            <?php } ?>
                                            <th>{{trans('lang.image')}}</th>
                                            <th>{{trans('lang.position')}}</th>
                                            <th>{{trans('lang.status')}}</th>
                                            <th>{{trans('lang.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="append_list1">
                                        @foreach($banners as $banner)
                                        <tr>
                                            <?php if (in_array('banners.delete', json_decode(@session('user_permissions')))) { ?>
                                            <td class="delete-all">
                                                <input type="checkbox" id="is_open_{{ $banner->id }}" class="is_open" dataId="{{ $banner->id }}">
                                                <label class="col-3 control-label" for="is_open_{{ $banner->id }}"></label>
                                            </td>
                                            <?php } ?>
                                            <td>
                                                 <img class="rounded" style="width:50px" 
     src="{{ $banner->image ?: asset('/images/default_user.png') }}" 
     alt="Banner Image" onerror="this.src='{{ asset('/images/default_user.png') }}'">                                           </td>
                                            <td>{{ $banner->position }}</td>
                                            <td>
                                                <label class="switch">
                                                    <input type="checkbox" {{ $banner->enable ? 'checked' : '' }} 
                                                           id="{{ $banner->id }}" name="isSwitch">
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td class="action-btn">
                                                <a href="{{ route('banners.save', $banner->id) }}" name="banner-edit">
                                                    <i class="mdi mdi-lead-pencil"></i>
                                                </a>
                                                <?php if (in_array('banners.delete', json_decode(@session('user_permissions')))) { ?>
                                                <a id="{{ $banner->id }}" class="delete-btn" name="banner-delete" href="javascript:void(0)">
                                                    <i class="mdi mdi-delete"></i>
                                                </a>
                                                <?php } ?>
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
    var deleteMsg = "{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg = "{{trans('lang.selected_delete_alert')}}";
    var user_permissions = '<?php echo @session('user_permissions') ?>';
    user_permissions = JSON.parse(user_permissions);
    var checkDeletePermission = false;

    if ($.inArray('banners.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }

    $(document).ready(function() {
        if (checkDeletePermission) {
            $('#taxTable').DataTable({
                order: [[2, 'asc']],
                columnDefs: [
                    {orderable: false, targets: [0, 1, 3, 4]},
                ],
                "language": {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}"
                },
                responsive: true
            });
        } else {
            $('#taxTable').DataTable({
                order: [[1, 'asc']],
                columnDefs: [
                    {orderable: false, targets: [0, 2, 3]},
                ],
                "language": {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}"
                },
                responsive: true
            });
        }
    });

    // Delete banner
    $(document).on("click", "a[name='banner-delete']", function(e) {
        if (confirm(deleteMsg)) {
            var id = this.id;
            jQuery("#overlay").show();
            
            $.ajax({
                url: '/banners/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        alert('Error: ' + response.message);
                        jQuery("#overlay").hide();
                    }
                },
                error: function(xhr) {
                    alert('Error deleting banner');
                    jQuery("#overlay").hide();
                }
            });
        }
    });

    // Select all checkboxes
    $("#is_active").click(function() {
        $("#taxTable .is_open").prop('checked', $(this).prop('checked'));
    });
</script>
@endsection
