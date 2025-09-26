@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.document_plural')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('documents') !!}">{{trans('lang.document_plural')}}</a>
                </li>
                <li class="breadcrumb-item active">{{ $id == 0 ? trans('lang.document_create') :
    trans('lang.document_edit')}}
                </li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card  pb-4">
            <div class="card-header">
                <ul class="nav nav-tabs" id="language-tabs" role="tablist">
                </ul>
            </div>
            <div class="card-body">

                <div class="error_top"></div>

                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{trans('lang.document_details')}}</legend>
                            <div class="tab-content" id="language-contents">
                    </div>
                    <div class="form-group row width-50">
                        <div class="form-check">
                            <input type="checkbox" class="document_active" id="document_active">
                            <label class="col-3 control-label" for="document_active">{{trans('lang.enable')}}</label>
                        </div>
                    </div>

                    <div class="form-group row width-50">
                        <div class="form-check">
                            <input type="checkbox" class="document_front_active" id="document_front_active" checked>
                            <label class="col-3 control-label"
                                for="document_front_active">{{trans('lang.document_front_active')}}<span
                                    class="required-field"></span></label>
                        </div>
                    </div>

                    <div class="form-group row width-50">
                        <div class="form-check">
                            <input type="checkbox" class="document_back_active" id="document_back_active">
                            <label class="col-3 control-label"
                                for="document_back_active">{{trans('lang.document_back_active')}}</label>
                        </div>
                    </div>

                    <div class="form-group row width-50">
                        <div class="form-check">
                            <input type="checkbox" class="document_expire_active" id="document_expire_active">
                            <label class="col-3 control-label"
                                for="document_expire_active">{{trans('lang.document_expire_active')}}</label>
                        </div>
                    </div>


                    </fieldset>
                </div>
            </div>

            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary  edit-setting-btn"><i class="fa fa-save"></i> {{
    trans('lang.save')}}
                </button>
                <a href="{!! route('documents') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
    trans('lang.cancel')}}</a>
            </div>

        </div>

    </div>
</div>
</div>

@endsection

@section('scripts')

<script>

    var database=firebase.firestore();
    var alldriver=database.collection('driver_users');
    var ref=database.collection('documents');
    var requestId="{{$id}}";
    var id=(requestId=='0')? database.collection("tmp").doc().id:requestId;
    var docDeleteAlert="{{trans('lang.doc_delete_alert')}}";

    $(document).ready(function() {
        fetchLanguages().then(createLanguageTabs);

        $('.document_sub_menu li').each(function() {
            var url=$(this).find('a').attr('href');
            if(url==document.referrer) {
                $(this).find('a').addClass('active');
                $('.document_menu').addClass('active').attr('aria-expanded',true);
            }
            $('.document_sub_menu').addClass('in').attr('aria-expanded',true);
        });
        if(requestId!='0') {
            jQuery("#overlay").show();
            var ref=database.collection('documents').doc(id.trim());
            ref.get().then(async function(snapshots) {
                var data=snapshots.data();
                if(data && Array.isArray(data.title)) {
                    data.title.forEach(function(titleObj) {
                        var inputField=$(`#document-title-${titleObj.type}`);  
                        if(inputField.length) {
                            inputField.val(titleObj.title);  
                        }
                    });
                }
                $('.document_active').prop('checked',data.enable? true:false);
                $('.document_front_active').prop('checked',data.frontSide? true:false);
                $('.document_back_active').prop('checked',data.backSide? true:false);
                $('.document_expire_active').prop('checked',data.expireAt? true:false);
                jQuery("#overlay").hide();
            })
        }
    });

    $(".edit-setting-btn").click(function() {

        var titles=[];

        $("[id^='document-title-']").each(function() {
            var languageCode=$(this).attr('id').replace('document-title-','');

            var nameValue=$(this).val();

            titles.push({
                title: nameValue,
                type: languageCode
            });
        });
        var isEnglishNameValid=titles.some(function(nameObj) {
            return nameObj.type==='en'&&nameObj.title.trim()!=='';
        });

        var enable=$(".document_active").is(':checked')? true:false;
        var frontSide=$(".document_front_active").is(':checked')? true:false;
        var backSide=$(".document_back_active").is(':checked')? true:false;
        var expireAt=$(".document_expire_active").is(':checked')? true:false;
        var length=0;
        database.collection('documents').where('enable',"==",true).get().then(function(snapshots) {
            length=snapshots.docs.length;
        });
        if(!isEnglishNameValid) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.document_title_en_required')}}</p>");
            window.scrollTo(0,0);
        } else if(frontSide==false) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.document_front_active')}}</p>");
            window.scrollTo(0,0);
        } else if(enable==false&&length==1) {
            $(".document_active").prop('checked',true);
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>"+docDeleteAlert+"</p>");
            window.scrollTo(0,0);
            return false;
        } else {
            jQuery("#overlay").show();

            requestId=='0'
                ? (database.collection('documents').doc(id.trim()).set({
                    'id': id,
                    'title': titles,
                    'enable': enable,
                    'frontSide': frontSide,
                    'backSide': backSide,
                    'expireAt': expireAt,
                    'isDeleted': false,
                }).then(async function(result) {
                    var enableDocIds=await getDocId();
                    await alldriver.get().then(async function(snapshotsdriver) {
                        if(snapshotsdriver.docs.length>0) {
                            var verification=await driverDocVerification(enableDocIds,snapshotsdriver);
                            if(verification) {
                                jQuery("#overlay").hide();
                                window.location.href='{{ route("documents")}}';
                            }
                        } else {
                            jQuery("#overlay").hide();
                            window.location.href='{{ route("documents")}}';
                        }
                    })
                }).catch(function(error) {
                    jQuery("#overlay").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>"+error+"</p>");
                }))
                :(database.collection('documents').doc(id.trim()).update({
                    'title': titles,
                    'enable': enable,
                    'frontSide': frontSide,
                    'backSide': backSide,
                    'expireAt': expireAt,
                    'isDeleted': false,
                }).then(async function(result) {
                    var enableDocIds=await getDocId();
                    await alldriver.get().then(async function(snapshotsdriver) {
                        if(snapshotsdriver.docs.length>0) {
                            var verification=await driverDocVerification(enableDocIds,snapshotsdriver);
                            if(verification) {
                                jQuery("#overlay").hide();
                                window.location.href='{{ route("documents")}}';
                            }
                        } else {
                            jQuery("#overlay").hide();
                            window.location.href='{{ route("documents")}}';
                        }
                    })
                }).catch(function(error) {
                    jQuery("#overlay").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>"+error+"</p>");
                }))
        }
    });

    async function getDocId() {
        var enableDocIds=[];
        await database.collection('documents').where('isDeleted','==',false).where('enable',"==",true).get().then(async function(snapshots) {
            await snapshots.forEach((doc) => {
                enableDocIds.push(doc.data().id);
            });
        });
        return enableDocIds;
    }

    async function driverDocVerification(enableDocIds,snapshotsdriver) {
        var isCompleted=false;
        await Promise.all(snapshotsdriver.docs.map(async (driver) => {
            await database.collection('driver_document').doc(driver.id).get().then(async function(docrefSnapshot) {
                if(docrefSnapshot.data()&&docrefSnapshot.data().documents.length>0) {
                    var driverDocId=await docrefSnapshot.data().documents.filter((doc) => doc.verified==true).map((docData) => docData.documentId);
                    if(driverDocId.length>=enableDocIds.length) {
                        await database.collection('driver_users').doc(driver.id).update({'documentVerification': true});
                    } else {
                        await enableDocIds.forEach(async (docId) => {
                            if(!driverDocId.includes(docId)) {
                                await database.collection('driver_users').doc(driver.id).update({'documentVerification': false});
                            }
                        });
                    }
                } else {
                    await database.collection('driver_users').doc(driver.id).update({'documentVerification': false});
                }
            });
            isCompleted=true;
        }));
        return isCompleted;
    }
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
            const tab=document.createElement('li');
            tab.classList.add('nav-item');
            var defaultClass='';
            if(language.isDefault){
                defaultClass='{{trans("lang.default")}}';
            }
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
                <label class="col-3 control-label" for="zone-${language.code}">{{trans('lang.document_title')}} (${language.code.toUpperCase()})<span class="required-field"></span></label>
                <div class="col-7">
                    <input type="text" class="form-control" id="document-title-${language.code}">
                    <div class="form-text text-muted">{{ trans("lang.document_title_help") }}</div>
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