@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor restaurantTitle">{{trans('lang.driver_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('drivers') !!}">{{trans('lang.driver_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.driver_details')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-12 col-md-6">
                                <div class="card card-block p-card">
                                    <div class="profile-box">
                                        <div class="profile-card rounded">
                                            <img src="{{asset('images/default_user.png')}}" alt="profile-bg" class="avatar-100 d-block mx-auto img-fluid mb-3 avatar-rounded user-image">
                                            <h3 class="font-600 text-white text-center user-name"></h3>
                                            <div class="font-600 text-white text-center mb-3 user-total-ratings"></div>
                                            <h3 class="font-600 text-white text-center mb-5 user-wallet"></h3>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#addWalletModal" class="ml-3 mb-2 text-white add-wallate btn btn-sm btn-success">
                                                <i class="fa fa-plus"></i>{{trans("lang.add_wallet_amount")}}
                                            </a>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#changeSubscriptionModal" class="ml-3 mb-2 text-white change-plan btn btn-sm btn-success">
                                                {{trans("lang.change_subscription_plan")}}
                                            </a>
                                        </div>
                                        <div class="pro-content rounded">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="p-icon mr-3"><i class="fa fa-envelope"></i></div>
                                                <p class="mb-0 eml user-email"></p>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="p-icon mr-3"><i class="fa fa-phone"></i></div>
                                                <p class="mb-0 user-phone"></p>
                                            </div>
                                        </div>

                                        <div class="personal-detail">
                                            <h3>Vehicle Information</h3>
                                            <div class="table-responsive user-list-table">
                                                <table class="table mb-0">
                                                    <tbody id="vehicle_information">
                                                        <tr>
                                                            <td class="py-2 px-0"><span class="font-weight-bold w-100">{{trans("lang.vehicle_number")}}:</span></td>
                                                            <td class="py-2 px-0"><span class="num-plat vehicle_number"></span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-2 px-0"><span class="font-weight-bold w-100">{{trans("lang.vehicle_type")}}:</span></td>
                                                            <td class="py-2 px-0 vehicle_type"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="personal-detail">
                                            <h3>{{trans('lang.active_subscription_plan')}}</h3>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#updateLimitModal" class="btn-primary btn rounded-full update-limit text-white btn btn-sm btn-success">
                                                {{trans('lang.update_plan_limit')}}
                                            </a>
                                            <div class="table-responsive user-list-table active_subscription_div">
                                                <table class="table mb-0">
                                                    <tbody id="subscription-plan-detail">
                                                        <tr>
                                                            <td class="py-2 px-0"><span class="font-weight-bold w-100">{{trans('lang.plan_name')}}:</span></td>
                                                            <td class="py-2 px-0 plan_name"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-2 px-0"><span class="font-weight-bold w-100">{{trans("lang.plan_type")}}:</span></td>
                                                            <td class="py-2 px-0 plan_type"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-2 px-0"><span class="font-weight-bold w-100">{{trans("lang.plan_expires_at")}}:</span></td>
                                                            <td class="py-2 px-0"><span class="plan_expire_at"></span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-2 px-0"><span class="font-weight-bold w-100">{{trans("lang.booking_limit")}}:</span></td>
                                                            <td class="py-2 px-0 booking_limit"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-2 px-0"><span class="font-weight-bold w-100">{{trans("lang.available_features")}}</span></td>
                                                        </tr>
                                                        <tr><td colspan="2"><p class="plan-points"></p></td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="personal-detail">
                                            <h3>Rules</h3>
                                            <div class="rules-list">
                                                <ul id="driver_rules"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card card-block card-stretch">
                                    <div class="card-header bg-white">
                                        <ul class="nav nav-pills mb-3" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link ride_list active" data-toggle="pill" href="#ride_list" role="tab">{{trans('lang.ride_list')}}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link intercity_ride_list" data-toggle="pill" href="#intercity_ride_list" role="tab">{{trans('lang.intercity_ride_list')}}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link wallet_transactions" data-toggle="pill" href="#wallet_transactions" role="tab">{{trans('lang.wallet_transactions')}}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link payout_request" data-toggle="pill" href="#payout_request" role="tab">{{trans('lang.payout_request')}}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link subscription_history" data-toggle="pill" href="#subscription_history" role="tab">{{trans('lang.subscription_history')}}</a>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="card-body">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="ride_list" role="tabpanel">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-valign-middle" id="rideListTable">
                                                        <thead class="table-color-heading">
                                                            <tr class="text-secondary">
                                                                <th scope="col">{{trans('lang.ride_id')}}</th>
                                                                <th scope="col">{{trans('lang.customer')}}</th>
                                                                <th scope="col">{{trans('lang.date')}}</th>
                                                                <th scope="col">{{trans('lang.payment_method')}}</th>
                                                                <th scope="col">{{trans('lang.payment_status')}}</th>
                                                                <th scope="col">{{trans('lang.total_amount')}}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="ride_list_rows"></tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="tab-pane" id="intercity_ride_list" role="tabpanel">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-valign-middle" id="intercityRideListTable">
                                                        <thead class="table-color-heading">
                                                            <tr class="text-secondary">
                                                                <th scope="col">{{trans('lang.ride_id')}}</th>
                                                                <th scope="col">{{trans('lang.customer')}}</th>
                                                                <th scope="col">{{trans('lang.date')}}</th>
                                                                <th scope="col">{{trans('lang.payment_method')}}</th>
                                                                <th scope="col">{{trans('lang.payment_status')}}</th>
                                                                <th scope="col">{{trans('lang.total_amount')}}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="intercity_ride_list_rows"></tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="tab-pane" id="wallet_transactions" role="tabpanel">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-valign-middle" id="transactionListTable">
                                                        <thead class="table-color-heading">
                                                            <tr class="text-secondary">
                                                                <th scope="col">{{trans('lang.id')}}</th>
                                                                <th scope="col">{{trans('lang.payment_method')}}</th>
                                                                <th scope="col">{{trans('lang.order_type')}}</th>
                                                                <th scope="col">{{trans('lang.txn_id')}}</th>
                                                                <th scope="col">{{trans('lang.date')}}</th>
                                                                <th scope="col">{{trans('lang.note')}}</th>
                                                                <th scope="col">{{trans('lang.total_amount')}}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="wallet_transactions_rows"></tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="tab-pane" id="payout_request" role="tabpanel">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-valign-middle" id="payoutRequestTable">
                                                        <thead class="table-color-heading">
                                                            <tr class="text-secondary">
                                                                <th>{{trans('lang.amount')}}</th>
                                                                <th>{{trans('lang.note')}}</th>
                                                                <th>{{trans('lang.drivers_payout_paid_date')}}</th>
                                                                <th>{{trans('lang.status')}}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="payout_request_rows"></tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="tab-pane" id="subscription_history" role="tabpanel">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-valign-middle" id="subscriptionHistoryTable">
                                                        <thead class="table-color-heading">
                                                            <tr class="text-secondary">
                                                                <th>{{trans('lang.plan_name')}}</th>
                                                                <th>{{trans('lang.plan_type')}}</th>
                                                                <th>{{trans('lang.plan_expires_at')}}</th>
                                                                <th>{{trans('lang.purchase_date')}}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="subscription_history_rows"></tbody>
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
            <div class="form-group col-12 text-center btm-btn doc-footer">
                <a href="{!! route('drivers') !!}" class="btn btn-default"><i class="fa fa-undo cancel-btn"></i>{{trans('lang.cancel')}}</a>
            </div>
        </div>
    </div>
</div>

<!-- Add Wallet Modal -->
<div class="modal fade" id="addWalletModal" tabindex="-1" role="dialog" aria-labelledby="addWalletModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWalletModalLabel">{{trans('lang.add_wallet_amount')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addWalletForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="wallet_amount">{{trans('lang.amount')}}</label>
                        <input type="number" class="form-control" id="wallet_amount" name="amount" required min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="wallet_note">{{trans('lang.note')}}</label>
                        <textarea class="form-control" id="wallet_note" name="note" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans('lang.close')}}</button>
                    <button type="submit" class="btn btn-primary">{{trans('lang.add')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Subscription Plan Modal -->
<div class="modal fade" id="changeSubscriptionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{trans('lang.subscription_plans')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" id="default-plan"></div>
            </div>
        </div>
    </div>
</div>

<!-- Checkout Subscription Modal -->
<div class="modal fade" id="checkoutSubscriptionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{trans('lang.confirm_subscription')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="plan-details">
                    <h4 class="plan-title"></h4>
                    <p class="plan-description"></p>
                    <div class="plan-price"></div>
                    <div class="plan-points"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans('lang.cancel')}}</button>
                <button type="button" class="btn btn-primary" id="confirmSubscriptionBtn">{{trans('lang.confirm')}}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">

    var id = '{{$id}}';
    var decimal_degits = 0;
    var symbolAtRight = false;
    var currentCurrency = '';
    var commisionModel = false;
    var AdminCommission = '';
    var subscriptionModel = false;
    var driverData = null;
    var settingsData = null;

    $(document).ready(function() {
        // Load settings first
        loadSettings();
        
        // Load driver data
        loadDriverData();
    });

    function loadSettings() {
        $.ajax({
            url: '/api/settings',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    settingsData = response.data;
                    
                    // Currency settings
                    if (settingsData.currency) {
                        currentCurrency = settingsData.currency.symbol || '';
                        symbolAtRight = settingsData.currency.symbol_at_right || false;
                        decimal_degits = settingsData.currency.decimal_digits || 2;
                    }
                    
                    // Commission settings
                    if (settingsData.admin_commission) {
                        if (settingsData.admin_commission.type === 'percentage') {
                            AdminCommission = settingsData.admin_commission.value + '%';
                            commisionModel = true;
                        } else {
                            AdminCommission = formatPrice(settingsData.admin_commission.value);
                            commisionModel = true;
                        }
                    }
                    
                    // Subscription model
                    if (settingsData.global_settings && settingsData.global_settings.subscription_model) {
                        subscriptionModel = settingsData.global_settings.subscription_model === 'true' || settingsData.global_settings.subscription_model === true;
                    }
                }
            },
            error: function(xhr) {
                console.error('Error loading settings:', xhr);
            }
        });
    }

    function loadDriverData() {
        $.ajax({
            url: '/api/drivers/' + id + '/data',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    driverData = response.driver;
                    displayDriverInfo(driverData);
                    
                    // Load related data
                    loadDriverOrders();
                    loadDriverReviews();
                    loadDriverSubscriptionHistory();
                    loadDriverWalletTransactions();
                    loadDriverWithdrawals();
                }
            },
            error: function(xhr) {
                console.error('Error loading driver data:', xhr);
                alert('Error loading driver information');
            }
        });
    }

    function displayDriverInfo(driver) {
        // Basic info
        $('.user-name').text(driver.full_name || '');
        $('.user-email').text(driver.email || '');
        
        // Phone with country code
        var phone = '';
        if (driver.country_code) {
            phone = '+' + driver.country_code.replace('+', '');
        }
        if (driver.phone) {
            phone += driver.phone;
        }
        $('.user-phone').text(phone);
        
        // Profile picture
        if (driver.profile_pic) {
            $('.user-image').attr('src', driver.profile_pic);
        }
        
        // Wallet
        var walletAmount = driver.wallet_amount || 0;
        $('.user-wallet').text('{{trans("lang.wallet_Balance")}} : ' + formatPrice(walletAmount));
        
        // Rating
        var rating = driver.rating || 0;
        var reviewsCount = driver.reviews_count || 0;
        $('.user-total-ratings').html('<span class="badge badge-warning text-white dr-review"><i class="fa fa-star"></i>' + parseFloat(rating).toFixed(1) + '</span>');
        
        // Vehicle Information
        if (driver.vehicle_number || driver.vehicle_type) {
            $('.vehicle_number').text(driver.vehicle_number || '');
            $('.vehicle_type').text(driver.vehicle_type || '');
            $('.vehicle_color').text(driver.vehicle_color || '');
            $('.seats').text(driver.vehicle_seats || '');
            
            // Zone names
            if (driver.zone_names) {
                $('.zone_name').text(driver.zone_names);
            }
        } else {
            $('#vehicle_information').html('<tr><td><span class="font-weight-bold w-100">{{trans("lang.no_vehicle_information")}}</span></td></tr>');
        }
        
        // Active Subscription Plan
        if (driver.subscription_plan) {
            var plan = driver.subscription_plan;
            $('.plan_name').text(plan.title);
            $('.plan_type').text(formatPrice(plan.amount));
            
            if (driver.subscription_expiry_date) {
                $('.plan_expire_at').text(formatDate(driver.subscription_expiry_date));
            }
            
            if (plan.total_orders !== null && plan.total_orders !== undefined) {
                $('.booking_limit').text(plan.total_orders);
            }
            
            // Plan points
            if (plan.plan_points && plan.plan_points.length > 0) {
                var html = '<ul>';
                plan.plan_points.forEach(function(point) {
                    html += '<li>' + point + '</li>';
                });
                html += '</ul>';
                $('.plan-points').html(html);
            }
        } else {
            $('.active_subscription_div').html('{{trans("lang.no_active_subscription_plan_found")}}');
        }
    }

    function formatPrice(amount) {
        var price = parseFloat(amount).toFixed(decimal_degits);
        return symbolAtRight ? price + currentCurrency : currentCurrency + price;
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }

    function loadDriverOrders() {
        $.ajax({
            url: '/api/drivers/' + id + '/orders',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    displayOrders(response.orders);
                    displayIntercityOrders(response.intercity_orders);
                }
            },
            error: function(xhr) {
                console.error('Error loading orders:', xhr);
            }
        });
    }

    function displayOrders(orders) {
        var html = '';
        if (orders && orders.length > 0) {
            orders.forEach(function(order) {
                html += '<tr>';
                html += '<td>' + (order.id || '') + '</td>';
                html += '<td>' + (order.customer_name || '') + '</td>';
                html += '<td>' + formatDate(order.created_at) + '</td>';
                html += '<td>' + (order.payment_method || '') + '</td>';
                html += '<td><span class="badge badge-' + getPaymentStatusClass(order.payment_status) + '">' + (order.payment_status || '') + '</span></td>';
                html += '<td>' + formatPrice(order.total_amount || 0) + '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="6" class="text-center font-weight-bold">{{trans("lang.no_record_found")}}</td></tr>';
        }
        $('#ride_list_rows').html(html);
    }

    function displayIntercityOrders(orders) {
        var html = '';
        if (orders && orders.length > 0) {
            orders.forEach(function(order) {
                html += '<tr>';
                html += '<td>' + (order.id || '') + '</td>';
                html += '<td>' + (order.customer_name || '') + '</td>';
                html += '<td>' + formatDate(order.created_at) + '</td>';
                html += '<td>' + (order.payment_method || '') + '</td>';
                html += '<td><span class="badge badge-' + getPaymentStatusClass(order.payment_status) + '">' + (order.payment_status || '') + '</span></td>';
                html += '<td>' + formatPrice(order.total_amount || 0) + '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="6" class="text-center font-weight-bold">{{trans("lang.no_record_found")}}</td></tr>';
        }
        $('#intercity_ride_list_rows').html(html);
    }

    function getPaymentStatusClass(status) {
        if (!status) return 'secondary';
        status = status.toLowerCase();
        if (status === 'paid' || status === 'success') return 'success';
        if (status === 'pending') return 'warning';
        if (status === 'failed' || status === 'cancelled') return 'danger';
        return 'secondary';
    }

    function loadDriverReviews() {
        $.ajax({
            url: '/api/drivers/' + id + '/reviews',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    displayReviews(response.reviews);
                }
            },
            error: function(xhr) {
                console.error('Error loading reviews:', xhr);
            }
        });
    }

    function displayReviews(reviews) {
        var html = '';
        if (reviews && reviews.length > 0) {
            reviews.forEach(function(review) {
                html += '<tr>';
                html += '<td><a href="/users/edit/' + (review.user_id || '') + '">' + (review.customer_name || '') + '</a></td>';
                html += '<td>' + (review.comment || '') + '</td>';
                html += '<td>' + getStars(review.rating || 0) + '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="3" class="text-center font-weight-bold">{{trans("lang.no_record_found")}}</td></tr>';
        }
        $('#review_list_rows').html(html);
    }

    function getStars(rating) {
        var stars = '';
        rating = parseFloat(rating);
        for (var i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += '<i class="fa fa-star text-warning"></i>';
            } else if (i - 0.5 <= rating) {
                stars += '<i class="fa fa-star-half-o text-warning"></i>';
            } else {
                stars += '<i class="fa fa-star-o text-warning"></i>';
            }
        }
        return stars;
    }

    function loadDriverWalletTransactions() {
        $.ajax({
            url: '/api/drivers/' + id + '/wallet-transactions',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    displayWalletTransactions(response.transactions);
                }
            },
            error: function(xhr) {
                console.error('Error loading wallet transactions:', xhr);
            }
        });
    }

    function displayWalletTransactions(transactions) {
        var html = '';
        if (transactions && transactions.length > 0) {
            transactions.forEach(function(txn) {
                html += '<tr>';
                html += '<td>' + (txn.id || '') + '</td>';
                html += '<td>' + (txn.payment_method || '') + '</td>';
                html += '<td>' + (txn.order_type || '') + '</td>';
                html += '<td>' + (txn.txn_id || '') + '</td>';
                html += '<td>' + formatDate(txn.created_at) + '</td>';
                html += '<td>' + (txn.note || '') + '</td>';
                html += '<td class="text-' + (txn.amount >= 0 ? 'success' : 'danger') + '">' + formatPrice(Math.abs(txn.amount || 0)) + '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="7" class="text-center font-weight-bold">{{trans("lang.no_record_found")}}</td></tr>';
        }
        $('#wallet_transactions_rows').html(html);
    }

    function loadDriverWithdrawals() {
        $.ajax({
            url: '/api/drivers/' + id + '/withdrawals',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    displayWithdrawals(response.withdrawals);
                }
            },
            error: function(xhr) {
                console.error('Error loading withdrawals:', xhr);
            }
        });
    }

    function displayWithdrawals(withdrawals) {
        var html = '';
        if (withdrawals && withdrawals.length > 0) {
            withdrawals.forEach(function(withdrawal) {
                html += '<tr>';
                html += '<td>' + formatPrice(withdrawal.amount || 0) + '</td>';
                html += '<td>' + (withdrawal.note || '') + '</td>';
                html += '<td>' + formatDate(withdrawal.paid_date) + '</td>';
                html += '<td><span class="badge badge-' + getWithdrawalStatusClass(withdrawal.status) + '">' + (withdrawal.status || '') + '</span></td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="4" class="text-center font-weight-bold">{{trans("lang.no_record_found")}}</td></tr>';
        }
        $('#payout_request_rows').html(html);
    }

    function getWithdrawalStatusClass(status) {
        if (!status) return 'secondary';
        status = status.toLowerCase();
        if (status === 'paid' || status === 'success') return 'success';
        if (status === 'pending') return 'warning';
        if (status === 'rejected') return 'danger';
        return 'secondary';
    }

    function loadDriverSubscriptionHistory() {
        $.ajax({
            url: '/api/drivers/' + id + '/subscription-history',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    displaySubscriptionHistory(response.history);
                }
            },
            error: function(xhr) {
                console.error('Error loading subscription history:', xhr);
            }
        });
    }

    function displaySubscriptionHistory(history) {
        var html = '';
        if (history && history.length > 0) {
            history.forEach(function(item) {
                html += '<tr>';
                html += '<td>' + (item.plan_name || '') + '</td>';
                html += '<td>' + (item.plan_type || '') + '</td>';
                html += '<td>' + formatDate(item.expires_at) + '</td>';
                html += '<td>' + formatDate(item.created_at) + '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="4" class="text-center font-weight-bold">{{trans("lang.no_record_found")}}</td></tr>';
        }
        $('#subscription_history_rows').html(html);
    }

    // Load subscription plans when modal opens
    $('#changeSubscriptionModal').on('show.bs.modal', function() {
        getSubscriptionPlans();
    });

    function getSubscriptionPlans() {
        $.ajax({
            url: '/subscription-plans/active',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    displaySubscriptionPlans(response.plans);
                }
            },
            error: function(xhr) {
                console.error('Error loading subscription plans:', xhr);
                alert('Error loading subscription plans');
            }
        });
    }

    function displaySubscriptionPlans(plans) {
        var html = '';
        var activeSubscriptionId = driverData && driverData.subscription_plan ? driverData.subscription_plan.id : null;
        
        if (plans && plans.length > 0) {
            plans.forEach(function(plan) {
                var activeClass = (plan.id == activeSubscriptionId) ? '<span class="badge badge-success">{{trans("lang.active")}}</span>' : '';
                var buttonText = (plan.id == activeSubscriptionId) ? "{{trans('lang.renew_plan')}}" : "{{trans('lang.select_plan')}}";
                
                html += '<div class="col-md-3 mt-2 pricing-card pricing-card-subscription">';
                html += '  <div class="pricing-card-inner">';
                html += '    <div class="pricing-card-top">';
                html += '      <div class="d-flex align-items-center pb-4">';
                if (plan.image) {
                    html += '        <span class="pricing-card-icon mr-4"><img src="' + plan.image + '" style="max-width:50px;"></span>';
                }
                html += '        <h2 class="text-dark-2">' + plan.title + ' ' + activeClass + '</h2>';
                html += '      </div>';
                html += '      <p class="text-muted">' + (plan.description || '') + '</p>';
                html += '      <div class="pricing-card-price">';
                html += '        <h3 class="text-dark-2">' + formatPrice(plan.amount) + '</h3>';
                html += '        <span class="price-day">' + (plan.duration_days == -1 ? '{{trans("lang.unlimited")}}' : plan.duration_days) + ' {{trans("lang.days")}}</span>';
                html += '      </div>';
                html += '    </div>';
                html += '    <div class="pricing-card-content pt-3 mt-3 border-top">';
                html += '      <ul class="pricing-card-list text-dark-2">';
                
                if (plan.plan_points && plan.plan_points.length > 0) {
                    plan.plan_points.forEach(function(point) {
                        html += '<li><span class="mdi mdi-check"></span>' + point + '</li>';
                    });
                }
                
                html += '        <li><span class="mdi mdi-check"></span>' + (plan.total_orders == -1 ? '{{trans("lang.unlimited")}}' : plan.total_orders) + ' {{trans("lang.bookings")}}</li>';
                html += '      </ul>';
                html += '    </div>';
                html += '    <div class="pricing-card-btm">';
                html += '      <a href="javascript:void(0)" onclick="chooseSubscriptionPlan(' + plan.id + ')" class="btn rounded-full btn-primary">' + buttonText + '</a>';
                html += '    </div>';
                html += '  </div>';
                html += '</div>';
            });
        } else {
            html = '<div class="col-12 text-center"><p>{{trans("lang.no_plans_available")}}</p></div>';
        }
        
        $('#default-plan').html(html);
    }

    var selectedPlanId = null;

    function chooseSubscriptionPlan(planId) {
        selectedPlanId = planId;
        $('#changeSubscriptionModal').modal('hide');
        $('#checkoutSubscriptionModal').modal('show');
        showPlanDetail(planId);
    }

    function showPlanDetail(planId) {
        $.ajax({
            url: '/subscription-plans/active',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    var plan = response.plans.find(p => p.id == planId);
                    if (plan) {
                        $('.plan-title').text(plan.title);
                        $('.plan-description').text(plan.description || '');
                        $('.plan-price').html('<h4>' + formatPrice(plan.amount) + '</h4>');
                        
                        var pointsHtml = '<ul>';
                        if (plan.plan_points && plan.plan_points.length > 0) {
                            plan.plan_points.forEach(function(point) {
                                pointsHtml += '<li>' + point + '</li>';
                            });
                        }
                        pointsHtml += '<li>' + (plan.total_orders == -1 ? '{{trans("lang.unlimited")}}' : plan.total_orders) + ' {{trans("lang.bookings")}}</li>';
                        pointsHtml += '</ul>';
                        $('.plan-points').html(pointsHtml);
                    }
                }
            }
        });
    }

    // Add Wallet Form Submit
    $('#addWalletForm').on('submit', function(e) {
        e.preventDefault();
        
        var amount = $('#wallet_amount').val();
        var note = $('#wallet_note').val();
        
        if (!amount || amount <= 0) {
            alert('Please enter a valid amount');
            return;
        }
        
        $.ajax({
            url: '/api/drivers/' + id + '/add-wallet',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                amount: amount,
                note: note
            },
            success: function(response) {
                if (response.success) {
                    alert('Wallet amount added successfully');
                    $('#addWalletModal').modal('hide');
                    $('#addWalletForm')[0].reset();
                    loadDriverData(); // Reload driver data to show updated wallet
                    loadDriverWalletTransactions(); // Reload transactions
                } else {
                    alert('Error: ' + (response.message || 'Failed to add wallet amount'));
                }
            },
            error: function(xhr) {
                console.error('Error adding wallet amount:', xhr);
                alert('Error adding wallet amount');
            }
        });
    });

    // Confirm Subscription Button
    $('#confirmSubscriptionBtn').on('click', function() {
        if (!selectedPlanId) {
            alert('Please select a plan');
            return;
        }
        
        if (!confirm('Are you sure you want to assign this subscription plan to the driver?')) {
            return;
        }
        
        $.ajax({
            url: '/api/drivers/' + id + '/assign-subscription',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                subscription_plan_id: selectedPlanId
            },
            success: function(response) {
                if (response.success) {
                    alert('Subscription plan assigned successfully');
                    $('#checkoutSubscriptionModal').modal('hide');
                    selectedPlanId = null;
                    loadDriverData(); // Reload driver data to show new subscription
                    loadDriverSubscriptionHistory(); // Reload subscription history
                } else {
                    alert('Error: ' + (response.message || 'Failed to assign subscription plan'));
                }
            },
            error: function(xhr) {
                console.error('Error assigning subscription:', xhr);
                alert('Error assigning subscription plan');
            }
        });
    });

    // Helper function to shorten email
    function shortEmail(email) {
        if (!email) return '';
        if (email.length <= 20) return email;
        return email.substring(0, 17) + '...';
    }

    // Helper function to shorten phone number
    function shortNumber(countryCode, phoneNumber) {
        if (!phoneNumber) return '';
        var fullNumber = (countryCode || '') + phoneNumber;
        if (fullNumber.length <= 15) return fullNumber;
        return fullNumber.substring(0, 12) + '...';
    }

</script>
@endsection
