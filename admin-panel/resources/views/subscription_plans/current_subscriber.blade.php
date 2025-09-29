@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor plan_title">{{trans('lang.current_subscriber_list_of')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{{url('/subscription-plans')}}">{{trans('lang.subscription_plans')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.current_subscriber_list')}}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li class="nav-item">
                                <a class="nav-link active" href="{!! url()->current() !!}"><i
                                        class="fa fa-list mr-2"></i>{{trans('lang.current_subscriber_list')}}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive m-t-10">
                            <table id="subscriptionHistoryTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{ trans('lang.driver')}}</th>
                                        <th>{{trans('lang.plan_name')}}</th>
                                        <th>{{trans('lang.plan_type')}}</th>
                                        <th>{{trans('lang.booking_limit')}}</th>
                                        <th>{{trans('lang.plan_expires_at')}}</th>
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
@endsection
@section('scripts')
<script>
    var database=firebase.firestore();
    var intRegex=/^\d+$/;
    var floatRegex=/^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
    var planId='{{$id}}';
    database.collection('subscription_plans').where('id','==',planId).get().then(async function(snapshot) {
        var data=snapshot.docs[0].data();
        $('.plan_title').html('{{trans("lang.current_subscriber_list_of")}} - '+data.name);
    })
    var subscriberListRef=database.collection('driver_users').where('subscriptionPlanId','==',planId);
    var currentCurrency='';
    var currencyAtRight=false;
    var decimal_degits=0;
    var refCurrency=database.collection('currency').where('enable','==',true);
    refCurrency.get().then(async function(snapshots) {
        var currencyData=snapshots.docs[0].data();
        currentCurrency=currencyData.symbol;
        currencyAtRight=currencyData.symbolAtRight;
        if(currencyData.decimal_degits) {
            decimal_degits=currencyData.decimalDigits;
        }
    });
    var append_list='';
    $(document).ready(function() {
        $(document.body).on('click','.redirecttopage',function() {
            var url=$(this).attr('data-url');
            window.location.href=url;
        });
        jQuery("#data-table_processing").show();
        const table=$('#subscriptionHistoryTable').DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: async function(data,callback,settings) {
                const start=data.start;
                const length=data.length;
                const searchValue=data.search.value.toLowerCase();
                const orderColumnIndex=data.order[0].column;
                const orderDirection=data.order[0].dir;
                const orderableColumns=['driver','subscription_plan.name','subscription_plan.type','subscription_plan.bookingLimit','subscriptionExpiryDate'];
                const orderByField=orderableColumns[orderColumnIndex];
                $('#data-table_processing').show();
                await subscriberListRef.orderBy('subscriptionExpiryDate','desc').get().then(async function(querySnapshot) {
                    if(querySnapshot.empty) {
                        console.error("No data found in Firestore.");
                        $('#data-table_processing').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: [] // No data
                        });
                        return;
                    }
                    let records=[];
                    let filteredRecords=[];
                    await Promise.all(querySnapshot.docs.map(async (doc) => {
                        let childData=doc.data();
                        childData.driver=childData.fullName;
                        childData.id=doc.id;
                        if(childData.hasOwnProperty('subscriptionTotalOrders')&&childData.subscriptionTotalOrders!=null&&childData.subscriptionTotalOrders!='') {
                                childData.bookingCreated=childData.subscriptionTotalOrders;
                        } else {
                            childData.bookingCreated=0;
                        }
                        if(searchValue) {
                            var date='';
                            var time='';
                            if(childData.subscriptionExpiryDate?.toDate) {
                                try {
                                    date=childData.subscriptionExpiryDate.toDate().toDateString();
                                    time=childData.subscriptionExpiryDate.toDate().toLocaleTimeString('en-US');
                                } catch(err) {
                                    console.error('Error processing expire_date:',err);
                                }
                            }
                            var paidDate=date+' '+time;
                            if(
                                (childData.driver&&(childData.driver).toString().toLowerCase().includes(searchValue))||
                                (childData.subscription_plan.name&&(childData.subscription_plan.name).toLowerCase().includes(searchValue))||
                                (childData.subscription_plan.type&&(childData.subscription_plan.type).toLowerCase().includes(searchValue))||
                                (childData.subscription_plan.bookingLimit.includes(searchValue))||
                                (childData.subscription_plan.bookingCreated.includes(searchValue))||
                                (childData.subscriptionExpiryDate&&(childData.subscriptionExpiryDate).includes(searchValue))
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
                        if(orderByField==='subscriptionExpiryDate') {
                            try {
                                aValue=a[orderByField]&&a[orderByField].toDate? new Date(a[orderByField].toDate()).getTime():0;
                                bValue=b[orderByField]&&a[orderByField].toDate? new Date(b[orderByField].toDate()).getTime():0;
                            } catch(err) {
                            }
                        }
                        if(orderDirection==='asc') {
                            return (aValue>bValue)? 1:-1;
                        } else {
                            return (aValue<bValue)? 1:-1;
                        }
                    });
                    const totalRecords=filteredRecords.length;
                    const paginatedRecords=filteredRecords.slice(start,start+length);
                    await Promise.all(paginatedRecords.map(async (childData) => {
                        var getData=await buildHTML(childData);
                        records.push(getData);
                    }));
                    $('#data-table_processing').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords,
                        recordsFiltered: totalRecords,
                        data: records
                    });
                }).catch(function(error) {
                    console.error("Error fetching data from Firestore:",error);
                    $('#data-table_processing').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        data: []
                    });
                });
            },
            order: [4,'asc'],
            columnDefs: [
                {
                    targets: 4,
                    type: 'date',
                    render: function(data) {
                        return data;
                    }
                },
            ],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}",
                "processing": ""
            },
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
                $('#data-table_processing').show();
                table.search(searchValue).draw();
            } else if(searchValue.length===0) {
                $('#data-table_processing').show();
                table.search('').draw();
            }
        },300));
    });
        async function buildHTML(val) {
            var html=[];
            var route='{{route("drivers.view", ":id")}}';
            route=route.replace(':id',val.id);
            html.push('<a href="'+route+'" class="redirecttopage" >'+val.driver+'</a>');
            html.push('<span>'+val.subscription_plan.name+'</span>');
            if(val.subscription_plan.type=='free') {
                html.push('<span class="badge badge-success">'+val.subscription_plan.type.toUpperCase()+'</span>');
            } else {
                html.push('<span class="badge badge-danger">'+val.subscription_plan.type.toUpperCase()+'</span>');
            }
            if(val.subscription_plan.bookingLimit=='-1') {
                html.push('<span>{{trans("lang.unlimited")}}</span>')
            } else {
                var available=parseInt(val.subscription_plan.bookingLimit)-parseInt(val.bookingCreated);
                html.push('<span>{{trans("lang.total")}} :'+val.subscription_plan.bookingLimit+' </span><br><span>{{trans("lang.available")}} :'+available+' </span>')
            }
            if(val.subscriptionExpiryDate!=null && val.subscriptionExpiryDate!='') {
                var date=val.subscriptionExpiryDate.toDate().toDateString();
                var time=val.subscriptionExpiryDate.toDate().toLocaleTimeString('en-US');
                html.push('<span class="dt-time">'+date+' '+time+'</span>');
            } else {
                html.push('<span class="dt-time">{{trans("lang.unlimited")}}</span>');
            }
            return html;
        }
      
</script>
@endsection