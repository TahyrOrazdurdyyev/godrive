@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.all_driver_rule_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.driver_rule_table')}}</li>
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
                            <span class="icon mr-3"><img src="{{ asset('images/driver_rules.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.all_driver_rule_plural')}}</h3>
                            <span class="counter ml-3 total_count"></span>
                        </div>
                        <div class="d-flex top-title-right align-self-center">
                            <div class="select-box pl-3">

                            </div>
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.driver_rule_table')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.driver_rule_table_text')}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a class="btn-primary btn rounded-full"
                                        href="{!! url('/driver-rules/save/0') !!}"><i
                                            class="mdi mdi-plus mr-2"></i>{{trans('lang.driver_rule_create')}}</a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="data-table_processing" class="dataTables_processing panel panel-default"
                                style="display: none;">{{trans('lang.processing')}}
                            </div>

                            <div class="table-responsive m-t-10">
                                <table id="taxTable"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <?php if (in_array('rule.delete', json_decode(@session('user_permissions')))) { ?>

                                                <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                        class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                            class="do_not_delete" href="javascript:void(0)"><i
                                                                class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label>
                                                </th>
                                            <?php } ?>
                                            <th>{{trans('lang.name')}}</th>

                                            <th>{{trans('lang.status')}}</th>
                                            <th>{{trans('lang.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="append_list1">
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
    var database=firebase.firestore();

    var ref=database.collection('driver_rules').where('isDeleted','==',false);
    var defaultImg="{{ asset('/images/default_user.png') }}";
    var append_list='';
    var setLanguageCode=getCookie('setLanguage');
    var defaultLanguageCode=getCookie('defaultLanguage');

    var deleteMsg="{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg="{{trans('lang.selected_delete_alert')}}";

    var user_permissions='<?php echo @session('user_permissions') ?>';

    user_permissions=JSON.parse(user_permissions);

    var checkDeletePermission=false;

    if($.inArray('rule.delete',user_permissions)>=0) {
        checkDeletePermission=true;
    }

    $(document).ready(function() {

        append_list=document.getElementById('append_list1');
        append_list.innerHTML='';
        jQuery("#overlay").show();

        ref.get().then(async function(snapshots) {
            var html='';
            if(snapshots.docs.length>0) {
                $('.total_count').html(snapshots.docs.length);
                html=await buildHTML(snapshots);
            }else{
                $('.total_count').html(0);
            }
            if(html!='') {
                append_list.innerHTML=html;
            }

            if(checkDeletePermission) {
                $('#taxTable').DataTable({
                    order: [[1,'asc']],
                    columnDefs: [
                        {orderable: false,targets: [0,2,3]},
                    ],
                    "language": {
                        "zeroRecords": "{{trans("lang.no_record_found")}}",
                        "emptyTable": "{{trans("lang.no_record_found")}}"
                    },
                });
            } else {
                $('#taxTable').DataTable({
                    order: [[0,'asc']],
                    columnDefs: [
                        {orderable: false,targets: [1,2]},
                    ],
                    "language": {
                        "zeroRecords": "{{trans("lang.no_record_found")}}",
                        "emptyTable": "{{trans("lang.no_record_found")}}"
                    },
                });
            }
            jQuery("#overlay").hide();
        });
    });

    $('#search').keypress(function(e) {
        if(e.which==13) {
            $('.search_button').click();
        }
    });

    async function buildHTML(snapshots) {
        var html='';
        await Promise.all(snapshots.docs.map(async (listval) => {
            var val=listval.data();
            var getData=await getListData(val);
            html+=getData;
        }));
        return html;
    }

    async function getListData(val) {
        var html='';
        html=html+'<tr>';
        newdate='';
        var id=val.id;
        var route1='{{route("driver-rules.save",":id")}}';
        route1=route1.replace(':id',id);
        if(checkDeletePermission) {

            html=html+'<td class="delete-all"><input type="checkbox" id="is_open_'+id+'" class="is_open" dataId="'+id+'"><label class="col-3 control-label"\n'+
                'for="is_open_'+id+'" ></label></td>';
        }
        var name='';
        if(Array.isArray(val.name)) {
            var foundItem=val.name.find(item => item.type===setLanguageCode);
            if(foundItem&&foundItem.name!='') {
                name=foundItem.name;
            } else {
                var foundItem=val.name.find(item => item.type===defaultLanguageCode);
                if(foundItem&&foundItem.name!='') {
                    name=foundItem.name;
                } else {
                    var foundItem=val.name.find(item => item.type==='en');
                    name=foundItem.name;

                }
            }

        }
        if(val.image=='') {
            ImageHtml='<img width="100%" style="width:70px;height:70px;" src="'+defaultImg+'" alt="image">';
        } else {
            ImageHtml='<img width="100%" style="width:70px;height:70px;" src="'+val.image+'" alt="image">';
        }
        html=html+'<td>'+ImageHtml+'<a href="'+route1+'">'+name+'</a></td>';

        if(val.enable) {
            html=html+'<td><label class="switch"><input type="checkbox" checked id="'+val.id+'" name="isSwitch"><span class="slider round"></span></label></td>';
        } else {
            html=html+'<td><label class="switch"><input type="checkbox" id="'+val.id+'" name="isSwitch"><span class="slider round"></span></label></td>';
        }
        html=html+'<td class="action-btn"><a href="'+route1+'" name="edit-rules"><i class="mdi mdi-lead-pencil"></i></a>';
        if(checkDeletePermission) {


            html=html+'<a id="'+val.id+'" class="delete-btn" name="driver_rule-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
        }
        html=html+'</td>';
        html=html+'</tr>';
        return html;
    }

    $("#is_active").click(function() {
        $("#taxTable .is_open").prop('checked',$(this).prop('checked'));
    });

    $("#deleteAll").click(function() {
        if($('#taxTable .is_open:checked').length) {
            if(confirm(deleteSelectedRecordMsg)) {
                jQuery("#overlay").show();
                $('#taxTable .is_open:checked').each(function() {
                    var dataId=$(this).attr('dataId');
                    database.collection('driver_rules').doc(dataId).update({
                        'isDeleted': true,
                        'enable': false
                    }).then(function() {
                        window.location.reload();
                    });
                });
            } else {
                return false;
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });

    $(document).on("click","input[name='isSwitch']",function(e) {
        var ischeck=$(this).is(':checked');
        var id=this.id;
        database.collection('driver_rules').doc(id).update({
            'enable': ischeck? true:false
        }).then(function(result) {
        });
    });

    $(document).on("click","a[name='driver_rule-delete']",function(e) {
        if(confirm(deleteMsg)) {
            var id=this.id;
            jQuery("#overlay").show();
            database.collection('driver_rules').doc(id).update({
                'isDeleted': true,
                'enable': false
            }).then(function(result) {
                window.location.href='{{ url()->current() }}';
            });
        } else {
            return false;
        }
    });

</script>

@endsection