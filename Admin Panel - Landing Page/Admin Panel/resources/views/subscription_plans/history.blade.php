@extends('layouts.app')


@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.subscription_history')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.subscription_history_table')}}</li>
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
                            <span class="icon mr-3"><img src="{{ asset('images/subscription.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.subscription_history_plural')}}</h3>
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.subscription_history_table')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.subscription_history_table_text')}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                </div>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="subscriptionHistoryTable"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                        <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                        class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                            class="do_not_delete" href="javascript:void(0)"><i
                                                                class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label>
                                        </th>
                                            <th>{{trans('lang.driver')}}</th>
                                            <th>{{trans('lang.plan_name')}}</th>
                                            <th>{{trans('lang.plan_type')}}</th>
                                            <th>{{trans('lang.plan_expires_at')}}</th>
                                            <th>{{trans('lang.purchase_date')}}</th>
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

<script>

    var database=firebase.firestore();
    var intRegex=/^\d+$/;
    var floatRegex=/^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
    var refData=database.collection('subscription_history')
   
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

        jQuery("#overlay").show();

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
                const orderableColumns=['','owner','planName','subscription_plan.type','paidDate','createdAt'];

                const orderByField=orderableColumns[orderColumnIndex];
                if(searchValue.length>=3||searchValue.length===0) {
                    $('#overlay').show();
                }
                await refData.orderBy('createdAt','desc').get().then(async function(querySnapshot) {
                    if(querySnapshot.empty) {
                        $('.total_count').text(0);
                        console.error("No data found in Firestore.");
                        $('#overlay').hide(); // Hide loader
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
                        childData.owner='';
                        if(childData.user_id) {
                            childData.owner=await planUsedUser(childData.user_id);
                        }
                        
                        childData.id=doc.id;
                        childData.planName=childData.subscription_plan.name;
                                                    var date='';
                        var time='';
                        if(childData.expiry_date?.toDate) {
                            try {
                                date=childData.expiry_date.toDate().toDateString();
                                time=childData.expiry_date.toDate().toLocaleTimeString('en-US');
                            } catch(err) {
                                console.error('Error processing expiry_date:',err);
                            }
                        }
                        childData.paidDate=date+' '+time;
                        if(childData.createdAt?.toDate) {
                            try {
                                purchasedate=childData.createdAt.toDate().toDateString();
                                purchasetime=childData.createdAt.toDate().toLocaleTimeString('en-US');
                            } catch(err) {
                                console.error('Error processing expiry_date:',err);
                            }
                        }
                        childData.purchaseDate=purchasedate+' '+purchasetime;

                        if(searchValue) {

                            if(
                                (childData.owner&&(childData.owner).toString().toLowerCase().includes(searchValue))||
                                (childData.subscription_plan.name&&(childData.subscription_plan.name).toString().toLowerCase().includes(searchValue))||
                                (childData.subscription_plan.type&&(childData.subscription_plan.type).toString().toLowerCase().includes(searchValue))||
                                (childData.paidDate && childData.paidDate.toString().toLowerCase().indexOf(searchValue) > -1) ||
                                (childData.purchaseDate && childData.purchaseDate.toString().toLowerCase().indexOf(searchValue) > -1)
                            ) {
                                filteredRecords.push(childData);
                            }
                        } else {
                            filteredRecords.push(childData);
                        }
                    }));

                    filteredRecords.sort((a,b) => {
                        let aValue=a[orderByField]? a[orderByField].toString().toLowerCase().trim():'';
                        let bValue=b[orderByField]? b[orderByField].toString().toLowerCase().trim():'';
                        if(orderByField==='expiry_date' || orderByField==='createdAt') {
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
                    $('.total_count').text(totalRecords);

                    const paginatedRecords=filteredRecords.slice(start,start+length);

                    await Promise.all(paginatedRecords.map(async (childData) => {
                        var getData=await buildHTML(childData);
                        records.push(getData);
                    }));
                    $('#overlay').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords,
                        recordsFiltered: totalRecords,
                        data: records
                    });
                }).catch(function(error) {
                    console.error("Error fetching data from Firestore:",error);
                    $('#overlay').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        data: []
                    });
                });
            },
            order: [5,'desc'],
            columnDefs: [
                {
                    targets: [0,3,4],
                    orderable: false,
                },
                {
                    targets: 5,
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
                $('#overlay').show();
                table.search(searchValue).draw();
            } else if(searchValue.length===0) {
                $('#overlay').show();
                table.search('').draw();
            }
        },300));

    });
    async function planUsedUser(id) {
        var planUsedUser='';
        if(id!=null&&id!=''&&id!=undefined) {
            await database.collection('driver_users').doc(id).get().then(async function(snapshot) {
                if(snapshot&&snapshot.data()) {
                    var data=snapshot.data();
                    planUsedUser=data.fullName;
                }
            });
        }
        return planUsedUser;
    }


    async function buildHTML(val) {
        var html=[];
        var id = val.id;
        html.push('<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +
        'for="is_open_' + id + '" ></label></td>');
        
            var route='{{route("drivers.view", ":id")}}';
            route=route.replace(':id',val.user_id);
            var route1='{{route("subscription-plans.save", ":id")}}';
            route1=route1.replace(':id',val.subscription_plan.id);
            html.push('<a href="'+route+'" class="redirecttopage" >'+val.owner+'</a>');
        

        html.push('<a href="'+route1+'" class="redirecttopage" >'+val.subscription_plan.name+'</a>');

        if(val.subscription_plan && val.subscription_plan.type ) {
            if(val.subscription_plan.type=='free'){
                html.push('<span class="badge badge-success">'+val.subscription_plan.type.toUpperCase()+'</span>');
            }else{
                html.push('<span class="badge badge-danger">'+val.subscription_plan.type.toUpperCase()+'</span>');
            }
        } else {
            html.push('<span class="badge">-</span>');
        }
        if(val.hasOwnProperty('expiry_date')) {
            if(val.expiry_date!=null&&val.expiry_date!=''&&val.expiry_date!='-1') {
                var date=val.expiry_date.toDate().toDateString();
                var time=val.expiry_date.toDate().toLocaleTimeString('en-US');
                html.push('<span class="dt-time">'+date+' '+time+'</span>');

            } else {
                html.push("{{trans('lang.unlimited')}}")
            }
        } else {
            html.push('');
        }
        if(val.hasOwnProperty('createdAt')) {
            if(val.createdAt!=null&&val.createdAt!=''&&val.createdAt!='-1') {
                var date=val.createdAt.toDate().toDateString();
                var time=val.createdAt.toDate().toLocaleTimeString('en-US');
                html.push('<span class="dt-time">'+date+' '+time+'</span>');

            } else {
                html.push("{{trans('lang.unlimited')}}")
            }
        } else {
            html.push('');
        }
        return html;
    }
    $("#is_active").click(function () {
        $("#subscriptionHistoryTable .is_open").prop('checked', $(this).prop('checked'));
    });
    $("#deleteAll").click(function () {
        if ($('#subscriptionHistoryTable .is_open:checked').length) {
            if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#overlay").show();
                $('#subscriptionHistoryTable .is_open:checked').each(function () {
                    var dataId = $(this).attr('dataId');
                    deleteDocumentWithImage('subscription_history', dataId)
                    .then(() => {
                        window.location.reload();
                    })
                    .catch((error) => {
                        console.error('Error deleting document or store data:', error);
                    });
                });
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });



</script>

@endsection