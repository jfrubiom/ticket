@extends(Config::get('mis::views.master'))
@section('content')
<script src="{{ asset('packages/efusionsoft/ticket/js/ticket.js') }}"></script>
<div class="container" id="main-container">
    <div class="row">
        <div class="col-lg-8" id="dashboard-center">
           @include('ticket::ticket.discussion');
        </div>
    </div>
</div>
@stop
