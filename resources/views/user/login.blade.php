{{-- 用户登录--}}
<title>登录</title>
@extends('layouts.bst')

@section('content')
    <form class="form-signin" action="/userlogin" method="post">
        {{csrf_field()}}
        <h2 class="form-signin-heading">请登录</h2>
      <div style="width: 300px">
        <label for="inputEmail">Email</label>
           <input type="email" name="u_email" id="inputEmail" class="form-control" placeholder="@" required autofocus>

        <label for="inputPassword" >Password</label>
           <input type="password" name="u_pwd" id="inputPassword" class="form-control" placeholder="***" required>
     <p></p>
          <div style="width: 100px">
              <button class="btn btn-info" type="submit">登录</button>
          </div>

      </div>
    </form>
@endsection



