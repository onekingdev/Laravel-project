@extends('layouts.app')

@section('content')
<div class="container">
    <div class="columns">
        <div class="column is-three-fifths is-offset-one-fifth">
            <div class="box">
                <h2 class="title">Reset Password</h2>

                @if (session('status'))
                    <article class="message is-success">
                        <div class="message-body">
                            {{ session('status') }}
                        </div>
                    </article>
                @endif

                <form method="POST" action="{{ route('password.request') }}">
                    {{ csrf_field() }}

                    <input type="hidden" class="input" name="token" value="{{ $token }}">

                    <div class="field">
                        <p class="control has-icons-left has-icons-right">
                            <input class="input {{ $errors->has('email') ? ' is-danger' : '' }}" type="email" name="email" value="{{ $email or old('email') }}" placeholder="E-Mail Address" required autofocus>
                            <span class="icon is-small is-left">
                                <i class="fa fa-envelope"></i>
                            </span>
                        </p>
                        @if ($errors->has('email'))
                            <p class="help is-danger">
                                {{ $errors->first('email') }}
                            </p>
                        @endif
                    </div>
                    <div class="field">
                        <p class="control has-icons-left">
                            <input class="input {{ $errors->has('password') ? ' is-danger' : '' }}" type="password" name="password" placeholder="Password">
                            <span class="icon is-small is-left">
                                <i class="fa fa-lock"></i>
                            </span>
                        </p>
                        @if ($errors->has('password'))
                            <p class="help is-danger">
                                {{ $errors->first('password') }}
                            </p>
                        @endif
                    </div>
                    <div class="field">
                        <p class="control has-icons-left">
                            <input id="password-confirm" type="password" class="input" name="password_confirmation" placeholder="Confirm password">
                            <span class="icon is-small is-left">
                                <i class="fa fa-lock"></i>
                            </span>
                        </p>
                    </div>

                    <div class="field">
                        <p class="control">
                            <button class="button is-primary" type="submit">
                                Reset Password
                            </button>
                        </p>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection
