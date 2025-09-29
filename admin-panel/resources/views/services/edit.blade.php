@extends('layouts.app')



@section('content')

<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.service_plural')}}</h3>

        </div>



        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                <li class="breadcrumb-item"><a href="{!! route('services') !!}">{{trans('lang.service_plural')}}</a>

                </li>

                <li class="breadcrumb-item active">{{trans('lang.service_edit')}}</li>

            </ol>

        </div>

    </div>

    <div class="container-fluid">

        <div class="card pb-4">

             <div class="card-header">

                <ul class="nav nav-tabs" id="language-tabs" role="tablist">

                </ul>

            </div>

            <div class="card-body">



                <div class="error_top"></div>



                <div class="row restaurant_payout_create">

                    <div class="restaurant_payout_create-inner">

                        <fieldset>

                            <legend>{{trans('lang.service_details')}}</legend>

                            <div class="tab-content" id="language-contents">  </div>

                           

                            <input type="hidden" id="distanceType" />



                            <div class="form-group row width-100">

                                <label class="col-3 control-label">{{trans('lang.image')}}</label>

                                <div class="col-7">

                                    <input type="file" onChange="handleFileSelect(event)" class="form-control image"

                                        id="service_image">

                                    <div class="placeholder_img_thumb service_image"></div>

                                    <div id="uploding_image"></div>

                                </div>

                            </div>







                            <div class="form-group row width-50">

                                <div class="form-check">

                                    <input type="checkbox" class="service_active" id="active">

                                    <label class="col-3 control-label" for="active">{{trans('lang.enable')}}</label>

                                </div>

                            </div>



                           
                            <div class="form-group row width-50">

                                <div class="form-check">

                                    <input type="checkbox" class="intercity_type" id="intercityType">

                                    <label class="col-3 control-label" for="intercityType">{{

    trans('lang.service_intercity') }}</label>
                                    <div class="form-text text-muted">

                                    {{ trans('lang.intercity_help') }} 


                                    </div>   

                                </div>

                            </div>



                            <div class="form-group row width-50">

                                <div class="form-check">

                                    <input type="checkbox" class="offer_rate" id="offer_rate">

                                    <label class="col-3 control-label" for="offer_rate">{{

    trans('lang.enable_offer_rate') }}</label>
                                    <div class="form-text text-muted">

                                          {{ trans('lang.offer_rate_help') }} 


                                    </div>

                                </div>

                            </div>

                            <div class="form-group row width-50">

                                <div class="form-check">

                                    <input type="checkbox" class="IsglobalAdminComission" id="IsglobalAdminComission"

                                        onclick="ShowHideDiv()">

                                    <label class="col-3 control-label" for="IsglobalAdminComission">{{

                                         trans('lang.IsglobalAdminComossion') }}</label>

                                         <div class="form-text text-muted">

                                            {{ trans('lang.global_commission_help') }} <a href="{{ route('settings.businessModel') }}" target="_blank">Here</a>


                                        </div> 
                                </div>

                            </div>

                            <div class="form-group row width-50" id="comissionType">

                                <label class="col-4 control-label">{{ trans('lang.commission_type') }}</label>

                                <div class="col-7">

                                    <select class="form-control commission_type" id="commission_type">

                                        <option value="fix">{{ trans('lang.fixed') }}</option>

                                        <option value="percentage">{{ trans('lang.percentage') }}</option>

                                    </select>

                                </div>

                            </div>



                            <div class="form-group row width-50" id="comission">

                                <label class="col-4 control-label">{{ trans('lang.admin_commission') }}<span

                                        class="required-field"></span></label>

                                <div class="col-7">

                                    <input type="number" class="form-control commission">

                                </div>

                            </div>



                        </fieldset>



                        <fieldset>

                            <legend>{{ trans('lang.basic_fare') }} {{ trans('lang.settings') }}</legend>

                            <div class="form-group row width-100">

                                    <label class="col-4 control-label">{{ trans('lang.enter_basic') }} <span class="global_basic_label"></span><span

                                                class="required-field"></span></label>

                                    <div class="col-7">

                                        <input type="number" class="form-control basic_fare_km">

                                        <div class="form-text text-muted">{{ trans('lang.basic_fare_help') }}</div>

                                    </div>

                            </div>

                            <div class="form-group row width-100">

                                <label class="col-4 control-label">{{ trans('lang.basic_fare_amount') }}<span

                                            class="required-field"></span></label>

                                <div class="col-7">

                                    <div class="control-inner">

                                            <input type="number" class="form-control basic_fare_charges currency_input">

                                            <span class="currentCurrency"></span>
                                            <div class="form-text text-muted">



                                            {{ trans('lang.basic_fare_amount') }}



                                            </div>
                                    </div>

                                </div>

                            </div>
                            
                           
                        </fieldset>

                        

                        <fieldset>

                            <legend>{{ trans('lang.ac_nonac') }} {{ trans('lang.settings') }}</legend>

                            <div class="form-group row width-100">

                            <div class="form-check">

                                    <input type="checkbox" class="is_ac_non_ac" id="is_ac_non_ac" onclick="acNonAcDiv()">

                                    <label class="col-3 control-label" for="is_ac_non_ac">{{trans('lang.is_ac_non_ac')}}</label>

                                </div>

                            </div>

                            <div class="show_ac_non_ac_div d-none">

                                <div class="form-group row width-100">

                                    <label class="col-3 control-label ">{{trans('lang.max_ac_charges')}}/<span class="global_basic_label"></span><span

                                    class="required-field"></span></label>

                                    <div class="col-7">

                                        <div class="control-inner">

                                                <input type="number" class="form-control ac_charges currency_input" min="0">

                                                <span class="currentCurrency"></span>

                                                <div class="form-text text-muted">

                                                {{ trans('lang.ac_charges_help') }}

                                                </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="form-group row width-100">

                                    <label class="col-3 control-label">{{trans('lang.max_nonac_charges')}}/<span class="global_basic_label"></span><span

                                    class="required-field"></span></label>

                                    <div class="col-7">

                                        <div class="control-inner">

                                            <input type="number" class="form-control nonac_charges currency_input" min="0">

                                            <span class="currentCurrency"></span>

                                            <div class="form-text text-muted">



                                            {{ trans('lang.nonac_charges_help') }}



                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>



                            <div class="form-group row width-100 km_charges_div">

                                    <label class="col-3 control-label">{{ trans('lang.max_per_km') }} <span class="global_basic_label"></span><span

                                    class="required-field"></span></label>

                                    <div class="col-7">

                                        <div class="control-inner">

                                            <input type="number" class="form-control km_charges currency_input" min="0">

                                            <span class="currentCurrency"></span>

                                            <div class="form-text text-muted">


                                              {{ trans('lang.max_per_km_help') }}



                                            </div>

                                        </div>

                                    </div>

                           </div>



                        </fieldset>



                        <fieldset>

                            <legend>{{ trans('lang.holding_charge_details') }}</legend>

                            <div class="form-group row width-100">

                                    <label class="col-4 control-label">{{ trans('lang.holding_charge_minute') }}<span

                                                class="required-field"></span></label>

                                    <div class="col-7">

                                        <input type="number" class="form-control holding_charge_minute"

                                               id="holding_charge_minute">

                                        <div class="form-text text-muted">



                                            {{ trans('lang.holding_charge_minute_help') }}



                                        </div>

                                    </div>

                                </div>

                                <div class="form-group row width-100">

                                    <label class="col-4 control-label">{{ trans('lang.holding_charges') }}<span

                                                class="required-field"></span></label>

                                    <div class="col-7">

                                        <div class="control-inner">

                                            <input type="number" class="form-control holding_charges currency_input" id="holding_charges">

                                            <span class="currentCurrency"></span>

                                            <div class="form-text text-muted">



                                                {{ trans('lang.holding_charges_help') }}



                                            </div>

                                        </div>

                                    </div>

                                </div>

                        </fieldset>

                        

                        <fieldset>

                            <legend>{{ trans('lang.ride_time_fare_details') }}</legend>

                            <div class="form-group row width-100">

                                    <label class="col-4 control-label">{{ trans('lang.ride_time_fare_per_minute') }}<span class="required-field"></span></label>

                                    <div class="col-7">

                                        <div class="control-inner">

                                            <input type="number" class="form-control ride_time_fare_per_minute currency_input"

                                                id="ride_time_fare_per_minute">

                                                <span class="currentCurrency"></span>

                                                <div class="form-text text-muted">
                                                    {{ trans('lang.ride_time_fare_per_minute_help') }}
                                                </div>

                                        </div>

                                    </div>

                                </div>

                        </fieldset>



                        <fieldset>

                            <legend>{{ trans('lang.night_fare_details') }}</legend>

                            <div class="form-group row width-50">

                                    <label class="col-4 control-label">{{ trans('lang.start_night_time') }}<span class="required-field"></span></label>

                                    <div class="col-7">

                                        <input type="time" class="form-control start_night_time"

                                               id="start_night_time">

                                        <div class="form-text text-muted">



                                            {{ trans('lang.start_night_time_help') }}



                                        </div>

                                    </div>

                                </div>



                                <div class="form-group row width-50">

                                    <label class="col-4 control-label">{{ trans('lang.end_night_time') }}<span class="required-field"></span></label>

                                    <div class="col-7">

                                        <input type="time" class="form-control end_night_time"

                                               id="end_night_time">

                                        <div class="form-text text-muted">



                                            {{ trans('lang.end_night_time_help') }}



                                        </div>

                                    </div>

                                </div>

                                <div class="form-group row width-100">

                                    <label class="col-4 control-label">{{ trans('lang.night_time_fare') }}<span class="required-field"></span></label>

                                    <div class="col-7">

                                        <div class="control-inner">

                                            <select class="form-control night_time_fare" id="night_time_fare">

                                                <option value="0">Default(0)</option>
                                                <option value="1.5">1.5x</option>
                                                <option value="2">2x</option>
                                                <option value="2.5">2.5x</option>
                                                <option value="3">3x</option>

                                            </select>

                                            <div class="form-text text-muted">



                                                {{ trans('lang.night_time_fare_help') }} {{trans('lang.for_ride_fare')}}

                                                {{ trans('lang.night_fare_desc')}}

                                            </div>

                                        </div>

                                    </div>

                                </div>



                        </fieldset>
                    </div>

                </div>



                <div class="form-group col-12 text-center btm-btn">

                    <button type="button" class="btn btn-primary  edit-setting-btn"><i class="fa fa-save"></i> {{

    trans('lang.save')}}

                    </button>

                    <a href="{!! route('services') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{

    trans('lang.cancel')}}</a>

                </div>



            </div>



        </div>

    </div>

</div>



@endsection



@section('scripts')



<script>



    var id = "<?php echo $id; ?>";

    var database = firebase.firestore();

    var ref = database.collection('service').where("id", "==", id);

    var storageRef = firebase.storage().ref('images');

    var storage = firebase.storage();

    var photo = "";

    var fileName = "";

    var serviceImageFile = '';

    var append_list = '';

    var placeholderImage = "{{ asset('/images/default_user.png') }}";

    function ShowHideDiv() {

        let enableCommision = $("#IsglobalAdminComission").is(":checked");

        if (enableCommision) {

            $("#comissionType").hide();

            $("#comission").hide();

        } else {

            $("#comissionType").show();

            $("#comission").show();

        }

    }



    function acNonAcDiv() {

        let is_ac_non_ac = $(".is_ac_non_ac").is(":checked");

        if (is_ac_non_ac) {

            $(".show_ac_non_ac_div").removeClass('d-none');

            $(".km_charges_div").addClass('d-none');

        } else {

            $(".show_ac_non_ac_div").addClass('d-none');

            $(".km_charges_div").removeClass('d-none');

        }

    }



    $(document).ready(function () {

        fetchLanguages().then(createLanguageTabs);



        $('.ride_sub_menu li').each(function () {

            var url = $(this).find('a').attr('href');

            if (url == document.referrer) {

                $(this).find('a').addClass('active');

                $('.rides_menu').addClass('active').attr('aria-expanded', true);

            }

            $('.ride_sub_menu').addClass('in').attr('aria-expanded', true);

        });



        jQuery("#overlay").show();

        ref.get().then(async function (snapshots) {

            var data = snapshots.docs[0].data();

           

            if (data.hasOwnProperty('adminCommission')) {



                if (data.adminCommission.isEnabled) {

                    $("#IsglobalAdminComission").prop('checked', true);

                    $("#comissionType").hide();

                    $("#comission").hide();

                } else {

                    $("#commission_type").val(data.adminCommission.type);

                    $(".commission").val(data.adminCommission.amount);

                }

            }

            if(Array.isArray(data.title)) {

                    data.title.forEach(function(titleObj) {

                        var inputField=$(`#service-title-${titleObj.type}`);

                        if(inputField.length) {

                            inputField.val(titleObj.title);

                        }

                    });

            }



            if(data.isAcNonAc){

                $('.is_ac_non_ac').prop('checked',true);

  

                $(".show_ac_non_ac_div").removeClass('d-none');

                $(".km_charges_div").addClass('d-none');



                if(data.acCharge){

                    $(".ac_charges").val(data.acCharge);

                }

                

                if(data.nonAcCharge){

                    $(".nonac_charges").val(data.nonAcCharge);



                }

            

            } else {

                $(".show_ac_non_ac_div").addClass('d-none');

                $(".km_charges_div").removeClass('d-none');

                

                if(data.kmCharge){

                    $(".km_charges").val(data.kmCharge);



                }



            }

           

            if (data.offerRate) {

                $('.offer_rate').prop('checked', true);

            }

            if (data.enable) {

                $('.service_active').prop('checked', true);

            }

            $('.intercity_type').prop('checked', data.intercityType ? true : false);

            photo = data.image;

            if (photo != '') {



                $(".service_image").append('<span class="image-item"><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + photo + '" alt="image">');

                serviceImageFile = data.image;

            } else {

                photo = "";

                $(".service_image").append('<span class="image-item"><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');

            }



            if(data.basicFare){

                $(".basic_fare_km").val(data.basicFare);

            }

            if(data.basicFareCharge){

                $(".basic_fare_charges").val(data.basicFareCharge);

            }

            if(data.holdingMinute){

                $(".holding_charge_minute").val(data.holdingMinute);

            }

            if(data.holdingMinuteCharge){

                $(".holding_charges").val(data.holdingMinuteCharge);

            }

            if(data.perMinuteCharge){

                $(".ride_time_fare_per_minute").val(data.perMinuteCharge);

            }

            if(data.startNightTime){

                $(".start_night_time").val(data.startNightTime);

            }

            if(data.endNightTime){

                $(".end_night_time").val(data.endNightTime);

            }

            if(data.nightCharge){

                $(".night_time_fare").val(data.nightCharge);

            }

            jQuery("#overlay").hide();

        });

    });



    async function storeImageData() {

        var newPhoto = '';

        try {

            if (serviceImageFile != "" && photo != serviceImageFile) {

                var serviceOldImageUrlRef = await storage.refFromURL(serviceImageFile);

                imageBucket = serviceOldImageUrlRef.bucket;

                var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";



                if (imageBucket == envBucket) {



                    await serviceOldImageUrlRef.delete().then(() => {

                        console.log("Old file deleted!")

                    }).catch((error) => {

                        console.log("ERR File delete ===", error);

                    });



                } else {

                    console.log('Bucket not matched');

                }



            }

            if (photo != serviceImageFile) {

                photo = photo.replace(/^data:image\/[a-z]+;base64,/, "")

                var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', { contentType: 'image/jpg' });

                var downloadURL = await uploadTask.ref.getDownloadURL();

                newPhoto = downloadURL;

                photo = downloadURL;

            } else {

                newPhoto = photo;

            }

        } catch (error) {

            console.log("ERR ===", error);

        }

        return newPhoto;

    }



    $(".edit-setting-btn").click(function () {



        var  type=$('#distanceType').val();



        var titles=[];



        $("[id^='service-title-']").each(function() {

                var languageCode=$(this).attr('id').replace('service-title-','');

                var nameValue=$(this).val();



                titles.push({

                    title: nameValue,

                    type: languageCode

                });

            });

            var isEnglishNameValid=titles.some(function(nameObj) {

                return nameObj.type === 'en' && nameObj.title.trim() !== '';

            });



        var is_ac_non_ac = $(".is_ac_non_ac").is(":checked");

       

        var ac_charges = null;

        var nonac_charges = null;

        var km_charges = null;



        if(is_ac_non_ac){

            ac_charges = $('.ac_charges').val();

            nonac_charges = $('.nonac_charges').val();

        }else{

            km_charges = $('.km_charges').val();

        }

       



        var enable = false;

        if ($(".service_active").is(':checked')) {

            enable = true;

        }

        var offerRate = false;

        if ($(".offer_rate").is(':checked')) {

            offerRate = true;

        }





        var isGlobalAdminCommission = $("#IsglobalAdminComission").is(":checked") ? true : false;

        if (isGlobalAdminCommission == false) {

            var comission_type = $("#commission_type :selected").val();

            var admin_comission = $(".commission").val();

        } else {

            var comission_type = '';

            var admin_comission = '';

        }

        var adminCommission = { 'isEnabled': isGlobalAdminCommission, 'type': comission_type, 'amount': admin_comission };



        var basicFareKm = $(".basic_fare_km").val();

        var basicFareCharges = $(".basic_fare_charges").val();

        var holdingChargeMinute = $(".holding_charge_minute").val();

        var holdingCharges = $(".holding_charges").val();

        var rideTimeFarePerMinute = $(".ride_time_fare_per_minute").val();

        var startNightTime = $(".start_night_time").val();

        var endNightTime = $(".end_night_time").val();

        var nightFareCharge = $(".night_time_fare").val();



        var intercityType = $(".intercity_type").is(':checked') ? true : false;

        if(!isEnglishNameValid) {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{trans('lang.service_title_en_required')}}</p>");

            window.scrollTo(0,0);

        } else if(admin_comission==''&&isGlobalAdminCommission==false) {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{ trans('lang.commission_help') }}</p>");

            window.scrollTo(0,0);

        } else if (basicFareKm == '' || basicFareKm < 0) {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{ trans('lang.please_enter_valid') }} {{ trans('lang.basic_fare_km') }} "+type+" {{trans('lang.for_ride_fare')}}</p>");

            window.scrollTo(0, 0);

        } else if (basicFareCharges == '' || basicFareCharges < 0) {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{ trans('lang.please_enter_valid') }} {{ trans('lang.basic_fare_km') }} "+type+" {{ trans('lang.charges') }} {{trans('lang.for_ride_fare')}}</p>");

            window.scrollTo(0, 0);

        }else if(is_ac_non_ac && (ac_charges==''||ac_charges<=0)) {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{ trans('lang.please_enter_valid') }} {{ trans('lang.ac_charges') }} {{trans('lang.for_ride_fare')}}</p>");

            window.scrollTo(0,0);

        } else if(is_ac_non_ac && (nonac_charges==''||nonac_charges<=0)) {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{ trans('lang.please_enter_valid') }} {{ trans('lang.nonac_charges') }} {{trans('lang.for_ride_fare')}}</p>");

            window.scrollTo(0,0);

        } else if(!is_ac_non_ac && (km_charges == '' || km_charges <= 0)){

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{ trans('lang.please_enter_valid') }} "+type+" {{ trans('lang.charges') }} {{trans('lang.for_ride_fare')}}</p>");

            window.scrollTo(0,0);

        }else if (holdingChargeMinute == '' || holdingChargeMinute < 0) {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{ trans('lang.please_enter_valid') }} {{ trans('lang.holding_charge_minute') }}</p>");

            window.scrollTo(0, 0);

        } else if (holdingCharges == '' || holdingCharges < 0) {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{ trans('lang.please_enter_valid') }} {{ trans('lang.holding_charges') }}</p>");

            window.scrollTo(0, 0);

        } else if (rideTimeFarePerMinute == '' || rideTimeFarePerMinute < 0) {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{ trans('lang.please_enter_valid') }}  {{ trans('lang.ride_time_fare_per_minute') }} {{trans('lang.for_ride_fare')}}</p>");

            window.scrollTo(0, 0);

        } else if (startNightTime == '') {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{ trans('lang.start_night_time_help') }}</p>");

            window.scrollTo(0, 0);

        } else if (endNightTime == '') {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{ trans('lang.end_night_time_help') }}</p>");

            window.scrollTo(0, 0);

        } else if (nightFareCharge == '' || nightFareCharge < 0) {

            $(".error_top").show();

            $(".error_top").html("");

            $(".error_top").append("<p>{{ trans('lang.please_enter_valid') }} {{ trans('lang.night_time_fare') }} {{trans('lang.for_ride_fare')}}</p>");

            window.scrollTo(0, 0);

        } else {

            jQuery("#overlay").show();



            storeImageData().then(IMG => {

                database.collection('service').doc(id).update({



                    'title': titles,

                    'offerRate': offerRate,

                    'isAcNonAc':is_ac_non_ac,

                    'acCharge': ac_charges,

                    'nonAcCharge': nonac_charges,

                    'kmCharge':km_charges,

                    'image': IMG,

                    'enable': enable,

                    'intercityType': intercityType,

                    'adminCommission': adminCommission,

                    'basicFare': basicFareKm,

                    'basicFareCharge': basicFareCharges,

                    'holdingMinute': holdingChargeMinute,

                    'holdingMinuteCharge': holdingCharges,

                    'perMinuteCharge': rideTimeFarePerMinute,

                    'startNightTime': startNightTime,

                    'endNightTime': endNightTime,

                    'nightCharge': nightFareCharge,

                }).then(function (result) {

                    jQuery("#overlay").hide();



                    window.location.href = '{{ route("services")}}';

                });

            }).catch(function (error) {

                $(".error_top").show();

                $(".error_top").html("");

                $(".error_top").append("<p>" + error + "</p>");

            });

        }

    });





    function handleFileSelect(evt) {



        var f = evt.target.files[0];

        var reader = new FileReader();



        reader.onload = (function (theFile) {

            return function (e) {



                var filePayload = e.target.result;

                var val = f.name;

                var ext = val.split('.')[1];

                var docName = val.split('fakepath')[1];

                var filename = (f.name).replace(/C:\\fakepath\\/i, '')



                var timestamp = Number(new Date());

                var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;

                photo = filePayload;

                fileName = filename;

                $(".service_image").empty();

                $(".service_image").append('<span class="image-item" ><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + filePayload + '" alt="image"></span>');



            };

        })(f);

        reader.readAsDataURL(f);

    }



    $(document).on('click', '.remove-btn', function () {

        $(".image-item").remove();

        $('#service_image').val('');

    });

        async function fetchLanguages() {

        const languagesRef=database.collection('languages').where('isDeleted','==',false);

        const snapshot=await languagesRef.get();

        const languages=[];

        snapshot.forEach(doc => {

            languages.push(doc.data());

        });

        return languages;

    }

    function createLanguageTabs(languages) {

        const tabsContainer=document.getElementById('language-tabs');

        const contentsContainer=document.getElementById('language-contents');



        tabsContainer.innerHTML='';

        contentsContainer.innerHTML='';



        const defaultLanguage=languages.find(language => language.isDefault);

        const otherLanguages=languages.filter(language => !language.isDefault);

        otherLanguages.sort((a,b) => a.name.localeCompare(b.name));

        const sortedLanguages=[defaultLanguage,...otherLanguages];

        sortedLanguages.forEach((language,index) => {

            var defaultClass='';

            if(language.isDefault){

                defaultClass='{{trans("lang.default")}}';

            }

            const tab=document.createElement('li');

            tab.classList.add('nav-item');

            tab.innerHTML=`

            <a class="nav-link ${index===0? 'active':''}" id="tab-${language.code}" data-bs-toggle="tab" href="#content-${language.code}" role="tab" aria-selected="${index===0}">

                ${language.name} (${language.code.toUpperCase()})

                <span class="badge badge-success ml-2">${defaultClass}</span>

            </a>

        `;

            tabsContainer.appendChild(tab);



            const content=document.createElement('div');

            content.classList.add('tab-pane','fade');

            if(index===0) {

                content.classList.add('show','active');

            }

            content.id=`content-${language.code}`; // Ensure this matches the tab link's href

            content.role="tabpanel";

            content.innerHTML=`

            <div class="form-group row width-100">

                <label class="col-3 control-label" for="service-title-${language.code}">{{trans('lang.service_title')}} (${language.code.toUpperCase()})<span class="required-field"></span></label>

                <div class="col-7">

                    <input type="text" class="form-control" id="service-title-${language.code}">

                    <div class="form-text text-muted">{{ trans("lang.service_title_help") }}</div>

                </div>                             

            </div>

        `;

            contentsContainer.appendChild(content);

        });



        const triggerTabList=document.querySelectorAll('#language-tabs a');

        triggerTabList.forEach(tab => {

            tab.addEventListener('click',function(event) {

                event.preventDefault();



                document.querySelectorAll('.tab-pane').forEach(function(pane) {

                    pane.classList.remove('active','show');

                });



                document.querySelectorAll('.nav-link').forEach(function(navTab) {

                    navTab.classList.remove('active');

                });



                this.classList.add('active');

                const target=this.getAttribute('href');

                const targetPane=document.querySelector(target);

                if(targetPane) {

                    targetPane.classList.add('active','show');

                }

            });

        });

    }





</script>

@endsection