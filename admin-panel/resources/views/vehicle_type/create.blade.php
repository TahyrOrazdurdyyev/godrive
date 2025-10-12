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

                        <!-- Image Upload Section -->
                        <div class="form-group row width-100">
                            <label class="col-3 control-label">{{trans('lang.vehicle_image')}}</label>
                            <div class="col-7">
                                <input type="file" id="vehicle_image" accept="image/*" class="form-control">
                                <div class="placeholder_img_thumb vehicle_image"></div>
                                <div id="uploding_image_vehicle"></div>
                            </div>
                        </div>

                        <div class="tab-content" id="language-contents">
                        </div>

                    <div class="form-group row width-100">
                        <div class="form-check">
                            <input type="checkbox" class="vehicle_active" id="active" checked>
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

    var photo = "";

    $(document).ready(function() {
        fetchLanguages().then(createLanguageTabs);

        $('.vehicle_type_menu').addClass('active');

        // Image upload handler
        $("#vehicle_image").change(function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    photo = e.target.result;
                    $(".vehicle_image").html('<img src="' + e.target.result + '" style="max-width:200px; max-height:200px;">');
                }
                reader.readAsDataURL(file);
            }
        });
    });

    $(".save-setting-btn").click(function() {

        var names = [];

        $("[id^='vehicle-name-']").each(function() {
            var languageCode = $(this).attr('id').replace('vehicle-name-', '');
            var nameValue = $(this).val();

            names.push({
                name: nameValue,
                type: languageCode
            });
        });

        var isEnglishNameValid = names.some(function(nameObj) {
            return nameObj.type === 'en' && nameObj.name.trim() !== '';
        });

        var enable = false;

        if ($(".vehicle_active").is(':checked')) {
            enable = true;
        }

        if (!isEnglishNameValid) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.vehicle_name_en_required')}}</p>");
            window.scrollTo(0, 0);
        } else if (!photo) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>Please upload a vehicle image</p>");
            window.scrollTo(0, 0);
        } else {
            jQuery("#overlay").show();

            const formData = {
                title: JSON.stringify(names),
                image: photo,
                enable: enable
            };

            fetch('http://185.10.16.248:8080/api/v1/vehicle-types', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                jQuery("#overlay").hide();
                if (data.success) {
                    alert('Vehicle Type created successfully!');
                    window.location.href = '{{ route("vehicle-type") }}';
                } else {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>" + data.message + "</p>");
                }
            })
            .catch(error => {
                jQuery("#overlay").hide();
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>Error: " + error + "</p>");
            });
        }
    });

    async function fetchLanguages() {
        console.log("🔥 fetchLanguages called");
        try {
            const response = await fetch('http://185.10.16.248/api/v1/languages');
            const result = await response.json();
            console.log("🔥 Languages fetched:", result);
            if (result.success) {
                return result.data.map(lang => ({
                    code: lang.code,
                    name: lang.name,
                    isDefault: lang.is_default,
                    enable: lang.enable
                }));
            }
            return [];
        } catch (error) {
            console.error('Error fetching languages:', error);
            return [];
        }
    }

    function createLanguageTabs(languages) {
        console.log("🔥 createLanguageTabs called with:", languages);
        const tabsContainer = document.getElementById('language-tabs');
        const contentsContainer = document.getElementById('language-contents');

        tabsContainer.innerHTML = '';
        contentsContainer.innerHTML = '';

        const defaultLanguage = languages.find(language => language.isDefault);
        const otherLanguages = languages.filter(language => !language.isDefault);
        otherLanguages.sort((a, b) => a.name.localeCompare(b.name));
        const sortedLanguages = [defaultLanguage, ...otherLanguages];
        sortedLanguages.forEach((language, index) => {
            var defaultClass = '';
            if (language.isDefault) {
                defaultClass = '{{trans("lang.default")}}';
            }
            const tab = document.createElement('li');
            tab.classList.add('nav-item');
            tab.innerHTML = `
            <a class="nav-link ${index===0? 'active':''}" id="tab-${language.code}" data-bs-toggle="tab" href="#content-${language.code}" role="tab" aria-selected="${index===0}">
                ${language.name} (${language.code.toUpperCase()})
                <span class="badge badge-success ml-2">${defaultClass}</span>
            </a>
        `;
            tabsContainer.appendChild(tab);

            const content = document.createElement('div');
            content.classList.add('tab-pane', 'fade');
            if (index === 0) {
                content.classList.add('show', 'active');
            }
            content.id = `content-${language.code}`;
            content.role = "tabpanel";
            content.innerHTML = `
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

        const triggerTabList = document.querySelectorAll('#language-tabs a');
        triggerTabList.forEach(tab => {
            tab.addEventListener('click', function(event) {
                event.preventDefault();

                document.querySelectorAll('.tab-pane').forEach(function(pane) {
                    pane.classList.remove('active', 'show');
                });

                document.querySelectorAll('.nav-link').forEach(function(navTab) {
                    navTab.classList.remove('active');
                });

                this.classList.add('active');
                const target = this.getAttribute('href');
                const targetPane = document.querySelector(target);
                if (targetPane) {
                    targetPane.classList.add('active', 'show');
                }
            });
        });
    }

</script>
@endsection
