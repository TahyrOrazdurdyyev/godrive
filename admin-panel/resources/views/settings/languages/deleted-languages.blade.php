@extends('layouts.app')





@section('content')

<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.deleted_languages')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.languages')}}</li>
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
                            <span class="icon mr-3"><img src="{{ asset('images/language.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.deleted_languages')}}</h3>
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.deleted_languages')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.deleted_language_table_text')}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                </div>
                            </div>
                        </div>
                       
                        <div class="card-body">

                            <div id="data-table_processing" class="dataTables_processing panel panel-default"
                                style="display: none;">{{trans('lang.processing')}}
                            </div>


                            <div class="table-responsive m-t-10">
                                <table id="languageTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{trans('lang.image')}}</th>
                                            <th>{{trans('lang.name')}}</th>
                                            <th>{{trans('lang.code')}}</th>
                                            <th>{{trans('lang.active')}}</th>
                                            <th>{{trans('lang.reinstate')}}</th>
                                            <th>{{trans('lang.permanent_delete')}}</th>
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

        var database = firebase.firestore();



        var user_number = [];

        var languages = [];

        var ref = database.collection('languages').where('isDeleted', '==', true);

        var placeholderImage = "{{ asset('/images/default_user.png') }}";

        var append_list = '';



        $(document).ready(function () {



            jQuery("#overlay").show();



            append_list = document.getElementById('append_list1');

            append_list.innerHTML = '';



            ref.get().then(async function (snapshots) {



                var html = '';

                html = await buildHTML(snapshots);



                if (html != '') {

                    append_list.innerHTML = html;

                }

                $('#languageTable').DataTable({

                    order: [[1, 'asc']],

                    columnDefs: [

                        {orderable: false, targets: [0, 3, 4]},

                    ],

                    "language": {

                        "zeroRecords": "{{trans('lang.no_record_found')}}",

                        "emptyTable": "{{trans('lang.no_record_found')}}"

                    }

                });

                jQuery("#overlay").hide();

            });



        });



        async function buildHTML(snapshots) {

            var html = '';
            $(".total_count").text(snapshots.docs.length);
            await Promise.all(snapshots.docs.map(async (listval) => {

                var val = listval.data();

                var getData = await getListData(val);

                html += getData;

            }));

            return html;

        }





        async function getListData(val) {

            var html = '';



            html = html + '<tr>';

            newdate = '';

            var id = val.id;

            var route1 = '{{route("settings.languages.save", ":id")}}';

            route1 = route1.replace(':id', id);

            if (val.image == '' || val.image == null) {

                var image = placeholderImage;

            } else {

                var image = val.image;

            }

            html = html + '<td><a href="' + route1 + '"><img src="' + image + '" class="rounded" style="width:50px" ></a></td>';



            html = html + '<td><a href="' + route1 + '">' + val.name + '</a></td>';



            html = html + '<td>' + val.code + '</td>';



            if (val.enable) {

                html = html + '<td><label class="switch"><input type="checkbox" checked id="' + val.id + '" name="isActive"><span class="slider round"></span></label></td>';

            } else {

                html = html + '<td><label class="switch"><input type="checkbox" id="' + val.id + '" name="isActive"><span class="slider round"></span></label></td>';

            }



            html = html + '<td class="action-btn"><a id="' + val.id + '" data-code="' + val.code + '" class="do_not_delete" name="revoke-data" href="javascript:void(0)"><i class="mdi mdi-undo"></i></a></td>';

            html = html + '<td class="action-btn"><a id="' + val.id + '" data-code="'+val.code+ '" class="permanent-delete" name="permanent-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a></td>';



            html = html + '</tr>';



            return html;

        }



        /* toggal publish action code start*/

        $(document).on("click", "input[name='isActive']", function (e) {

            var ischeck = $(this).is(':checked');

            var id = this.id;

            if (ischeck) {

                database.collection('languages').doc(id).update({

                    'enable': true

                }).then(function (result) {

                });

            } else {

                database.collection('languages').doc(id).update({

                    'enable': false

                }).then(function (result) {

                });

            }



        });



        $(document).on("click", "a[name='revoke-data']", function (e) {

            var id = this.id;

            var code=$(this).attr('data-code');

            jQuery("#overlay").show();

            database.collection('languages').doc(id).update({

                'isDeleted': false,



            }).then(async function (result) {

                window.location.href='{{ url()->current() }}';

            });

            jQuery("#overlay").show();

        });



        $(document).on("click", "a[name='permanent-delete']", async function (e) {

         var id = $(this).attr('id');

         var code=$(this).attr('data-code');



            jQuery("#overlay").show();

            deleteDocumentWithImage('languages', id, 'image')

            .then(() => {

                window.location.href = '{{ url()->current() }}';

            })

            .catch(error => {

                console.error("Error deleting document:", error);

            });

        });



    </script>



@endsection

