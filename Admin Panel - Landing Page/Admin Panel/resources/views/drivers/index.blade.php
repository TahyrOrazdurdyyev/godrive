@extends('layouts.app')
@section('content')
@php
$type = 'all';
@endphp
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                @if(request()->is('drivers/approved'))
                @php $type = 'approved'; @endphp
                {{trans('lang.approved_drivers')}}
                @elseif(request()->is('drivers/pending'))
                @php $type = 'pending'; @endphp
                {{trans('lang.approval_pending_drivers')}}
                @else
                {{trans('lang.all_drivers')}}
                @endif
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.driver_table')}}</li>
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
                            <span class="icon mr-3"><img src="{{ asset('images/driver.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.driver_table')}}</h3>
                            <span class="counter ml-3 total_count"></span>
                        </div>
                        <div class="d-flex top-title-right align-self-center">
                            <div class="select-box pl-3 d-none">
                                <select class="form-control status_selector filteredRecords">
                                    <option value="">{{trans("lang.status")}}</option>
                                    <option value="active">{{trans("lang.active")}}</option>
                                    <option value="inactive">{{trans("lang.in_active")}}</option>
                                </select>
                            </div>
                            <div class="select-box pl-3">
                                <div id="daterange"><i class="fa fa-calendar"></i>&nbsp;
                                    <span></span>&nbsp; <i class="fa fa-caret-down"></i>
                                </div>
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.driver_table')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.driver_table_text')}}</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="driverTable"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <?php if (in_array('driver.delete', json_decode(@session('user_permissions')))) { ?>
                                                <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                        class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                            class="do_not_delete" href="javascript:void(0)"><i
                                                                class="fa fa-trash"></i> {{trans('lang.all')}}</a></label>
                                                </th>
                                            <?php } ?>
                                            <th>{{trans('lang.user_info')}}</th>
                                            <th>{{trans('lang.email')}}</th>
                                            <th>{{trans('lang.phone')}}</th>
                                            <th>{{trans('lang.document_plural')}}</th>
                                            <th>{{trans('lang.date')}}</th>
                                            <th>{{trans('lang.current_plan')}}</th>
                                            <th>{{trans('lang.expiry_date')}}</th>
                                            <th>{{trans('lang.service')}}</th>
                                            <th>{{trans('lang.vehicle_type')}}</th>
                                            <th>{{trans('lang.dashboard_total_orders')}}</th>
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
    const urlParams=new URLSearchParams(window.location.search);
    var type="{{$type}}";
    if(urlParams.has('today')) {
        const today=new Date();
        const startOfDay=new Date(today.setHours(0,0,0,0));
        const endOfDay=new Date(today.setHours(23,59,59,999));
        var ref=database.collection('driver_users').where("createdAt",">=",startOfDay).where("createdAt","<=",endOfDay);
    } else {
        var ref=database.collection('driver_users');
    }
    if(type=='pending') {
        ref=database.collection('driver_users').where("documentVerification","==",false);
    } else if(type=='approved') {
        ref=database.collection('driver_users').where("documentVerification","==",true);
    }
    var placeholderImage="{{ asset('/images/default_user.png') }}";
    var deleteMsg="{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg="{{trans('lang.selected_delete_alert')}}";
    var setLanguageCode=getCookie('setLanguage');
    var defaultLanguageCode=getCookie('defaultLanguage');
    var user_permissions='<?php echo @session('user_permissions') ?>';
    user_permissions=JSON.parse(user_permissions);
    var checkDeletePermission=false;
    if($.inArray('driver.delete',user_permissions)>=0) {
        checkDeletePermission=true;
    }
    $('.status_selector').select2({
        placeholder: '{{trans("lang.status")}}',
        minimumResultsForSearch: Infinity,
        allowClear: true
    });
    $('select').on("select2:unselecting",function(e) {
        var self=$(this);
        setTimeout(function() {
            self.select2('close');
        },0);
    });
    function setDate() {
        $('#daterange span').html('{{trans("lang.select_range")}}');
        $('#daterange').daterangepicker({
            autoUpdateInput: false,
        },function(start,end) {
            $('#daterange span').html(start.format('MMMM D, YYYY')+' - '+end.format('MMMM D, YYYY'));
            $('.filteredRecords').trigger('change');
        });
        $('#daterange').on('apply.daterangepicker',function(ev,picker) {
            $('#daterange span').html(picker.startDate.format('MMMM D, YYYY')+' - '+picker.endDate.format('MMMM D, YYYY'));
            $('.filteredRecords').trigger('change');
        });
        $('#daterange').on('cancel.daterangepicker',function(ev,picker) {
            $('#daterange span').html('{{trans("lang.select_range")}}');
            $('.filteredRecords').trigger('change');
        });
    }
    setDate();
    var initialRef=ref;
    $('.filteredRecords').change(async function() {
        var daterangepicker=$('#daterange').data('daterangepicker');
        filterRef=initialRef;
        if($('#daterange span').html()!='{{trans("lang.select_range")}}'&&daterangepicker) {
            var from=moment(daterangepicker.startDate).toDate();
            var to=moment(daterangepicker.endDate).toDate();
            if(from&&to) {
                var fromDate=firebase.firestore.Timestamp.fromDate(new Date(from));
                filterRef=filterRef.where('createdAt','>=',fromDate);
                var toDate=firebase.firestore.Timestamp.fromDate(new Date(to));
                filterRef=filterRef.where('createdAt','<=',toDate);
            }
        }
        ref=filterRef;
        $('#driverTable').DataTable().ajax.reload();
    });
    var append_list='';
    $(document).ready(function() {
        jQuery("#overlay").show();
        $(document).on('click','.dt-button-collection .dt-button',function() {
            $('.dt-button-collection').hide();
            $('.dt-button-background').hide();
        });
        $(document).on('click',function(event) {
            if(!$(event.target).closest('.dt-button-collection, .dt-buttons').length) {
                $('.dt-button-collection').hide();
                $('.dt-button-background').hide();
            }
        });
        var fieldConfig={
            columns: [
                {key: 'fullName',header: "{{trans('lang.user_info')}}"},
                {key: 'email',header: "{{trans('lang.email')}}"},
                {key: 'phone',header: "{{trans('lang.phone')}}"},
                {key: 'serviceName',header: "{{trans('lang.service')}}"},
                {key: 'vehicleType',header: "{{trans('lang.vehicle_type')}}"},
                {key: 'createdAt',header: "{{trans('lang.date')}}"},
            ],
            fileName: "{{trans('lang.driver_table')}}",
        };
        const table=$('#driverTable').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: async function(data,callback,settings) {
                const start=data.start;
                const length=data.length;
                const searchValue=data.search.value.toLowerCase();
                const orderColumnIndex=data.order[0].column;
                const orderDirection=data.order[0].dir;
                const orderableColumns=(checkDeletePermission)? ['','fullName','email','phone','','createdAt','expiryDate','activePlanName','serviceName','vehicleType','totalRide','']:['fullName','email','phone','','createdAt','expiryDate','activePlanName','service','vehicleType','totalRide','']; // Ensure this matches the actual column names
                const orderByField=orderableColumns[orderColumnIndex]; // Adjust the index to match your table
                if(searchValue.length>=3||searchValue.length===0) {
                    $('#overlay').show();
                }
                ref.orderBy('createdAt','desc').get().then(async function(querySnapshot) {
                    if(querySnapshot.empty) {
                        $('.total_count').text(0);
                        console.error("No data found in Firestore.");
                        $('#overlay').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            filteredData: [],
                            data: [] // No data
                        });
                        return;
                    }
                    let records=[];
                    let filteredRecords=[];
                    let serviceNames={};
                    // Fetch driver names
                    const servicDocs=await database.collection('service').get();
                    servicDocs.forEach(doc => {
                        serviceNames[doc.id]=doc.data().title;
                    });
                    await Promise.all(querySnapshot.docs.map(async (doc) => {
                        childData=doc.data();
                        childData.id=doc.id; // Ensure the document ID is included in the data              
                        var serviceName=serviceNames[childData.serviceId]||'';
                        var title='';
                        if(Array.isArray(serviceName)) {
                            var foundItem=serviceName.find(item => item.type===setLanguageCode);
                            if(foundItem&&foundItem.title!='') {
                                title=foundItem.title;
                            } else {
                                var foundItem=serviceName.find(item => item.type===defaultLanguageCode);
                                if(foundItem&&foundItem.title!='') {
                                    title=foundItem.title;
                                } else {
                                    var foundItem=serviceName.find(item => item.type==='en');
                                    title=foundItem.title;
                                }
                            }
                        }
                        childData.serviceName=title;
                        if(childData.hasOwnProperty('vehicleInformation')&&childData.vehicleInformation.vehicleType) {
                            var vehicleType='';
                            if(Array.isArray(childData.vehicleInformation.vehicleType)) {
                                var foundItem=childData.vehicleInformation.vehicleType.find(item => item.type===setLanguageCode);
                                if(foundItem&&foundItem.name!='') {
                                    vehicleType=foundItem.name;
                                } else {
                                    var foundItem=childData.vehicleInformation.vehicleType.find(item => item.type===defaultLanguageCode);
                                    if(foundItem&&foundItem.name!='') {
                                        vehicleType=foundItem.name;
                                    } else {
                                        var foundItem=childData.vehicleInformation.vehicleType.find(item => item.type==='en');
                                        vehicleType=foundItem.name;
                                    }
                                }
                            }
                            childData.vehicleType=vehicleType;
                        }
                        childData.phone=childData.countryCode&&childData.phoneNumber? shortNumber(childData.countryCode,childData.phoneNumber):"";
                        if(childData.hasOwnProperty("subscriptionExpiryDate") && childData.subscriptionExpiryDate!=null) {
                            try {
                                date=childData.subscriptionExpiryDate.toDate().toDateString();
                                time=childData.subscriptionExpiryDate.toDate().toLocaleTimeString('en-US');
                            } catch(err) {
                            }
                            childData.expiryDate=date+' '+time;
                        }
                        if(childData.hasOwnProperty('subscription_plan')&&childData.subscription_plan&&childData.subscription_plan.name) {
                            childData.activePlanName=childData.subscription_plan.name;
                        } else {
                            childData.activePlanName='';
                        }
                        if(searchValue) {
                            var date='';
                            var time='';
                            if(childData.hasOwnProperty("createdAt")) {
                                try {
                                    date=childData.createdAt.toDate().toDateString();
                                    time=childData.createdAt.toDate().toLocaleTimeString('en-US');
                                } catch(err) {
                                }
                            }
                            var createdAt=date+' '+time;
                            childData.createDate=createdAt;
                            if(
                                (childData.fullName&&childData.fullName.toLowerCase().toString().includes(searchValue))||
                                (childData.serviceName&&childData.serviceName.toLowerCase().toString().includes(searchValue))||
                                (createdAt&&createdAt.toString().toLowerCase().indexOf(searchValue)>-1)||
                                (childData.phone&&childData.phone.toString().includes(searchValue))||
                                (childData.email&&childData.email.toString().toLowerCase().includes(searchValue))||
                                (childData.vehicleType&&childData.vehicleType.toString().toLowerCase().includes(searchValue)||
                                    (childData.expiryDate&&childData.expiryDate.toString().toLowerCase().indexOf(searchValue)>-1)||
                                    (childData.hasOwnProperty('activePlanName')&&childData.activePlanName.toLowerCase().toString().includes(searchValue)))
                            ) {
                                filteredRecords.push(childData);
                            }
                        } else {
                            filteredRecords.push(childData);
                        }
                    }));
                    filteredRecords.sort((a,b) => {
                        let aValue=a[orderByField]? a[orderByField].toString().toLowerCase():'';
                        let bValue=b[orderByField]? b[orderByField].toString().toLowerCase():'';
                        if(orderByField==='createdAt') {
                            aValue=a[orderByField]? new Date(a[orderByField].toDate()).getTime():0;
                            bValue=b[orderByField]? new Date(b[orderByField].toDate()).getTime():0;
                        }
                        if(orderByField==='subscriptionExpiryDate') {
                            aValue=a[orderByField]? new Date(a[orderByField].toDate()).getTime():0;
                            bValue=b[orderByField]? new Date(b[orderByField].toDate()).getTime():0;
                        }
                        if(orderDirection==='asc') {
                            return (aValue>bValue)? 1:-1;
                        } else {
                            return (aValue<bValue)? 1:-1;
                        }
                    });
                    const totalRecords=filteredRecords.length;
                    $('.total_count').text(totalRecords);
                    const paginatedRecords=filteredRecords.slice(start,start+length);
                    await Promise.all(paginatedRecords.map(async (childData) => {
                        if(childData.id) {
                            const totalOrderSnapShot=await database.collection('orders').where('driverId','==',childData.id).get();
                            const rides=totalOrderSnapShot.size;
                            const totalIntercityOrderSnapShot=await database.collection('orders_intercity').where('driverId','==',childData.id).get();
                            const intercity=totalIntercityOrderSnapShot.size;
                            childData.total_rides=rides+intercity;
                        } else {
                            childData.total_rides=0;
                        }
                        var getData=await buildHTML(childData);
                        records.push(getData);
                    }));
                    $('#overlay').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords,
                        recordsFiltered: totalRecords,
                        filteredData: filteredRecords,
                        data: records
                    });
                }).catch(function(error) {
                    console.error("Error fetching data from Firestore:",error);
                    $('#overlay').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        filteredData: [],
                        data: []
                    });
                });
            },
            order: (checkDeletePermission)? [[5,'desc']]:[[4,'desc']],
            columnDefs: [
                {
                    targets: (checkDeletePermission)? 5:4,
                    type: 'date',
                    render: function(data) {
                        return data;
                    }
                },
                {orderable: false,targets: (checkDeletePermission)? [0,4,10,11]:[3,9,10]},
            ],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}",
                "processing": "" // Remove default loader
            },
            dom: 'lfrtipB',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="mdi mdi-cloud-download"></i> Export as',
                    className: 'btn btn-info',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Export Excel',
                            action: function(e,dt,button,config) {
                                exportData(dt,'excel',fieldConfig);
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'Export PDF',
                            action: function(e,dt,button,config) {
                                exportData(dt,'pdf',fieldConfig);
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'Export CSV',
                            action: function(e,dt,button,config) {
                                exportData(dt,'csv',fieldConfig);
                            }
                        }
                    ]
                }
            ],
            initComplete: function() {
                $(".dataTables_filter").append($(".dt-buttons").detach());
                $('.dataTables_filter input').attr('placeholder','Search here...').attr('autocomplete','new-password').val('');
                $('.dataTables_filter label').contents().filter(function() {
                    return this.nodeType===3;
                }).remove();
            }
        });
        table.columns.adjust().draw();
        function debounce(func,wait) {
            let timeout;
            const context=this;
            return function(...args) {
                clearTimeout(timeout);
                timeout=setTimeout(() => func.apply(context,args),wait);
            };
        }
        $('#search-input').on('input',debounce(function() {
            const searchValue=$(this).val();
            if(searchValue.length>=3) {
                $('#overlay').show();
                table.search(searchValue).draw();
            } else if(searchValue.length===0) {
                $('#overlay').show();
                table.search('').draw();
            }
        },300));
    });
    async function buildHTML(val) {
        var html=[];
        newdate='';
        var id=val.id;
        var route1='{{route("drivers.edit", ":id")}}';
        route1=route1.replace(':id',id);
        var driverView='{{route("drivers.view", ":id")}}';
        driverView=driverView.replace(':id',id);
        if(checkDeletePermission) {
            html.push('<input type="checkbox" id="is_open_'+id+'" class="is_open" dataId="'+id+'"><label class="col-3 control-label"\n'+
                'for="is_open_'+id+'" ></label>');
        }
        if(val.profilePic==''||val.profilePic==null) {
            var userImg='<img width="100%" style="width:70px;height:70px;" src="'+placeholderImage+'" alt="image">';
        } else {
            var userImg='<img width="100%" style="width:70px;height:70px;" src="'+val.profilePic+'" alt="image">';
        }
        html.push(userImg+'<a href="'+driverView+'">'+val.fullName+'</a>');
        html.push(shortEmail(val.email));
        if(val.countryCode!=null&&val.countryCode.includes('+')) {
            val.countryCode=val.countryCode.slice(1);
        }
        else {
            val.countryCode=val.countryCode;
        }
        html.push(val.phone);
        var driverDocView='{{route("drivers.document", ":id")}}';
        driverDocView=driverDocView.replace(':id',id);
        html.push('<span class="action-btn"><a href="'+driverDocView+'"><i class="mdi mdi-file"></i></a>');
        if(val.hasOwnProperty("createdAt")&&val.createdAt!=null&&val.createdAt!='') {
            var date=val.createdAt.toDate().toDateString();
            var time=val.createdAt.toDate().toLocaleTimeString('en-US');
            html.push('<span class="dt-time">'+date+' '+time+'</span>');
        } else {
            html.push('');
        }
        if(val.hasOwnProperty('subscription_plan')&&val.subscription_plan&&val.subscription_plan.name) {
            html.push(val.subscription_plan.name);
        } else {
            html.push('');
        }
        if(val.hasOwnProperty('subscriptionExpiryDate') && val.subscriptionExpiryDate!=null) {
            html.push(val.expiryDate);
        } else if(val.hasOwnProperty('subscriptionExpiryDate') && val.subscriptionExpiryDate==null) {
            html.push('{{trans("lang.unlimited")}}');
        }else{
            html.push('');
        }
        html.push(val.serviceName);
        var trroute1='';
        trroute1=trroute1.replace(':id','driverId='+id);
        if(val.hasOwnProperty('vehicleInformation')&&val.vehicleInformation.vehicleType) {
            html.push(val.vehicleType);
        } else {
            html.push('');
        }
        html.push(val.total_rides);
        var actionHtml='';
        actionHtml=actionHtml+'<span class="action-btn"><a href="'+driverView+'"><i class="mdi mdi-eye"></i></a><a href="'+route1+'"><i class="mdi mdi-lead-pencil"></i></a>';
        if(checkDeletePermission) {
            actionHtml=actionHtml+'<a id="'+val.id+'" name="driver-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
        }
        actionHtml+='</span>';
        html.push(actionHtml);
        return html;
    }
    $(document).on("click","input[name='isActive']",function(e) {
        var ischeck=$(this).is(':checked');
        var id=this.id;
        if(ischeck) {
            database.collection('users').doc(id).update({
                'isActive': true
            }).then(function(result) {
            });
        } else {
            database.collection('users').doc(id).update({
                'isActive': false
            }).then(function(result) {
            });
        }
    });
    $("#is_active").click(function() {
        $("#driverTable .is_open").prop('checked',$(this).prop('checked'));
    });
    $("#deleteAll").click(async function() {
        if($('#driverTable .is_open:checked').length) {
            jQuery("#overlay").show();
            if(confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#overlay").show();
                let deletePromises = [];
                $('#driverTable .is_open:checked').each(async function() {
                    var dataId=$(this).attr('dataId');
                    let deletePromise = (async () => {
                        await deleteDocumentWithImage('driver_users',dataId,'profilePic')
                        await deleteUserData(dataId);
                    })();
                    deletePromises.push(deletePromise);
                });
                await Promise.all(deletePromises);
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });
    async function deleteUserData(userId) {
        // Delete user from authentication
        const idToken = await firebase.auth().currentUser.getIdToken();
        return new Promise((resolve, reject) => {
            var dataObject = { "data": { "uid": userId } };
            var projectId = '<?php echo env('FIREBASE_PROJECT_ID') ?>';
            jQuery.ajax({
                url: 'https://us-central1-' + projectId + '.cloudfunctions.net/deleteUser',
                method: 'POST',
                crossDomain: true,
                contentType: "application/json; charset=utf-8",
                data: JSON.stringify(dataObject),
                headers: {
                    Authorization: 'Bearer ' + idToken
                },
                success: function (data) {
                    console.log('Delete user success:', data.result);
                    resolve(data);
                },
                error: function (xhr, status, error) {
                    var responseText = JSON.parse(xhr.responseText);
                    console.log('Delete user error:', responseText.error);
                    reject(new Error(responseText.error));
                }
            });
        });
    }
    $(document).on("click","a[name='driver-delete']",async function(e) {
        if(confirm(deleteMsg)) {
            jQuery("#overlay").show();
            var id=this.id;
            await deleteDocumentWithImage('driver_users',id,'profilePic');
            await deleteUserData(id);
            setTimeout(function () {
                window.location.reload();
            }, 2000);
        }
    });
    async function deleteDriverData(driverId) {
        await database.collection('order_transactions').where('driverId','==',driverId).get().then(async function(snapshotsOrderTransacation) {
            if(snapshotsOrderTransacation.docs.length>0) {
                snapshotsOrderTransacation.docs.forEach((temData) => {
                    var item_data=temData.data();
                    database.collection('order_transactions').doc(item_data.id).delete().then(function() {
                    });
                });
            }
        });
        await database.collection('driver_payouts').where('driverID','==',driverId).get().then(async function(snapshotsItem) {
            if(snapshotsItem.docs.length>0) {
                snapshotsItem.docs.forEach((temData) => {
                    var item_data=temData.data();
                    database.collection('driver_payouts').doc(item_data.id).delete().then(function() {
                    });
                });
            }
        });
    }
</script>
@endsection