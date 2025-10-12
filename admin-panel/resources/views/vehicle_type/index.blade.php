@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.vehicle_type_table')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.vehicle_type_table')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="admin-top-section">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex top-title-section pb-4 justify-content-between">
                        <div class="d-flex top-title-left align-self-center">
                            <span class="icon mr-3"><img src="{{ asset('images/driver.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.vehicle_type_table')}}</h3>
                            <span class="counter ml-3 total_count"></span>
                        </div>
                        <div class="d-flex top-title-right align-self-center">
                            <div class="select-box pl-3">

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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.vehicle_type_table')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.vehicle_type_table_text')}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a class="btn-primary btn rounded-full" href="{{route('vehicle-type.create')}}"><i
                                            class="mdi mdi-plus mr-2"></i>{{trans('lang.vehicle_add')}}</a>
                                </div>
                            </div>
                            
                        </div>
                        <div class="card-body">

                            <div class="table-responsive m-t-10">
                                <table id="taxTable"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <?php if (in_array('vehicle.type.delete', json_decode(@session('user_permissions')))) { ?>

                                                <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                        class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                            class="do_not_delete" href="javascript:void(0)"><i
                                                                class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label>
                                                </th>

                                            <?php } ?>

                                            <th>{{trans('lang.image')}}</th>
                                            <th>{{trans('lang.name')}}</th>
                                            <th>{{trans('lang.active')}}</th>
                                            <th>{{trans('lang.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="append_list1">
                                    </tbody>
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

    var append_list = '';
    var deleteMsg = "{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg = "{{trans('lang.selected_delete_alert')}}";
    var setLanguageCode = getCookie('setLanguage');
    var defaultLanguageCode = getCookie('defaultLanguage');
    var user_permissions = '<?php echo @session('user_permissions') ?>';
    try {
    user_permissions = JSON.parse(user_permissions);
} catch (e) {
    console.error('🔥 Failed to parse user_permissions:', e);
    user_permissions = [];
}
    var checkDeletePermission = false;

    if ($.inArray('vehicle.type.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }

    $(document).ready(function() {
        jQuery("#overlay").show();
        loadVehicleTypes();
    });

    async function loadVehicleTypes() {
        try {
            const response = await fetch('http://185.10.16.248:8080/api/v1/vehicle-types');
            const result = await response.json();
            
            if (result.success) {
                displayVehicleTypes(result.data);
            } else {
                console.error('Failed to load vehicle types:', result.message);
                jQuery("#overlay").hide();
            }
        } catch (error) {
            console.error('Error loading vehicle types:', error);
            jQuery("#overlay").hide();
        }
    }

    function displayVehicleTypes(vehicleTypes) {
        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';
        
        $('.total_count').html(vehicleTypes.length);
        
        var html = '';
        vehicleTypes.forEach((val) => {
            html += buildRowHTML(val);
        });
        
        append_list.innerHTML = html;

        // Initialize DataTable
        if (checkDeletePermission) {
            $('#taxTable').DataTable({
                order: [[2, 'asc']],
                columnDefs: [
                    {orderable: false, targets: [0, 1, 3, 4]},
                ],
                "language": {
                    "zeroRecords": "{{trans("lang.no_record_found")}}",
                    "emptyTable": "{{trans("lang.no_record_found")}}"
                },
            });
        } else {
            $('#taxTable').DataTable({
                order: [[1, 'asc']],
                columnDefs: [
                    {orderable: false, targets: [0, 2, 3]},
                ],
                "language": {
                    "zeroRecords": "{{trans("lang.no_record_found")}}",
                    "emptyTable": "{{trans("lang.no_record_found")}}"
                },
            });
        }
        
        jQuery("#overlay").hide();
    }

    function buildRowHTML(val) {
        var html = '<tr>';
        var id = val.id;
        var route1 = '{{route("vehicle-type.edit",":id")}}';
        route1 = route1.replace(':id', id);

        if (checkDeletePermission) {
            html += '<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label" for="is_open_' + id + '" ></label></td>';
        }

        // Image
        var imageUrl = val.image || '{{ asset("images/placeholder.png") }}';
        html += '<td><img src="' + imageUrl + '" style="width:50px;height:50px;" onerror="this.src=\'{{ asset("images/placeholder.png") }}\'"></td>';

        // Name - title is already an array from API (no parsing needed!)
        var name = '';
        try {
            var titleObj = val.title;
            
            // titleObj is already an array from API
            if (Array.isArray(titleObj)) {
                var foundItem = titleObj.find(item => item.type === setLanguageCode);
                if (foundItem && foundItem.name != '') {
                    name = foundItem.name;
                } else {
                    var foundItem = titleObj.find(item => item.type === defaultLanguageCode);
                    if (foundItem && foundItem.name != '') {
                        name = foundItem.name;
                    } else {
                        var foundItem = titleObj.find(item => item.type === 'en');
                        name = foundItem ? foundItem.name : 'N/A';
                    }
                }
            } else {
                name = 'Invalid format';
            }
        } catch (e) {
            console.error('Error for ID ' + id + ':', e);
            name = 'Error';
        }
        html += '<td>' + name + '</td>';

        // Enable toggle
        if (val.enable) {
            html += '<td><label class="switch"><input type="checkbox" checked id="' + val.id + '" name="isSwitch"><span class="slider round"></span></label></td>';
        } else {
            html += '<td><label class="switch"><input type="checkbox" id="' + val.id + '" name="isSwitch"><span class="slider round"></span></label></td>';
        }

        // Actions
        html += '<td class="action-btn"><a href="' + route1 + '"><i class="mdi mdi-lead-pencil"></i></a>';
        if (checkDeletePermission) {
            html += '<a id="' + val.id + '" class="delete-btn" name="vehicle-type-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
        }
        html += '</td>';
        html += '</tr>';
        
        return html;
    }

    $("#is_active").click(function() {
        $("#taxTable .is_open").prop('checked', $(this).prop('checked'));
    });

    $("#deleteAll").click(function() {
        if ($('#taxTable .is_open:checked').length) {
            if (confirm(deleteSelectedRecordMsg)) {
                jQuery("#overlay").show();
                $('#taxTable .is_open:checked').each(function() {
                    var dataId = $(this).attr('dataId');
                    deleteVehicleType(dataId);
                });
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });

    $(document).on("click", "input[name='isSwitch']", function(e) {
        var ischeck = $(this).is(':checked');
        var id = this.id;
        
        fetch('http://185.10.16.248:8080/api/v1/vehicle-types/' + id + '/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({enable: ischeck})
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Failed to toggle:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    $(document).on("click", "a[name='vehicle-type-delete']", function(e) {
        if (confirm(deleteMsg)) {
            var id = this.id;
            jQuery("#overlay").show();
            deleteVehicleType(id);
        }
    });

    function deleteVehicleType(id) {
        fetch('http://185.10.16.248:8080/api/v1/vehicle-types/' + id, {
            method: 'DELETE',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                jQuery("#overlay").hide();
                alert('Failed to delete: ' + data.message);
            }
        })
        .catch(error => {
            jQuery("#overlay").hide();
            console.error('Error:', error);
            alert('Error deleting vehicle type');
        });
    }

</script>

@endsection
