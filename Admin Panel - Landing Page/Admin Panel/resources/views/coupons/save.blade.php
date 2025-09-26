@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.coupon_plural')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('coupons') !!}">{{trans('lang.coupon_plural')}}</a>
                </li>
                <li class="breadcrumb-item active">
                    {{ $id == 0? trans('lang.coupon_create') : trans('lang.coupon_edit')}}
                </li>
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
                <div id="data-table_processing" class="dataTables_processing panel panel-default"
                    style="display: none;">{{trans('lang.processing')}}</div>
                <div class="error_top"></div>

                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{trans('lang.coupon_details')}}</legend>
                            <div class="tab-content" id="language-contents"></div>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.coupon_type')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <select class="form-control coupon_type">
                                        <option value="fix">
                                            {{trans('lang.fix')}}
                                        </option>
                                        <option value="percentage">
                                            {{trans('lang.percentage')}}
                                        </option>
                                    </select>
                                    <div class="form-text text-muted">
                                        {{ trans("lang.coupon_type_help") }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.coupon_amount')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="number" class="form-control coupon_amount" min="0">
                                    <div class="form-text text-muted w-50">
                                        {{ trans("lang.coupon_amount_help") }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.coupon_code')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control coupon_code">
                                    <div class="form-text text-muted w-50">
                                        {{ trans("lang.coupon_code_help") }}
                                    </div>
                                </div>
                            </div>


                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.coupon_validity')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="date" class="form-control coupon_validity"
                                        value="{{ date('Y-m-d', time()); }}" min="{{ date('Y-m-d', time()); }}">
                                    <div class="form-text text-muted w-50">
                                        {{ trans("lang.coupon_validity_help") }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row width-50">
                                <div class="form-check">
                                    <input type="checkbox" class="coupon_active" id="coupon_active">
                                    <label class="col-3 control-label"
                                        for="coupon_active">{{trans('lang.enable')}}</label>
                                </div>
                            </div>

                            <div class="form-group row width-50">
                                <div class="form-check">
                                    <input type="checkbox" class="coupon_public" id="coupon_public">
                                    <label class="col-3 control-label"
                                        for="coupon_public">{{trans('lang.ispublic')}}</label>
                                </div>
                            </div>

                        </fieldset>
                    </div>
                </div>

                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary  save-form-btn"><i class="fa fa-save"></i>
                        {{ trans('lang.save')}}</button>
                    <a href="{!! route('coupons') !!}" class="btn btn-default"><i
                            class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
                </div>

            </div>

        </div>
    </div>

</div>

@endsection

@section('scripts')

<script>

    var database=firebase.firestore();


    var requestId="{{$id}}";
    var id=(requestId=='0')? database.collection("tmp").doc().id:requestId;

    $(document).ready(function() {
        fetchLanguages().then(createLanguageTabs);

        $('.coupon_sub_menu li').each(function() {
            var url=$(this).find('a').attr('href');
            if(url==document.referrer) {
                $(this).find('a').addClass('active');
                $('.coupon_menu').addClass('active').attr('aria-expanded',true);
            }
            $('.coupon_sub_menu').addClass('in').attr('aria-expanded',true);
        });
        if(requestId!='0') {
            jQuery("#overlay").show();
            var ref=database.collection('coupon').where("id","==",id);
            ref.get().then(async function(snapshots) {
                var data=snapshots.docs[0].data();
                if(data&&Array.isArray(data.title)) {
                    data.title.forEach(function(titleObj) {
                        var inputField=$(`#coupon-title-${titleObj.type}`);
                        if(inputField.length) {
                            inputField.val(titleObj.title);
                        }
                    });
                }

                $(".coupon_type").val(data.type);
                $(".coupon_amount").val(data.amount);
                $('.coupon_code').val(data.code);

                var date=new Date(data.validity.seconds*1000);
                $(".coupon_validity").val(date.toLocaleDateString('en-CA'));
                if(data.enable) {
                    $('.coupon_active').prop('checked',true);
                }
                if(data.isPublic) {
                    $('.coupon_public').prop('checked',true);
                }
                jQuery("#overlay").hide();
            })
        }
    });

    $(".save-form-btn").click(function() {

        var titles=[];

        $("[id^='coupon-title-']").each(function() {
            var languageCode=$(this).attr('id').replace('coupon-title-','');

            var nameValue=$(this).val();

            titles.push({
                title: nameValue,
                type: languageCode
            });
        });
        var isEnglishNameValid=titles.some(function(nameObj) {
            return nameObj.type==='en'&&nameObj.title.trim()!=='';
        });
        var type=$(".coupon_type :selected").val();
        var amount=$(".coupon_amount").val();
        var code=$(".coupon_code").val();
        var enable=false;
        var isPublic=false;
        if($(".coupon_active").is(':checked')) {
            enable=true;
        }
        if($(".coupon_public").is(':checked')) {
            isPublic=true;
        }
        var validity=$(".coupon_validity").val();

        if(!isEnglishNameValid) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.coupon_title_error_en_required')}}</p>");
            window.scrollTo(0,0);
        } else if(amount==''||amount<=0) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.coupon_amount_error')}}</p>");
            window.scrollTo(0,0);
        } else if(code=='') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.coupon_code_help')}}</p>");
            window.scrollTo(0,0);
        } else if(validity=='') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.coupon_validity_help')}}</p>");
            window.scrollTo(0,0);
        } else {
            requestId=='0'
                ? (database.collection('coupon').doc(id).set({
                    'id': id,
                    'title': titles,
                    'type': type,
                    'amount': amount,
                    'code': code.toUpperCase(),
                    'enable': enable,
                    'isPublic': isPublic,
                    'validity': new Date(validity),
                    'isDeleted': false,
                }).then(function(result) {
                    window.location.href='{{ route("coupons")}}';
                }).catch(function(error) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>"+error+"</p>");
                }))
                :(database.collection('coupon').doc(id).update({
                    'id': id,
                    'title': titles,
                    'type': type,
                    'amount': amount,
                    'code': code.toUpperCase(),
                    'enable': enable,
                    'isPublic': isPublic,
                    'validity': new Date(validity),
                    'isDeleted': false,
                }).then(function(result) {
                    window.location.href='{{ route("coupons")}}';
                }).catch(function(error) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>"+error+"</p>");
                }))
        }
    })
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
            if(language.isDefault) {
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
            content.id=`content-${language.code}`;
            content.role="tabpanel";
            content.innerHTML=`
           <div class="form-group row width-100">
                <label class="col-3 control-label" for="coupon-title-${language.code}">{{trans('lang.coupon_title')}} (${language.code.toUpperCase()})<span class="required-field"></span></label>
                <div class="col-7">
                    <input type="text" class="form-control" id="coupon-title-${language.code}">
                    <div class="form-text text-muted">{{ trans("lang.coupon_title_help") }}</div>
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