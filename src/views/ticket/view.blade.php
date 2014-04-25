@extends(Config::get('mis::views.master'))
@section('content')
<script src="{{ asset('packages/efusionsoft/ticket/js/ticket.js') }}"></script>
<div class="container" id="main-container">
    <div class="row">
        <div class="col-lg-8">
            <section class="module">
                <div class="module-head">
                    <b>{{ 'Ticket Number' }} - {{ $ticket['id'] }}</b>
                </div>
                <div class="module-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label">Status :  </label>@if($ticket['status'][0]['status']){{$ticket['status'][0]['status']}}@endif
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ 'Priority : ' }}</label>@if($ticket['criticallity'][0]['criticallity']){{$ticket['criticallity'][0]['criticallity']}}@endif
                            </div>
                         
                            <div class="form-group">
                                <label class="control-label">{{ 'Title : ' }}</label>@if($ticket['title']){{$ticket['title']}}@endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            
                            <div class="form-group">
                                <label class="control-label">{{ 'Raised For : ' }}</label>{{$raisedFor->first_name}} {{$raisedFor->last_name}}
                            </div>
                            <div class="form-group">
                                <label class="control-label">{{ 'Assigned To : ' }}</label>{{$raisedFor->first_name}} {{$raisedFor->last_name}}
                                
                            </div>
                             <div class="form-group">
                               
                                <label class="control-label">{{ 'Raised By : ' }}</label>{{$raisedBy->first_name}} {{$raisedBy->last_name}}
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </section>
            
            <div class="row">
                        <div  class="col-lg-12">
                            <div class="module">
                                <form id="comment_form" method="post">
                                     <div class="form-group">
                                         <textarea name="comment" required="true" placeholder="Enter Your comment here" style="width: 100%; height: 170px;"></textarea>
                                </div> 
                                    <div class="form-group">
                                        <label class="control-label">Status</label>
                                        <?php 
                                        $status = Config::get('ticket::ticket_status');
                                        $curStatus  = end($ticket['status']);
                                        ?>
                                        <p> <select id="status" name="ticket_status">
                                             @foreach($status as $crit)
                                        <option value="{{$crit}}" @if($crit== $curStatus['status']) selected="selected" @endif>{{$crit}}</option>
                                        @endforeach
                                        </select>
                                            </p>
                                    </div>
                                    <div class="form-group">
                                    <label class="control-label">Reassign To</label>
                                    <p><select name="assigned_to_id" required="true">
                                        @foreach($workers as $worker)
                                        <option value="{{$worker['id']}}" @if($raisedFor->id==$worker['id']) selected="selected" @endif>{{$worker['first_name']}} {{$worker['last_name']}}</option>
                                        @endforeach
                                        </select></p>
                                </div>
                                    <div class="form-group">
                                    <label class="control-label">Priority</label>
                                    <p><select name="criticallity" required="true">
                                        @foreach($critical as $crit)
                                        <option value="{{$crit}}" @if(!empty($ticket['criticallity'][0]['criticallity']) && $ticket['criticallity'][0]['criticallity']==$crit)  @endif >{{$crit}}</option>
                                        @endforeach
                                        </select></p>
                                </div>
                                    
                                    <div class="form-group">
                                    <button id="add-comment" class="btn btn-primary" style="margin-top: 15px;">{{ trans('mis::all.create') }}</button>
                                </div>
                            </form>
                            </div>
                            @if(count($threads))
                                @foreach($threads as $thread)
                                <div class="module">
                                    <div class="module-head">Created on: <span>{{$thread['created_at']}}</span></div>
                                <div class="module-body">
                                    {{$thread['comment']}}
                                </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
        </div>
    </div>
</div>
@stop
