@extends(Config::get('mis::views.master'))

@section('content')
<script src="{{ asset('packages/efusionsoft/ticket/js/ticket.js') }}"></script>
<script src="{{ asset('packages/efusionsoft/ticket/js/typeahead.bundle.js') }}"></script>
<script>
var clientMgrId = "{{$clientManager['id']}}"
var maidMgrId = "{{$maidManager['id']}}";
var clientCompType = {{$clientCompType}};
var maidCompType = {{$maidCompType}};
</script>

<div class="container" id="main-container">
    <div class="row">
        <div class="col-lg-12">
            <section class="module">
                <div class="module-head">
                    <b>{{ trans('ticket::ticket.new') }}</b>
                </div>
                <div class="module-body">
                    <form class="form-horizontal" id="create-ticket-form" method="POST">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-inline">
                                    Complaint against:
                                    
                                    <div class="form-group">
                                        <select name="complained_against_type" id="complained_against_type" class="form-control" onchange="changeBoxes(this.value)">
                                        <option value="Maid">Maid</option>    
                                        <option value="Client">Client</option>    
                                    </select>
                                    </div>
                                     <div id="complained_against_name_div" class="form-group">
                                        <label class="sr-only" for="complained_against">complained_against</label>
                                        <input type="text"  class="form-control typehead"  id="complained_against_name" name="complained_against_name" value="" placeholder="Complaint Against " />
                                        <input type="hidden" name="complained_against_id" id="complained_against_id" />
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="sr-only" for="complained_by">Title</label>
                                        <input type="text"  class="form-control typeahead "  id="complained_by_name" name="complained_by_name" value="" placeholder="Complained By " />
                                        
                                        <input type="hidden" name="complained_by_id" id="compalained_by_id" />
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Complained Type</label>
                                        <p>
                                            <select id="complaint_type" name="complaint_type">
                                            </select>
                                        </p>
                                    </div>
                                    
                                </div>
                                
                                
                                <div class="form-group">
                                    <label class="control-label">Title</label>
                                    <p><input class="col-lg-12 form-control" type="text" required="true" placeholder="{{ trans('ticket::ticket.place_title') }}" id="title" name="title"></p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Comment</label>
                                    <p><textarea class="col-lg-12 form-control" type="text" required="true" placeholder="Enter Your Comment" id="comment" name="comment"></textarea></p>
                                </div>
                                 <div class="form-group">
                                    <label class="control-label">Ticket Time</label>
                                    <p><input class="col-lg-12 form-control" type="text" required="true" value="{{date('Y-m-d H:i')}}" id="time" name="time"></p>
                                    <input  type="hidden"  value="{{$maidManager['id']}}" id="assigned_to_id" name="assigned_to_id">
                                </div>
                               @if($currentUser->hasAccess('superuser'))
                                 <div class="form-group">
                                    <label class="control-label">Assignee</label>
                                    <p><select name="from_admin" id="from_admin"  onchange="$('#assigned_to_id').val(this.value)">
                                        @foreach($workers as $worker)
                                        <option value="{{$worker['id']}}">{{$worker['first_name']}} {{$worker['last_name']}}</option>
                                        @endforeach
                                        </select></p>
                                        
                                </div>
                                @endif
                                <div class="form-group">
                                    <label class="control-label">Priority</label>
                                    <p><select name="criticallity" required="true">
                                        @foreach($critical as $crit)
                                        <?php
                                        $critVal = json_decode($crit->value)
                                        ?>
                                        <option value="{{$critVal->title}}" style="background-color: #{{$critVal->color}}">{{$critVal->title}}</option>
                                        @endforeach
                                        </select></p>
                                </div>

                            </div>
                            <div class="col-lg-6">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <button id="add-ticket" class="btn btn-primary" style="margin-top: 15px;">{{ trans('mis::all.create') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
    
</div>
<script>
    
    
     $('#create-ticket-form').submit(function(){
        var frmData  = $(this).serialize();
        console.log(frmData);
        //return false;
        $.ajax({
            url:document.url,
            data: $('#create-ticket-form').serialize(),
            method:'PUT',
            success:function(data){
                    console.log(data);
                    showMessage(data);
                    if(data.status){
                        window.location="{{URL::to('complaints')}}";
                    }
            }
        });
        return false;
    });
    
    
var maidUrl = ' {{url('suggest/users/maids')}}';
var clientUrl = ' {{url('suggest/users/Client')}}';
 var labels, mapped;
 var maidData = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  remote: maidUrl+'?q=%QUERY'
});
 
maidData.initialize();


var clientData = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  remote: clientUrl+'?q=%QUERY'
});
 clientData.initialize();
function maidCall(){
    $('#complained_against_name').typeahead(null, {
  name: 'cam',
  displayKey: 'name',
  valueKey :'id',
  source: maidData.ttAdapter()
});


$('#complained_by_name').typeahead(null, {
  name: 'cbc',
  displayKey: 'name',
  valueKey :'id',
  source: clientData.ttAdapter()
});
}

maidCall();

function destroyAll(){
     $("#complained_against_name").typeahead('destroy');
        $("#complained_by_name").typeahead('destroy');
}

function clientCall(){
    $('#complained_against_name').typeahead(null, {
  name: 'cam',
  displayKey: 'name',
  valueKey :'id',
  source: clientData.ttAdapter()
});


$('#complained_by_name').typeahead(null, {
  name: 'cbc',
  displayKey: 'name',
  valueKey :'id',
  source: maidData.ttAdapter()
});
}


function changeBoxes(val){
    destroyAll()
        if(val=='Maid'){
            console.log('Maid');
            fillSelect(maidCompType);
            maidCall();

        }else if(val=='Client'){
       fillSelect(clientCompType);
        clientCall();
    }
}

function fillSelect(data){
    $('#complaint_type').html('');
    //console.log(data);
    var opt ='';
    $.each(data,function(key,val ){
        opt+='<option value="'+val+'">'+val+'</option>'
    });
    $('#complaint_type').html(opt);
}
fillSelect(maidCompType);
$('.typeahead.input-sm').siblings('input.tt-hint').addClass('hint-small');
$('.typeahead.input-lg').siblings('input.tt-hint').addClass('hint-large');
</script>
@stop