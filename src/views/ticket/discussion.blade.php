<section class="module">
    <div class="module-head">
        <b>{{ 'Ticket Number' }} - {{ $ticket['id'] }}</b>
    </div>
    <div class="module-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="control-label">Complaint Type:</label>{{$ticket['complaint_type']}}
                </div>
                <div class="form-group">
                    <?php
                    $status = Config::get('ticket::ticket_status');
                    $curStatus = end($ticket['status']);
                    ?>
                    <label class="control-label">Status :  </label>@if($curStatus['status']){{$curStatus['status']}}@endif
                </div>
                <div class="form-group">
                    <label class="control-label">{{ 'Priority : ' }}</label>@if($ticket['criticallity'][0]['criticallity']){{last($ticket['criticallity'])['criticallity']}}@endif
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
                    <label class="control-label">{{ 'Assigned To : ' }}</label>{{$assignedTo->first_name}} {{$assignedTo->last_name}}

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
                            <option value="{{$worker['id']}}" @if($assignedTo->id==$worker['id']) selected="selected" @endif>{{$worker['first_name']}} {{$worker['last_name']}}</option>
                            @endforeach
                        </select></p>
                </div>
                <div class="form-group">
                    <?php
                    
                    ?>
                    <label class="control-label">Priority </label>
                    <p><select name="criticallity" required="true">
                            
                             @foreach($critical as $crit)
                                        <?php
                                        $critVal = json_decode($crit->value)
                                        ?>
                                        <option value="{{$critVal->title}}" @if(!empty($ticket['criticallity'][0]['criticallity']) && last($ticket['criticallity'])['criticallity']==$critVal->title) selected="selected"  @endif style="background-color: #{{$critVal->color}}">{{$critVal->title}}</option>
                                        @endforeach
                            
                          
                        </select></p>
                </div>

                <div class="form-group">
                    <button id="add-comment" class="btn btn-primary" style="margin-top: 15px;">{{ trans('mis::all.create') }}</button>
                </div>
            </form>
        </div>
      @include('ticket::common.thread');
    </div>
</div>

<script>
    
$('#comment_form').submit(function(){
       var url ="{{URL::to('complaint-detail')}}/{{ $ticket['id'] }}";
       $('#add-comment').addClass('disabled');
        $.ajax({
            url:url,
            data: $('#comment_form').serialize(),
            method:'POST',
            success:function(data){
                console.log(data);
                //showMessage(data);
                if(data.status){
                    //location.reload();
                    $('#dashboard-center').load(url);
                    $('#add-comment').removeClass('disabled');
                }
            }
        });
        return false;
    });    
</script>