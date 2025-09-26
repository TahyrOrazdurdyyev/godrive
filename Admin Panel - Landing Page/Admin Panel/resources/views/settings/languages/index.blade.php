@extends('layouts.app')


@section('content')

<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.languages')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.languages')}}</li>
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
                            <span class="icon mr-3"><img src="{{ asset('images/language.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.languages')}}</h3>
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.languages')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.language_table_text')}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a class="btn-primary btn rounded-full" href="{!! route('settings.languages.save') !!}"><i
                                            class="mdi mdi-plus mr-2"></i>{{trans('lang.language_create')}}</a>
                                </div>
                            </div>
                        </div>
                       
                        <div class="card-body">

                            <div id="data-table_processing" class="dataTables_processing panel panel-default"
                                style="display: none;">{{trans('lang.processing')}}
                            </div>


                            <div class="table-responsive m-t-10">
                                <table id="languageTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <?php if (in_array('language.delete', json_decode(@session('user_permissions')))) { ?>

                                                <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                        class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                            class="do_not_delete" href="javascript:void(0)"><i
                                                                class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label>
                                                </th>
                                            <?php } ?>
                                            <th>{{trans('lang.image')}}</th>
                                            <th>{{trans('lang.name')}}</th>
                                            <th>{{trans('lang.code')}}</th>
                                            <th>{{trans('lang.active')}}</th>
                                            <th>{{trans('lang.is_default')}}</th>
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

    var ref=database.collection('languages').where('isDeleted','==',false);
    var placeholderImage="{{ asset('/images/default_user.png') }}";
    var append_list='';
    var deleteMsg="{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg="{{trans('lang.selected_delete_alert')}}";
    var atleastOneDefaultLangAlert="{{trans('lang.atleast_one_default_lang_alert')}}";
    var user_permissions='<?php echo @session('user_permissions') ?>';

    user_permissions=JSON.parse(user_permissions);

    var checkDeletePermission=false;

    if($.inArray('language.delete',user_permissions)>=0) {
        checkDeletePermission=true;
    }


    $(document).ready(function() {

        jQuery("#overlay").show();

        append_list=document.getElementById('append_list1');
        append_list.innerHTML='';

        ref.get().then(async function(snapshots) {

            var html='';
            html=await buildHTML(snapshots);
            if(html!='') {
                append_list.innerHTML=html;
            }

            if(checkDeletePermission) {

                $('#languageTable').DataTable({
                    order: [[2,'asc']],
                    columnDefs: [
                        {orderable: false,targets: [0,1,4,5,6]},
                    ],
                    "language": {
                        "zeroRecords": "{{trans('lang.no_record_found')}}",
                        "emptyTable": "{{trans('lang.no_record_found')}}"
                    }
                });
            } else {

                $('#languageTable').DataTable({
                    order: [[1,'asc']],
                    columnDefs: [
                        {orderable: false,targets: [0,3,4,5]},
                    ],
                    "language": {
                        "zeroRecords": "{{trans('lang.no_record_found')}}",
                        "emptyTable": "{{trans('lang.no_record_found')}}"
                    }
                });
            }
            jQuery("#overlay").hide();
        });
    });

    async function buildHTML(snapshots) {
        var html='';
        $(".total_count").text(snapshots.docs.length);
        await Promise.all(snapshots.docs.map(async (listval) => {
            var val=listval.data();
            var getData=await getListData(val);
            html+=getData;
        }));
        return html;
    }

    function getListData(val) {
        var html='';

        html=html+'<tr>';
        newdate='';
        var id=val.id;
        var route1='{{route("settings.languages.save",":id")}}';
        route1=route1.replace(':id',id);

        if(checkDeletePermission && val.isDefault==false && val.code!='en' ) {

            html=html+'<td class="delete-all"><input type="checkbox" id="is_open_'+id+'" class="is_open" dataId="'+id+'" dataCode="'+val.code+'"><label class="col-3 control-label"\n'+
                'for="is_open_'+id+'" ></label></td>';
        }else{
            html+='<td></td>';
        }
        if(val.image==''||val.image==null) {
            var image=placeholderImage;
        } else {
            var image=val.image;
        }
        html=html+'<td><a href="'+route1+'"><img src="'+image+'" class="rounded" style="width:50px" ></a></td>';

        html=html+'<td><a href="'+route1+'">'+val.name+'</a></td>';

        html=html+'<td>'+val.code+'</td>';

        if(val.enable) {
            html=html+'<td><label class="switch"><input type="checkbox" checked id="'+val.id+'" name="isSwitch"><span class="slider round"></span></label></td>';
        } else {
            html=html+'<td><label class="switch"><input type="checkbox" id="'+val.id+'" name="isSwitch"><span class="slider round"></span></label></td>';
        }
         if(val.isDefault) {
            html=html+'<td><label class="switch"><input type="checkbox" checked id="is_default_'+val.id+'" data-id="'+val.id+'" name="isDefault"><span class="slider round"></span></label></td>';
        } else {
            html=html+'<td><label class="switch"><input type="checkbox" id="is_default_'+val.id+'" data-id="'+val.id+'" name="isDefault"><span class="slider round"></span></label></td>';
        }

        html=html+'<td class="action-btn"><a href="'+route1+'"><i class="mdi mdi-lead-pencil"></i></a>';
        if(checkDeletePermission && val.isDefault==false && val.code!='en' ) {

            html=html+'<a id="'+val.id+'" data-code="'+val.code+'" class="delete-btn" name="lang-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
        }
        html=html+'</td>';

        html=html+'</tr>';

        return html;
    }

    $("#is_active").click(function() {
        $("#languageTable .is_open").prop('checked',$(this).prop('checked'));
    });

    $("#deleteAll").click(function() {
        if($('#languageTable .is_open:checked').length) {
            if(confirm(deleteSelectedRecordMsg)) {
                jQuery("#overlay").show();
                $('#languageTable .is_open:checked').each(function() {
                    var dataId=$(this).attr('dataId');
                    var code=$(this).attr('dataCode');
                    database.collection('languages').doc(dataId).update({
                        'isDeleted': true,
                        'enable': false
                    }).then(async function() {
                        window.location.href='{{ url()->current() }}';                       
                    });
                });
            } else {
                return false;
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });

    /* toggal publish action code start*/
    $(document).on("click","input[name='isSwitch']",function(e) {

        var ischeck=$(this).is(':checked');
        var id=this.id;
        if(ischeck) {
            database.collection('languages').doc(id).update({
                'enable': true
            }).then(function(result) {
            });
        } else {
            database.collection('languages').doc(id).update({
                'enable': false
            }).then(function(result) {
            });
        }

    });

    /*toggal publish action code end*/

    $(document).on("click","a[name='lang-delete']",function(e) {

        var id=this.id;
        var code=$(this).attr('data-code');
        if(confirm(deleteMsg)) {
            jQuery("#overlay").show();
            database.collection('languages').doc(id).update({
                'isDeleted': true,
                'enable': false
            }).then(async function(result) {
                window.location.href='{{ url()->current() }}';             
            });
        } else {
            return false;
        }

    });
    
    $(document).on("click", "input[name='isDefault']", async function (e) {

        var ischeck = $(this).is(':checked');
        var id=$(this).attr('data-id');
        if (ischeck) {         
            await database.collection('languages').where('isDefault', "==", true).get().then(async function (snapshots) {
                var activeLanguage = snapshots.docs[0].data();
                var activeLanguageId = activeLanguage.id;
                await database.collection('languages').doc(activeLanguageId).update({ 'isDefault': false });

                $("#append_list1 tr").each(function () {
                    $(this).find("#is_default_" + activeLanguageId).prop('checked', false);
                });
            });
            await database.collection('languages').doc(id).update({ 'isDefault': true }).then(async function(result){
                window.location.reload();
            })
        } else {
            await database.collection('languages').where('isDefault', "==", true).get().then(function (snapshots) {
               var activeLanguage = snapshots.docs[0].data();
                var activeLanguageId = activeLanguage.id;
                if (snapshots.docs.length == 1 && activeLanguageId == id) {
                    alert(atleastOneDefaultLangAlert);
                    $("#is_default_" + id).prop('checked', true);
                    return false;
                } else {
                    database.collection('languages').doc(id).update({ 'isDefault': false }).then(function (result) {
                        window.location.reload();
                    });
                }
            });
        }

    });

</script>

@endsection