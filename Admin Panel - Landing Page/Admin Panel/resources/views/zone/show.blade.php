@extends('layouts.app')

@section('content')
    <div class="page-wrapper pb-5">
        <div class="row page-titles">

            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.parking_orders')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                    <li class="breadcrumb-item"><a
                            href="{!! route('parking-bookings') !!}">{{trans('lang.parking_orders')}}</a>
                    </li>

                    <li class="breadcrumb-item">{{trans('lang.booking_show')}}</li>
                </ol>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card-body p-0 no_data_found">
                <div id="data-table_processing" class="dataTables_processing panel panel-default"
                     style="display: none;">
                    {{trans('lang.processing')}}
                </div>

                <div class="col-md-12">
                    <div class="print-top non-printable mt-3">
                        <div class="text-right print-btn non-printable">
                            <button type="button" class="fa fa-print non-printable"
                                    onclick="printDiv('printableArea')"></button>
                        </div>
                    </div>

                    <hr class="non-printable">
                </div>

                <div class="row restaurant_payout_create" style="max-width:100%;" role="tabpanel" id="printableArea">

                    <div class="tab-content">

                        <div role="tabpanel" class="tab-pane active" id="category_information">
                            <div class="order_detail" id="order_detail">

                                <div class="order_detail-top mb-3 printableArea">
                                    <div class="row">


                                        <div class="order_edit-genrl col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-white">
                                                    <h3>{{trans('lang.general_details')}}</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="order_detail-top-box">
                                                        <div class="form-group row widt-100 gendetail-col">
                                                            <label
                                                                class="col-12 control-label"><strong>{{trans('lang.ride_id')}}
                                                                    : </strong><span id="ride_id"></span></label>

                                                        </div>

                                                        <div class="form-group row widt-100 gendetail-col">
                                                            <label
                                                                class="col-12 control-label"><strong>{{trans('lang.date_created')}}
                                                                    : </strong><span id="createdAt"></span></label>

                                                        </div>

                                                        <div
                                                            class="form-group row widt-100 gendetail-col payment_method">
                                                            <label
                                                                class="col-12 control-label"><strong>{{trans('lang.payment_status')}}
                                                                    : </strong><span
                                                                    id="payment_status"></span></label>

                                                        </div>


                                                        <div
                                                            class="form-group row widt-100 gendetail-col payment_method">
                                                            <label
                                                                class="col-12 control-label"><strong>{{trans('lang.payment_methods')}}
                                                                    : </strong><span
                                                                    id="payment_method"></span></label>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class=" order_edit-genrl col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-white">
                                                    <h3>{{ trans('lang.billing_details')}}</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="address order_detail-top-box user-details">
                                                        <p><strong>{{trans('lang.name')}}: </strong><span
                                                                id="billing_name" class="d-flex"></span></p>

                                                        <p><strong>{{trans('lang.email')}}:</strong>
                                                            <span id="billing_email"></span>
                                                        </p>
                                                        <p><strong>{{trans('lang.phone')}}:</strong>
                                                            <span id="billing_phone"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                                <div class="row ride-map-dredetail">
                                    <div class="col-md-7" id="ride-map-dredetail">
                                        <div class="card">
                                            <div class="box card-body p-0">
                                                <div class="box-header bb-2 card-header bg-white">
                                                    <h3 class="box-title">{{trans('lang.map_view')}}</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div id="map" style="height:400px">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 ml-auto">
                                        <div class="card">
                                            <div class="box card-body p-0">
                                                <div class="box-header bb-2 card-header bg-white">
                                                    <h3 class="box-title">{{trans('lang.parking_details')}}</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="address order_detail-top-box user-details">
                                                        <p><strong>{{trans('lang.name')}}: </strong><span
                                                                id="parking_name" class="d-flex"></span></p>

                                                        <p><strong>{{trans('lang.address')}}:</strong>
                                                            <span id="from-ride"></span>
                                                        </p>
                                                        <p><strong>{{trans('lang.booking_date')}}:</strong>
                                                            <span id="booking_date"></span>
                                                        </p>
                                                        <p><strong>{{trans('lang.booking_time')}}:</strong>
                                                            <span id="booking_time"></span>
                                                        </p>
                                                        <p><strong>{{trans('lang.duration')}}:</strong>
                                                            <span id="duration"></span>
                                                        </p>
                                                        <p><strong>{{trans('lang.slot')}}:</strong>
                                                            <span id="parking-slot"></span>
                                                        </p>
                                                        <p><strong>{{trans('lang.ride_status')}}:</strong>
                                                            <span id="order_status"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="row ride-loct-pricedet printableArea">
                                    <div class="col-md-7 ">

                                        <div class="card">
                                            <div class="order_addre-edit ">
                                                <div class="card-header bg-white">
                                                    <h3>{{ trans('lang.price_detail')}}</h3>
                                                </div>
                                                <div class="card-body price_detail">
                                                    <div class="order-deta-btm-right">
                                                        <div class="order-totals-items pt-0">
                                                            <div class="row">
                                                                <div class="col-md-12 ml-auto">
                                                                    <div class="table-responsive bk-summary-table">
                                                                        <table class="order-totals">

                                                                            <tbody id="order_products_total">

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
                                    <div class="col-md-5">
                                        <div class="card">
                                            <div class="box-header bb-2 card-header bg-white">
                                                <h3>{{trans("lang.vehicle_information")}}</h3>
                                            </div>
                                            <div class="card-body">

                                                <div class="order_detail-review mt-0">
                                                    <div class="rental-review">
                                                        <div class="review-inner">
                                                            <div class="address order_detail-top-box user-details">
                                                                <p><strong>{{trans('lang.brand')}}: </strong><span
                                                                        id="brand" class="d-flex"></span></p>

                                                                <p><strong>{{trans('lang.model')}}:</strong>
                                                                    <span id="model"></span>
                                                                </p>
                                                                <p><strong>{{trans('lang.vehicle_number')}}:</strong>
                                                                    <span id="vehicle_number"></span>
                                                                </p>

                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="box-header bb-2 card-header bg-white">
                                                <h3>{{trans("lang.ride_reviews")}}</h3>
                                            </div>
                                            <div class="card-body">

                                                <div class="order_detail-review mt-0">
                                                    <div class="rental-review">
                                                        <div class="review-inner">

                                                            <div id="customers_rating_and_review">

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
                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <a href="{!! route('parking-bookings') !!}" class="btn btn-default"><i
                        class="fa fa-undo"></i>{{trans('lang.cancel')}}
                </a>

            </div>

        </div>

    </div>

    <link rel="stylesheet" href="{{ asset('css/leaflet/leaflet.css') }}" />

@endsection

<style>
#map {
  position: relative;
  z-index: 0; /* Make sure the map is rendered correctly */
}

</style>

@section('scripts')
<script src="{{ asset('js/leaflet/leaflet.js')}}"></script>
<script src="{{ asset('js/jquery-masking.js') }}"></script>

    <script type="text/javascript">

        var database = firebase.firestore();

        var refCurrency = database.collection('currency').where('enable', '==', true).limit('1');

        var decimal_degits = 0;
        var symbolAtRight = false;
        var currentCurrency = '';
        var placeholderImage = "{{ asset('/images/default_user.png') }}";
        refCurrency.get().then(async function (snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            decimal_degits = currencyData.decimalDigits;

            if (currencyData.symbolAtRight) {
                symbolAtRight = true;
            }
        });

        var refData = database.collection('booked_parking_order').where('id', '==', '{{$id}}');


        $(document).ready(async function () {


            $('.bookings_menu').addClass('active');
            getRideDeatils();
        });

        async function getRideDeatils() {
            jQuery("#overlay").show();

            await refData.get().then(async function (snapshots) {

                if (snapshots.docs[0]) {
                    var orders = snapshots.docs[0].data();
                    getCutomerReview(orders);
                    var user_id = orders.userId;
                    if (orders.createdAt) {
                        var date1 = orders.createdAt.toDate().toDateString();
                        var date = new Date(date1);
                        var dd = String(date.getDate()).padStart(2, '0');
                        var mm = String(date.getMonth() + 1).padStart(2, '0');
                        var yyyy = date.getFullYear();
                        var createdAt_val = yyyy + '-' + mm + '-' + dd;
                        var time = orders.createdAt.toDate().toLocaleTimeString('en-US');

                        $('#createdAt').text(createdAt_val + ' ' + time);
                    }
                    $('#ride_id').html(orders.id);
                    if (orders.paymentCompleted) {
                        $('#payment_status').html('<span class="badge badge-success py-2 px-3">Paid</span>');
                    } else {
                        $('#payment_status').html('<span class="badge badge-warning py-2 px-3">Not Paid</span>');
                    }
                    if (orders.paymentType) {
                        getPaymentImage(orders.paymentType);
                    } else {
                        $('#payment_method').html("-");
                    }

                    if (orders.status == "placed") {
                        $('#order_status').html('<span class="badge badge-primary py-2 px-3">{{trans("lang.placed")}}</span>');
                    } else if (orders.status == "canceled") {
                        $('#order_status').html('<span class="badge badge-danger py-2 px-3">{{trans("lang.canceled")}}</span>');
                    } else if (orders.status == "onGoing") {
                        $('#order_status').html('<span class="badge badge-info py-2 px-3">{{trans("lang.ongoing")}}</span>');
                    } else if (orders.status == "completed") {
                        $('#order_status').html('<span class="badge badge-success py-2 px-3">{{trans("lang.completed")}}</span>');
                    } else {
                        $('#order_status').html('<span class="badge badge-primary py-2 px-3">' + orders.status + '</span>');

                    }
                    $('#duration').html(orders.duration + " {{trans('lang.hours')}}");
                    if (orders.hasOwnProperty("bookingDate")) {
                        date = orders.bookingDate.toDate().toDateString();
                        $('#booking_date').html(date);
                    }
                    if (orders.hasOwnProperty("bookingStartTime") && orders.hasOwnProperty('bookingEndTime')) {
                        startTime = orders.bookingStartTime.toDate().toLocaleTimeString('en-US');
                        endTime = orders.bookingEndTime.toDate().toLocaleTimeString('en-US');
                        time = startTime + " - " + endTime;
                        $('#booking_time').html(time);
                    }
                    if (orders.hasOwnProperty('userVehicle')) {
                        $('#brand').text(orders.userVehicle.vehicleBrand['name']);
                        $('#model').text(orders.userVehicle.vehicleModel['name']);
                        $('#vehicle_number').text(orders.userVehicle.vehicleNumber);

                    }
                    var user_info = getUserInfo(user_id);

                    var order_details = getOrderDetails(orders);
                } else {
                    $('.no_data_found').html('<p align="center">{{trans("lang.no_data_found")}}</p>');
                }

            });

            jQuery("#overlay").hide();

        }

        async function getCutomerReview(orders) {
            var refCustomerReview = database.collection('review').where('id', "==", orders.id);
            refCustomerReview.get().then(async function (userreviewsnapshot) {
                var reviewHTML = '';
                reviewHTML = buildCustomerRatingsHTML(orders, userreviewsnapshot);

                if (userreviewsnapshot.docs.length > 0) {
                    jQuery("#customers_rating_and_review").append(reviewHTML);
                } else {
                    jQuery("#customers_rating_and_review").html('<h5 class="no-review">No Reviews Found</h5>');
                }
            });
        }


        async function getPaymentImage(paymentType) {

            await database.collection('settings').doc('payment').get().then(async function (snapshots) {
                var payment = snapshots.data();
                var payamentData = Object.values(payment).filter((data) => data.name == paymentType).map((filterData) => filterData);

                $('#payment_method').html('<img src="' + payamentData[0].image + '" alt="image">');

            });
        }


        async function getUserInfo(userId) {

            await database.collection('users').where('id', '==', userId).get().then(async function (snapshots) {
                if (snapshots.docs.length > 0) {
                    var user = snapshots.docs[0].data();
                    if (user.profilePic != '' && user.profilePic != null) {
                        profile = '<span class="user-img"><img class="rounded" style="width:50px" src="' + user.profilePic + '" alt="Image"></span>';
                    } else {
                        profile = '<span class="user-img"><img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="Image"></span>';
                    }
                    var userHtml = '<div class="drove-det"><span class="drv-name">' + user.fullName + '</span></div>';
                    $('#billing_name').html(profile + userHtml);
                    $('#billing_email').html(shortEmail(user.email));
                    $('#billing_phone').html("+" + shortNumber(user.countryCode.slice(1),user.phoneNumber));
                } else {
                    $(".user-details").html('<p>{{trans("lang.unknown_user")}}</p>');
                }

            });
        }

        function getOrderDetails(orderData) {

            $('#from-ride').html(orderData.parkingDetails.address);
            var parkingHtml = '';
            if (orderData.parkingDetails.image != '' && orderData.parkingDetails.image != null) {
                parkingHtml += '<span class="user-img"><img class="rounded" style="width:50px" src="' + orderData.parkingDetails.image + '" alt="Image"></span>';
            } else {
                parkingHtml += '<span class="user-img"><img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="Image"></span>';
            }
            var rating = 0;
            if (orderData.parkingDetails.hasOwnProperty('reviewCount') && orderData.parkingDetails.reviewCount && orderData.parkingDetails.reviewCount != "0.0" && orderData.parkingDetails.reviewCount != null && orderData.parkingDetails.hasOwnProperty('reviewSum') && orderData.parkingDetails.reviewSum && orderData.parkingDetails.reviewSum != "0.0" && orderData.parkingDetails.reviewSum != null) {

                rating = (parseFloat(orderData.parkingDetails.reviewSum) / parseFloat(orderData.parkingDetails.reviewCount));
            }
            parkingHtml += '<div class="drove-det"><span class="drv-name">' + orderData.parkingDetails.name + '</span>';
            parkingHtml += '<span class="badge badge-warning text-white ml-auto"><i class="fa fa-star"></i>' + rating.toFixed(1) + '</span></div>';
            $('#parking_name').html(parkingHtml);
            $('#parking-slot').html(orderData.parkingSlotId);
            var order_amount_html = '';

            var amount = 0;

            var total_amount = 0;

            var transactionId = getTransactionId(orderData.id);

            order_amount_html += '<tr class="transaction_id_' + orderData.id + '"></tr>';

            order_amount_html += '<tr><td class="seprater" colspan="2"><hr><span>{{trans("lang.ride_price")}}</span></td></tr>';

            if (orderData.subTotal) {
                amount = parseFloat(orderData.subTotal);

            }

            if (symbolAtRight) {
                order_amount_html += '<tr class="final-rate"><td class="label">{{trans("lang.final_rate")}}</td><td>' + amount.toFixed(decimal_degits) + currentCurrency + '</td></tr>';

            } else {
                order_amount_html += '<tr class="final-rate"><td class="label">{{trans("lang.final_rate")}}</td><td>' + currentCurrency + amount.toFixed(decimal_degits) + '</td></tr>';
            }
            total_amount = amount;
            discount_amount = 0;
            if (orderData.hasOwnProperty('coupon') && orderData.coupon.enable) {
                order_amount_html += '<tr><td class="seprater" colspan="2"><hr><span>{{trans('lang.discount_calculation')}}</span></td></tr>';
                var data = orderData.coupon;

                order_amount_html += '';

                var discount_html = '<tr><td class="label">' + data.title + '(';

                if (data.type == "fix") {
                    discount_amount = data.amount;
                    if (symbolAtRight) {
                        discount_html += parseFloat(data.amount).toFixed(decimal_degits) + currentCurrency;

                    } else {
                        discount_html += currentCurrency + parseFloat(data.amount).toFixed(decimal_degits);

                    }

                } else {
                    discount_html += data.amount + '%';
                    discount_amount = (data.amount * amount) / 100;
                }

                discount_amount = parseFloat(discount_amount).toFixed(decimal_degits);

                discount_html += ')</td>';

                if (symbolAtRight) {
                    discount_html += '<td><span style="color:red">(-' + discount_amount + currentCurrency + ')</span></td>';

                } else {
                    discount_html += '<td><span style="color:red">(-' + currentCurrency + discount_amount + ')</span></td>';
                }

                discount_html += '</tr>';

                total_amount -= parseFloat(discount_amount);

                order_amount_html += discount_html;

            }
            var calculateCommssionAmount = total_amount;
            if (orderData.hasOwnProperty('taxList') && orderData.taxList.length > 0) {
                order_amount_html += '<tr><td class="seprater" colspan="2"><hr><span>{{trans("lang.tax_calculation")}}</span></td></tr>';
                var taxData = orderData.taxList;
                order_amount_html += '';
                var tax_amount_total = parseFloat(0);
                for (var i = 0; i < taxData.length; i++) {

                    var data = taxData[i];

                    if (data.enable) {

                        var tax_html = '<tr><td class="label">' + data.title + '(';

                        var tax_amount = data.tax;

                        if (data.type == "percentage") {
                            tax_html += data.tax + '%';
                            tax_amount = (data.tax * total_amount) / 100;
                        } else {
                            if (symbolAtRight) {
                                tax_html += parseFloat(data.tax).toFixed(decimal_degits) + currentCurrency;

                            } else {
                                tax_html += currentCurrency + parseFloat(data.tax).toFixed(decimal_degits);

                            }
                        }

                        tax_amount = parseFloat(tax_amount).toFixed(decimal_degits);
                        tax_amount_total = parseFloat(tax_amount_total) + parseFloat(tax_amount);
                        tax_html += ')</td>';

                        if (symbolAtRight) {
                            tax_html += '<td>' + tax_amount + currentCurrency + '</td></tr>';

                        } else {
                            tax_html += '<td>' + currentCurrency + tax_amount + '</td></tr>';

                        }


                    }

                    order_amount_html += tax_html;
                }
                total_amount += parseFloat(tax_amount_total);

            }

            var payableAmount = total_amount;
            if (orderData.hasOwnProperty('adminCommission') && orderData.adminCommission.enable) {
                order_amount_html += '<tr><td class="seprater" colspan="2"><hr><span>{{trans("lang.commission")}}</span></td></tr>';
                var data = orderData.adminCommission;
                order_amount_html += '';

                var commission_html = '<tr><td class="label">{{trans("lang.admin_commission")}}(';

                if (data.type == "fix") {
                    commission_amount = data.amount;
                    if (symbolAtRight) {
                        commission_html += parseFloat(data.amount).toFixed(decimal_degits) + currentCurrency;

                    } else {
                        commission_html += currentCurrency + parseFloat(data.amount).toFixed(decimal_degits);

                    }

                } else {
                    commission_html += data.amount + '%';
                    commission_amount = (data.amount * calculateCommssionAmount) / 100;
                }

                commission_amount = parseFloat(commission_amount).toFixed(decimal_degits);

                commission_html += ')</td>';
                if (symbolAtRight) {
                    commission_html += '<td ><span style="color:red">(-' + commission_amount + currentCurrency + ')</span></td>';

                } else {
                    commission_html += '<td ><span style="color:red">(-' + currentCurrency + commission_amount + ')</span></td>';
                }

                commission_html += '</tr>';


                order_amount_html += commission_html;

                if (commission_amount) {
                    total_amount = total_amount - commission_amount;

                }

            }
            order_amount_html += '<tr><td class="seprater" colspan="2"><hr></td></tr>';
            total_amount = total_amount.toFixed(decimal_degits);
            payableAmount = payableAmount.toFixed(decimal_degits);

            if (symbolAtRight) {
                total_amount = total_amount + currentCurrency;
                payableAmount = payableAmount + currentCurrency;
            } else {
                total_amount = currentCurrency + total_amount;
                payableAmount = currentCurrency + payableAmount;
            }
            order_amount_html += '<tr class="grand-total"><td class="label"><strong>{{trans("lang.payable_amount")}}</strong></td><td><strong>' + payableAmount + '</strong></td></tr>';

            order_amount_html += '<tr><td class="label"><strong>{{trans("lang.total")}}</strong><span> ({{trans("lang.after_admin_commission")}}) </span></td><td><strong>' + total_amount + '</strong></td></tr>';
            if (orderData.status == 'canceled') {
                order_amount_html += '<tr><td class="label"><strong>{{trans("lang.note")}}</strong></td><td><span>your payment is refunded to your wallet</span></td></tr>';
            }
            $('#order_products_total').html(order_amount_html);
            setTimeout(() => {
                setMap(orderData);
            }, 3000);


        }

        async function getTransactionId(orderId) {

            var transactionId = '';

            await database.collection('wallet_transaction').where('transactionId', '==', orderId).get().then(async function (snapshots) {

                if (snapshots.docs.length > 0) {
                    var transactionData = snapshots.docs[0].data();
                    transactionId = transactionData.id.substring(0, 7);
                    $('.transaction_id_' + orderId).html('<td class="label"><strong>{{trans("lang.transaction_id")}}</strong></td><td><strong>' + transactionData.id + '</strong></td>');
                }
            });
            return transactionId;
        }

        function setMap(orders) {

            if (mapType == "OFFLINE" ) { 
                var map = L.map('map').setView([
                    orders.parkingDetails.location.latitude,
                    orders.parkingDetails.location.longitude
                ], 10);

                // Add OSM tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                // Add marker
                var marker = L.marker([
                    orders.parkingDetails.location.latitude,
                    orders.parkingDetails.location.longitude
                ], { draggable: true }).addTo(map);

                // Add popup with parking address
                marker.bindPopup(orders.parkingDetails.address).openPopup();

                return; // Exit function to prevent execution of Google Maps code
            }

            var map;
            var marker;

            var myLatlng = new google.maps.LatLng(orders.parkingDetails.location.latitude, orders.parkingDetails.location.longitude);
            var geocoder = new google.maps.Geocoder();
            var infowindow = new google.maps.InfoWindow();

            var mapOptions = {
                zoom: 10,
                center: myLatlng,
                streetViewControl: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("map"), mapOptions);

            marker = new google.maps.Marker({
                map: map,
                position: myLatlng,
                draggable: true
            });

            google.maps.event.addListener(marker, 'click', function () {
                infowindow.setContent(orders.parkingDetails.address);
                infowindow.open(map, marker);
            });

            let directionsService = new google.maps.DirectionsService();

            let directionsRenderer = new google.maps.DirectionsRenderer();

            directionsRenderer.setOptions({
                polylineOptions: {
                    strokeColor: '#000000'
                }
            });

            directionsRenderer.setMap(map);

        }

        function buildCustomerRatingsHTML(vendorOrder, userreviewsnapshot) {
            var allreviewdata = [];
            var reviewhtml = '';

            userreviewsnapshot.docs.forEach((listval) => {
                var reviewDatas = listval.data();
                reviewDatas.id = listval.id;
                allreviewdata.push(reviewDatas);
            });

            reviewhtml += '<div class="user-ratings">';
            allreviewdata.forEach((listval) => {
                var val = listval;

                rating = val.rating;
                reviewhtml = reviewhtml + '<div class="reviews-members py-3 border mb-3"><div class="media">';
                reviewhtml = reviewhtml + '<div class="media-body d-flex"><div class="reviews-members-header"><div class="star-rating"><div class="d-inline-block" style="font-size: 14px;">';
                reviewhtml = reviewhtml + ' <ul class="rating" data-rating="' + parseFloat(rating) + '">';
                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                reviewhtml = reviewhtml + '<li class="rating__item"></li>';
                reviewhtml = reviewhtml + '</ul>';
                reviewhtml = reviewhtml + '</div></div>';
                reviewhtml = reviewhtml + '</div>';
                reviewhtml = reviewhtml + '<div class="review-date ml-auto">';
                if (val.date != null && val.date != "") {
                    var review_date = val.date.toDate().toLocaleDateString('en', {
                        year: "numeric",
                        month: "short",
                        day: "numeric"
                    });
                    reviewhtml = reviewhtml + '<span>' + review_date + '</span>';
                }
                reviewhtml = reviewhtml + '</div>';


                reviewhtml = reviewhtml + '</div></div><div class="reviews-members-body w-100"><p class="mb-2">' + val.comment + '</div>';
                reviewhtml += '</div>';

                reviewhtml += '</div>';
            });


            reviewhtml += '</div>';

            return reviewhtml;
        }


        function printDiv(divName) {

            var css = '@page { size: portrait; }',
                head = document.head || document.getElementsByTagName('head')[0],
                style = document.createElement('style');

            style.type = 'text/css';
            style.media = 'print';

            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }

            head.appendChild(style);

            document.getElementById('ride-map-dredetail').innerHTML = '';

            var printContents = document.getElementById(divName).innerHTML;

            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            document.getElementById('ride-map-dredetail').innerHTML = '<div class="card">\n' +
                '                                            <div class="box card-body p-0">\n' +
                '                                                <div class="box-header bb-2 card-header bg-white">\n' +
                '                                                    <h3 class="box-title">{{trans('lang.map_view')}}</h3>\n' +
                '                                                </div>\n' +
                '                                                <div class="card-body">\n' +
                '                                                    <div id="map" style="height:300px">\n' +
                '                                                    </div>\n' +
                '                                                </div>\n' +
                '                                            </div>\n' +
                '                                        </div>';
            getRideDeatils();

        }


    </script>


@endsection
