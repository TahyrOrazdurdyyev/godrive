@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.deleted_coupon_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.coupon_table')}}</li>
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
                            <span class="icon mr-3"><img src="{{ asset('images/coupon.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.deleted_coupon_plural')}}</h3>
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.coupon_table')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.deleted_coupon_table_text')}}</p>
                            </div>

                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <!-- add coupon create button code -->
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
                                            <th>{{trans('lang.coupon_title')}}</th>
                                            <th>{{trans('lang.coupon_code')}}</th>
                                            <th>{{trans('lang.coupon_type')}}</th>
                                            <th>{{trans('lang.coupon_discount')}}</th>
                                            <th>{{trans('lang.coupon_validity')}}</th>
                                            <th>{{trans('lang.reinstate')}}</th>
                                            <th>{{trans('lang.permanent_delete')}}</th>
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
    var offest=1;
    var pagesize=10;
    var end=null;
    var endarray=[];
    var start=null;
    var user_number=[];
    var setLanguageCode=getCookie('setLanguage');
    var defaultLanguageCode=getCookie('defaultLanguage');

    var ref=database.collection('coupon').where('isDeleted','==',true).orderBy('title');

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
    var append_list='';

    var deleteMsg="{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg="{{trans('lang.selected_delete_alert')}}";

    $(document).ready(function() {
        $(document.body).on('click','.redirecttopage',function() {
            var url=$(this).attr('data-url');
            window.location.href=url;
        });
        var inx=parseInt(offest)*parseInt(pagesize);
        jQuery("#overlay").show();
        append_list=document.getElementById('append_list1');
        append_list.innerHTML='';
        ref.get().then(async function(snapshots) {
            html='';
            $(".total_count").text(snapshots.docs.length);
            if(snapshots.docs.length>0) {
                html=await buildHTML(snapshots);
            }

            jQuery("#overlay").hide();
            if(html!='') {
                append_list.innerHTML=html;
                start=snapshots.docs[snapshots.docs.length-1];
                endarray.push(snapshots.docs[0]);

                if(snapshots.docs.length<pagesize) {
                    jQuery("#data-table_paginate").hide();
                }
            }

            $('#taxTable').DataTable({
                order: [[0,'asc']],
                columnDefs: [
                    {
                        targets: 4,
                        type: 'date',
                        render: function(data) {
                            return data;
                        }
                    },
                    {orderable: false,targets: [5,6]},
                ],
                "language": {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}"
                }
            });
        });

        $('#selected_search').on('change',function() {
            selected_search=$('#selected_search').val();
            if(selected_search=="type") {
                $('#search').hide();
                $('#search_tax_type').show();
            } else {
                $('#search').show();
                $('#search_tax_type').hide();
            }
        })
    });


    async function buildHTML(snapshots) {


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
        var route1='{{route("coupons.save", ":id")}}';
        route1=route1.replace(':id',id);
        var trroute1='';
        trroute1=trroute1.replace(':id',id);
        var title='';
        if(Array.isArray(val.title)) {
            var foundItem=val.title.find(item => item.type===setLanguageCode);
            if(foundItem&&foundItem.title!='') {
                title=foundItem.title;
            } else {
                var foundItem=val.title.find(item => item.type===defaultLanguageCode);
                if(foundItem&&foundItem.title!='') {
                    title=foundItem.title;
                } else {
                    var foundItem=val.title.find(item => item.type==='en');
                    title=foundItem.title;

                }
            }

        }

        html=html+'<td>'+title+'</td>';
        html=html+'<td>'+val.code+'</td>';
        var type=val.type;
        html=html+'<td>'+(type.charAt(0).toUpperCase())+type.slice(1)+'</td>';

        if(val.type=="fix") {

            var amount=parseFloat(val.amount);
            if(symbolAtRight) {
                html+='<td>'+amount.toFixed(decimal_degits)+currentCurrency+'</td>';

            } else {
                html+='<td>'+currentCurrency+amount.toFixed(decimal_degits)+'</td>';

            }
        } else {
            html=html+'<td>'+val.amount+'%</td>';

        }

        var date=new Date(val.validity.seconds*1000);
        var dateFormat=date.toDateString()+" , "+date.toLocaleString('en-US',{
            hour: 'numeric',
            minute: 'numeric',
            hour12: true
        });
        html=html+'<td class="dt-time">'+dateFormat+'</td>';
        html=html+'<td class="action-btn"><a id="'+val.id+'" class="revoke-coupon" name="revoke-data" href="javascript:void(0)"><i class="mdi mdi-undo"></i></a></td>';
        html=html+'<td class="action-btn"><a id="'+val.id+'" class="permanent-delete" name="permanent-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a></td>';
        html=html+'</tr>';
        return html;
    }

    $("#is_active").click(function() {
        $("#taxTable .is_open").prop('checked',$(this).prop('checked'));
    });

    function prev() {
        if(endarray.length==1) {
            return false;
        }
        end=endarray[endarray.length-2];

        if(end!=undefined||end!=null) {

            if(jQuery("#selected_search").val()=='title'&&jQuery("#search").val().trim()!='') {
                listener=ref.orderBy('title').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').startAt(end).get();

            } else if(jQuery("#selected_search").val()=='code'&&jQuery("#search").val().trim()!='') {

                listener=ref.orderBy('code').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').startAt(end).get();

            } else if(jQuery("#selected_search").val()=='type'&&jQuery("#search_tax_type").val().trim()!='') {

                listener=ref.orderBy('type').limit(pagesize).startAt(jQuery("#search_tax_type").val()).endAt(jQuery("#search_tax_type").val()+'\uf8ff').startAt(end).get();

            } else {
                listener=ref.startAt(end).limit(pagesize).get();
            }

            listener.then((snapshots) => {
                html='';
                html=buildHTML(snapshots);

                if(html!='') {
                    append_list.innerHTML=html;
                    start=snapshots.docs[snapshots.docs.length-1];
                    endarray.splice(endarray.indexOf(endarray[endarray.length-1]),1);
                }
            });
        }
    }

    function next() {

        if(start!=undefined||start!=null) {

            if(jQuery("#selected_search").val()=='title'&&jQuery("#search").val().trim()!='') {

                listener=ref.orderBy('title').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').startAfter(start).get();

            } else if(jQuery("#selected_search").val()=='code'&&jQuery("#search").val().trim()!='') {

                listener=ref.orderBy('code').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').startAfter(start).get();

            } else if(jQuery("#selected_search").val()=='type'&&jQuery("#search_tax_type").val().trim()!='') {

                listener=ref.orderBy('type').limit(pagesize).startAt(jQuery("#search_tax_type").val()).endAt(jQuery("#search_tax_type").val()+'\uf8ff').startAfter(start).get();

            } else {
                listener=ref.startAfter(start).limit(pagesize).get();
            }
            listener.then((snapshots) => {

                html='';
                html=buildHTML(snapshots);

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
        jQuery('#search_tax_type').val("").trigger('change');
        searchtext();
    }

    $('#search').keypress(function(e) {
        if(e.which==13) {
            $('.search_button').click();
        }
    });

    function searchtext() {

        append_list.innerHTML='';

        if(jQuery("#selected_search").val()=='title'&&jQuery("#search").val().trim()!='') {

            wherequery=ref.orderBy('title').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').get();

        } else if(jQuery("#selected_search").val()=='code'&&jQuery("#search").val().trim()!='') {

            wherequery=ref.orderBy('code').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').get();

        } else if(jQuery("#selected_search").val()=='type'&&jQuery("#search_tax_type").val().trim()!='') {

            wherequery=ref.orderBy('type').limit(pagesize).startAt(jQuery("#search_tax_type").val()).endAt(jQuery("#search_tax_type").val()+'\uf8ff').get();

        } else {

            wherequery=ref.limit(pagesize).get();
        }

        wherequery.then((snapshots) => {

            html='';
            html=buildHTML(snapshots);

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

    $(document).on("click","a[name='revoke-data']",function(e) {
        var id=$(this).attr('id');
        jQuery("#overlay").show();
        database.collection('coupon').doc(id).update({
            'isDeleted': false,
        }).then(function(result) {
            window.location.href='{{ url()->current() }}';
        });
    });

    $(document).on("click","a[name='permanent-delete']",function(e) {
        var id=$(this).attr('id');
        jQuery("#overlay").show();
        database.collection('coupon').doc(id).delete().then(function(result) {
            window.location.href='{{ url()->current() }}';
        });
    });
</script>

@endsection