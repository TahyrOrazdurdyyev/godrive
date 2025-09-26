@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.cms_plural')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.cms_plural')}}</li>
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
                            <span class="icon mr-3"><img src="{{ asset('images/cms.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.cms_plural')}}</h3>
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.cms_table')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.cms_table_text')}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a class="btn-primary btn rounded-full" href="{!! route('cms.create') !!}"><i
                                            class="mdi mdi-plus mr-2"></i>{{trans('lang.cms_create')}}</a>
                                </div>
                            </div>
                        </div>
                       
                        <div class="card-body">

                            <div id="data-table_processing" class="dataTables_processing panel panel-default"
                                style="display: none;">Processing...
                            </div>


                            <div class="table-responsive m-t-10">


                                <table id="example24"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">

                                    <thead>

                                        <tr>
                                            <?php if (in_array('cms.delete', json_decode(@session('user_permissions')))) { ?>

                                                <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                        class="col-3 control-label" for="is_active">
                                                        <a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i
                                                                class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label>
                                                </th>

                                            <?php } ?>
                                            <th>{{trans('lang.cms_name')}}</th>

                                            <th>{{trans('lang.cms_slug')}}</th>

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

</div>
</div>


@endsection


@section('scripts')
<script type="text/javascript">
    var database=firebase.firestore();
    var offest=1;
    var pagesize=10;
    var end=null;
    var endarray=[];
    var start=null;
    var user_number=[];
    var ref=database.collection('cms_pages');
    var append_list='';
    var placeholderImage='';
    var deleteMsg="{{trans('lang.delete_alert')}}";

    var user_permissions='<?php echo @session('user_permissions') ?>';

    user_permissions=JSON.parse(user_permissions);

    var checkDeletePermission=false;

    if($.inArray('cms.delete',user_permissions)>=0) {
        checkDeletePermission=true;
    }
    $(document).ready(function() {
        var inx=parseInt(offest)*parseInt(pagesize);
        jQuery("#overlay").show();
        append_list=document.getElementById('append_list1');
        append_list.innerHTML='';
        ref.get().then(async function(snapshots) {
            html='';
            if(snapshots.docs.length>0) {
                $('.total_count').html(snapshots.docs.length);
                html=await buildHTML(snapshots);
            }else{
                $('.total_count').html(0)
            }
            if(html!='') {
                append_list.innerHTML=html;
                start=snapshots.docs[snapshots.docs.length-1];
                endarray.push(snapshots.docs[0]);
                if(snapshots.docs.length<pagesize) {
                    jQuery("#data-table_paginate").hide();
                }
            }

            if(checkDeletePermission) {
                $('#example24').DataTable({
                    order: [[1,'asc']],
                    columnDefs: [
                        {orderable: false,targets: [0,3,4]},
                    ],
                    "language": {
                        "zeroRecords": "{{trans('lang.no_record_found')}}",
                        "emptyTable": "{{trans('lang.no_record_found')}}"
                    },
                    responsive: true
                });
            } else {
                $('#example24').DataTable({
                    order: [[0,'asc']],
                    columnDefs: [
                        {orderable: false,targets: [2,3]},
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
        var id=val.id;
        var route1='{{route("cms.edit",":id")}}';
        route1=route1.replace(':id',id);

        if(checkDeletePermission) {

            html=html+'<td class="delete-all"><input type="checkbox" id="is_open_'+id+'" class="is_open" dataId="'+id+'"><label class="col-3 control-label"\n'+
                'for="is_open_'+id+'" ></label></td>';
        }
        html=html+'<td><a href="'+route1+'">'+val.name+'</a></td>';
        html=html+'<td>'+val.slug+'</td>';
        if(val.publish) {
            html=html+'<td><label class="switch"><input type="checkbox" checked id="'+val.id+'" name="isSwitch"><span class="slider round"></span></label></td>';
        } else {
            html=html+'<td><label class="switch"><input type="checkbox" id="'+val.id+'" name="isSwitch"><span class="slider round"></span></label></td>';
        }
        html=html+'<td class="action-btn"><a href="'+route1+'"><i class="mdi mdi-lead-pencil"></i></a>';
        if(checkDeletePermission) {

            html=html+'<a id="'+val.id+'" name="cms-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
        }
        html=html+'</td>';
        html=html+'</tr>';
        return html;
    }

    $("#is_active").click(function() {
        $("#example24 .is_open").prop('checked',$(this).prop('checked'));

    });
    $("#deleteAll").click(function() {
        if($('#example24 .is_open:checked').length) {
            if(confirm('Are You Sure want to Delete Selected Data ?')) {
                jQuery("#overlay").show();
                $('#example24 .is_open:checked').each(function() {
                    var dataId=$(this).attr('dataId');
                    database.collection('cms_pages').doc(dataId).delete().then(function() {
                        window.location.reload();
                    });
                });
            }
        } else {
            alert('Please Select Any One Record .');
        }
    });

    $(document).on("click","input[name='isSwitch']",function(e) {
        var ischeck=$(this).is(':checked');
        var id=this.id;
        if(ischeck) {
            database.collection('cms_pages').doc(id).update({
                'publish': true
            }).then(function(result) {
            });
        } else {
            database.collection('cms_pages').doc(id).update({
                'publish': false
            }).then(function(result) {
            });
        }
    });


    function prev() {
        if(endarray.length==1) {
            return false;
        }
        end=endarray[endarray.length-2];

        if(end!=undefined||end!=null) {
            jQuery("#overlay").show();
            if(jQuery("#selected_search").val()=='name'&&jQuery("#search").val().trim()!='') {

                listener=ref.orderBy('name').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').startAt(end).get();
            } else {
                listener=ref.startAt(end).limit(pagesize).get();
            }

            listener.then((snapshots) => {
                html='';
                html=buildHTML(snapshots);
                jQuery("#overlay").hide();
                if(html!='') {
                    append_list.innerHTML=html;
                    start=snapshots.docs[snapshots.docs.length-1];
                    endarray.splice(endarray.indexOf(endarray[endarray.length-1]),1);

                    if(snapshots.docs.length<pagesize) {

                        jQuery("#users_table_previous_btn").hide();
                    }

                }
            });
        }
    }


    function next() {
        if(start!=undefined||start!=null) {

            jQuery("#overlay").hide();

            if(jQuery("#selected_search").val()=='name'&&jQuery("#search").val().trim()!='') {

                listener=ref.orderBy('name').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').startAfter(start).get();
            } else {
                listener=ref.startAfter(start).limit(pagesize).get();
            }
            listener.then((snapshots) => {

                html='';
                html=buildHTML(snapshots);
                console.log(snapshots);
                jQuery("#overlay").hide();
                if(html!='') {
                    append_list.innerHTML=html;
                    start=snapshots.docs[snapshots.docs.length-1];

                    if(endarray.indexOf(snapshots.docs[0])!=-1) {
                        endarray.splice(endarray.indexOf(snapshots.docs[0]),1);
                    }
                    endarray.push(snapshots.docs[0]);
                }
            });
        }
    }

    function searchclear() {
        jQuery("#search").val('');
        searchtext();
    }

    $('#search').keypress(function(e) {
        if(e.which==13) {
            $('.search_button').click();
        }
    });

    function searchtext() {

        jQuery("#overlay").show();

        append_list.innerHTML='';

        if(jQuery("#selected_search").val()=='name'&&jQuery("#search").val().trim()!='') {

            wherequery=ref.orderBy('name').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').get();

        } else {

            wherequery=ref.limit(pagesize).get();
        }

        wherequery.then((snapshots) => {
            html='';
            html=buildHTML(snapshots);
            jQuery("#overlay").hide();
            if(html!='') {
                append_list.innerHTML=html;
                start=snapshots.docs[snapshots.docs.length-1];
                endarray.push(snapshots.docs[0]);

                if(snapshots.docs.length<pagesize) {
                    jQuery("#data-table_paginate").hide();
                } else {
                    jQuery("#data-table_paginate").show();
                }
            }
        });
    }

    $(document).on("click","a[name='cms-delete']",function(e) {
        if(confirm(deleteMsg)) {
            var id=this.id;
            jQuery("#overlay").show();
            database.collection('cms_pages').doc(id).delete().then(function(result) {
                window.location.reload();
            });
        } else {
            return false;
        }
    });

</script>

@endsection