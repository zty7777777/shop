{{-- 用户注册--}}
<title>注册</title>
@extends('layouts.bst')

@section('content')
    <form class="form-signin" action="/userreg" method="post">
        {{csrf_field()}}
        <div style="width: 300px">
            <h2 class="form-signin-heading">用户注册</h2>
            <label for="inputNickName">Nickname</label>
            <input type="text" name="u_name" id="inputNickName" class="form-control" placeholder="nickname" required autofocus>
            <label for="inputEmail">Email</label>
            <input type="email" name="u_email" id="inputEmail" class="form-control" placeholder="@" required autofocus>
            <label for="inputPassword" >Password</label>
            <input type="password" name="u_pwd" id="inputPassword" class="form-control" placeholder="***" required>
            <label for="inputPassword2" >Password</label>
            <input type="password" name="u_pwd2" id="inputPassword2" class="form-control" placeholder="***" required>

            <div style="width: 100px">
            <p></p><button class="btn btn-info" type="submit">注册</button>
            </div>
        </div>
    </form>
@endsection