@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.privacy_policy')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.privacy_policy')}}</li>
            </ol>
        </div>


    </div>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs" id="language-tabs" role="tablist">
                </ul>
            </div>
            <div class="card-body">

                <div id="data-table_processing" class="dataTables_processing panel panel-default"
                    style="display: none;">{{trans('lang.processing')}}</div>
                <div class="error_top"></div>

                <div class="terms-cond restaurant_payout_create row">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{trans('lang.privacy_policy')}}</legend>
                            <div class="tab-content" id="language-contents">
                            </div>

                        </fieldset>

                    </div>
                </div>
                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary  edit-setting-btn"><i class="fa fa-save"></i>
                        {{ trans('lang.save')}}</button>
                    <a href="{!! route('settings.privacyPolicy') !!}" class="btn btn-default"><i
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
    var photo="";
    var ref=database.collection('settings').doc('global');
    $(document).ready(async function() {
        await fetchLanguages().then(createLanguageTabs);
        $('.privacy_policy').summernote({
            height: 400,
            toolbar: [
                ['style',['bold','italic','underline','clear']],
                ['font',['strikethrough','superscript','subscript']],
                ['fontsize',['fontsize']],
                ['color',['color']],
                ['forecolor',['forecolor']],
                ['backcolor',['backcolor']],
                ['para',['ul','ol','paragraph']],
                ['height',['height']]
            ]
        });

        try {
            jQuery("#overlay").show();
            ref.get().then(async function(snapshots) {
                var globalSetting=snapshots.data();
                if(Array.isArray(globalSetting.privacyPolicy)) {
                    globalSetting.privacyPolicy.forEach(function(titleObj) {
                        var inputField=$(`#privacy-policy-${titleObj.type}`);
                        if(inputField.length) {
                            inputField.summernote("code",titleObj.privacyPolicy);
                        }
                    });
                }
                /*if(globalSetting.privacyPolicy) {
                    $('#privacy_policy').summernote("code",globalSetting.privacyPolicy);
                }*/
                jQuery("#overlay").hide();
            });
           
        } catch(error) {
            jQuery("#overlay").hide();
        }

        $(".edit-setting-btn").click(function() {
            var privacies=[];

            $("[id^='privacy-policy-']").each(function() {
                var languageCode=$(this).attr('id').replace('privacy-policy-','');

                var nameValue=$(this).summernote('code');

                privacies.push({
                    privacyPolicy: nameValue,
                    type: languageCode
                });
            });
            var isEnglishNameValid=privacies.some(function(nameObj) {
                return nameObj.type==='en'&&nameObj.privacyPolicy.trim()!=='';
            });

            if(!isEnglishNameValid) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.privacy_policy_error_en_required')}}</p>");
                window.scrollTo(0,0);
            } else {
                database.collection('settings').doc('global').update({'privacyPolicy': privacies}).then(function(result) {
                    window.location.href='{{ route("settings.privacyPolicy")}}';
                })
            }
        })
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
            content.id=`content-${language.code}`;
            content.role="tabpanel";
            content.innerHTML=`
           <div class="form-group row width-100">
            <label class="col-3 control-label" for="privacy-policy-${language.code}">{{trans('lang.privacy')}} (${language.code.toUpperCase()})<span class="required-field"></span></label>

             <textarea class="form-control col-7 privacy_policy"  id="privacy-policy-${language.code}"></textarea>

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