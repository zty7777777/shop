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
              <a href="https://open.weixin.qq.com/connect/qrconnect?appid=wxe24f70961302b5a5&amp;redirect_uri=http%3a%2f%2fmall.77sc.com.cn%2fweixin.php%3fr1%3dhttp%3a%2f%2fzty.tactshan.com%2fweixin%2fgetcode&amp;response_type=code&amp;scope=snsapi_login&amp;state=STATE#wechat_redirect">
                  <img src="/upload/wechat.jpg" alt="" style="width: 40px;height: 40px"></a>
          </div>

      </div>
    </form>
@endsection



