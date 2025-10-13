@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.zone_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.zone_table')}}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>
    <div class="container-fluid">
       <div class="admin-top-section"> 
        <div class="row">
            <div class="col-12">
                <div class="d-flex top-title-section pb-4 justify-content-between">
                    <div class="d-flex top-title-left align-self-center">
                        <span class="icon mr-3"><img src="{{ asset('images/zone.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.zone_plural')}}</h3>
                        <span class="counter ml-3 total_count"></span>
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
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.zone_table')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.zone_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                        <div class="card-header-btn mr-3"> 
                            <a class="btn-primary btn rounded-full" href="{!! route('zone.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.zone_create')}}</a>
                        </div>
                    </div>
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="example24" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <?php if (in_array('zone.delete', json_decode(@session('user_permissions')))) { ?>
                                        <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                    class="col-3 control-label" for="is_active">
                                                <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i
                                                            class="fa fa-trash"></i> {{trans('lang.all')}}</a></label></th>
                                    <?php } ?>
                                    <th>{{trans('lang.zone_name')}}</th>
                                    <th>{{trans('lang.status')}}</th>
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>
                                </thead>
                                <tbody id="append_list1">
                                @foreach($zones as $zone)
                                    <tr>
                                        <td>{{ $zone->name }}</td>
                                        <td>
                                            @if($zone->enable)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('zone.edit', $zone->id) }}" class="btn btn-warning btn-sm">Edit</a>
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

    var database = firebase.firestore();
    var offest = 1;
    var pagesize = 10;
    var end = null;
    var endarray = [];
    var start = null;
    var user_number = [];
    var ref = database.collection('zone');
    var append_list = '';
    var placeholderImage = '';
    var setLanguageCode=getCookie('setLanguage');
    var defaultLanguageCode=getCookie('defaultLanguage');
    var user_permissions = '<?php echo @session("user_permissions")?>';
    user_permissions = JSON.parse(user_permissions);
    var checkDeletePermission = false;
    if ($.inArray('zone.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }
    $(document).ready(async function () {
   
        var inx = parseInt(offest) * parseInt(pagesize);
        jQuery("#overlay").show();
        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';
         ref.get().then(async function (snapshots) {
            html = '';
            $('.total_count').text(snapshots.docs.length);
            if (snapshots.docs.length > 0) {
                html = await buildHTML(snapshots);
            }
            if (html != '') {
                append_list.innerHTML = html;
                start = snapshots.docs[snapshots.docs.length - 1];
                endarray.push(snapshots.docs[0]);
                if (snapshots.docs.length < pagesize) {
                    jQuery("#data-table_paginate").hide();
                }
            }

            if (checkDeletePermission) {
                $('#example24').DataTable({
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
            } else {
                $('#example24').DataTable({
                    order: [[0, 'asc']],
                    columnDefs: [
                        {orderable: false, targets: [1, 2]},
                    ],
                    "language": {
                        "zeroRecords": "{{trans('lang.no_record_found')}}",
                        "emptyTable": "{{trans('lang.no_record_found')}}"
                    },
                    responsive: true
                });
            }
            jQuery("#overlay").hide();
        });

         
    });   
 

    async function buildHTML(snapshots) {
        var html = '';
        await Promise.all(snapshots.docs.map(async (listval) => {
            var val = listval.data();
            var getData = await getListData(val);
            html += getData;
        }));
        return html;
    }

    async function getListData(val) {
        var html = '';
        html = html + '<tr>';
        var id = val.id;
        var route1 = '{{route("zone.edit", ":id")}}';
        route1 = route1.replace(':id', id);

        if (checkDeletePermission) {

            html = html + '<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +
                'for="is_open_' + id + '" ></label></td>';
        }
        var languageName='';
        if(Array.isArray(val.name)){
            var foundItem=val.name.find(item => item.type===setLanguageCode);
            if(foundItem && foundItem.name!=''){
                 languageName=foundItem.name;
            }else{
                var foundItem =val.name.find(item => item.type===defaultLanguageCode);
                if(foundItem&&foundItem.name!='') {
                    languageName=foundItem.name;
                }else{
                    var foundItem=val.name.find(item => item.type==='en');
                    languageName=foundItem.name;

                }
            }
            
        }
        html = html + '<td><a href="' + route1 + '">' + languageName + '</a></td>';
        if (val.publish) {
            html = html + '<td><label class="switch"><input type="checkbox" checked id="' + val.id + '" name="isSwitch"><span class="slider round"></span></label></td>';
        } else {
            html = html + '<td><label class="switch"><input type="checkbox" id="' + val.id + '" name="isSwitch"><span class="slider round"></span></label></td>';
        } 
html = html + '<td class="action-btn"><a href="' + route1 + '"><i class="mdi mdi-lead-pencil"></i></a><a href="javascript:void(0)" class="delete-zone" data-id="' + val.id + '"><i class="mdi mdi-delete"></i></a></td>';
        if (checkDeletePermission) {

            html = html + '<a id="' + val.id + '" name="zone-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
        }
        html = html + '</td>';
        html = html + '</tr>';
        return html;
    }

    $("#is_active").click(function () {
        $("#example24 .is_open").prop('checked', $(this).prop('checked'));

    });

    $("#deleteAll").click(function () {
        if ($('#example24 .is_open:checked').length) {
            if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#overlay").show();
                $('#example24 .is_open:checked').each(function () {
                    var dataId = $(this).attr('dataId');
                    database.collection('zone').doc(dataId).delete().then(function () {
                        window.location.reload();
                    });
                });
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });

    $(document).on("click", "input[name='isSwitch']", function (e) {
        var ischeck = $(this).is(':checked');
        var id = this.id;
        if (ischeck) {
            database.collection('zone').doc(id).update({
                'publish': true
            }).then(function (result) {
            });
        } else {
            database.collection('zone').doc(id).update({
                'publish': false
            }).then(function (result) {
            });
        }
    });

    $(document).on("click", "a[name='zone-delete']", function (e) {
        if (confirm("{{trans('lang.delete_alert')}}")) {
            var id = this.id;
            jQuery("#overlay").show();
            database.collection('zone').doc(id).delete().then(function (result) {
                window.location.reload();
            });
        } else {
            return false;
        }
    });
// Delete zone
$(document).on('click', '.delete-zone', function() {
    var zoneId = $(this).data('id');
    
    if(confirm('Are you sure you want to delete this zone?')) {
        jQuery("#overlay").show();
        
        $.ajax({
            url: '/zone/delete/' + zoneId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                jQuery("#overlay").hide();
                if(response.success) {
                    alert('Zone deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                jQuery("#overlay").hide();
                alert('Error deleting zone');
            }
        });
    }
});
</script>

@endsection
