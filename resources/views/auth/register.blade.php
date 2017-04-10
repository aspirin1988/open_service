@extends('layouts.app')

@section('content')
    <section style="background: url('/img/email-send-ss-1920.jpg');height: 100%; position: absolute; left: 0; top: 0; width: 100%; padding: 100px; box-sizing: border-box;">
        <div class="uk-container uk-container-center">
            <div class="uk-grid">
                <div class="uk-width-1-4"></div>
                <div class="uk-width-2-4 uk-form uk-form-horizontal" style="background: rgba(0, 0, 0, 0.6); padding: 10px; color: #FFF;">
                    <div class="panel panel-default">
                        <div class="panel-heading">Register</div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}">
                                {{ csrf_field() }}

                                <div class="uk-form-row uk-margin-small-top form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="col-md-4 control-label">Name</label>

                                    <div class="uk-form-controls">
                                        <input id="name" type="text" class="uk-width-9-10" name="name"
                                               value="{{ old('name') }}" required autofocus placeholder="Имя (Фамилия)" >

                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="uk-form-row uk-margin-small-top form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                    <div class="uk-form-controls">
                                        <input id="email" type="email" class="uk-width-9-10" name="email"
                                               value="{{ old('email') }}" required placeholder="E-mail" >

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="uk-form-row uk-margin-small-top form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password" class="col-md-4 control-label">Password</label>

                                    <div class="uk-form-controls">
                                        <input id="password" type="password" class="uk-width-9-10" name="password"
                                               required  placeholder="Password">

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="uk-form-row uk-margin-small-top">
                                    <label for="password-confirm" class="col-md-4 control-label">Confirm
                                        Password</label>

                                    <div class="uk-form-controls">
                                        <input id="password-confirm" type="password" class="uk-width-9-10"
                                               name="password_confirmation" required placeholder="Confirm">
                                    </div>
                                </div>

                                <div class="uk-form-row uk-margin-small-top">
                                    <div class="uk-form-controls">
                                        <button type="submit" class=" uk-button uk-button-primary">
                                            Зарегистрироваться
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="uk-width-1-4"></div>
            </div>
        </div>
    </section>
@endsection
