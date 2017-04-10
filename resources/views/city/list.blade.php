@extends('layouts.app')

@section('content')
    <section>
        <form action="" method="post" class=" uk-panel-box uk-form uk-form-horizontal">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <fieldset data-uk-margin="">
                <legend>Добавить город</legend>
                <div class="uk-form-row">
                    <label for="name" class="uk-form-label">Название города:*</label>
                    <div class="uk-form-controls">
                        <input type="text" id="name" name="name" ng-model="Event.name" class="uk-width-9-10"
                               placeholder="Название города">
                    </div>
                </div>
                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <input type="submit" class="uk-button uk-button-success">
                    </div>
                </div>
            </fieldset>
        </form>
        @if(count($cities))
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
                    @foreach($cities as $city)
                        <tr>
                            <td>{{$city->id}}</td>
                            <td>{{$city->name}}</td>
                            <td>
                                <a class="uk-button uk-button-danger"
                                   href="{{url('/admin/city/delete/'.$city->id)}}">
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