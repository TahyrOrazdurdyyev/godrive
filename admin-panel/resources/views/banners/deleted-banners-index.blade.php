@extends('layouts.app')



@section('content')



<div class="page-wrapper">



    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.deleted_banner_plural')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>

                <li class="breadcrumb-item active">{{trans('lang.banner_table')}}</li>

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
                            <span class="icon mr-3"><img src="{{ asset('images/banner.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.all_banner_plural')}}</h3>
                            <span class="counter ml-3 total_count"></span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-12">

                <div class="card border">

                    <div class="card-header d-flex justify-content-between align-items-center border-0">
                        <div class="card-header-title">
                            <h3 class="text-dark-2 mb-2 h4">{{trans('lang.banner_table')}}</h3>
                            <p class="mb-0 text-dark-2">{{trans('lang.deleted_banner_table_text')}}</p>
                        </div>
                       
                    </div>

                    <div class="card-body">

                        <div id="data-table_processing" class="dataTables_processing panel panel-default"
                            style="display: none;">{{trans('lang.processing')}}

                        </div>

                        <div class="table-responsive m-t-10">

                            <table id="taxTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">

                                <thead>

                                    <tr>

                                        <th>{{trans('lang.image')}}</th>

                                        <th>{{trans('lang.position')}}</th>

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



@endsection



@section('scripts')





<script type="text/javascript">



    var database=firebase.firestore();

    var offest=1;

    var pagesize=10;

    var end=null;

    var endarray=[];

    var start=null;

    var user_number=[];



    var ref=database.collection('banner').where('isDeleted','==',true);



    var append_list='';



    var deleteMsg="{{trans('lang.delete_alert')}}";

    var deleteSelectedRecordMsg="{{trans('lang.selected_delete_alert')}}";



    $(document).ready(function() {

        $(document.body).on('click','.redirecttopage',function() {

            var url=$(this).attr('data-url');

            window.location.href=url;

        });

        var inx=parseInt(offest)*parseInt(pagesize);

        jQuery("#overlay").show();

        append_list=document.getElementById('append_list1');

        append_list.innerHTML='';

        ref.get().then(async function(snapshots) {

            html='';

            if(snapshots.docs.length>0) {
                $('.total_count').html(snapshots.docs.length);
                html=await buildHTML(snapshots);

            }else{
                $('.total_count').html(0);
            }

            if(html!='') {

                append_list.innerHTML=html;

                start=snapshots.docs[snapshots.docs.length-1];

                endarray.push(snapshots.docs[0]);

                if(snapshots.docs.length<pagesize) {

                    jQuery("#data-table_paginate").hide();

                }

                // disableClick();

            }

            $('#taxTable').DataTable({

                order: [[1,'asc']],

                columnDefs: [

                    {orderable: false,targets: [0,2,3]},

                ],

                "language": {

                    "zeroRecords": "{{trans('lang.no_record_found')}}",

                    "emptyTable": "{{trans('lang.no_record_found')}}"

                },

                responsive: true

            });

            jQuery("#overlay").hide();

        });

    });



    async function buildHTML(snapshots) {

        var html='';

        await Promise.all(snapshots.docs.map(async (listval) => {

            var val=listval.data();

            var getData=await getListData(val);

            html+=getData;

        }));

        return html;

    }



    async function getListData(val) {

        var html='';

        html=html+'<tr>';

        newdate='';

        var id=val.id;

        if(val.image=='') {

            html=html+'<td><img class="rounded" style="width:50px" src="'+defaultImg+'" alt="image"></td>';

        } else {

            html=html+'<td><img class="rounded" style="width:50px" src="'+val.image+'" alt="image"></td>';

        }

        html=html+'<td>'+val.position+'</td>';

        html=html+'<td class="action-btn"><a id="'+val.id+'" class="revoke-banner" name="revoke-data" href="javascript:void(0)"><i class="fa fa-undo"></i></a></td>';

        html=html+'<td class="action-btn"><a id="'+val.id+'" class="permanent-delete" name="permanent-delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a></td>';

        html=html+'</tr>';

        return html;

    }



    $("#is_active").click(function() {

        $("#taxTable .is_open").prop('checked',$(this).prop('checked'));

    });



    function prev() {

        if(endarray.length==1) {

            return false;

        }

        end=endarray[endarray.length-2];



        if(end!=undefined||end!=null) {

            listener=ref.startAt(end).limit(pagesize).get();

            listener.then((snapshots) => {

                html='';

                html=buildHTML(snapshots);



                if(html!='') {

                    append_list.innerHTML=html;

                    start=snapshots.docs[snapshots.docs.length-1];

                    endarray.splice(endarray.indexOf(endarray[endarray.length-1]),1);

                }

            });

        }

    }



    function next() {



        if(start!=undefined||start!=null) {

            listener=ref.startAfter(start).limit(pagesize).get();

            listener.then((snapshots) => {



                html='';

                html=buildHTML(snapshots);



                if(html!='') {

                    append_list.innerHTML=html;

                    start=snapshots.docs[snapshots.docs.length-1];





                    if(endarray.indexOf(snapshots.docs[0])!=-1) {

                        endarray.splice(endarray.indexOf(snapshots.docs[0]),1);

                    }

                    endarray.push(snapshots.docs[0]);

                }

            });

        }

    }



    $(document).on("click","a[name='revoke-data']",function(e) {

        var id=$(this).attr('id');

        jQuery("#overlay").show();

        database.collection('banner').doc(id).update({

            'isDeleted': false,

        }).then(function(result) {

            window.location.href='{{ url()->current() }}';

        });

    });



    $(document).on("click","a[name='permanent-delete']",function(e) {

        var id=$(this).attr('id');

        jQuery("#overlay").show();

        deleteDocumentWithImage('banner',id,'image')

            .then(() => {

                window.location.href='{{ url()->current() }}';

            })

            .catch(error => {

                console.error("Error deleting document:",error);

            });

    });



</script>



@endsection