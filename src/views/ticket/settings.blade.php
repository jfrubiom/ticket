@extends(Config::get('mis::views.master'))
@section('content')
<script src="{{ asset('packages/efusionsoft/ticket/js/ticket.js') }}"></script>
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-11">
        
        <ul class="nav nav-tabs">
            <li class="active"><a href="#priority" data-toggle="tab">Priority</a></li>
            <li><a href="#assignements" data-toggle="tab">Assignment</a></li>

        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active" id="priority">
                <div class="jumbotron">
                    <form method="post" class="form-inline" id="add-priority-frm">
                        
                               

                               
                                    <div class="form-group">
                                        <label class="sr-only" for="title">Title</label>
                                        <input type="text"  class="form-control" name="title" value="" placeholder="Enter Title" />
                                    </div>
                               
                               
                                    <div class="form-group">
                                        <label class="sr-only" for="title">Color</label>
                                        <input type="text" name="color"  class="form-control color" style="background:#FFFFFF;" value="FFFFFF"  />
                                    </div>
                               
                                <button type="submit" id="save-priority" name="save" class="btn btn-primary" data-loading-text="Saving..">Save</button>
                            
                    </form>
                </div>
                <div class="row">



                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th >Title</th>
                                    <th >Color code</th>

                                    <th >Action</th>     
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($priorities as $prior)
                                <tr id="ticket-{{$prior['id']}}">
                                    <?php
                                    $val = json_decode($prior['value']);
                                    ?>
                                    <td >&nbsp;{{{ $val->title }}}</td>

                                    <td class="hidden-xs"><div style="background-color: {{$val->color}}; width: 100px;" class="btn" >&nbsp;</div></td>
                                    <td class="visible-lg">&nbsp;<a class="btn btn-danger" data-loading-text="Requesting.." href="javascript:void(0);" onclick="deletePriority({{$prior['id']}})">Delete</a></td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>


                </div>
            </div>
            <div class="tab-pane" id="assignements">
                <div class="jumbotron">
                    <form name="assignment-form" id="assignment-form">
                        <div class="panel">
                            <div class="row pad-row">
                                <div class="col-md-4">
                                    Default assignee for maids 
                                </div>

                                <div class="col-md-2 ">
                                    <select name="maid_manager">
                                        @foreach($staff as $st)
                                        <option value="{{$st->id}}" @if($st->id==$maidMgrId) selected="selected" @endif>{{$st->first_name}} {{$st->last_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 ">
                                    <input type="text" name="color_maid_manager"  class="color" value="{{$maidColor}}" style="background-color:#{{$maidColor}}; "  />
                                </div>
                            </div>

                            <div class="row pad-row">
                                <div class="col-md-4">
                                    Default assignee for Client 
                                </div>
                                <div class="col-md-2 ">
                                    <select name="client_manager">
                                        @foreach($staff as $st)
                                        <option value="{{$st->id}}" @if($st->id==$clientMgrId) selected="selected" @endif>{{$st->first_name}} {{$st->last_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="color_client_manager"  class="color" value="{{$clientColor}}" style="background-color:#{{$maidColor}}; "  />
                                </div>
                                 <div class="col-md-2">
                                     <button type="submit" id="save-assignment" name="save" class="btn btn-primary" data-loading-text="Saving..">Save</button>
                                 </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
            jQuery('#assignment-form').submit(function(){
    var serialised = $(this).serialize();
           
           
                $('#save-assignment').button('loading');
                        $.ajax({
                        url:document.url,
                                data: serialised,
                                method:'POST',
                                success:function(data){
                                console.log(data);
                                        showMessage(data);
                                        if (data.status){
                                    window.location = '';
                                }
                                $('#save-assignment').button('reset');
                                }
                        });
         
    return false;
    });
    
    
     jQuery('#add-priority-frm').submit(function(){
    var serialised = $(this).serialize();
            var title = $.trim($(this).find('input[name="title"]').val());
            var color = $.trim($(this).find('input[name="color"]').val());
            if (title && color){
                $('#save-priority').button('loading');
                        $.ajax({
                        url:document.url,
                                data: serialised,
                                method:'PUT',
                                success:function(data){
                                console.log(data);
                                        showMessage(data);
                                        if (data.status){
                                window.location = '';
                                }
                                }
                        });
            }
    return false;
    });
    
            function deletePriority(id){
        $('#ticket-'+id).find('.btn-danger').button('loading');
        $.ajax({
            url:document.url,
                    data: 'id=' + id,
                    method:'DELETE'
            }).done(function(data){
            console.log(data);
                    if (data.status){
            // console.log(id);
            $('#ticket-' + id).fadeOut(600).remove();
            } else{
            console.log('not' + id);
            }
            showMessage(data);
            }
            );
            }
</script>
<script src="{{ asset('packages/efusionsoft/ticket/js/jscolor/jscolor.js') }}"></script>

@stop