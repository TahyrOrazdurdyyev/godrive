<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?> dir="rtl" <?php } ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GoRide') }}</title>
    <link rel="icon" id="favicon" type="image/x-icon" href="">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>
    <link href="{{asset('assets/plugins/bootstrap/css/bootstrap-rtl.min.css')}}" rel="stylesheet">
    <?php } ?>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>
    <link href="{{asset('css/style_rtl.css')}}" rel="stylesheet">
    <?php } ?>
    <link href="{{ asset('css/icons/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/icons/font-awesome/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/toast-master/css/jquery.toast.css')}}" rel="stylesheet">
    <link href="{{ asset('css/colors/blue.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chosen.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-tagsinput.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/summernote/summernote-bs4.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2/dist/css/select2.min.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/leaflet/leaflet.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/leaflet/leaflet.draw.css') }}">
</head>
<body>
<div id="app" class="fix-header fix-sidebar card-no-border">
    <div id="main-wrapper">
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                @include('layouts.header')
            </nav>
        </header>
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                @include('layouts.menu')
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <footer class="footer">
            @include('layouts.footer')
        </footer>
    </div>
    <main class="py-4">
        @yield('content')
        <div id="overlay" style="display:none">
            <img src="{{ asset('images/spinner.gif') }}">
        </div>
    </main>
</div>

<script src="{{ asset('js/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('js/leaflet/leaflet.draw.js') }}"></script>
<script src="{{ asset('js/leaflet/leaflet.editable.min.js') }}"></script>
<script src="{{ asset('js/leaflet/leaflet.draw-src.js') }}"></script>
<script src="https://unpkg.com/leaflet-geojson-layer/src/leaflet.geojson.js"></script>
<script src="{{ asset('js/leaflet/leaflet-routing-machine.js') }}"></script>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
<script src="{{ asset('js/waves.js') }}"></script>
<script src="{{ asset('js/sidebarmenu.js') }}"></script>
<script src="{{ asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
<script src="{{ asset('js/custom.min.js') }}"></script>
<script src="{{ asset('assets/plugins/summernote/summernote-bs4.js')}}"></script>
<script src="{{ asset('js/jquery.resizeImg.js') }}"></script>
<script src="{{ asset('js/mobileBUGFix.mini.js') }}"></script>
<script src="{{ asset('js/jquery.masking.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-functions.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-storage.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-database.js"></script>
<script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script src="{{ asset('js/chosen.jquery.js') }}"></script>
<script src="{{ asset('js/bootstrap-tagsinput.js') }}"></script>
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.24/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>
<script type="text/javascript">
    
    var appLogo = '';
    var appFavIconLogo = '';
    var googleApiKey = '';
    var database = firebase.firestore();
    let globalRef = database.collection('settings').doc('global');
    globalRef.get().then(async function (snapshots) {
        var globalSetting = snapshots.data();
        if (globalSetting.appVersion != '') {
            $(".web_version").text('V:' + globalSetting.appVersion);
        }
    });
        var langcount=0;
         database.collection('languages').where('isDeleted','==',false).get().then(async function(snapshotslang) {
            if(snapshotslang.docs.length>0) {
                snapshotslang.docs.forEach((doc) => {
                    var data=doc.data();
                    if(data.enable==true) {
                        langcount++;
                        $('#language_dropdown').append($("<option></option>").attr("value",data.id).attr("data-isrtl",data.isRtl).attr("data-code",data.code).text(data.name));
                    }
                    if(data.isDefault){
                         if(!getCookie('setLanguage')) {
                            setCookie('setLanguage',data.code,365);
                        }
                        setCookie('defaultLanguage',data.code,365);
                    }
                });
                if(getCookie('setLanguage')) {
                    $('#language_dropdown option').each(function() {
                        if($(this).attr('data-code')==getCookie('setLanguage')) {
                            $(this).prop('selected',true); // Select the matching option
                        }
                    });
                }
                if(langcount>1) {
                    $("#language_dropdown_box").css('visibility','visible');
                }
                <?php if (session()->get('locale')) { ?>
                $("#language_dropdown").val("<?php    echo session()->get('locale'); ?>");
                <?php } ?>
            }
        });
    $(document).ready(async function () {
        let globalLogoRef = database.collection('settings').doc('logo');
        globalLogoRef.get().then(async function (snapshots) {
            var globalLogoSetting = snapshots.data();
            $("#favicon").attr("href", globalLogoSetting.appFavIconLogo)
            $(".dark-logo").attr("src", globalLogoSetting.appLogo);
            $(".light-logo").attr("src", globalLogoSetting.appLogo);
        });
        });
        var refCurrency = database.collection('currency').where('enable', '==', true);
        refCurrency.get().then(async function(snapshots) {
            var currencyData = snapshots.docs[0].data();
            $(".currentCurrency").text(currencyData.symbol);
        });
    var refGlobalSetting = database.collection('settings').doc('globalValue');
    refGlobalSetting.get().then(function (globalData) {
        var globalValue = globalData.data();
        if (globalValue && globalValue.hasOwnProperty('distanceType')) {
            $('.global_value').html(globalValue.distanceType + " {{trans('lang.charge')}}");
            $('.global_value_label').html(globalValue.distanceType + " {{trans('lang.charge')}}" + '<span class="required-field"></span>');
            $('.global_value_text').html("{{trans('lang.enter')}} " + globalValue.distanceType + " {{trans('lang.charge')}}");
            $('.global_basic_label').html(globalValue.distanceType);
            $('#distanceType').val(globalValue.distanceType);
        } else {
            $('.global_value').html('{{trans("lang.km_charge")}}');
            $('.global_value_text').html('{{ trans("lang.km_charge_help") }}');
            $('.global_basic_label').html("{{trans('lang.km')}}");
            $('#distanceType').val('Km');
        }
    });
    async function sendNotification(fcmToken = '', title, body) {
        var checkFlag = false;
        var sendNotificationUrl = "{{ route('send-notification') }}";
        if (fcmToken !== '') {
            await $.ajax({
                type: 'POST',
                url: sendNotificationUrl,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    'fcm': fcmToken,
                    'title': title,
                    'message': body
                },
                success: function (data) {
                    checkFlag = true;
                },
                error: function (error) {
                    checkFlag = true;
                }
            });
        } else {
            checkFlag = true;
        }
        return checkFlag;
    }
    function exportData(dt,format,config) {
        const {
            columns,
            fileName='Export',
        }=config;
        const filteredRecords=dt.ajax.json().filteredData;
        const fieldTypes={};
        const dataMapper=(record) => {
            return columns.map((col) => {
                const value=record[col.key];
                if(!fieldTypes[col.key]) {
                    if(value===true||value===false) {
                        fieldTypes[col.key]='boolean';
                    } else if(value&&typeof value==='object'&&value.seconds) {
                        fieldTypes[col.key]='date';
                    } else if(typeof value==='number') {
                        fieldTypes[col.key]='number';
                    } else if(typeof value==='string') {
                        fieldTypes[col.key]='string';
                    } else {
                        fieldTypes[col.key]='string';
                    }
                }
                switch(fieldTypes[col.key]) {
                    case 'boolean':
                        return value? 'Yes':'No';
                    case 'date':
                        return value? new Date(value.seconds*1000).toLocaleString():'-';
                    case 'number':
                        return typeof value==='number'? value:0;
                    case 'string':
                    default:
                        return value||'-';
                }
            });
        };
        const tableData=filteredRecords.map(dataMapper);
        const data=[columns.map(col => col.header),...tableData];
        const columnWidths=columns.map((_,colIndex) =>
            Math.max(...data.map(row => row[colIndex]?.toString().length||0))
        );
        if(format==='csv') {
            const csv=data.map(row => row.map(cell => {
                if(typeof cell==='string'&&(cell.includes(',')||cell.includes('\n')||cell.includes('"'))) {
                    return `"${cell.replace(/"/g,'""')}"`;
                }
                return cell;
            }).join(',')).join('\n');
            const blob=new Blob([csv],{type: 'text/csv;charset=utf-8;'});
            saveAs(blob,`${fileName}.csv`);
        } else if(format==='excel') {
            const ws=XLSX.utils.aoa_to_sheet(data,{cellDates: true});
            ws['!cols']=columnWidths.map(width => ({wch: Math.min(width+5,30)}));
            const wb=XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb,ws,'Data');
            XLSX.writeFile(wb,`${fileName}.xlsx`);
        } else if(format==='pdf') {
            const {jsPDF}=window.jspdf;
            const doc=new jsPDF();
            const totalLength=columnWidths.reduce((sum,length) => sum+length,0);
            const columnStyles={};
            columnWidths.forEach((length,index) => {
                columnStyles[index]={
                    cellWidth: (length/totalLength)*180,
                };
            });
            doc.setFontSize(16);
            doc.text(fileName,14,16);
            doc.autoTable({
                head: [columns.map(col => col.header)],
                body: tableData,
                startY: 20,
                theme: 'striped',
                styles: {
                    cellPadding: 2,
                    fontSize: 10,
                },
                columnStyles,
                margin: {top: 30,bottom: 30},
                didDrawPage: function(data) {
                    doc.setFontSize(10);
                    doc.text(fileName,data.settings.margin.left,10);
                }
            });
            doc.save(`${fileName}.pdf`);
        } else {
            console.error('Unsupported format');
        }
    }
</script>
<script type="text/javascript">
    
    jQuery(window).scroll(function () {
        var scroll = jQuery(window).scrollTop();
        if (scroll <= 60) {
            jQuery("body").removeClass("sticky");
        } else {
            jQuery("body").addClass("sticky");
        }
    });
    var url = "{{ route('changeLang') }}";
    $(".changeLang").change(async function () {
        var slug = $(this).val();
        var code=$(this).find(':selected').data('code');
        setCookie('setLanguage',code,365);
        var isrtl = $(this).find(':selected').data('isrtl');
        if (isrtl == true) {
            setCookie('is_rtl', isrtl.toString(), 365);
        } else {
            setCookie('is_rtl', 'false', 365);
        }
        window.location.href = url + "?lang=" + slug;
    });
    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    var mapType = 'ONLINE';
    database.collection('settings').doc('globalValue').get().then(async function (snapshots) {
        var data = snapshots.data();
        if (data && data.selectedMapType && data.selectedMapType == "osm") {
            mapType = "OFFLINE";
        }
    });
    async function loadGoogleMapsScript(callback) {
        var globalKeySnapshot = await database.collection('settings').doc("globalKey").get();
        var globalKeyData = globalKeySnapshot.data();
        googleMapKey = globalKeyData.googleMapKey;
        if (window.google && window.google.maps) {
                callback();
                return;
            }
        const script = document.createElement('script');
        if (mapType == "OFFLINE"){
            script.src = "{{ asset('js/leaflet/leaflet.js')}}"; 
            script.src = "{{ asset('js/leaflet/leaflet.draw.js')}}"; 
            script.src = "{{ asset('js/leaflet/leaflet.editable.min.js')}}";
            script.src = "{{ asset('js/leaflet/leaflet.draw-src.js')}}";
            script.src = "{{ asset('js/leaflet/leaflet.ajax.min.js')}}";
            script.src = "https://unpkg.com/leaflet-geojson-layer/src/leaflet.geojson.js";
            script.src = "{{ asset('js/leaflet/leaflet-routing-machine.js')}}";  
        }
        else
        {
            
            script.src = "https://maps.googleapis.com/maps/api/js?key=" + googleMapKey + "&libraries=drawing,geometry,places";
        }
        
        script.onload = function () {
            navigator.geolocation.getCurrentPosition(GeolocationSuccessCallback,GeolocationErrorCallback);

            if(typeof window['InitializeGodsEyeMap'] === 'function') {

                InitializeGodsEyeMap();

            }
            
        };
        document.head.appendChild(script);
    }
    const GeolocationSuccessCallback = (position) => {
        if(position.coords != undefined){
            default_latitude = position.coords.latitude
            default_longitude = position.coords.longitude
            setCookie('default_latitude', default_latitude, 365);
            setCookie('default_longitude', default_longitude, 365);
        }
    };
    const GeolocationErrorCallback = (error) => {
        console.log('Error: You denied for your default Geolocation',error.message);
        setCookie('default_latitude', '23.022505', 365);
        setCookie('default_longitude','72.571365', 365);
    };
    
    loadGoogleMapsScript();
    
    //On delete item delete image also from bucket general code
    const deleteDocumentWithImage = async (collection, id, singleImageField, arrayImageField) => {
        // Reference to the Firestore document
        const docRef = database.collection(collection).doc(id);
        try {
            const doc = await docRef.get();
            if (!doc.exists) {
                console.log("No document found for deletion");
                return;
            }
            const data = doc.data();
            // Handle single image deletion
                    // Deleting single image field
                    if (singleImageField) {
                    if (Array.isArray(singleImageField)) {
                        for (const field of singleImageField) {
                            const imageUrl = data[field];
                            if (imageUrl) await deleteImageFromBucket(imageUrl);
                        }
                    } else {
                        const imageUrl = data[singleImageField];
                        if (imageUrl) await deleteImageFromBucket(imageUrl);
                    }
                }
                // Deleting array image field
                if (arrayImageField) {
                    if (Array.isArray(arrayImageField)) {
                        for (const field of arrayImageField) {
                            const arrayImages = data[field];
                            if (arrayImages && Array.isArray(arrayImages)) {
                                for (const imageUrl of arrayImages) {
                                    if (imageUrl) await deleteImageFromBucket(imageUrl);
                                }
                            }
                        }
                    } else {
                        const arrayImages = data[arrayImageField];
                        if (arrayImages && Array.isArray(arrayImages)) {
                            for (const imageUrl of arrayImages) {
                                if (imageUrl) await deleteImageFromBucket(imageUrl);
                            }
                        }
                    }
                }
            // Handle variant images deletion
            const item_attribute = data.item_attribute || {};  // Access item_attribute
            const variants = item_attribute.variants || [];    // Access variants array inside item_attribute
            if (variants.length > 0) {
                for (let i = 0; i < variants.length; i++) {
                    const variantImageUrl = variants[i].variant_image;
                    if (variantImageUrl) {
                        await deleteImageFromBucket(variantImageUrl);
                    }
                }
            }
            // Optionally delete the Firestore document after image deletion
            await docRef.delete();
            console.log("Document and images deleted successfully.");
        } catch (error) {
            console.error("Error deleting document and images:", error);
        }
    };
    const deleteImageFromBucket = async (imageUrl) => {
        try {
            const storageRef = firebase.storage().ref();
            // Check if the imageUrl is a full URL or just a child path
            let oldImageUrlRef;
            if (imageUrl.includes('https://')) {
                // Full URL
                oldImageUrlRef = storageRef.storage.refFromURL(imageUrl);
            } else {
                // Child path, use ref instead of refFromURL
                oldImageUrlRef = storageRef.storage.ref(imageUrl);
            }
            var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
            var imageBucket = oldImageUrlRef.bucket;
            // Check if the bucket name matches
            if (imageBucket === envBucket) {
                // Delete the image
                await oldImageUrlRef.delete();
                console.log("Image deleted successfully.");
            }
        } catch (error) {
        }
    };
</script>
@yield('scripts')
</body>
</html>
