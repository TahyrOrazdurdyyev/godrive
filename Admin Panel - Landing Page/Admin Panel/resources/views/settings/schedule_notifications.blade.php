@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.schedule_ride_notification')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.schedule_ride_notification')}}</li>
            </ol>
        </div>
    </div>
    <div class="card-body">
        <div class="error_top" style="display:none"></div>

        <div class="row restaurant_payout_create">
            <div class="restaurant_payout_create-inner">

                <fieldset>
                    <legend><i class="mr-3 mdi mdi-shopping"></i>{{trans('lang.schedule_ride_notification')}}</legend>
                    <div class="form-group row width-50 no-record-div">
                        <label class="col-4 control-label">{{ trans('lang.time')}}</label>
                        <div class="col-7">
                            <input type="number" placeholder="{{trans('lang.enter_time')}}" id="notify_time" class="form-control time">
                        </div>
                    </div>
                    <div class="form-group row width-50 no-record-div">
                        <label class="col-4 control-label">{{ trans('lang.time_unit')}}</label>
                        <div class="col-7">
                            <select class="form-control time_unit" id="time_unit">
                                <option value="days">{{trans('lang.days')}}</option>
                                <option value="hours">{{trans('lang.hours')}}</option>
                                <option value="minutes">{{trans('lang.minutes')}}</option>
                            </select>
                        </div>
                    </div>
                    <div id="options-container"></div>
                    <button id="add-plan-point" onclick="addMore()"
                        class="btn btn-primary ml-3">{{ trans('lang.add_more') }}</button>

                   
                </fieldset>
                <div class="form-group col-12 text-center">
                    <button type="button" class="btn btn-primary edit-setting-btn"><i class="fa fa-save"></i>
                        {{trans('lang.save')}}</button>
                    <a href="{{url('/dashboard')}}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
                </div>

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
        var ref=database.collection('settings').doc("schedule_ride_notification");
        var addTime=[];
        $(document).ready(function() {
            jQuery("#overlay").show();
           ref.get().then(async function(snapshots) {
                var data=snapshots.data();

                if(data==undefined) {
                    database.collection('settings').doc('schedule_ride_notification').set({'notificationTiming': addTime});
                }
                try {
                    
                    if(data.notificationTiming.length>0) {
                        $('.no-record-div').addClass('d-none');
                        addTime=data.notificationTiming;
                        renderPlanPoints();
                    }
                   
                } catch(error) {
                }
                jQuery("#overlay").hide();
            })
            

            $(".edit-setting-btn").click(function() {
                var time=$("#notify_time").val();
                var timeUnit=$('#time_unit').val();
                if(time!=null && time!=undefined && time!=''){
                    addTime.push({time: time,unit: timeUnit});
                }
                addTime=addTime.filter(item => item.time&&item.unit);
                if(addTime.length==0){
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.add_the_time_and_unit')}}</p>");
                    window.scrollTo(0,0);
                }else{
                    database.collection('settings').doc("schedule_ride_notification").update({'notificationTiming': addTime}).then(function(result) {
                        Swal.fire('{{trans("lang.update_complete")}}',`Successfully updated.`,'success');
                        window.location.reload();
                    });
                }
               
            })

        })
        function addMore() {
            addTime.push({time: '',unit: ''});
            renderPlanPoints();
        }
        function deleteTime(index) {
            addTime.splice(index,1);
            renderPlanPoints();
        }

        function updateTime(index,value,type) {
            if(type==='time') {
                addTime[index].time=value? String(parseInt(value,10)):"";
            } else if(type==='unit') {
                addTime[index].unit=value;
            }
            
        }
        function renderPlanPoints() {
            const container=document.getElementById('options-container');
            container.innerHTML='';
            addTime.forEach((point,index) => {
                const html=`
            <div class="form-group d-flex ml-1 option-row mt-1" id="time-${index}">
                <div class="form-group row width-50">
                    <input type="number" placeholder="{{trans('lang.enter_time')}}" class="form-control" 
                           id="input-${index}" value="${point.time||''}" 
                           oninput="updateTime(${index}, this.value, 'time')">                        
                </div>
                <div class="form-group row width-50">
                    <select class="form-control time_unit" id="time_unit-${index}" 
                            onchange="updateTime(${index}, this.value, 'unit')">
                        <option value="" ${point.unit===''? 'selected':''}>{{trans('lang.select_time_unit')}}</option>
                        <option value="days" ${point.unit==='days'? 'selected':''}>{{trans('lang.days')}}</option>
                        <option value="hours" ${point.unit==='hours'? 'selected':''}>{{trans('lang.hours')}}</option>
                        <option value="minutes" ${point.unit==='minutes'? 'selected':''}>{{trans('lang.minutes')}}</option>
                    </select>
                </div>
                <button type="button" class="btn btn-primary ml-2" title="Delete" onclick="deleteTime(${index})">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>`;

                container.insertAdjacentHTML('beforeend',html);
            });
        }


    </script>
    @endsection