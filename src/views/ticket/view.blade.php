@extends(Config::get('mis::views.master'))
@section('content')
<script src="{{ asset('packages/efusionsoft/ticket/js/ticket.js') }}"></script>
<div class="container" id="main-container">
        <div class="row upper-menu">
                        <div style="float:right;">
                            <a class="btn btn-info btn-new" href="{{ URL::to('complaint-logs') }}/{{$ticket['id']}}">Complaint Logs</a>
                        </div>
                    </div>
    <div class="row">
        <div  id="dashboard-center">
           @include('ticket::ticket.discussion');
        </div>
    </div>
</div>
@stop
