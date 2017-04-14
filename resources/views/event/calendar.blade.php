@extends('layouts.app')

@section('content')
    <section ng-controller="EventCalendarCtrl"
             ng-init="Year='{{$year}}'; Month='{{$month}}'; CurrentDate='{{$current_date}}'">
        <nav class="uk-navbar uk-navbar-attached">
            <div class="uk-container uk-container-center uk-text-center">
                <ul class="uk-button-group uk-margin-top">
                    <li><a class="uk-button" href="{{url('/admin/events/calendar/'.($year-1).'/'.$month)}}"><<</a></li>
                    <li>
                        @if($month==1)
                            <button class="uk-button" disabled="disabled"><</button>
                        @else
                            <a class="uk-button" href="{{url('/admin/events/calendar/'.$year.'/'.($month-1))}}"><</a>
                        @endif
                    </li>
                    <li class="uk-parent" data-uk-dropdown="pos:'bottom-center'">
                        <button class="uk-button calendar-navigation">
                            {{__('date.months.'.$month)}} {{$year}}
                            <i class="uk-icon-caret-down"></i>
                        </button>
                        <div class="uk-dropdown uk-dropdown-navbar uk-dropdown-autoflip uk-dropdown-bottom">
                            <ul class="uk-nav uk-nav-navbar" data-uk-nav>
                                @if (date('Y-m')!=$query)
                                    <li>
                                        <a href="{{url('/admin/events/calendar/')}}">{{__('date.Now')}}</a>
                                    </li>
                                @endif
                                <li class="uk-parent">
                                    <a href="#">{{__('date.Year')}}</a>
                                    <ul class="uk-nav-sub" role="menu">
                                        @for($i=$year-5;$i<$year+5;$i++)
                                            <li><a href="{{url('/admin/events/calendar/'.$i.'/'.$month)}}">{{$i}}</a>
                                            </li>
                                        @endfor
                                    </ul>
                                </li>
                                <li class="uk-parent">
                                    <a href="#">{{__('date.Month')}}</a>
                                    <ul class="uk-nav-sub" role="menu">
                                        @for($i=1;$i<=12;$i++)
                                            <li>
                                                <a href="{{url('/admin/events/calendar/'.$year.'/'.$i)}}">{{__('date.months.'.$i)}}</a>
                                            </li>
                                        @endfor
                                    </ul>
                                </li>
                            </ul>
                        </div>

                    </li>
                    <li>
                        @if($month==12)
                            <button class="uk-button" disabled="disabled">></button>
                        @else
                            <a class="uk-button" href="{{url('/admin/events/calendar/'.$year.'/'.($month+1))}}">></a>
                        @endif
                    </li>
                    <li><a class="uk-button" href="{{url('/admin/events/calendar/'.($year+1).'/'.$month)}}">>></a></li>
                </ul>
            </div>
        </nav>
        <div class="uk-grid calendar" ng-repeat="(key,week) in Events">
            <div class="uk-width-1-7 calendar-day" ng-repeat="(key,day) in week">
                <div class="uk-panel-box day-content"
                     ng-class="{'uk-panel-box-secondary':day.day==null,'uk-panel-box-primary':day.day!=null,'current_day':CurrentDate==day.date}">
                    <div ng-if="day.day!=null">
                        <i ng-click="ShowAddEvent(day.date)" class="uk-icon-plus uk-icon-small uk-icon-hover"></i>
                        [[ DayList[day.day] ]]
                        <div ng-if="day.events.length" class="uk-display-inline-block">
                            ([[day.events.length]])
                        </div>
                        <br>
                        <b class="uk-float-right">[[ day.date ]]</b>

                        <div class="uk-width-1-1 events-list">
                            <a class="uk-width-1-1 uk-badge" ng-repeat="(key_e,event) in day.events"
                               ng-click="ShowEvent([[event.id]])">[[event.name]]</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div id="my-id" class="uk-modal">
            <div class="uk-modal-dialog">
                <a class="uk-modal-close uk-close"></a>
                <div class="uk-form uk-form-horizontal">
                    <div class="uk-margin uk-modal-content">
                        <a ng-if="EventID" class="uk-float-left uk-button-mini uk-button-danger uk-text-center"
                           ng-click="deleteEvent()">
                            <i class="uk-icon-trash"></i>
                        </a>
                        &nbsp;Событие
                    </div>
                    <fieldset data-uk-margin="">
                        <legend></legend>
                        <div class="uk-form-row">
                            <label for="name" class="uk-form-label">Название события:*</label>
                            <div class="uk-form-controls">
                                <input type="text" id="name" ng-model="Event.name" class="uk-width-9-10"
                                       placeholder="Название события">
                            </div>
                        </div>
                        <div class="uk-form-row">
                            <label for="name" class="uk-form-label">Тип события:</label>
                            <div class="uk-form-controls">
                                <select ng-model="Event.event_type">
                                    @foreach($types as $type)
                                        <option value="{{$type->id}}">{{$type->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="uk-form-row" ng-class="{'uk-hidden':ChannelList.length==0}">
                            <label for="name" class="uk-form-label">Каналы :*</label>
                            <div class="uk-form-controls">

                                <select ng-model="AddChannelID" ng-class="{'uk-hidden':!EventID}">
                                    <option ng-repeat="(key,channel) in ChannelList"
                                            value="[[channel.id]]">[[channel.name]]
                                    </option>
                                </select>

                                <select ng-model="AddChannelID" ng-class="{'uk-hidden':EventID}">
                                    <option ng-repeat="(key,channel) in ChannelList"
                                            value="[[key]]">[[channel.name]]
                                    </option>
                                </select>
                                <i ng-click="addChannel()" ng-class="{'uk-hidden':!EventID}"
                                   class="uk-icon-plus uk-icon-small uk-icon-hover"></i>
                                <i ng-click="addChannelNew()" ng-class="{'uk-hidden':EventID}"
                                   class="uk-icon-plus uk-icon-small uk-icon-hover"></i>
                            </div>
                        </div>
                        <div class="uk-container" id="channel_list">
                            <label class="uk-form-label">Каналы:</label>
                            <div class="uk-grid uk-grid-small">
                                <div class="uk-width-1-2" ng-repeat="(key,channel) in Event.channels">
                                    <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                                        <a class="uk-float-right uk-button-mini uk-button-danger uk-text-center"
                                           ng-class="{'uk-hidden':!EventID}"
                                           ng-click="deleteChannel(channel.id)">
                                            <i class="uk-icon-trash"></i>
                                        </a>
                                        <a class="uk-float-right uk-button-mini uk-button-danger uk-text-center"
                                           ng-class="{'uk-hidden':EventID}"
                                           ng-click="deleteChannelNew(key)">
                                            <i class="uk-icon-trash"></i>
                                        </a>
                                        <div class="uk-form-row">
                                            <label for="send_date" style="width: 110px"
                                                   class="uk-form-label uk-overflow-hidden">[[channel.channel.name]]</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label for="content" class="uk-form-label">Контент:*</label>
                            <div class="uk-form-controls">
                                <textarea id="content" class="uk-width-1-1" ng-model="Event.content"></textarea>
                            </div>
                        </div>
                        <div class="uk-form-row">
                            <label for="city" class="uk-form-label">Город:</label>
                            <div class="uk-form-controls">
                                <select ng-model="Event.city">
                                    @foreach($cities as $city)
                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="uk-form-row">
                            <label for="address" class="uk-form-label">Адрес:</label>
                            <div class="uk-form-controls">
                                <input type="text" id="address" ng-model="Event.address" class="uk-width-1-2"
                                       placeholder="Адрес">
                            </div>
                        </div>
                        <div class="uk-form-row">
                            <label for="the_date" class="uk-form-label">Дата начала:</label>
                            <div class="uk-form-controls">
                                <input id="the_date" type="text" data-uk-datepicker="{format:'YYYY-MM-DD'}"
                                       ng-model="Event.the_date">
                            </div>
                        </div>
                        <div class="uk-form-row">
                            <label for="time" class="uk-form-label">Время начала:</label>
                            <div class="uk-form-controls">
                                <input type="text" id="time" ng-model="Event.time"
                                       data-uk-timepicker="{format:'24h'}">
                            </div>
                        </div>
                        <div class="uk-form-row">
                            <label for="registration_date" class="uk-form-label">Дата регистрации:</label>
                            <div class="uk-form-controls">
                                <input id="registration_date" type="text" data-uk-datepicker="{format:'YYYY-MM-DD'}"
                                       ng-model="Event.registration_date">
                            </div>
                        </div>
                        <div class="uk-form-row">
                            <label for="link" class="uk-form-label">Ссылка:</label>
                            <div class="uk-form-controls">
                                <input type="text" id="link" ng-model="Event.link" class="uk-width-1-2"
                                       placeholder="Ссылка">
                            </div>
                        </div>
                        <div class="uk-container" id="reminder_list">
                            <label class="uk-form-label">Напоминания:
                                <i ng-click="AddReminder(Event.id)"
                                   class="uk-icon-plus uk-icon-small uk-icon-hover"></i>
                            </label>
                            <br>
                            <br>
                            <div class="uk-grid uk-grid-small">
                                <div class="uk-width-1-1" ng-repeat="(key,reminder) in Event.reminders">
                                    <div class="uk-panel uk-panel-box uk-panel-box-secondary">
                                        <a class="uk-float-right uk-button-mini uk-button-danger uk-text-center"
                                           ng-click="deleteReminder(reminder.id)"><i class="uk-icon-trash"></i></a>
                                        <div class="uk-form-row">
                                            <label for="send_date" class="uk-form-label">Дата отправки:</label>
                                            <div class="uk-form-controls">
                                                <input type="text" id="link" ng-model="reminder.send_date"
                                                       class="uk-width-1-2"
                                                       data-uk-datepicker="{format:'YYYY-MM-DD'}"
                                                       placeholder="Дата отправки">
                                                <input type="text" id="form-time" ng-model="reminder.send_time"
                                                       data-uk-timepicker="{format:'24h'}">
                                            </div>
                                        </div>
                                        <div class="uk-form-row">
                                            <label for="done" class="uk-form-label">Статус :</label>
                                            <div class="uk-form-controls">
                                                <label ng-if="reminder.done" for=""
                                                       class="uk-text-success">Выполнено</label>
                                                <label ng-if="!reminder.done" for="" class="uk-text-warning">Не
                                                    выполнено</label>
                                            </div>
                                        </div>
                                        <div class="uk-form-row">
                                            <label for="active_[[key]]" class="uk-form-label">Включен :</label>
                                            <div class="uk-form-controls">
                                                <div class="onoffswitch">
                                                    <input type="checkbox" class="onoffswitch-checkbox"
                                                           id="active_[[key]]" ng-model="reminder.active"
                                                           ng-checked="reminder.active==1">
                                                    <label class="onoffswitch-label" for="active_[[key]]"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <div class="uk-modal-footer uk-text-right">
                        <div ng-if="Event.id">
                            <button class="uk-button uk-button-primary" ng-click="saveEvent()">
                                Save
                            </button>
                        </div>
                        <div ng-if="!Event.id">
                            <button class="uk-button uk-button-primary" ng-click="addEvent()">Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection