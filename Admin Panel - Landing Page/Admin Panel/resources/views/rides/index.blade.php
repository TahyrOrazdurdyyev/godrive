@extends('layouts.app')



@section('content')

<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor orderTitle">{{trans('lang.order_plural')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                <li class="breadcrumb-item active">{{trans('lang.order_plural')}}</li>

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

                            <span class="icon mr-3"><img src="{{ asset('images/rides.png') }}"></span>

                            <h3 class="mb-0">{{trans('lang.order_plural')}}</h3>

                            <span class="counter ml-3 order_count"></span>

                        </div>

                        <div class="d-flex top-title-right align-self-center">

                            <div class="d-flex top-title-right align-self-center">

                                <div class="select-box pl-3">

                                    <select class="form-control zone_selector filteredRecords">

                                        <option value="" selected>{{trans("lang.select_zone")}}</option>

                                    </select>

                                </div>

                                <div class="select-box pl-3">

                                    <select class="form-control status_selector filteredRecords">

                                        <option value="" selected>{{trans("lang.status")}}</option>

                                        <option value="Ride Placed">{{trans("lang.order_placed")}}</option>

                                        <option value="Ride Active">{{trans("lang.order_active")}}</option>

                                        <option value="Ride InProgress">{{trans("lang.ride_inprogress")}}</option>

                                        <option value="Ride Canceled">{{trans("lang.dashboard_ride_canceled")}}</option>

                                        <option value="Ride Completed">{{trans("lang.order_completed")}}</option>

                                    </select>

                                </div>

                                <div class="select-box pl-3">

                                    <select class="form-control service_type_selector filteredRecords">

                                        <option value="" selected>{{trans("lang.service")}}</option>

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

            <div class="row">

                <div class="col-md-3">

                    <div class="card card-box-with-icon bg--15">

                        <div class="card-body d-flex justify-content-between align-items-center">

                            <div class="card-box-with-content">

                                <h4 class="text-dark-2 mb-1 h4 total_ride">00</h4>

                                <p class="mb-0 small text-dark-2">{{trans('lang.total_ride')}}</p>

                            </div>

                            <span class="box-icon ab"><img src="{{ asset('images/total_rides.png') }}"></span>

                        </div>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="card card-box-with-icon bg--5">

                        <div class="card-body d-flex justify-content-between align-items-center">

                            <div class="card-box-with-content">

                                <h4 class="text-dark-2 mb-1 h4 placed_ride">00</h4>

                                <p class="mb-0 small text-dark-2">{{trans('lang.placed_ride')}}</p>

                            </div>

                            <span class="box-icon ab"><img src="{{ asset('images/placed_rides.png') }}"></span>

                        </div>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="card card-box-with-icon bg--1">

                        <div class="card-body d-flex justify-content-between align-items-center">

                            <div class="card-box-with-content">

                                <h4 class="text-dark-2 mb-1 h4 active_ride">00</h4>

                                <p class="mb-0 small text-dark-2">{{trans('lang.active_ride')}}</p>

                            </div>

                            <span class="box-icon ab"><img src="{{ asset('images/active_rides.png') }}"></span>

                        </div>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="card card-box-with-icon bg--24">

                        <div class="card-body d-flex justify-content-between align-items-center">

                            <div class="card-box-with-content">

                                <h4 class="text-dark-2 mb-1 h4 completed_ride">00</h4>

                                <p class="mb-0 small text-dark-2">{{trans('lang.completed_ride')}}</p>

                            </div>

                            <span class="box-icon ab"><img src="{{ asset('images/complete_rides.png') }}"></span>

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

                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.ride_list')}}</h3>

                                <p class="mb-0 text-dark-2">{{trans('lang.ride_list_text')}}</p>

                            </div>

                        </div>

                        <div class="card-body">



                            <div class="table-responsive m-t-10">

                                <table id="orderTable"

                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"

                                    cellspacing="0" width="100%">

                                    <thead>

                                        <tr>

                                            <?php if (in_array('order.delete', json_decode(@session('user_permissions')))) { ?>



                                            <th class="delete-all"><input type="checkbox" id="is_active"><label

                                                    class="col-3 control-label" for="is_active"><a id="deleteAll"

                                                        class="do_not_delete" href="javascript:void(0)"><i

                                                            class="fa fa-trash"></i>

                                                        {{trans('lang.all')}}</a></label></th>

                                            <?php } ?>

                                            <th>{{trans('lang.order_id')}}</th>

                                            <th>{{trans('lang.customer')}}</th>

                                            <th>{{trans('lang.driver')}}</th>

                                            <th>{{trans('lang.date')}}</th>

                                            <th>{{trans('lang.service')}}</th>

                                            <th>{{trans('lang.amount')}}</th>

                                            <th>{{trans('lang.order_order_status_id')}}</th>

                                            <th>{{trans('lang.actions')}}</th>

                                        </tr>

                                    </thead>

                                    <tbody id="append_list1">

                                    </tbody>

                                </table>



                                <div class="ride-status-info" style="display:none">

                                    <h3>{{trans('lang.status_info')}}</h3>

                                    <ul>

                                        <li><span class="status"><span

                                                    class="badge badge-primary py-2 px-3">{{trans('lang.order_placed')}}</span></span><span

                                                class="info">{{trans('lang.ride_placed_info')}}</span>

                                        </li>

                                        <li><span class="status"><span

                                                    class="badge badge-warning py-2 px-3">{{trans('lang.order_active')}}</span></span><span

                                                class="info">{{trans('lang.ride_active_info')}}</span>

                                        </li>

                                        <li><span class="status"><span

                                                    class="badge badge-info py-2 px-3">{{trans('lang.ride_inprogress')}}</span></span><span

                                                class="info">{{trans('lang.ride_inprogress_info')}}</span>

                                        </li>

                                        <li><span class="status"><span

                                                    class="badge badge-danger py-2 px-3">{{trans('lang.dashboard_ride_canceled')}}</span></span><span

                                                class="info">{{trans('lang.ride_canceled_info')}}</span>

                                        </li>

                                        <li><span class="status"><span

                                                    class="badge badge-success py-2 px-3">{{trans('lang.order_completed')}}</span></span><span

                                                class="info">{{trans('lang.ride_completed_info')}}</span>

                                        </li>

                                        <li><span class="status"><span

                                                    class="badge py-2 px-3 unknown-badge">{{trans('lang.unknown_user')}}</span></span><span

                                                class="info">{{trans('lang.unknown_user_info')}}</span>

                                        </li>

                                    </ul>

                                </div>

                                <div class="tip-box search-info" style="display:none">

                                    <h5> <i class="fa fa-info-circle"> </i> Info</h5>

                                    <p>{{trans('lang.search_filter_info')}}</p>

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

    var end=null;

    var endarray=[];

    var start=null;

    var append_list='';

    var deleteSelectedRecordMsg="{{trans('lang.selected_delete_alert')}}";

    var setLanguageCode=getCookie('setLanguage');

    var defaultLanguageCode=getCookie('defaultLanguage');

    const urlParams=new URLSearchParams(window.location.search);

    

    var refCurrency=database.collection('currency').where('enable','==',true).limit('1');



    var decimal_degits=0;

    var symbolAtRight=false;

    var currentCurrency='';

    refCurrency.get().then(async function(snapshots) {



        var currencyData=snapshots.docs[0].data();

        currentCurrency=currencyData.symbol;

        decimal_degits=currencyData.decimalDigits;



        if(currencyData.symbolAtRight) {

            symbolAtRight=true;

        }

    });

    

    if(urlParams.has('today')) {

        

        const today=new Date();

        const startOfDay=new Date(today.setHours(0,0,0,0));

        const endOfDay=new Date(today.setHours(23,59,59,999));

        var refData=database.collection('orders').where("createdDate",">=",startOfDay).where("createdDate","<=",endOfDay);



    }else if(urlParams.has('today_earning')){

        const today=new Date();

        const startOfDay=new Date(today.setHours(0,0,0,0));

        const endOfDay=new Date(today.setHours(23,59,59,999));

        var refData=database.collection('orders').where("createdDate",">=",startOfDay).where("createdDate","<=",endOfDay).where('status','==','Ride Completed');



    } else if(urlParams.has('ride_placed')) {

        const today=new Date();

        const startOfDay=new Date(today.setHours(0,0,0,0));

        const endOfDay=new Date(today.setHours(23,59,59,999));

        var refData=database.collection('orders').where("createdDate",">=",startOfDay).where("createdDate","<=",endOfDay).where('status','==','Ride Placed');



    } else if(urlParams.has('ride_active')) {

        const today=new Date();

        const startOfDay=new Date(today.setHours(0,0,0,0));

        const endOfDay=new Date(today.setHours(23,59,59,999));

        var refData=database.collection('orders').where("createdDate",">=",startOfDay).where("createdDate","<=",endOfDay).where('status','==','Ride Active');



    } else if(urlParams.has('ride_completed')) {

        const today=new Date();

        const startOfDay=new Date(today.setHours(0,0,0,0));

        const endOfDay=new Date(today.setHours(23,59,59,999));

        var refData=database.collection('orders').where("createdDate",">=",startOfDay).where("createdDate","<=",endOfDay).where('status','==','Ride Completed');



    } else if(urlParams.has('ride_canceled')) {

        const today=new Date();

        const startOfDay=new Date(today.setHours(0,0,0,0));

        const endOfDay=new Date(today.setHours(23,59,59,999));

        var refData=database.collection('orders').where("createdDate",">=",startOfDay).where("createdDate","<=",endOfDay).where('status','==','Ride Canceled');



    } else if(urlParams.has('total_placed')) {

        const today=new Date();

        const startOfDay=new Date(today.setHours(0,0,0,0));

        const endOfDay=new Date(today.setHours(23,59,59,999));

        var refData=database.collection('orders').where('status','==','Ride Placed');



    } else if(urlParams.has('total_active')) {

        const today=new Date();

        const startOfDay=new Date(today.setHours(0,0,0,0));

        const endOfDay=new Date(today.setHours(23,59,59,999));

        var refData=database.collection('orders').where('status','==','Ride Active');



    } else if(urlParams.has('total_completed')) {

        const today=new Date();

        const startOfDay=new Date(today.setHours(0,0,0,0));

        const endOfDay=new Date(today.setHours(23,59,59,999));

        var refData=database.collection('orders').where('status','==','Ride Completed');



    } else if(urlParams.has('total_canceled')) {

        const today=new Date();

        const startOfDay=new Date(today.setHours(0,0,0,0));

        const endOfDay=new Date(today.setHours(23,59,59,999));

        var refData=database.collection('orders').where('status','==','Ride Canceled');



    } else if(urlParams.has('total_earning')) {

        

        var refData=database.collection('orders').where('status','==','Ride Completed');;



    }

     else {

        var refData=database.collection('orders');



    }

    var refUser=database.collection('users');

    var driverUser=database.collection('driver_users');

    let childData={};

    var user_permissions='<?php echo @session('user_permissions') ?>';



    user_permissions=JSON.parse(user_permissions);



    var checkDeletePermission=false;



    if($.inArray('order.delete',user_permissions)>=0) {

        checkDeletePermission=true;

    }

    database.collection('zone').orderBy('name','asc').get().then(async function(snapshots) {

        snapshots.docs.forEach((listval) => {

            var val=listval.data();

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

            if(name!='') {

                $('.zone_selector').append($("<option></option>")

                    .attr("value",val.id)

                    .text(name));

            }



        })

    });

    database.collection('service').orderBy('title','asc').get().then(async function(snapshots) {

        snapshots.docs.forEach((listval) => {

            var val=listval.data();

            var name='';

            if(Array.isArray(val.title)) {

                var foundItem=val.title.find(item => item.type===setLanguageCode);

                if(foundItem&&foundItem.title!='') {

                    name=foundItem.title;

                } else {

                    var foundItem=val.title.find(item => item.type===defaultLanguageCode);

                    if(foundItem&&foundItem.title!='') {

                        name=foundItem.title;

                    } else {

                        var foundItem=val.title.find(item => item.type==='en');

                        name=foundItem.title;

                    }

                }



            }

            if(name!='') {

                $('.service_type_selector').append($("<option></option>")

                    .attr("value",val.id)

                    .text(name));

            }



        })

    });

    $('.status_selector').select2({

        placeholder: '{{trans("lang.status")}}',

        minimumResultsForSearch: Infinity,

        allowClear: true

    });

    $('.zone_selector').select2({

        placeholder: '{{trans("lang.select_zone")}}',

        minimumResultsForSearch: Infinity,

        allowClear: true

    });

    $('.service_type_selector').select2({

        placeholder: '{{trans("lang.service_type")}}',

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

    var initialRef=refData;

    $('.filteredRecords').change(async function() {

        var status=$('.status_selector').val();

        var zoneValue=$('.zone_selector').val();

        var serviceType=$('.service_type_selector').val();

        var daterangepicker=$('#daterange').data('daterangepicker');

        var filterRef=initialRef;

        if(zoneValue) {

            filterRef=filterRef.where('zoneId','==',zoneValue);

        }

        if(status) {

            filterRef=filterRef.where('status','==',status);

        }

        if(serviceType) {

            filterRef=filterRef.where('service.id','==',serviceType);

        }

        if($('#daterange span').html()!='{{trans("lang.select_range")}}'&&daterangepicker) {

            var from=moment(daterangepicker.startDate).toDate();

            var to=moment(daterangepicker.endDate).toDate();

            if(from&&to) {

                var fromDate=firebase.firestore.Timestamp.fromDate(new Date(from));

                filterRef=filterRef.where('createdDate','>=',fromDate);

                var toDate=firebase.firestore.Timestamp.fromDate(new Date(to));

                filterRef=filterRef.where('createdDate','<=',toDate);

            }

        }

        refData=filterRef;

        $('#orderTable').DataTable().ajax.reload();

    });
    function isTimeBetween(current, start, end) {

        var currentTime = convertToMinutes(current);

        var startTime = convertToMinutes(start);

        var endTime = convertToMinutes(end);



        if (endTime < startTime) {

            return currentTime >= startTime || currentTime <= endTime;

        } else {

            return currentTime >= startTime && currentTime <= endTime;

        }
    }

    function convertToMinutes(time) {

        var parts = time.split(":");

        return parseInt(parts[0]) * 60 + parseInt(parts[1]);

    }





    $(document).ready(async function() {



        jQuery('#search').hide();



        $(document.body).on('click','.redirecttopage',function() {

            var url=$(this).attr('data-url');

            window.location.href=url;

        });



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

                {key: 'id',header: "{{trans('lang.order_order_status_id')}}"},

                {key: 'userName',header: "{{trans('lang.customer')}}"},

                {key: 'driverName',header: "{{trans('lang.driver')}}"},

                {key: 'createdDate',header: "{{trans('lang.date')}}"},

                {key: 'serviceName',header: "{{trans('lang.service')}}"},

                {key: 'amount',header: "{{trans('lang.amount')}}"},

                {key: 'status',header: "{{trans('lang.status')}}"},

            ],

            fileName: "{{trans('lang.order_table')}}",

        };



        const table=$('#orderTable').DataTable({

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

                const orderableColumns=(checkDeletePermission)? ['','id','userName','driverName','createdDate','serviceName','amount','status','']:['id','userName','driverName','createdDate','serviceName','amount','status','']; // Ensure this matches the actual column names

                const orderByField=orderableColumns[orderColumnIndex]; // Adjust the index to match your table



                if(searchValue.length>=3||searchValue.length===0) {

                    $('#overlay').show();

                }



                await refData.get().then(async function(querySnapshot) {



                    if(querySnapshot.empty) {

                        $('.order_count').text(0);

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

                    let userNames={};

                    let driverNames={};



                    // Fetch user names

                    const userDocs=await refUser.get();

                    userDocs.forEach(doc => {

                        userNames[doc.id]=doc.data().fullName;

                    });

                    // Fetch driver names

                    const driverDocs=await driverUser.get();

                    driverDocs.forEach(doc => {

                        driverNames[doc.id]=doc.data().fullName;

                    });



                    await Promise.all(querySnapshot.docs.map(async (doc) => {



                        childData=doc.data();

                        childData.id=doc.id; // Ensure the document ID is included in the data              



                        var amount=0;

                        if (childData.driverId && childData.driverId != null && childData.driverId != "") {

                            amount=getOrderDetails(childData);

                            amount=amount.toFixed(decimal_degits);
                        } else {



                            if(childData.offerRate&&!isNaN(parseFloat(childData.offerRate))) {



                                amount=parseFloat(childData.offerRate);



                            }

                            amount=amount.toFixed(decimal_degits);

                        }

                        childData.amount=amount;



                        var serviceName='';

                        if(childData.hasOwnProperty('service')&&Array.isArray(childData.service.title)) {



                            var foundItem=childData.service.title.find(item => item.type===setLanguageCode);

                            if(foundItem&&foundItem.title!='') {

                                serviceName=foundItem.title;

                            } else {

                                var foundItem=childData.service.title.find(item => item.type===defaultLanguageCode);

                                if(foundItem&&foundItem.title!='') {

                                    serviceName=foundItem.title;

                                } else {

                                    var foundItem=childData.service.title.find(item => item.type==='en');

                                    serviceName=foundItem.title;

                                }

                            }



                        }



                        childData.serviceName=serviceName;



                        childData.driverName=driverNames[childData.driverId]||'';

                        childData.userName=userNames[childData.userId]||'';



                        if(searchValue) {

                            var date='';

                            var time='';

                            if(childData.hasOwnProperty("createdDate")) {

                                try {

                                    date=childData.createdDate.toDate().toDateString();

                                    time=childData.createdDate.toDate().toLocaleTimeString('en-US');

                                } catch(err) {

                                }

                            }

                            var createdAt=date+' '+time;



                            if(

                                (childData.userName&&childData.userName.toLowerCase().toString().includes(searchValue))||

                                (childData.driverName&&childData.driverName.toLowerCase().toString().includes(searchValue))||

                                (childData.serviceName&&childData.serviceName.toLowerCase().toString().includes(searchValue))||

                                (childData.id&&childData.id.toLowerCase().toString().includes(searchValue))||

                                (createdAt&&createdAt.toString().toLowerCase().indexOf(searchValue)>-1)||

                                (childData.status&&childData.status.toLowerCase().toString().includes(searchValue))||

                                (amount&&amount.toString().includes(searchValue))



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

                        if(orderByField==='createdDate') {

                            aValue=a[orderByField]? new Date(a[orderByField].toDate()).getTime():0;

                            bValue=b[orderByField]? new Date(b[orderByField].toDate()).getTime():0;

                        }

                        if(orderByField==='amount') {

                            aValue=a[orderByField]? parseFloat(a[orderByField]):0.0;

                            bValue=b[orderByField]? parseFloat(b[orderByField]):0.0;

                        }

                        if(orderDirection==='asc') {

                            return (aValue>bValue)? 1:-1;

                        } else {

                            return (aValue<bValue)? 1:-1;

                        }

                    });





                    const totalRecords=filteredRecords.length;

                    $('.order_count').text(totalRecords);

                    $('.total_ride').text(totalRecords);

                    let active_ride=0;

                    let placed_ride=0;

                    let completed_ride=0;

                    await Promise.all(filteredRecords.map(async (childData) => {

                        if(childData.status&&childData.status=='Ride Active') {

                            active_ride+=1;

                        }

                        if(childData.status&&childData.status=='Ride Placed') {

                            placed_ride+=1;

                        }

                        if(childData.status&&childData.status=='Ride Completed') {

                            completed_ride+=1;

                        }

                    }));

                    $('.active_ride').text(active_ride);

                    $('.placed_ride').text(placed_ride);

                    $('.completed_ride').text(completed_ride);

                    const paginatedRecords=filteredRecords.slice(start,start+length);

                    await Promise.all(paginatedRecords.map(async (childData) => {



                        var getData=await buildHTML(childData);

                        records.push(getData);

                    }));



                    $('#overlay').hide(); // Hide loader

                    callback({

                        draw: data.draw,

                        recordsTotal: totalRecords, // Total number of records in Firestore

                        recordsFiltered: totalRecords, // Number of records after filtering (if any)

                        filteredData: filteredRecords,

                        data: records // The actual data to display in the table

                    });

                }).catch(function(error) {

                    console.error("Error fetching data from Firestore:",error);

                    $('#overlay').hide(); // Hide loader

                    callback({

                        draw: data.draw,

                        recordsTotal: 0,

                        recordsFiltered: 0,

                        filteredData: [],

                        data: [] // No data due to error

                    });

                });

            },

            order: (checkDeletePermission)? [[4,'desc']]:[[3,'desc']],

            columnDefs: [

                {

                    targets: (checkDeletePermission)? 4:3,

                    type: 'date',

                    render: function(data) {

                        return data;

                    }

                },

                {orderable: false,targets: (checkDeletePermission)? [0,7,8]:[6,7]},

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

        var id=val.id;

        var user_id=val.userId;

        var ride_view='{{route("rides.show", ":id")}}';

        ride_view=ride_view.replace(':id',val.id);

        if(checkDeletePermission) {

            html.push('<input type="checkbox" id="is_open_'+id+'" class="is_open" dataId="'+id+'"><label class="col-3 control-label"\n'+

                'for="is_open_'+id+'" ></label>');

        }

        id=id.substring(0,7);

        html.push('<a href="'+ride_view+'">'+id+'</a>');

        if(val.userId) {

            if(val.userName!='') {

                var customer_view='{{route("users.view", ":id")}}';

                customer_view=customer_view.replace(':id',val.userId);

                html.push('<a href="'+customer_view+'">'+val.userName+'</a>');

            } else {

                html.push('{{trans("lang.unknown_user")}}');

            }

        } else {

            html.push('');

        }



        if(val.driverId&&val.driverId!=null) {

            var driver_id=val.driverId;

            if(val.driverName!='') {

                var driver_view='{{route("drivers.view", ":id")}}';

                driver_view=driver_view.replace(':id',val.driverId);

                html.push('<a href="'+driver_view+'">'+val.driverName+'</a>');

            } else {

                html.push('{{trans("lang.unknown_user")}}');

            }

        } else {

            html.push('');

        }

        var date='';

        var time='';

        if(val.hasOwnProperty("createdDate")) {

            try {

                date=val.createdDate.toDate().toDateString();

                time=val.createdDate.toDate().toLocaleTimeString('en-US');

            } catch(err) {

            }

            html.push('<span class="dt-time">'+date+' '+time+'</span>');

        } else {

            html.push('');

        }

        if(val.hasOwnProperty('service')) {

            html.push(val.serviceName);

        } else {

            html.push('');

        }





        if(symbolAtRight) {

            html.push(val.amount+currentCurrency);

        } else {

            html.push(currentCurrency+val.amount);

        }

        if(val.status=="Ride Placed") {

            html.push('<span class="badge badge-primary py-2 px-3">'+val.status+'</span>');

        } else if(val.status=="Ride Completed") {

            html.push('<span  class="badge badge-success py-2 px-3">'+val.status+'</span>');

        } else if(val.status=="Ride Active") {

            html.push('<span class="badge badge-warning py-2 px-3">'+val.status+'</span>');

        } else if(val.status=="Ride InProgress") {

            html.push('<span class="badge badge-info py-2 px-3">'+val.status+'</span>');

        } else if(val.status=="Ride Canceled") {

            html.push('<span class="badge badge-danger py-2 px-3">'+val.status+'</span>');

        }else if(val.status=="Ride Hold" || val.status == "Ride Hold Accepted") {

            html.push('<span class="badge badge-info py-2 px-3">'+val.status+'</span>');

        } else {

            html.push('');

        }

        var actionHtml='';



        actionHtml+='<span class="action-btn"><a href="'+ride_view+'"><i class="mdi mdi-eye"></i></a>';

        if(checkDeletePermission) {

            actionHtml+='<a id="'+val.id+'" name="ride-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>'

        }

        actionHtml+='</span>';

        html.push(actionHtml);

        return html;

    }



    function getOrderDetails(orderData) {
        var amount = 0;
        var total_amount = 0;
        var offer_rate = 0;
        var final_rate = 0;

        if (orderData.offerRate) {
            amount = parseFloat(orderData.offerRate);
            offer_rate = parseFloat(orderData.offerRate);

        }
        if (orderData.finalRate) {
            amount = parseFloat(orderData.finalRate);
            final_rate = parseFloat(orderData.finalRate);

        }

        var total_amount=0;

        var total_item_amount = 0;

        var order_total_fare = 0;

        var startNightTime = orderData.service.startNightTime;

        var endNightTime = orderData.service.endNightTime;

        var nightCharge = orderData.service.nightCharge;

        var orderTime = orderData.createdDate.toDate().toTimeString().substring(0, 5); // Current time as "HH:mm"

        var basic_fare = parseFloat(orderData.service.basicFareCharge);
        
        var km_charges = parseFloat(parseFloat(orderData.distance) - parseFloat(orderData.service.basicFare)).toFixed(2);

        var total_km_charges = 0;

        var ride_minute_fare = 0;

        var holding_charges = 0;

        if(parseFloat(orderData.acNonAcCharges) > 0){
            total_km_charges = parseFloat(parseFloat(km_charges) * parseFloat(orderData.acNonAcCharges));

        }else if(orderData.vehicleInformation && parseFloat(orderData.vehicleInformation.perKmRate) > 0){
            total_km_charges = parseFloat(parseFloat(km_charges) * parseFloat(orderData.vehicleInformation.perKmRate));

        }

        if(orderData.duration){

            var duration_hours = orderData.duration.split(" ")[0];

            var duration_minutes = orderData.duration.split(" ")[2];

            duration_hours = parseFloat(duration_hours) * 60;

            var duration  = parseFloat(duration_minutes) + parseFloat(duration_hours);

            if(duration > 0){

                ride_minute_fare = parseFloat(orderData.service.perMinuteCharge) * parseFloat(duration);

            }
        }


        if(orderData.hasOwnProperty('totalHoldingCharges') && orderData.totalHoldingCharges != "" && orderData.totalHoldingCharges != null){

            holding_charges = parseFloat(orderData.totalHoldingCharges);

        }


        if(parseFloat(nightCharge) > 0){
            
            if (isTimeBetween(orderTime, startNightTime, endNightTime)) {

                    var night_charges =  parseFloat(nightCharge);

                    basic_fare = parseFloat(basic_fare) * parseFloat(night_charges);
                    total_km_charges = parseFloat(total_km_charges) * parseFloat(night_charges);
                    ride_minute_fare = parseFloat(ride_minute_fare) * parseFloat(night_charges);
                    holding_charges = parseFloat(holding_charges) * parseFloat(night_charges);


            } 
        }
        
        order_total_fare = order_total_fare + parseFloat(basic_fare) + parseFloat(total_km_charges) + parseFloat(ride_minute_fare) + parseFloat(holding_charges);

        total_amount=order_total_fare;

        discount_amount=0;

        if(orderData.hasOwnProperty('coupon')&&orderData.coupon.enable) {

            var data=orderData.coupon;

            if(data.type=="fix") {

                discount_amount=data.amount;

            } else {
        
                discount_amount=(data.amount*total_amount)/100;

            }

            discount_amount=parseFloat(discount_amount).toFixed(decimal_degits);

            total_amount-=parseFloat(discount_amount);

        }


        total_item_amount = total_amount;

        if(orderData.hasOwnProperty('taxList')&&orderData.taxList.length>0) {

            var taxData=orderData.taxList;

            var tax_amount_total=parseFloat(0);

            for(var i=0;i<taxData.length;i++) {

                var data=taxData[i];

                if(data.enable) {

                    var tax_amount=data.tax;

                    if(data.type=="percentage") {

                        tax_amount=(data.tax*total_amount)/100;

                    } 
                    tax_amount=parseFloat(tax_amount).toFixed(decimal_degits);

                    tax_amount_total=parseFloat(tax_amount_total)+parseFloat(tax_amount);
                }

            }

            total_amount+=parseFloat(tax_amount_total);
        }

        if(isNaN(parseFloat(total_amount))) {
            total_amount = amount;
        }
        return total_amount;

    }



    $("#is_active").click(function() {

        $("#orderTable .is_open").prop('checked',$(this).prop('checked'));



    });



    $("#deleteAll").click(async function() {

        if($('#orderTable .is_open:checked').length) {

            if(confirm("{{trans('lang.selected_delete_alert')}}")) {

                jQuery("#overlay").show();

                $('#orderTable .is_open:checked').each(async function() {

                    var dataId=$(this).attr('dataId');

                    await database.collection('orders').doc(dataId).delete().then(function() {



                        window.location.reload();



                    });



                });



            }

        } else {

            alert("{{trans('lang.select_delete_alert')}}");

        }

    });



    $(document).on("click","a[name='ride-delete']",function(e) {



        var id=this.id;

        jQuery("#overlay").show();

        database.collection('orders').doc(id).delete().then(function(result) {



            window.location.href='{{ url()->current() }}';

        });



    });



</script>



@endsection