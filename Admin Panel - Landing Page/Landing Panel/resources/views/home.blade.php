@extends('app')

@section('scripts')

<script type="text/javascript">
    $(document).ready(function () {
        bodyTemplate = document.getElementById('body-template');
        bodyTemplate.innerHTML = '';
        database.collection('settings').doc('landingPageTemplate').get().then(async function (snapshots) {
            html = '';
            var data = snapshots.data();
            html = data.landingPageTemplate;
            if(html != ''){
                bodyTemplate.innerHTML = html;
            }
        });
    });

</script>

@endsection
