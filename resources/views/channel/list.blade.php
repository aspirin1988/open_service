@extends('layouts.app')

@section('content')
    <section>
        <form action="" method="post" class=" uk-panel-box uk-form uk-form-horizontal">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <fieldset data-uk-margin="">
                <legend>Добавить канал</legend>
                <div class="uk-form-row">
                    <label for="name" class="uk-form-label">Название канала:*</label>
                    <div class="uk-form-controls">
                        <input type="text" id="name" name="name" class="uk-width-9-10"
                               placeholder="Название канала" autocomplete="off">
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="link" class="uk-form-label">Link канала:*</label>
                    <div class="uk-form-controls">
                        <input type="text" id="link" name="link" class="uk-width-9-10"
                               placeholder="https://t.me/{Link}" autocomplete="off">
                    </div>
                </div>
                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <input type="submit" value="Добавить" class="uk-button uk-button-success">
                    </div>
                </div>
            </fieldset>
        </form>
        @if(count($channels))
            <div class="uk-panel-box">
                <table class="uk-table uk-width-1-1">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>Название канала</th>
                        <th>Link канала</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($channels as $channel)
                        <tr>
                            <td>{{$channel->id}}</td>
                            <td>{{$channel->name}}</td>
                            <td>{{$channel->link}}</td>
                            <td>
                                <a class="uk-button uk-button-danger"
                                   href="{{url('/admin/channel/delete/'.$channel->id)}}">
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