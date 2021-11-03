@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 20px">
  <div class="row">
    <div class="col"></div>
      <div class="col">
        <form role="form" method="POST" action="{{ route('teacher.login') }}">
          {{ csrf_field() }}
          <h1 class="h3 mb-3 fw-normal">教师登录</h1>

          <div class="form-floating">
            <input id="username" type="text" class="form-control" name="username" value="" required>
            <label for="floatingInput">用户名</label>
            @if ($errors->has('username'))
                <span class="help-block">
                    <strong>{{ $errors->first('username') }}</strong>
                </span>
            @endif
          </div>
          <div class="form-floating">
            <input id="password" type="password" class="form-control" name="password" value="" required>
            <label for="floatingPassword">密码</label>
            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
          </div>

          <div class="checkbox mb-3">
            <label>
              <input type="checkbox" value="remember-me"> 记住我
            </label>
          </div>
          <button class="w-100 btn btn-lg btn-primary" type="submit">登录</button>
          <a class="btn btn-link disabled" href="{{ url('/password/reset') }}">
                                          忘记密码?
                                      </a>
          <p class="mt-5 mb-3 text-muted">© 2017–2021</p>
        </form>
      </div>
      <div class="col"></div>
    </div>
</div>
@endsection
