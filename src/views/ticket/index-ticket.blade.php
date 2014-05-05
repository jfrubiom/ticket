@extends(Config::get('mis::views.master'))
@section('content')
<script src="{{ asset('packages/efusionsoft/ticket/js/ticket.js') }}"></script>
<div class="container" id="main-container">
    <div class="row">
        <div class="col-lg-10">
            <section class="module">
                <div class="module-head">
                    <b>{{ trans('ticket::ticket.all') }}</b>
                </div>
                <div class="module-body ajax-content">
                    <div class="row upper-menu">


                        <div style="float:right;">


                            <a class="btn btn-info btn-new" href="{{ URL::route('addTicket') }}">{{ trans('ticket::ticket.new') }}</a>

                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="col-lg-1 hidden-xs" style="text-align: center;">{{ trans('ticket::ticket.id') }}</th>
                                    <th class="col-lg-1">{{ trans('ticket::ticket.date') }}</th>
                                    <th class="col-lg-2 visible-lg visible-xs">{{ trans('ticket::ticket.title') }}</th>
                                    <th class="col-lg-2 hidden-xs">{{ trans('ticket::ticket.from') }}</th>
                                    <th class="col-lg-2 hidden-xs">{{ trans('ticket::ticket.criticallity') }}</th>
                                    <th class="col-lg-2 hidden-xs">{{ trans('ticket::ticket.status') }}</th>
                                    <th class="col-lg-1 visible-lg">{{ trans('ticket::ticket.assigend') }}</th>  
                                    <th class="col-lg-1 visible-lg">Action</th>     
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $ticket)
                                <tr id="ticket-{{$ticket['id']}}">
                                    <td class="hidden-xs" style="text-align: center;">{{ HTML::link('/complaint-detail/'.$ticket['id'], $ticket['id']) }}</td>
                                    <td >&nbsp;{{ $ticket['ticket_date'] }}</td>
                                    <td class="visible-xs visible-lg">&nbsp;{{ HTML::link('/complaint-detail/'.$ticket['id'], $ticket['title']) }}</td>
                                    <?php
                                    $raisedBy = $ticket->raisedBy;
                                    ?>
                                    <td class="hidden-xs">&nbsp;{{$raisedBy->first_name }} {{$raisedBy->last_name }}</td>
                                    <?php
                                    $critArr = $ticket->criticallity->toArray();
                                    //print_r($critArr);
                                    $crit = last($critArr);
                                    ?>
                                    <td class="hidden-xs">&nbsp;{{$crit['criticallity']}}</td>
                                    <?php
                                    $stArr = $ticket->status->toArray();
                                    $st = last($stArr);
                                    ?>
                                    <td class="visible-lg">&nbsp;{{$st['status']}}</td>
                                    <?php
                                    $fname = $ticket->raisedFor->email;
                                    ?>
                                    <td class="visible-lg">&nbsp;{{ $fname }}</td>
                                    <td class="visible-lg">&nbsp;<a href="javascript:void(0);" onclick="closeTicket({{$ticket['id']}})">Close</a></td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>

                </div>
                <div class="row">
                    <?php echo $tickets->links(); ?>
                </div>
            </section>
        </div>
    </div>
</div>
@stop