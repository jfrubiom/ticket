@extends(Config::get('mis::views.master'))
@section('content')
<script src="{{ asset('packages/efusionsoft/mis/assets/js/dashboard/user.js') }}"></script>
     @include(Config::get('mis::views.left-nav'))
     <div class="module-body ajax-content">
                    @include('mis::user.list-users')
                </div>


@stop