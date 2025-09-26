@extends('layouts.app')


@section('content')

<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.payout_request')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.payout_request')}}</li>
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
                        {{--<div class="d-flex top-title-left align-self-center">
                            <span class="icon mr-3"><img src="{{ asset('images/payment.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.payout_request')}}</h3>
                            <span class="counter ml-3 total_count"></span>
                        </div>--}}
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.payout_request')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.payout_request_table_text')}}</p>
                            </div>
                            
                        </div>
                       
                        <div class="card-body">

                            <div id="data-table_processing" class="dataTables_processing panel panel-default"
                                style="display: none;">{{trans('lang.processing')}}
                            </div>


                            <div class="table-responsive m-t-10">
                                <table id="payoutTable"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">

                                    <thead>


                                        <tr>
                                            <?php if ($id == "") { ?>
                                                <th>{{ trans('lang.driver')}}</th>

                                            <?php } ?>

                                            <th>{{trans('lang.amount')}}</th>
                                            <th>{{trans('lang.note')}}</th>
                                            <th>{{trans('lang.drivers_payout_paid_date')}}</th>
                                            <th>{{trans('lang.status')}}</th>
                                            <th>{{trans('lang.admin_note')}}</th>
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

<div class="modal fade" id="bankdetailsModal" tabindex="-1" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered location_modal">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title locationModalTitle">{{trans('lang.bankdetails')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <form class="">

                    <div class="form-row">

                        <input type="hidden" name="driverId" id="driverId">

                        <div class="form-group row">

                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{
                                    trans('lang.bank_name')}}</label>
                                <div class="col-12">
                                    <input type="text" name="bank_name" class="form-control" id="bankName">
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{
                                    trans('lang.branch_name')}}</label>
                                <div class="col-12">
                                    <input type="text" name="branch_name" class="form-control" id="branchName">
                                </div>
                            </div>


                            <div class="form-group row width-100">
                                <label class="col-4 control-label">{{
                                    trans('lang.holder_name')}}</label>
                                <div class="col-12">
                                    <input type="text" name="holer_name" class="form-control" id="holderName">
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{
                                    trans('lang.account_number')}}</label>
                                <div class="col-12">
                                    <input type="text" name="account_number" class="form-control" id="accountNumber">
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-12 control-label">{{
                                    trans('lang.other_information')}}</label>
                                <div class="col-12">
                                    <input type="text" name="other_information" class="form-control" id="otherDetails">
                                </div>
                            </div>

                        </div>

                    </div>

                </form>

                <div class="modal-footer">

                    {{--
                    <button type="button" class="btn btn-primary" data-dismiss="modal"
                        aria-label="Close">{{trans('close')}}</a>
                    </button>
                    --}}
                    <a class="btn btn-primary acceptBtn" href="javascript:void(0)">{{trans("lang.accept")}}</a>
                    <a name="reject-request" class="btn btn-primary rejectBtn" href="javascript:void(0)"
                        data-toggle="modal" data-target="#reasonModal">{{trans("lang.reject")}}</a>

                </div>
            </div>
        </div>

    </div>

</div>
<div class="modal fade" id="reasonModal" tabindex="-1" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered location_modal">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title locationModalTitle">{{trans('lang.reason')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <form class="">

                    <div class="form-row">

                        <div class="form-group row">

                            <div class="form-group row width-100">

                                <div class="col-12">
                                    <label class="col-12 control-label">{{trans('lang.reason_for_rejection')}}</label>
                                    <input type="text" name="reason" class="form-control" id="reason">
                                    <input type="text" name="ride_id" class="form-control" id="ride_id" hidden>
                                    <input type="text" name="driver_id" class="form-control" id="driver_id" hidden>
                                    <input type="text" name="price_add" class="form-control" id="price_add" hidden>
                                </div>
                            </div>

                        </div>

                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary save_reason">{{trans('submit')}}</a>
                        </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"
                            aria-label="Close">{{trans('close')}}</a>
                        </button>


                    </div>
                </form>
            </div>
        </div>

    </div>

</div>


@endsection


@section('scripts')

<script>
    var driverCheckConfirm = "{{ trans('lang.driver_check_confirm') }}";

    var database = firebase.firestore();

    if ('<?php echo $id ?>' != "") {
        var refData = database.collection('withdrawal_history').where('userId', '==', '<?php echo $id ?>');

    } else {
        var refData = database.collection('withdrawal_history');


    }

    var ref = refData.orderBy('createdDate', 'desc');
    var append_list = '';
    var currentCurrency = '';

    var currencyAtRight = false;
    var decimal_degits = 0;

    var refCurrency = database.collection('currency').where('enable', '==', true);

    refCurrency.get().then(async function (snapshots) {

        var currencyData = snapshots.docs[0].data();

        currentCurrency = currencyData.symbol;

        currencyAtRight = currencyData.symbolAtRight;

        currencyAtRight = currencyData.symbolAtRight;

        if (currencyData.decimalDigits) {
            decimal_degits = currencyData.decimalDigits;
        }


    });
    var driverUser = database.collection('driver_users');

    $(document).ready(function () {

        jQuery("#overlay").show();

        const table = $('#payoutTable').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: async function (data, callback, settings) {
                const start = data.start;
                const length = data.length;
                const searchValue = data.search.value.toLowerCase();
                const orderColumnIndex = data.order[0].column;
                const orderDirection = data.order[0].dir;
                const orderableColumns = ['driverName', 'amount', 'note', 'createdDate', 'paymentStatus', 'adminNote', '']; // Ensure this matches the actual column names
                const orderByField = orderableColumns[orderColumnIndex]; // Adjust the index to match your table

                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#overlay').show();
                }

                ref.get().then(async function (querySnapshot) {

                    if (querySnapshot.empty) {
                        $(".total_count").text(0);
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

                    let records = [];
                    let filteredRecords = [];
                    let driverNames = {};

                    const driverDocs = await driverUser.get();
                    driverDocs.forEach(doc => {
                        driverNames[doc.id] = doc.data().fullName;
                    });
                   
                    await Promise.all(querySnapshot.docs.map(async (doc) => {

                        childData = doc.data();

                        childData.id = doc.id; // Ensure the document ID is included in the data 

                        childData.driverName = driverNames[childData.userId] || "";


                        if (searchValue) {
                            var date = '';
                            var time = '';
                            if (childData.hasOwnProperty("createdDate")) {
                                try {
                                    date = childData.createdDate.toDate().toDateString();
                                    time = childData.createdDate.toDate().toLocaleTimeString('en-US');
                                } catch (err) {
                                }
                            }
                            var createdAt = date + ' ' + time;

                            if (
                                (childData.driverName && childData.driverName.toLowerCase().toString().includes(searchValue)) ||
                                (childData.note && childData.note.toLowerCase().toString().includes(searchValue)) ||
                                (childData.paymentStatus && childData.paymentStatus.toLowerCase().toString().includes(searchValue)) ||
                                (createdAt && createdAt.toString().toLowerCase().indexOf(searchValue) > -1) ||
                                (childData.adminNote && childData.adminNote.toLowerCase().toString().includes(searchValue)) ||
                                (childData.amount && childData.amount.toString().includes(searchValue))

                            ) {
                                filteredRecords.push(childData);
                            }
                        } else {
                            filteredRecords.push(childData);
                        }
                    }));
                    filteredRecords.sort((a, b) => {
                        let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                        let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : '';
                        if (orderByField === 'createdDate') {
                            aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                            bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                        }
                        if (orderByField === 'amount') {
                            aValue = a[orderByField] ? parseFloat(a[orderByField]) : 0.0;
                            bValue = b[orderByField] ? parseFloat(b[orderByField]) : 0.0;
                        }
                        if (orderDirection === 'asc') {
                            return (aValue > bValue) ? 1 : -1;
                        } else {
                            return (aValue < bValue) ? 1 : -1;
                        }
                    });


                    const totalRecords = filteredRecords.length;
                    $(".total_count").text(totalRecords);

                    const paginatedRecords = filteredRecords.slice(start, start + length);
                    await Promise.all(paginatedRecords.map(async (childData) => {

                        var getData = await buildHTML(childData);
                        records.push(getData);
                    }));

                    $('#overlay').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords, // Total number of records in Firestore
                        recordsFiltered: totalRecords, // Number of records after filtering (if any)
                        data: records // The actual data to display in the table
                    });
                }).catch(function (error) {
                    console.error("Error fetching data from Firestore:", error);
                    $('#overlay').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        data: [] // No data due to error
                    });
                });
            },
            order: [[3, 'desc']],
            columnDefs: [
                {
                    targets: 3,
                    type: 'date',
                    render: function (data) {
                        return data;
                    }
                },
                { orderable: false, targets: [6] },
            ],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}",
                "processing": "" // Remove default loader
            },

        });



    });

    $(document.body).on('click', '.redirecttopage', function () {
        var url = $(this).attr('data-url');
        window.location.href = url;
    });


    function buildHTML(val) {
        var html = [];

        var routedriver = '{{route("drivers.view",":id")}}';

        routedriver = routedriver.replace(':id', val.userId);

        if ('<?php echo $id ?>' == "") {
            
            if(val.driverName!=null && val.driverName!='' && val.driverName!=undefined){
                html.push('<a href=' + routedriver + '>'+ val.driverName +'</a>');
            }else{
                 html.push('{{trans("lang.unknown_user")}}');
            }
             

        }

        if (currencyAtRight) {
            html.push(parseFloat(val.amount).toFixed(decimal_degits) + '' + currentCurrency);

        } else {

            html.push(currentCurrency + '' + parseFloat(val.amount).toFixed(decimal_degits));

        }


        var date = val.createdDate.toDate().toDateString();

        var time = val.createdDate.toDate().toLocaleTimeString('en-US');

        html.push(val.note);

        html.push('<span class="dt-time">' + date + ' ' + time + '</span>');

        if (val.paymentStatus) {
            if (val.paymentStatus == "approved") {
                html.push('<td><span  class="badge badge-success py-2 px-3">Approved</span></td>');
            } else if (val.paymentStatus == "pending") {
                html.push('<td><span class="badge badge-warning py-2 px-3">Pending</span></td>');
            } else if (val.paymentStatus == "rejected") {
                html.push('<td><span class="badge badge-danger py-2 px-3">Rejected</span></td>');
            }

        } else {
            html.push('');

        }

        if (val.adminNote) {
            html.push(val.adminNote);

        } else {
            html.push('');
        }
        console.log(val.driverName)
        if(val.driverName!='' && val.driverName!=null && val.driverName!=undefined){
        var actionHtml = '';
        if (val.paymentStatus && val.paymentStatus == "pending") {
            actionHtml += '<span class="action-btn"><div class="user_not_found_' + val.userId + '">';
            actionHtml += '<a id="' + val.id + '" data-price="' + val.amount + '" name="driver_view" data-auth="' + val.userId + '" href="javascript:void(0)" data-toggle="modal" data-target="#bankdetailsModal"><i class="mdi mdi-eye"></i></a>';
            actionHtml += '<a id="' + val.id + '" name="driver_check" data-price="' + val.amount + '" data-auth="' + val.userId + '" href="javascript:void(0)" data-toggle="modal" data-target="#bankdetailsModal"><i class="mdi mdi-check" style="color:green"></i></a>';
            actionHtml += '<a id="' + val.id + '" data-price="' + val.amount + '" name="reject-request" data-auth="' + val.userId + '" href="javascript:void(0)" data-toggle="modal" data-target="#reasonModal"><i class="mdi mdi-close" ></i></a>';
            actionHtml += '</div></span>';
        }
        html.push(actionHtml);
    }else{
        html.push('');
    }
        return html;
    }

    async function getDriverBankDetails() {
        var driverId = $('#driverId').val();

        await database.collection('bank_details').where("userId", "==", driverId).get().then(async function (snapshotss) {

            if (snapshotss.docs[0]) {
                var user_data = snapshotss.docs[0].data();
                if (user_data) {

                    $('#bankName').val(user_data.bankName);
                    $('#branchName').val(user_data.branchName);
                    $('#holderName').val(user_data.holderName);
                    $('#accountNumber').val(user_data.accountNumber);
                    $('#otherDetails').val(user_data.otherInformation);

                }

            }
        });

    }

    $(document).on("click", "a[name='driver_view']", function (e) {
        $('#bankName').val("");
        $('#branchName').val("");
        $('#holderName').val("");
        $('#accountNumber').val("");
        $('#otherDetails').val("");

        var id = this.id;
        var auth = $(this).attr('data-auth');
        var price = $(this).attr('data-price');
        $('#driverId').val(auth);
        $('.acceptBtn').attr('data-auth', auth);
        $('.acceptBtn').attr('data-price', price);
        $('.acceptBtn').attr('id', id);
        $('.rejectBtn').attr('data-auth', auth);
        $('.rejectBtn').attr('data-price', price);
        $('.rejectBtn').attr('id', id);
        getDriverBankDetails();

    });

    
    $(document).on("click", "a[name='driver_check']", function (e) {
        var id = this.id;
        var fullname = $(this).attr('data-name');
        var auth = $(this).attr('data-auth');
        var price = $(this).attr('data-price');
        $('.acceptBtn').attr('data-auth', auth);
        $('.acceptBtn').attr('data-price', price);
        $('.acceptBtn').attr('id', id);
        $('.rejectBtn').attr('data-auth', auth);
        $('.rejectBtn').attr('data-price', price);
        $('.rejectBtn').attr('id', id);
        $('#driverId').val(auth);


        getDriverBankDetails();


    });

    $(document).on("click", "a[name='reject-request']", function (e) {
        $('#bankdetailsModal').modal('hide');
        var id = this.id;
        var auth = $(this).attr('data-auth');
        var priceadd = $(this).attr('data-price');
        $('#ride_id').val(id);
        $('#driver_id').val(auth);
        $('#price_add').val(priceadd);
    })

    $('.save_reason').click(function () {

        var id = $('#ride_id').val();
        var auth = $('#driver_id').val();
        var priceadd = $('#price_add').val();
        var reason = $('#reason').val();
        jQuery("#overlay").show().html("{{trans('lang.saving')}}");
        database.collection('driver_users').where("id", "==", auth).get().then(function (resultDriver) {
            if (resultDriver.docs.length) {
                var driver = resultDriver.docs[0].data();
                var walletAmount = 0;
                if (isNaN(driver.walletAmount) || driver.walletAmount == undefined) {
                    walletAmount = 0;
                } else {
                    walletAmount = driver.walletAmount;
                }

                price = parseFloat(walletAmount) + parseFloat(priceadd);
                price = price.toString();
                database.collection('withdrawal_history').doc(id).update({
                    'paymentStatus': 'rejected',
                    'adminNote': reason
                }).then(function (result) {
                    database.collection('driver_users').doc(driver.id).update({ 'walletAmount': price }).then(function (result) {
                        window.location.href = '{{ url()->current() }}';
                    });
                });

            } else {
                alert('driver not found.');
            }
        });
    });

    $('.acceptBtn').click(function () {

        var id = this.id;
        var auth = $(this).attr('data-auth');
        jQuery("#overlay").show().html("{{trans('lang.saving')}}");
        database.collection('withdrawal_history').doc(id).update({ 'paymentStatus': 'approved' }).then(function (result) {
            window.location.href = '{{ url()->current() }}';
        });
    });


</script>


@endsection