<div class="col-sm-2 btn-fa fa-pg">
    <ul style="max-width: 200px;" class="nav nav-pills nav-stacked">
        <li class="@if(Request()->segment(1)=="settings") active @endif">
            <a href="{{route('settings')}}"><i class="fa fa-th"></i> @lang("Settings")</a>
        </li>

        <li class="@if(Request()->segment(1)=="roles") active @endif">
            <a href="{{route('roles')}}">
                <i class="fa fa-chevron-right"></i> @lang("Roles") </a>
        </li>

    </ul>
</div>
