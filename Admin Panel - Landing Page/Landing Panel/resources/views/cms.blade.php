@extends('app')

@section('content')

<div class="siddhi-cms-pages" style="padding-top:70px;">
    <div class="container">
        <div class="cms-page pt-5 pb-3">
            <h1 class="head pt-3 pb-3" id="cms_title"></h1>
            <div class="content" id="cms_description"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script type="text/javascript">

    $(document).ready(function () {
        database.collection('cms_pages').where('slug','==','{!! $slug !!}').get().then(async function (snapshots) {
            var title = document.getElementById('cms_title');
            var description = document.getElementById('cms_description');
            var data = snapshots.docs[0].data();
            title.innerHTML=data.name;
            description.innerHTML=data.description;
        });

    });

</script>

@endsection
