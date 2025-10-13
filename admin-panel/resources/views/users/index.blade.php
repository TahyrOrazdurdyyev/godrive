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
                                <option value="active">{{trans("lang.active")}}</option>
                                <option value="inactive">{{trans("lang.in_active")}}</option>
                            </select>
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
                                    <th>{{trans('lang.user_info')}}</th>
                                    <th>{{trans('lang.email')}}</th>
                                    <th>{{trans('lang.phone')}}</th>
                                    <th>{{trans('lang.date')}}</th>
                                    <th>{{trans('lang.active')}}</th>
                                    <th>{{trans('lang.dashboard_total_orders')}}</th>
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>
                                </thead>
                                <tbody></tbody>                               
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
    var defaultImg = "{{ asset('/images/default_user.png') }}";
    var allUsers = [];

    $(document).ready(function() {
        loadUsers();

        $('.status_selector').on('change', function() {
            $('#userTable').DataTable().ajax.reload();
        });
    });

    function loadUsers() {
        var status = $('.status_selector').val() || 'all';
        
        $.ajax({
            url: '/api/users/list',
            method: 'GET',
            data: { status: status },
            success: function(response) {
                if (response.success) {
                    allUsers = response.users;
                    $('.total_count').text(allUsers.length);
                    initDataTable();
                }
            },
            error: function(xhr) {
                console.error('Error loading users:', xhr);
            }
        });
    }

    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#userTable')) {
            $('#userTable').DataTable().destroy();
        }

        $('#userTable').DataTable({
            data: allUsers,
            columns: [
                {
                    data: null,
                    render: function(data) {
                        var userImg = data.profile_pic ? 
                            '<img width="70" height="70" src="' + data.profile_pic + '" alt="image">' :
                            '<img width="70" height="70" src="' + defaultImg + '" alt="image">';
                        var viewUrl = '/users/view/' + data.id;
                        return userImg + ' <a href="' + viewUrl + '">' + (data.full_name || '') + '</a>';
                    }
                },
                {
                    data: 'email',
                    render: function(data) {
                        return shortEmail(data || '');
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        var phone = (data.country_code || '') + (data.phone_number || '');
                        return phone;
                    }
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        if (!data) return '';
                        var date = new Date(data);
                        return date.toDateString() + ' ' + date.toLocaleTimeString('en-US');
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        var checked = data.is_active ? 'checked' : '';
                        return '<label class="switch"><input type="checkbox" ' + checked + ' class="status-toggle" data-id="' + data.id + '"><span class="slider round"></span></label>';
                    }
                },
                {
                    data: 'total_orders',
                    render: function(data) {
                        return data || 0;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        var viewUrl = '/users/view/' + data.id;
                        var editUrl = '/users/edit/' + data.id;
                        return '<span class="action-btn">' +
                            '<a href="' + viewUrl + '"><i class="mdi mdi-eye"></i></a>' +
                            '<a href="' + editUrl + '"><i class="mdi mdi-lead-pencil"></i></a>' +
                            '</span>';
                    }
                }
            ],
            order: [[3, 'desc']],
            pageLength: 10,
            language: {
                zeroRecords: "{{trans('lang.no_record_found')}}",
                emptyTable: "{{trans('lang.no_record_found')}}"
            }
        });
    }

    $(document).on('change', '.status-toggle', function() {
        var userId = $(this).data('id');
        var isActive = $(this).is(':checked');
        
        $.ajax({
            url: '/api/users/' + userId + '/toggle-status',
            method: 'POST',
            data: {
                is_active: isActive,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (!response.success) {
                    alert('Error updating status');
                    location.reload();
                }
            },
            error: function() {
                alert('Error updating status');
                location.reload();
            }
        });
    });

    function shortEmail(email) {
        if (!email || email.length <= 20) return email;
        return email.substring(0, 20) + '...';
    }
</script>
@endsection
