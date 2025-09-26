@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.sos')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                <li class="breadcrumb-item active">{{trans('lang.sos')}}</li>

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
                            <span class="icon mr-3"><img src="{{ asset('images/SOS.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.sos')}}</h3>
                            <span class="counter ml-3 total_count"></span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
            {{trans('lang.processing')}}
        </div>
        <div class="table-list">
            <div class="row">

                <div class="col-12">

                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-0">
                            <div class="card-header-title">
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.sos')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.sos_text')}}</p>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive m-t-10">

                                <table id="example24"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">

                                    <thead>

                                        <tr>
                                            <?php if (in_array('sos.delete', json_decode(@session('user_permissions')))) { ?>
                                                <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                        class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                            class="do_not_delete" href="javascript:void(0)"><i
                                                                class="fa fa-trash"></i>
                                                            {{trans('lang.all')}}</a></label></th>

                                            <?php } ?>
                                            <th>{{trans('lang.sos_id')}}</th>
                                            <th>{{trans('lang.order_user_id')}}</th>
                                            <th class="driverClass">{{trans('lang.driver_plural')}}</th>
                                            <th>{{trans('lang.address')}}</th>
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
    var ref=database.collection('sos');
    var placeholderImage='';
    var append_list='';
    var deleteSelectedRecordMsg="{{trans('lang.selected_delete_alert')}}";

    var user_permissions='<?php echo @session('user_permissions') ?>';

    user_permissions=JSON.parse(user_permissions);

    var checkDeletePermission=false;

    if($.inArray('sos.delete',user_permissions)>=0) {
        checkDeletePermission=true;
    }

    $(document).ready(function() {

        var inx=parseInt(offest)*parseInt(pagesize);
        jQuery("#overlay").show();


        append_list=document.getElementById('append_list1');
        append_list.innerHTML='';
        ref.get().then(async function(snapshots) {
            html='';
            html=await buildHTML(snapshots);            

            jQuery("#overlay").hide();
            if(html!='') {
                append_list.innerHTML=html;
                start=snapshots.docs[snapshots.docs.length-1];
                endarray.push(snapshots.docs[0]);
                if(snapshots.docs.length<pagesize) {
                    jQuery("#overlay").hide();
                }
            }
            $('#example24').DataTable({

                order: [],
                columnDefs: [
                    {orderable: false,targets: (checkDeletePermission)? [0,5,6]:[4,5]},
                ],
                "language": {
                    "zeroRecords": "{{trans("lang.no_record_found")}}",
                    "emptyTable": "{{trans("lang.no_record_found")}}"
                },
                responsive: true,
            });

        });

    });


    async function buildHTML(snapshots) {
        var html='';
        var count=0;
        await Promise.all(snapshots.docs.map(async (listval) => {
            var val=listval.data();
            var getData=await getListData(val);
            if(getData != ''){
                count = Number(count) + 1;
            }
            html+=getData;
        }));
        $('.total_count').html(count);
        return html;
    }

    async function getListData(val) {
        var html='';
        

        if(val.orderId&&val.orderType) {

            const rideData=await rideDetails(val.orderId,val.orderType);

            if(rideData) {

                html=html+'<tr>';
                newdate='';
                var id=val.id;
                var route1='{{route("sos.edit",":id")}}';
                route1=route1.replace(':id',id);
                if(val.orderType=="city") {
                    var trroute1='{{route("rides.show",":id")}}';
                    trroute1=trroute1.replace(':id',val.orderId);
                } else {
                    var trroute1='{{route("intercity-service-rides.view",":id")}}';
                    trroute1=trroute1.replace(':id',val.orderId);
                }
                if(checkDeletePermission) {

                    html=html+'<td class="delete-all"><input type="checkbox" id="is_open_'+id+'" class="is_open" dataId="'+id+'"><label class="col-3 control-label"\n'+
                        'for="is_open_'+id+'" ></label></td>';
                }
                html=html+'<td><a href="'+route1+'">'+(val.id).substring(0,7)+'</a></td>';
                if(rideData.userId) {
                    var userData=await getUserName(rideData.userId);
                    if(Object.keys(userData).length>0) {
                        var customer_view='{{route("users.view",":id")}}';
                        customer_view=customer_view.replace(':id',rideData.userId);
                        html+='<td class="redirecttopage"><a href="'+customer_view+'">'+userData.fullName+'</a></td>';
                    } else {
                        html+='<td class="redirecttopage">'+'{{trans("lang.unknown_user")}}'+'</td>';
                    }
                } else {
                    html+='<td class="redirecttopage"></td>';
                }

                if(rideData.driverId&&rideData.driverId!=null) {
                    var driver_id=rideData.driverId;
                    var driverData=await getDriverName(driver_id);
                    if(Object.keys(driverData).length>0) {
                        var driver_view='{{route("drivers.view",":id")}}';
                        driver_view=driver_view.replace(':id',driverData.id);
                        html+='<td class="redirecttopage"><a href="'+driver_view+'">'+driverData.fullName+'</a></td>';
                    } else {
                        console.log('else');
                        html+='<td class="redirecttopage">{{trans("lang.unknown_user")}}</td>';
                    }
                } else {
                    html+='<td class="redirecttopage"></td>';
                }
                html=html+'<td>'+rideData.destinationLocationName+'</td>';

                if(val.status=="Completed") {
                    html=html+'<td><span class=" py-2 px-3 badge badge-success">'+val.status+'</span></td>';
                } else if(val.status=="Processing") {
                    html=html+'<td><span class=" py-2 px-3 badge badge-info">'+val.status+'</span></td>';
                } else {
                    html=html+'<td><span class="py-2 px-3 badge badge-primary">'+val.status+'</span></td>';
                }

                html=html+'<td class="action-btn"><a href="'+route1+'"><i class="mdi mdi-lead-pencil"></i></a>';

                if(checkDeletePermission) {
                    html=html+'<a id="'+val.id+'" name="sos-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';

                }
                html=html+'</td>';

                html=html+'</tr>';
            }
        }

        return html;
    }


    async function rideDetails(rideId,rideType) {
        if(rideType=="city") {
            var rideDetails=await database.collection('orders').doc(rideId).get();
            if(rideDetails.data()) {
                return rideDetails.data();
            } else {
                return '';
            }
        } else {
            var rideDetails=await database.collection('orders_intercity').doc(rideId).get();
            if(rideDetails.data()) {
                return rideDetails.data();
            } else {
                return '';
            }

        }
    }

    $(document.body).on('click','.redirecttopage',function() {
        var url=$(this).attr('data-url');
        window.location.href=url;
    });

    $("#is_active").click(function() {
        $("#example24 .is_open").prop('checked',$(this).prop('checked'));
    });

    $("#deleteAll").click(function() {
        if($('#example24 .is_open:checked').length) {
            if(confirm(deleteSelectedRecordMsg)) {
                jQuery("#overlay").show();
                $('#example24 .is_open:checked').each(function() {
                    var dataId=$(this).attr('dataId');
                    database.collection('sos').doc(dataId).delete().then(function() {
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


    $(document).on("click","a[name='sos-delete']",function(e) {
        var id=this.id;
        database.collection('sos').doc(id).delete().then(function() {
            window.location.reload();
        });
    });

    async function getUserName(userId,id) {
        var user={};
        await database.collection('users').where('id','==',userId).get().then(async function(snapshots) {
            if(snapshots.docs.length>0) {
                user=snapshots.docs[0].data();
            }
        });
        return user;
    }

    async function getDriverName(driverId) {
        var driver={};
        await database.collection('driver_users').where('id','==',driverId).get().then(async function(snapshots) {
            if(snapshots.docs.length>0) {
                driver=snapshots.docs[0].data();
            }
        });
        return driver;
    }

</script>

@endsection