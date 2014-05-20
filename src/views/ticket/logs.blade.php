@extends(Config::get('mis::views.master'))
@section('content')

<div class="container" id="main-container">
    <div class="row">
        <div class="col-lg-10">
            <section class="module">
                <div class="module-head">
                    <b>Complaint Logs</b>
                </div>
                <div class="module-body ajax-content">
                    <div class="row upper-menu">


                        <div style="float:right;">


                            <a class="btn btn-info btn-new" href="{{ URL::to('complaint-detail') }}/{{$ticketId}}">Go Back</a>

                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="col-lg-1 hidden-xs" style="text-align: center;">Event Id</th>
                                    <th class="col-lg-2">Date</th>
                                    <th class="col-lg-7 visible-lg visible-xs">Event</th>
                                    <th class="col-lg-2 hidden-xs">User</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ticketLogs as $log)
                                <tr id="ticket-{{$log->id}}">
                                    <td class="hidden-xs" style="text-align: center;">{{$log->id}}</td>
                                    <td >&nbsp;{{ $log->created_at }}</td>
                                    <td class="visible-xs visible-lg">{{$log->message}}</td>
                                    <?php
                                    $createdBy = $log->createdBy;
                                    ?>
                                    <td class="hidden-xs">&nbsp;{{$createdBy->first_name }} {{$createdBy->last_name }}</td>
                                  
                                </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>

                </div>
                
            </section>
        </div>
    </div>
</div>
@stop