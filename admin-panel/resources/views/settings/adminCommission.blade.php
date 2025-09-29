@extends('layouts.app')
@section('content')
<div class="page-wrapper">
  <div class="row page-titles">
    <div class="col-md-5 align-self-center">
      <h3 class="text-themecolor">{{ trans('lang.business_model_settings')}}</h3>
    </div>
    <div class="col-md-7 align-self-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
        <li class="breadcrumb-item active">{{ trans('lang.business_model_settings')}}</li>
      </ol>
    </div>
  </div>
  <div class="card-body">
    <div class="row restaurant_payout_create">
      <div class="restaurant_payout_create-inner">
        <fieldset>
          <legend><i class="mr-3 mdi mdi-shopping"></i>{{ trans('lang.subscription_based_model_settings') }}</legend>
          <div class="form-group row mt-1 ">
            <div class="form-group row mt-1 ">
              <div class="col-12 switch-box">
                <div class="switch-box-inner">
                  <label class=" control-label">{{ trans('lang.subscription_based_model') }}</label>
                  <label class="switch"> <input type="checkbox" name="subscription_model" id="subscription_model"><span
                      class="slider round"></span></label>
                  <i class="text-dark fs-12 fa-solid fa fa-info" data-toggle="tooltip"
                    title="{{ trans('lang.subscription_tooltip') }}" aria-describedby="tippy-3"></i>
                </div>
              </div>
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend><i class="mr-3 mdi mdi-shopping"></i>{{trans('lang.commission_based_model_settings')}}</legend>
          <div class="form-group row width-100 switch-box">
            <div class="switch-box-inner">
              <label class=" control-label">{{ trans('lang.commission_based_model') }}</label>
              <label class="switch"> <input type="checkbox" name="enable_commission" onclick="ShowHideDiv()"
                  id="enable_commission"><span class="slider round"></span></label>
              <i class="text-dark fs-12 fa-solid fa fa-info" data-toggle="tooltip"
                title="{{ trans('lang.commission_tooltip') }}" aria-describedby="tippy-3"></i>
            </div>
          </div>
          <div class="form-group row width-50 admin_commision_detail" style="display:none">
            <label class="col-4 control-label">{{ trans('lang.commission_type')}}</label>
            <div class="col-7">
              <select class="form-control commission_type" id="commission_type">
                <option value="percentage">{{trans('lang.coupon_percent')}}</option>
                <option value="fix">{{trans('lang.coupon_fixed')}}</option>
              </select>
            </div>
          </div>
          <div class="form-group row width-50 admin_commision_detail" style="display:none">
            <label class="col-4 control-label">{{ trans('lang.admin_commission')}}</label>
            <div class="col-7">
              <input type="number" class="form-control commission_fix">
            </div>
          </div>
          <div class="form-group col-12 text-center">
            <button type="button" class="btn btn-primary edit-setting-btn"><i class="fa fa-save"></i>
              {{trans('lang.save')}}</button>
            <a href="{{url('/dashboard')}}" class="btn btn-default"><i
                class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
          </div>
        </fieldset>
      
      </div>
    </div>
  </div>
  <style>
    .select2.select2-container {
      width: 100% !important;
      position: static;
      margin-top: 1rem;
    }
  </style>
  @endsection
  @section('scripts')
  <script>
    var database=firebase.firestore();
    var ref=database.collection('settings').doc("adminCommission");
    var refSubscriptionModel=database.collection('settings').doc("globalValue");
    var photo="";
    $(document).ready(function() {
      $('#driver_type').on('change',function() {
        if($('#driver_type').val()==='custom') {
          $('#all_drivers').show();
          $('#all_drivers').select2({
            placeholder: "{{trans('lang.select_driver')}}",
            allowClear: true,
            width: '100%',
            dropdownAutoWidth: true
          });
        } else {
          $('#all_drivers').hide();
          $('#all_drivers').select2('destroy');
        }
      });
      database.collection('driver_users').orderBy('fullName','asc').get().then(async function(snapshots) {
        snapshots.docs.forEach((listval) => {
          var data=listval.data();
          $('#all_drivers').append($("<option></option>")
            .attr("value",data.id)
            .text(data.fullName));
        })
      });
      jQuery("#overlay").show();
      ref.get().then(async function(snapshots) {
        var adminCommissionSettings=snapshots.data();
        if(adminCommissionSettings==undefined) {
          database.collection('settings').doc('adminCommission').set({});
        }
        try {
          if(adminCommissionSettings.isEnabled) {
            $("#enable_commission").prop('checked',true);
            $(".admin_commision_detail").show();
          }
          $(".commission_fix").val(adminCommissionSettings.amount);
          $("#commission_type").val(adminCommissionSettings.type);
        } catch(error) {
        }
        jQuery("#overlay").hide();
      })

      refSubscriptionModel.get().then(async function(snapshots) {
        var data=snapshots.data();
        try {
          if(data.subscription_model) {
            $("#subscription_model").prop('checked',true);
          }
        } catch(error) {
        }
        jQuery("#overlay").hide();
      })

      $(document).on("click","input[name='subscription_model']",function(e) {

        var subscription_model=$("#subscription_model").is(":checked");
        var userConfirmed=confirm(subscription_model? "{{ trans('lang.enable_subscription_plan_confirm_alert')}}":"{{ trans('lang.disable_subscription_plan_confirm_alert')}}");
        if(!userConfirmed) {
          $(this).prop("checked",!subscription_model);
          return;
        }
        database.collection('settings').doc("globalValue").update({
          'subscription_model': subscription_model,
        });
        if(subscription_model) {
          Swal.fire('{{trans("lang.update_complete")}}',`Subscription model enabled.`,'success');
        } else {
          Swal.fire('{{trans("lang.update_complete")}}',`Subscription model disabled.`,'success');
        }
      });
      $(".edit-setting-btn").click(function() {
        var checkboxValue=$("#enable_commission").is(":checked");
        var commission_type=$("#commission_type").val();
        var howmuch=$(".commission_fix").val().toString();
        database.collection('settings').doc("adminCommission").update({'isEnabled': checkboxValue,'amount': howmuch,'type': commission_type}).then(function(result) {
          Swal.fire('{{trans("lang.update_complete")}}',`Successfully updated.`,'success');
        });
      })
    
    })
    function ShowHideDiv() {
      var checkboxValue=$("#enable_commission").is(":checked");
      if(checkboxValue) {
        $(".admin_commision_detail").show();
      } else {
        $(".admin_commision_detail").hide();
      }
    }
  </script>
  @endsection