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
                <li class="breadcrumb-item active">{{trans('lang.on_board_table')}}</li>
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
                            <span class="icon mr-3"><img src="{{ asset('images/onboarding.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.on_board_plural')}}</h3>
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
                            <h3 class="text-dark-2 mb-2 h4">{{trans('lang.on_board_plural')}}</h3>
                            <p class="mb-0 text-dark-2">{{trans('lang.on_board_plural_text')}}</p>
                        </div>
                       
                    </div>
                    <div class="card-body">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default"
                            style="display: none;">{{trans('lang.processing')}}
                        </div>

                        <div class="table-responsive m-t-10">
                            <table id="userTable"
                                class="display  table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>

                                        <th>{{trans('lang.image')}}</th>
                                        <th>{{trans('lang.title')}}</th>
                                        <th>{{trans('lang.description')}}</th>
                                        <th>{{trans('lang.app_screen')}}</th>
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
</div>

@endsection

@section('scripts')


<script type="text/javascript">

    var database=firebase.firestore();

    var defaultImg="{{ asset('/images/default_user.png') }}";
    var offest=1;
    var pagesize=10;
    var end=null;
    var endarray=[];
    var start=null;
    var user_number=[];
    var setLanguageCode=getCookie('setLanguage');
    var defaultLanguageCode=getCookie('defaultLanguage');

    var ref=database.collection('on_boarding');

    var append_list='';

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

            jQuery("#overlay").hide();
            if(html!='') {
                append_list.innerHTML=html;
                start=snapshots.docs[snapshots.docs.length-1];
                endarray.push(snapshots.docs[0]);

                if(snapshots.docs.length<pagesize) {
                    jQuery("#data-table_paginate").hide();
                }
                // disableClick();
            }
            $('#userTable').DataTable({
                order: [[3,'asc']],
                columnDefs: [
                    {orderable: false,targets: [0,4]},
                ],
                "language": {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}"
                },
                "bPaginate": false
            });
        });

    });


    async function buildHTML(snapshots) {

        await Promise.all(snapshots.docs.map(async (listval) => {
            var val=listval.data();
            var getData=await getListData(val);
            html+=getData;

        }));
        return html;
    }
    function getListData(val) {
        var html='';
        html=html+'<tr>';
        newdate='';
        var id=val.id;
        var route1='{{route("on-board.save",":id")}}';
        route1=route1.replace(':id',id);

        if(val.image==''||val.image==null) {

            html=html+'<td><img class="rounded" style="width:50px" src="'+defaultImg+'" alt="image"></td>';
        } else {
            html=html+'<td><img class="rounded" style="width:50px" src="'+val.image+'" alt="image"></td>';
        }
        var title='';
        if(Array.isArray(val.title)) {
            var foundItem=val.title.find(item => item.type===setLanguageCode);
            if(foundItem&&foundItem.title!='') {
                title=foundItem.title;
            } else {
                var foundItem=val.title.find(item => item.type===defaultLanguageCode);
                if(foundItem&&foundItem.title!='') {
                    title=foundItem.title;
                } else {
                    var foundItem=val.title.find(item => item.type==='en');
                    title=foundItem.title;

                }
            }

        }

        html=html+'<td><a href="'+route1+'" class="onboard-edit">'+title+'</a></td>';
        var description='';
        if(Array.isArray(val.description)) {
            var foundItem=val.description.find(item => item.type===setLanguageCode);
            if(foundItem&&foundItem.description!='') {
                description=foundItem.description;
            } else {
                var foundItem=val.description.find(item => item.type===defaultLanguageCode);
                if(foundItem&&foundItem.description!='') {
                    description=foundItem.description;
                } else {
                    var foundItem=val.description.find(item => item.type==='en');
                    description=foundItem.description;

                }
            }

        }

        html=html+'<td>'+description+'</td>';
        if(val.type=="customerApp") {
            var type="Customer APP";
        } else {
            var type="Driver APP";
        }
        html=html+'<td>'+type+'</td>';

        html=html+'<td class="action-btn"><a href="'+route1+'" class="onboard-edit"><i class="mdi mdi-lead-pencil"></i></a></td>';

        html=html+'</tr>';
        return html;
    }

    function prev() {
        if(endarray.length==1) {
            return false;
        }
        end=endarray[endarray.length-2];

        if(end!=undefined||end!=null) {

            if(jQuery("#selected_search").val()=='title'&&jQuery("#search").val().trim()!='') {
                listener=ref.orderBy('title').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').startAt(end).get();

            } else {
                listener=ref.startAt(end).limit(pagesize).get();
            }

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

            if(jQuery("#selected_search").val()=='title'&&jQuery("#search").val().trim()!='') {

                listener=ref.orderBy('title').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').startAfter(start).get();

            } else {
                listener=ref.startAfter(start).limit(pagesize).get();
            }
            listener.then((snapshots) => {

                html='';
                html=buildHTML(snapshots);
                console.log(snapshots);

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

    function searchclear() {
        jQuery("#search").val('');
        searchtext();
    }

    $('#search').keypress(function(e) {
        if(e.which==13) {
            $('.search_button').click();
        }
    });

    function searchtext() {

        append_list.innerHTML='';

        if(jQuery("#selected_search").val()=='title'&&jQuery("#search").val().trim()!='') {

            wherequery=ref.orderBy('fullName').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').get();

        } else {

            wherequery=ref.limit(pagesize).get();
        }

        wherequery.then((snapshots) => {

            html='';
            html=buildHTML(snapshots);

            if(html!='') {
                append_list.innerHTML=html;
                start=snapshots.docs[snapshots.docs.length-1];
                endarray.push(snapshots.docs[0]);
                if(snapshots.docs.length<pagesize) {

                    jQuery("#data-table_paginate").hide();
                } else {

                    jQuery("#data-table_paginate").show();
                }
            }
        });

    }


</script>

@endsection