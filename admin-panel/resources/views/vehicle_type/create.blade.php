@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.vehicle_add')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a
                        href="{!! route('vehicle-type') !!}">{{trans('lang.vehicle_type_table')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.vehicle_add')}}</li>
            </ol>
        </div>
    </div>
    <div class="card-header">
        <ul class="nav nav-tabs" id="language-tabs" role="tablist">
        </ul>
    </div>
    <div class="card-body">

        <div class="error_top"></div>

        <div class="row restaurant_payout_create">
            <div class="restaurant_payout_create-inner">
                <fieldset>
                    <legend>{{trans('lang.vehicle_type')}}</legend>
                        <div class="tab-content" id="language-contents">
                            </div>

                    <div class="form-group row width-100">
                        <div class="form-check">
                            <input type="checkbox" class="vehicle_active" id="active">
                            <label class="col-3 control-label" for="active">{{trans('lang.enable')}}</label>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <div class="form-group col-12 text-center btm-btn">
            <button type="button" class="btn btn-primary  save-setting-btn"><i class="fa fa-save"></i> {{
                trans('lang.save')}}
            </button>
            <a href="{!! route('vehicle-type') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
                trans('lang.cancel')}}</a>
        </div>

    </div>

</div>
@endsection

@section('scripts')

<script>

    var database=firebase.firestore();
    var photo="";

    $(document).ready(function() {
        fetchLanguages().then(createLanguageTabs);

        $('.vehicle_type_menu').addClass('active');

    });

    $(".save-setting-btn").click(function() {

        var names=[];

        $("[id^='vehicle-name-']").each(function() {
            var languageCode=$(this).attr('id').replace('vehicle-name-','');
            var nameValue=$(this).val();

            names.push({
                name: nameValue,
                type: languageCode
            });
        });
        var isEnglishNameValid=names.some(function(nameObj) {
            return nameObj.type==='en'&&nameObj.name.trim()!=='';
        });

        var enable=false;

        if($(".vehicle_active").is(':checked')) {
            enable=true;
        }

        var id=database.collection("tmp").doc().id;

        if(!isEnglishNameValid) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.vehicle_name_en_required')}}</p>");
            window.scrollTo(0,0);
        } else {
            jQuery("#overlay").show();

            database.collection('vehicle_type').doc(id).set({
                'name': names,
                'id': id,
                'enable': enable,
            }).then(function(result) {
                jQuery("#overlay").hide();

                window.location.href='{{ route("vehicle-type")}}';
            }).catch(function(error) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>"+error+"</p>");
            });
        }
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
            content.id=`content-${language.code}`; // Ensure this matches the tab link's href
            content.role="tabpanel";
            content.innerHTML=`
            <div class="form-group row width-100">
                <label class="col-3 control-label" for="vehicle-name-${language.code}">{{trans('lang.vehicle_name')}} (${language.code.toUpperCase()})<span class="required-field"></span></label>
                <div class="col-7">
                    <input type="text" class="form-control" id="vehicle-name-${language.code}">
                    <div class="form-text text-muted">{{ trans("lang.vehicle_name_help") }}</div>
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