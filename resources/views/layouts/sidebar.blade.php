<div class="tm-sidebar uk-width-medium-1-5 uk-hidden-small uk-row-first">
    <nav>
        <ul class="tm-nav uk-nav" data-uk-nav="">
            <li class="uk-nav-header">События</li>
            <li class="@if($that->request_url==url('/admin/events/calendar')) uk-active @endif">
                <a href="{{url('/admin/events/calendar')}}">Календарь собыий</a>
            </li>
            <li class="@if($that->request_url==url('/admin/events')) uk-active @endif">
                <a href="{{url('/admin/events')}}">Список событий</a>
            </li>
            <li class="@if($that->request_url==url('/admin/event/types')) uk-active @endif">
                <a href="{{url('/admin/event/types')}}">Типы событий</a>
            </li>

            {{--<li class="uk-nav-header">Пользователи</li>--}}
{{--            <li class="@if($that->request_url==url('/admin/users')) uk-active @endif">--}}
{{--                <a href="{{url('/admin/users')}}">Список пользователей</a>--}}
            {{--</li>--}}
            <li class="uk-nav-header">Города</li>
            <li class="@if($that->request_url==url('/admin/cities')) uk-active @endif">
                <a href="{{url('/admin/cities')}}">Список городов</a>
            </li>
        </ul>
    </nav>
</div>