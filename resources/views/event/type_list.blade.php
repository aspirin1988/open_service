@extends('layouts.app')

@section('content')
    <section>
        <form action="" method="post" class=" uk-panel-box uk-form uk-form-horizontal">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <fieldset data-uk-margin="">
                <legend>Добавить город</legend>
                <div class="uk-form-row">
                    <label for="name" class="uk-form-label">Название события:*</label>
                    <div class="uk-form-controls">
                        <input type="text" id="name" name="name" ng-model="Event.name" class="uk-width-9-10"
                               placeholder="Название события">
                    </div>
                </div>
                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <input type="submit" class="uk-button uk-button-success">
                    </div>
                </div>
            </fieldset>
        </form>
        @if(count($types))
            <div class="uk-panel-box">
                <table class="uk-table uk-width-1-1">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>Название горада</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($types as $event)
                        <tr>
                            <td>{{$event->id}}</td>
                            <td>{{$event->name}}</td>
                            <td>
                                <a class="uk-button uk-button-danger"
                                   href="{{url('/admin/event/types/delete/'.$event->id)}}">
                                    <i class="uk-icon-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection