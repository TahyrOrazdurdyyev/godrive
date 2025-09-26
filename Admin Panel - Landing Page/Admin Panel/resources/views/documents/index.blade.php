@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.all_document_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.document_table')}}</li>
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
                            <span class="icon mr-3"><img src="{{ asset('images/document.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.document_plural')}}</h3>
                            <span class="counter ml-3 doc_count"></span>
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
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.document_table')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.documents_table_text')}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <a class="btn-primary btn rounded-full" href="{!! url('/documents/save/0') !!}"><i
                                            class="mdi mdi-plus mr-2"></i>{{trans('lang.document_create')}}</a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">

                            <div class="table-responsive m-t-10">
                                <table id="documentTable"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <?php if (in_array('document.delete', json_decode(@session('user_permissions')))) { ?>

                                                <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                        class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                            class="do_not_delete" href="javascript:void(0)"><i
                                                                class="mdi mdi-delete"></i>
                                                            {{trans('lang.all')}}</a></label></th>
                                            <?php } ?>
                                            <th>{{trans('lang.document_title')}}</th>
                                            <th>{{trans('lang.enable')}}</th>
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
    var ref=database.collection('documents').orderBy('id','desc').where('isDeleted','==',false);
    var alldriver=database.collection('driver_users');
    var append_list='';
    var deleteMsg="{{trans('lang.delete_alert')}}";
    var deleteSelectedRecordMsg="{{trans('lang.selected_delete_alert')}}";
    var docDeleteAlert="{{trans('lang.doc_delete_alert')}}";
    var setLanguageCode=getCookie('setLanguage');
    var defaultLanguageCode=getCookie('defaultLanguage');
    var user_permissions='<?php echo @session('user_permissions') ?>';

    user_permissions=JSON.parse(user_permissions);

    var checkDeletePermission=false;

    if($.inArray('document.delete',user_permissions)>=0) {
        checkDeletePermission=true;
    }

    $(document).ready(function() {
        $(document.body).on('click','.redirecttopage',function() {
            var url=$(this).attr('data-url');
            window.location.href=url;
        });

        jQuery("#overlay").show();

        const table=$('#documentTable').DataTable({
            pageLength: 10, // Number of rows per page
            processing: false, // Show processing indicator
            serverSide: true, // Enable server-side processing
            responsive: true,
            ajax: async function(data,callback,settings) {
                const start=data.start;
                const length=data.length;
                const searchValue=data.search.value.toLowerCase();
                const orderColumnIndex=data.order[0].column;
                const orderDirection=data.order[0].dir;

                var orderableColumns=(checkDeletePermission)? ['','title','','']:['title','',''] // Ensure this matches the actual column names

                const orderByField=orderableColumns[orderColumnIndex]; // Adjust the index to match your table
                if(searchValue.length>=3||searchValue.length===0) {
                    $('#overlay').show();
                }
                await ref.get().then(async function(querySnapshot) {
                    if(querySnapshot.empty) {
                        $('.doc_count').html(0);
                        $('#overlay').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: [] // No data
                        });
                        return;
                    }
                    let records=[];
                    let filteredRecords=[];

                    await Promise.all(querySnapshot.docs.map(async (doc) => {
                        let childData=doc.data();
                        childData.id=doc.id; // Ensure the document ID is included in the data
                        if(searchValue) {
                            if(
                                (childData.title&&childData.title.toLowerCase().toString().includes(searchValue))

                            ) {
                                filteredRecords.push(childData);
                            }
                        } else {
                            filteredRecords.push(childData);
                        }
                    }));

                    filteredRecords.sort((a,b) => {
                        let aValue=a[orderByField]? a[orderByField].toString().toLowerCase():'';
                        let bValue=b[orderByField]? b[orderByField].toString().toLowerCase():'';

                        if(orderDirection==='asc') {
                            return (aValue>bValue)? 1:-1;
                        } else {
                            return (aValue<bValue)? 1:-1;
                        }
                    });

                    const totalRecords=filteredRecords.length;
                    $('.doc_count').html(totalRecords);
                    const paginatedRecords=filteredRecords.slice(start,start+length);

                    await Promise.all(paginatedRecords.map(async (childData) => {
                        var getData=await buildHTML(childData);
                        records.push(getData);
                    }));

                    $('#overlay').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords, // Total number of records in Firestore
                        recordsFiltered: totalRecords, // Number of records after filtering (if any)
                        data: records // The actual data to display in the table
                    });
                }).catch(function(error) {
                    console.error("Error fetching data from Firestore:",error);
                    $('#overlay').hide(); // Hide loader
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        data: [] // No data due to error
                    });
                });
            },
            order: (checkDeletePermission)? [[1,'asc']]:[[0,'asc']],
            columnDefs: [
                {orderable: false,targets: (checkDeletePermission)? [0,2,3]:[1,2]}
            ],
            "language": {
                "zeroRecords": "{{trans("lang.no_record_found")}}",
                "emptyTable": "{{trans("lang.no_record_found")}}",
                "processing": "" // Remove default loader
            },

        });
        function debounce(func,wait) {
            let timeout;
            const context=this;
            return function(...args) {
                clearTimeout(timeout);
                timeout=setTimeout(() => func.apply(context,args),wait);
            };
        }
        $('#search-input').on('input',debounce(function() {
            const searchValue=$(this).val();
            if(searchValue.length>=3) {
                $('#overlay').show();
                table.search(searchValue).draw();
            } else if(searchValue.length===0) {
                $('#overlay').show();
                table.search('').draw();
            }
        },300));

    });
    async function buildHTML(val) {
        var html=[];
        newdate='';
        var id=val.id;
        var route1='{{route("documents.save",":id")}}';
        route1=route1.replace(':id',id);
        var trroute1='';
        trroute1=trroute1.replace(':id',id);
        if(checkDeletePermission) {
            html.push('<td class="delete-all"><input type="checkbox" id="is_open_'+id+'" class="is_open" dataId="'+id+'"><label class="col-3 control-label"\n'+
                'for="is_open_'+id+'" ></label></td>');
        }
        var documentTitle='';
        if(Array.isArray(val.title)) {
            var foundItem=val.title.find(item => item.type===setLanguageCode);
            if(foundItem&&foundItem.title!='') {
                documentTitle=foundItem.title;
            } else {
                var foundItem=val.title.find(item => item.type===defaultLanguageCode);
                if(foundItem&&foundItem.title!='') {
                    documentTitle=foundItem.title;
                } else {
                    var foundItem=val.title.find(item => item.type==='en');
                    documentTitle=foundItem.title;
                }
            }

        }

        html.push('<a href="'+route1+'">'+documentTitle+'</a>');
        if(val.enable) {
            html.push('<label class="switch"><input type="checkbox" checked id="'+val.id+'" name="isSwitch"><span class="slider round"></span></label>');
        } else {
            html.push('<label class="switch"><input type="checkbox" id="'+val.id+'" name="isSwitch"><span class="slider round"></span></label>');
        }

        var actionHtml='';
        actionHtml=actionHtml+'<span class="action-btn"><a href="'+route1+'"><i class="mdi mdi-lead-pencil"></i></a>';
        if(checkDeletePermission) {
            actionHtml=actionHtml+'<a id="'+val.id+'" class="delete-btn" name="doc-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
        }
        actionHtml+='</span>';
        html.push(actionHtml);
        return html;

    }

    $("#is_active").click(function() {
        $("#documentTable .is_open").prop('checked',$(this).prop('checked'));
    });

    $("#deleteAll").click(function() {
        if($('#documentTable .is_open:checked').length) {
            if(confirm(deleteSelectedRecordMsg)) {
                jQuery("#overlay").show();
                $('#documentTable .is_open:checked').each(function(key) {
                    var arr=$('#documentTable .is_open:checked');
                    var dataId=$(this).attr('dataId');
                    database.collection('documents').doc(dataId).update({
                        'isDeleted': true,
                        'enable': false
                    }).then(async function() {
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
                    });
                });
            } else {
                return false;
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });

    function prev() {
        if(endarray.length==1) {
            return false;
        }
        end=endarray[endarray.length-2];

        if(end!=undefined||end!=null) {

            if(jQuery("#selected_search").val()=='title'&&jQuery("#search").val().trim()!='') {
                listener=ref.orderBy('title').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').startAt(end).get();
            } else {
                listener=ref.orderBy('id','desc').startAt(end).limit(pagesize).get();
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
                listener=ref.orderBy('id','desc').startAfter(start).limit(pagesize).get();
            }
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

    function searchclear() {
        jQuery("#search").val('');
        jQuery('#selected_search').val("title").trigger('change');
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

            wherequery=ref.orderBy('title').limit(pagesize).startAt(jQuery("#search").val()).endAt(jQuery("#search").val()+'\uf8ff').get();

        } else {

            wherequery=ref.orderBy('id','desc').limit(pagesize).get();
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

    $(document).on("click","input[name='isSwitch']",function(e) {
        var ischeck=$(this).is(':checked');
        var id=$(this).attr('id');
        database.collection('documents').where('enable',"==",true).get().then(function(snapshots) {
            if(ischeck==false&&snapshots.docs.length==1) {
                $("#"+id).prop('checked',true);
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>"+docDeleteAlert+"</p>");
                window.scrollTo(0,0);
                return false;
            } else {
                jQuery("#overlay").show();
                database.collection('documents').doc(id).update({
                    'enable': ischeck? true:false
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
                });
            }
        });
    });


    $(document).on("click","a[name='doc-delete']",function(e) {
        if(confirm(deleteMsg)) {
            var id=$(this).attr('id');
            jQuery("#overlay").show();
            database.collection('documents').doc(id).update({
                'isDeleted': true,
                'enable': false
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
            });
        } else {
            return false;
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
</script>

@endsection