  @if(count($threads))
        @foreach($threads as $thread)
        <div class="module">
            <div class="module-head">Created on: <span>{{$thread['created_at']}}</span> <span> Commented By : {{$thread->commentedBy->first_name}}</span></div>
            <div class="module-body">
                {{$thread['comment']}}
            </div>
        </div>
        @endforeach
        @endif