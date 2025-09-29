@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.user_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.user_table')}}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>
    <div class="container-fluid">
       <div class="admin-top-section"> 
        <div class="row">
            <div class="col-12">
                <div class="d-flex top-title-section pb-4 justify-content-between">
                    <div class="d-flex top-title-left align-self-center">
                        <span class="icon mr-3"><img src="{{ asset('images/users.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.user_plural')}}</h3>
                        <span class="counter ml-3 total_count"></span>
                    </div>
                    <div class="d-flex top-title-right align-self-center">
                        <div class="select-box pl-3">
                            <select class="form-control status_selector filteredRecords">
                                <option value="">{{trans("lang.status")}}</option>
                                <option value="active"  >{{trans("lang.active")}}</option>
                                <option value="inactive"  >{{trans("lang.in_active")}}</option>
                            </select>
                        </div>
                        <div class="select-box pl-3">
                            <div id="daterange"><i class="fa fa-calendar"></i>&nbsp;
                                <span></span>&nbsp; <i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
       </div>
       <div class="table-list">
       <div class="row">
           <div class="col-12">
               <div class="card border">
                 <div class="card-header d-flex justify-content-between align-items-center border-0">
                   <div class="card-header-title">
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.user_table')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.users_table_text')}}</p>
                   </div>
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="userTable"
                                   class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <?php if (in_array('user.delete', json_decode(@session('user_permissions'), true))) { ?>
                                    <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active"><a id="deleteAll"
                                    class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label></th>
                                    <?php } ?>
                                    <th>{{trans('lang.user_info')}}</th>
                                    <th>{{trans('lang.email')}}</th>
                                    <th>{{trans('lang.phone')}}</th>
                                    <th>{{trans('lang.date')}}</th>
                                    <th>{{trans('lang.active')}}</th>
                                    <th>{{trans('lang.dashboard_total_orders')}}</th>
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>
                                </thead>                               
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    var database = firebase.firestore();
    const urlParams=new URLSearchParams(window.location.search);
    var defaultImg="{{ asset('/images/default_user.png') }}";
    if(urlParams.has('today')) {
        const today=new Date();
        const startOfDay=new Date(today.setHours(0,0,0,0));
        const endOfDay=new Date(today.setHours(23,59,59,999));
        var ref=database.collection('users').where("createdAt",">=",startOfDay).where("createdAt","<=",endOfDay);
    } else {
        var ref=database.collection('users')
    }
    var user_permissions = '<?php echo @session("user_permissions")?>';
    user_permissions = Object.values(JSON.parse(user_permissions));
    var checkDeletePermission = false;
    if ($.inArray('user.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }
     var deleteMsg="{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg="{{trans('lang.selected_delete_alert')}}";
    $('.status_selector').select2({
        placeholder: '{{trans("lang.status")}}',  
        minimumResultsForSearch: Infinity,
        allowClear: true 
    });
    $('select').on("select2:unselecting", function(e) {
        var self = $(this);
        setTimeout(function() {
            self.select2('close');
        }, 0);
    });
    function setDate() {
        $('#daterange span').html('{{trans("lang.select_range")}}');
        $('#daterange').daterangepicker({
            autoUpdateInput: false, 
        }, function (start, end) {
            $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('.filteredRecords').trigger('change'); 
        });
        $('#daterange').on('apply.daterangepicker', function (ev, picker) {
            $('#daterange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
            $('.filteredRecords').trigger('change');
        });
        $('#daterange').on('cancel.daterangepicker', function (ev, picker) {
            $('#daterange span').html('{{trans("lang.select_range")}}');
            $('.filteredRecords').trigger('change'); 
        });
    }
    setDate(); 
    $('.filteredRecords').change(async function() {
        var status = $('.status_selector').val();
        var daterangepicker = $('#daterange').data('daterangepicker');
        ref = database.collection('users');
        if ($('#daterange span').html() != '{{trans("lang.select_range")}}' && daterangepicker) {
            var from = moment(daterangepicker.startDate).toDate();
            var to = moment(daterangepicker.endDate).toDate();
            if (from && to) { 
                var fromDate = firebase.firestore.Timestamp.fromDate(new Date(from));
                ref = ref.where('createdAt', '>=', fromDate);
                var toDate = firebase.firestore.Timestamp.fromDate(new Date(to));
                ref = ref.where('createdAt', '<=', toDate);
            }
        }
        if (status) {
            ref = (status == "active") ? ref.where('isActive', '==', true) : ref.where('isActive', '==', false);
        }
        $('#userTable').DataTable().ajax.reload();
    });
    $(document).ready(function () {
        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });
        jQuery("#overlay").show();
        $(document).on('click', '.dt-button-collection .dt-button', function () {
            $('.dt-button-collection').hide();
            $('.dt-button-background').hide();
        });
        $(document).on('click', function (event) {
            if (!$(event.target).closest('.dt-button-collection, .dt-buttons').length) {
                $('.dt-button-collection').hide();
                $('.dt-button-background').hide();
            }
        });
        var fieldConfig = {
            columns: [
                { key: 'fullName', header: "{{trans('lang.user_info')}}" },
                { key: 'email', header: "{{trans('lang.email')}}" },
                {key: 'phone',header: "{{trans('lang.phone')}}"},
                { key: 'isActive', header: "{{trans('lang.active')}}" },
                { key: 'createdAt', header: "{{trans('lang.date')}}" },
            ],
            fileName: "{{trans('lang.user_table')}}",
        };
        const table = $('#userTable').DataTable({
            pageLength: 10,
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: function (data, callback, settings) {
                const start = data.start;
                const length = data.length;
                const searchValue = data.search.value.toLowerCase();
                const orderColumnIndex = data.order[0].column;
                const orderDirection = data.order[0].dir;
                const orderableColumns=(checkDeletePermission)? ['','fullName','email','phone','createdAt','','total_rides']:['fullName','email','countryCode','createdAt','','total_rides']; // Ensure this matches the actual column names
                const orderByField=orderableColumns[orderColumnIndex]; // Adjust the index to match your table
                if (searchValue.length >= 3 || searchValue.length === 0) {
                    $('#overlay').show();
                }
                ref.get().then(async function (querySnapshot) {
                    if (querySnapshot.empty) {
                        $('.total_count').text(0); 
                        console.error("No data found in Firestore.");
                        $('#overlay').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            filteredData: [],
                            data: [] // No data
                        });
                        return;
                    }
                    let records = [];
                    let filteredRecords = []; 
                    querySnapshot.forEach(function (doc) {                        
                        let childData = doc.data();
                        childData.id = doc.id; // Ensure the document ID is included in the data
                        childData.phone='+'+(childData.countryCode.includes('+')? childData.countryCode.slice(1):childData.countryCode)+'-'+childData.phoneNumber;
                        if(searchValue) {
                            var date='';
                            var time='';
                            if(childData.hasOwnProperty("createdAt")) {
                                try {
                                    date=childData.createdAt.toDate().toDateString();
                                    time=childData.createdAt.toDate().toLocaleTimeString('en-US');
                                } catch(err) {
                                }
                            }
                            var createdAt=date+' '+time;
                            if(
                                (childData.fullName && childData.fullName.toString().toLowerCase().includes(searchValue))||
                                (createdAt&&createdAt.toString().toLowerCase().includes(searchValue))||
                                (childData.email&&childData.email.toString().includes(searchValue))||
                                (childData.phone&&childData.phone.toString().includes(searchValue))
                            ) {
                                filteredRecords.push(childData);
                            }
                        } else {
                            filteredRecords.push(childData);
                        }
                    });
                   filteredRecords.sort((a,b) => {
                        let aValue=a[orderByField] /* ? a[orderByField].toString().toLowerCase() : '' */;
                        let bValue=b[orderByField] /* ? b[orderByField].toString().toLowerCase() : '' */;
                        if(orderByField==='createdAt') {
                            aValue=a[orderByField]? new Date(a[orderByField].toDate()).getTime():0;
                            bValue=b[orderByField]? new Date(b[orderByField].toDate()).getTime():0;
                        } else if(orderByField==='total_rides') {
                            aValue=a[orderByField]? parseFloat(a[orderByField]):0.0;
                            bValue=b[orderByField]? parseFloat(b[orderByField]):0.0;
                        }
                        if(orderDirection==='asc') {
                            return (aValue>bValue)? 1:-1;
                        } else {
                            return (aValue<bValue)? 1:-1;
                        }
                    });
                    const totalRecords = filteredRecords.length;
                    $('.total_count').text(totalRecords); 
                    const paginatedRecords = filteredRecords.slice(start, start + length);
                    await Promise.all(paginatedRecords.map(async (childData) => {
                        if(childData.id) {
                            const totalOrderSnapShot=await database.collection('orders').where('userId','==',childData.id).get();
                            const rides=totalOrderSnapShot.size;
                            const totalIntercityOrderSnapShot=await database.collection('orders_intercity').where('userId','==',childData.id).get();
                            const intercity=totalIntercityOrderSnapShot.size;
                            childData.total_rides=rides+intercity;
                        } else {
                            childData.total_rides=0;
                        }
                    }));
                         paginatedRecords.forEach(function(childData) {
                        var id=childData.id;
                        var route1='{{route("users.edit", ":id")}}';
                        route1=route1.replace(':id',id);
                        var userview='{{route("users.view", ":id")}}';
                        userview=userview.replace(':id',id);
                        var date='';
                        var time='';
                        if(childData.hasOwnProperty("createdAt")) {
                            try {
                                date=childData.createdAt.toDate().toDateString();
                                time=childData.createdAt.toDate().toLocaleTimeString('en-US');
                            } catch(err) {
                            }
                        }
                        if(childData.countryCode.includes('+')) {
                            childData.countryCode=childData.countryCode.slice(1);
                        }
                        else {
                            childData.countryCode=childData.countryCode;
                        }
                        var userImg=childData.profilePic==''||childData.profilePic==null? '<img  width="100%" style="width:70px;height:70px;" src="'+defaultImg+'" alt="image">':'<img width="100%" style="width:70px;height:70px;" src="'+childData.profilePic+'" alt="image">';
                        records.push([
                            checkDeletePermission? '<td class="delete-all"><input type="checkbox" id="is_open_'+id+'" class="is_open" dataId="'+id+'"><label class="col-3 control-label" for="is_open_'+id+'" ></label></td>':'',
                            userImg+'<a href="'+userview+'">'+childData.fullName+'</a>',
                            shortEmail(childData.email),
                            '+'+shortNumber(childData.countryCode,childData.phoneNumber),
                            date+' '+time,
                            childData.isActive? '<label class="switch"><input type="checkbox" checked id="'+childData.id+'" name="isActive"><span class="slider round"></span></label>':'<label class="switch"><input type="checkbox" id="'+childData.id+'" name="isActive"><span class="slider round"></span></label>',
                            '<td class="total_rides_'+childData.id+'">'+childData.total_rides+'</td>',
                            '<span class="action-btn"><a href="'+userview+'"><i class="mdi mdi-eye"></i></a><a href="'+route1+'"><i class="mdi mdi-lead-pencil"></i></a><?php if (in_array('user.delete', json_decode(@session('user_permissions')))) { ?> <a id="'+childData.id+'" class="delete-btn" name="user-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a><?php } ?></span>'
                        ]);
                    });
                    $('#overlay').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords, 
                        recordsFiltered: totalRecords, 
                        filteredData: filteredRecords,
                        data: records 
                    });
                }).catch(function (error) {
                    console.error("Error fetching data from Firestore:", error);
                    $('#overlay').hide(); 
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        filteredData: [],
                        data: [] 
                    });
                });
            },           
            order: (checkDeletePermission)? [[4,'desc']] : [[3,'desc']],
            columnDefs: [
                {
                    targets: (checkDeletePermission)? 4:3,
                    type: 'date',
                    render: function(data) {
                        return data;
                    }
                },
                {orderable: false,targets: (checkDeletePermission)? [0,5,6,7]:[4,5,6]},
            ],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}",
                "processing": "" 
            },
            dom: 'lfrtipB',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="mdi mdi-cloud-download"></i> Export as',
                    className: 'btn btn-info',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Export Excel',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'excel',fieldConfig);
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'Export PDF',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'pdf',fieldConfig);
                            }
                        },   
                        {
                            extend: 'csvHtml5',
                            text: 'Export CSV',
                            action: function (e, dt, button, config) {
                                exportData(dt, 'csv',fieldConfig);
                            }
                        }
                    ]
                }
            ],
            initComplete: function() {
                $(".dataTables_filter").append($(".dt-buttons").detach());
                $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete','new-password').val('');
                $('.dataTables_filter label').contents().filter(function() {
                    return this.nodeType === 3; 
                }).remove();
            }
        });
        table.columns.adjust().draw();
        function debounce(func, wait) {
            let timeout;
            const context = this;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }
        $('#search-input').on('input', debounce(function () {
            const searchValue = $(this).val();
            if (searchValue.length >= 3) {
                $('#overlay').show();
                table.search(searchValue).draw();
            } else if (searchValue.length === 0) {
                $('#overlay').show();
                table.search('').draw();
            }
        }, 300));
    });
    $("#is_active").click(function () {
        $("#userTable .is_open").prop('checked', $(this).prop('checked'));
    });
    $("#deleteAll").click(async function () {
        if ($('#userTable .is_open:checked').length) {
            if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#overlay").show();
                let deletePromises = [];
                $('#userTable .is_open:checked').each(function () {
                    let dataId = $(this).attr('dataId');
                    let deletePromise = (async () => {
                        await deleteDocumentWithImage('users', dataId, 'profilePictureURL');
                        await deleteUserData(dataId);
                    })();
                    deletePromises.push(deletePromise);
                });
                await Promise.all(deletePromises);
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });
    async function deleteUserData(userId) {
        // Delete user from authentication
        const idToken = await firebase.auth().currentUser.getIdToken();
        return new Promise((resolve, reject) => {
            var dataObject = { "data": { "uid": userId } };
            var projectId = '<?php echo env('FIREBASE_PROJECT_ID') ?>';
            jQuery.ajax({
                url: 'https://us-central1-' + projectId + '.cloudfunctions.net/deleteUser',
                method: 'POST',
                crossDomain: true,
                contentType: "application/json; charset=utf-8",
                data: JSON.stringify(dataObject),
                headers: {
                    Authorization: 'Bearer ' + idToken
                },
                success: function (data) {
                    console.log('Delete user success:', data.result);
                    resolve(data);
                },
                error: function (xhr, status, error) {
                    var responseText = JSON.parse(xhr.responseText);
                    console.log('Delete user error:', responseText.error);
                    reject(new Error(responseText.error));
                }
            });
        });
    }
    $(document).on("click","a[name='user-delete']",async function(e) {
        if(confirm(deleteMsg)) {
            jQuery("#overlay").show();
            var id=this.id;
            await deleteDocumentWithImage('users',id,'profilePic');
            await deleteUserData(id);
            setTimeout(function () {
                window.location.reload();
            }, 2000);
        }
    });
    $(document).on("click","input[name='isActive']",function(e) {
        var ischeck=$(this).is(':checked');
        var id=this.id;
        database.collection('users').doc(id).update({'isActive': ischeck? true:false}).then(function(result) {
        });
    });
</script>
@endsection