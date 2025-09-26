@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.on_board_plural')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('on-board') !!}">{{trans('lang.on_board_plural')}}</a>
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

                <div class="error_top"></div>

                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{trans('lang.on_board_details')}}</legend>
                            <div class="tab-content" id="language-contents">
                            </div>
                            {{--<div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.title')}}<span
                                class="required-field"></span></label>
                            <div class="col-7">
                                <input type="text" class="form-control title">
                                <div class="form-text text-muted">
                                    {{ trans("lang.title_help") }}
                                </div>
                            </div>
                    </div>


                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{trans('lang.description')}}<span
                                class="required-field"></span></label>
                        <div class="col-7">
                            <textarea rows="6" id="description" class="description form-control"></textarea>
                            <div class="form-text text-muted">
                                {{ trans("lang.description_help") }}
                            </div>
                        </div>
                    </div>
                    --}}
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.image')}}</label>
                        <div class="col-7">
                            <input type="file" onChange="handleFileSelect(event)" class="form-control image" id="image">
                            <div class="placeholder_img_thumb user_image"></div>
                            <div id="uploding_image"></div>
                            <div class="form-text text-muted w-50">
                                {{ trans("lang.image_help") }}
                            </div>
                        </div>
                    </div>

                    </fieldset>
                </div>
            </div>

            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary  edit-setting-btn"><i class="fa fa-save"></i> {{
                        trans('lang.save')}}</button>
                <a href="{!! route('on-board') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
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

    var requestId="{{$id}}";
    var storageRef=firebase.storage().ref('on-boarding');
    var storage=firebase.storage();
    var photo="";
    var fileName="";
    var ImageFile='';
    var placeholderImage="{{ asset('/images/default_user.png') }}";
    $(document).ready(function() {

        $('.onboard_menu').addClass('active');
        fetchLanguages().then(createLanguageTabs);


        jQuery("#overlay").show();
        var ref=database.collection('on_boarding').where("id","==",requestId);
        ref.get().then(async function(snapshots) {
            var data=snapshots.docs[0].data();

            if(data&&Array.isArray(data.title)) {
                data.title.forEach(function(titleObj) {
                    var inputField=$(`#title-${titleObj.type}`);
                    if(inputField.length) {
                        inputField.val(titleObj.title);
                    }
                });
            }
            if(data&&Array.isArray(data.description)) {
                data.description.forEach(function(titleObj) {
                    var inputField=$(`#description-${titleObj.type}`);
                    if(inputField.length) {
                        inputField.val(titleObj.description);
                    }
                });
            }

            if(data.image==''||data.image==null) {

                $(".user_image").append('<span class="image-item"><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="'+placeholderImage+'" alt="image">');
            } else {
                console.log('else');
                photo=data.image;
                ImageFile=data.image;
                $(".user_image").append('<span class="image-item"><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="'+data.image+'" alt="image">');
            }
            jQuery("#overlay").hide();
        })

    });

    $(".edit-setting-btn").click(function() {
        jQuery("#overlay").show();

        var titles=[];
        var descriptions=[];
        $("[id^='title-']").each(function() {
            var languageCode=$(this).attr('id').replace('title-','');
            var nameValue=$(this).val();

            titles.push({
                title: nameValue,
                type: languageCode
            });
        });
        var isEnglishNameValid=titles.some(function(nameObj) {
            return nameObj.type==='en'&&nameObj.title.trim()!=='';
        });
        $("[id^='description-']").each(function() {
            var languageCode=$(this).attr('id').replace('description-','');
            var nameValue=$(this).val();

            descriptions.push({
                description: nameValue,
                type: languageCode
            });
        });
        var isEnglishDescValid=descriptions.some(function(nameObj) {
            return nameObj.type==='en'&&nameObj.description.trim()!=='';
        });

        if(!isEnglishNameValid) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.title_error_en_required')}}</p>");
            window.scrollTo(0,0);
        } if(!isEnglishDescValid) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.description_error_en_required')}}</p>");
            window.scrollTo(0,0);
        } else {
            storeImageData().then(IMG => {
                database.collection('on_boarding').doc(requestId).update({
                    'title': titles,
                    'description': descriptions,
                    'image': IMG,
                }).then(function(result) {
                    window.location.href='{{ route("on-board")}}';
                }).catch(function(error) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>"+error+"</p>");
                })
            });
        }
    })

    async function storeImageData() {
        var newPhoto='';
        try {
            if(ImageFile!=""&&photo!=ImageFile) {
                var OldImageUrlRef=await storage.refFromURL(ImageFile);
                imageBucket=OldImageUrlRef.bucket;
                var envBucket="<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                if(imageBucket==envBucket) {

                    await OldImageUrlRef.delete().then(() => {
                        console.log("Old file deleted!")
                    }).catch((error) => {
                        console.log("ERR File delete ===",error);
                    });
                } else {
                    console.log('Bucket not matched');
                }

            }
            if(photo!=ImageFile) {
                photo=photo.replace(/^data:image\/[a-z]+;base64,/,"")
                var uploadTask=await storageRef.child(fileName).putString(photo,'base64',{contentType: 'image/jpg'});
                var downloadURL=await uploadTask.ref.getDownloadURL();
                newPhoto=downloadURL;
                photo=downloadURL;
            } else {
                newPhoto=photo;
            }
        } catch(error) {
            console.log("ERR ===",error);
        }
        return newPhoto;
    }
    function handleFileSelect(evt) {
        var f=evt.target.files[0];
        var reader=new FileReader();
        reader.onload=(function(theFile) {

            return function(e) {

                var filePayload=e.target.result;
                var val=f.name;
                var ext=val.split('.')[1];
                var docName=val.split('fakepath')[1];
                var filename=(f.name).replace(/C:\\fakepath\\/i,'')
                var timestamp=Number(new Date());
                var filename=filename.split('.')[0]+"_"+timestamp+'.'+ext;
                photo=filePayload;
                fileName=filename;
                $(".user_image").empty();
                $(".user_image").append('<span class="image-item" id="photo_user"><span class="remove-btn" data-id="user-remove" data-img="'+photo+'"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="'+photo+'" alt="image"></span>');

            };
        })(f);
        reader.readAsDataURL(f);
    }

    $(document).on("click",".remove-btn",function() {
        $(".image-item").remove();
        $('#image').val('');
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
                <label class="col-3 control-label" for="title-${language.code}">{{trans('lang.title')}} (${language.code.toUpperCase()})<span class="required-field"></span></label>
                <div class="col-7">
                    <input type="text" class="form-control" id="title-${language.code}">
                    <div class="form-text text-muted">{{ trans("lang.title_help") }}</div>
                </div>                             
            </div>
            <div class="form-group row width-100">
                <label class="col-3 control-label" for="description-${language.code}">{{trans('lang.description')}} (${language.code.toUpperCase()})<span class="required-field"></span></label>
                <div class="col-7">
                    <textarea rows="6" id="description-${language.code}" class="description form-control"></textarea>

                    <div class="form-text text-muted">{{ trans("lang.description_help") }}</div>
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