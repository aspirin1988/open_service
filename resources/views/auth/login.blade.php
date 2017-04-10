@extends('layouts.app')

@section('content')
    <section style="background: url('/img/email-send-ss-1920.jpg');height: 100%; position: absolute; left: 0; top: 0; width: 100%; padding: 100px; box-sizing: border-box;">
        <div class="uk-container uk-container-center">
            <div class="uk-grid">
                <div class="uk-width-1-4"></div>
                <div class="uk-width-2-4 uk-form uk-form-horizontal uk-border-rounded " style="background: rgba(0, 0, 0, 0.6); padding: 10px; color: #FFF;">
                    <div class="panel panel-default">
                        <div class="uk-article-title">Вход</div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                                {{ csrf_field() }}

                                <div class="uk-form-row uk-margin-small-top">
                                    <label for="email" class="uk-form-label ">E-Mail</label>
                                    <div class="uk-form-controls">
                                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                                               class="uk-width-9-10 {{ $errors->has('email') ? ' uk-form-danger' : '' }}"
                                               placeholder="E-mail">
                                    </div>
                                </div>

                                <div class="uk-form-row uk-margin-small-top">
                                    <label for="email" class="uk-form-label ">Password</label>
                                    <div class="uk-form-controls">
                                        <input type="password" id="password" name="password" value="{{ old('email') }}"
                                               class="uk-width-9-10 {{ $errors->has('password') ? ' uk-form-danger' : '' }}"
                                               placeholder="password">
                                    </div>
                                </div>

                                <div class="uk-form-row uk-margin-small-top">
                                    <div class="col-md-6 col-md-offset-4">
                                        <div class="uk-form-controls">
                                            <label>
                                                <input type="checkbox"
                                                       name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                Запомнить меня
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="uk-form-row uk-margin-small-top">
                                    <div class="uk-form-controls">
                                        <button type="submit" class="uk-button uk-button-primary">
                                            Войти
                                        </button>

                                        {{--<a class="btn btn-link" href="{{ route('password.request') }}">
                                            Forgot Your Password?
                                        </a>--}}
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
