<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class CheckLoginToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       /*if(!$request->session()->get('u_token')){
           header('Refresh:2;url=/userlogin');
           echo '请先登录';
           exit;
        }*/
       if(!empty($_COOKIE['token']) && !empty($_COOKIE['uid'])){
          //验证token
           $key='user_token_'.$_COOKIE['uid'];
           $token=Redis::get($key);
           if($token==$_COOKIE['token']){
                echo 'token失效';
               $request->attributes->add(['is_login'=>1]);
               exit;
           }else{
               //未登录
               $request->attributes->add(['is_login'=>0]);
           }

       }else{
           header('Refresh:2;url=http://passport.shop.com/api/login');
           $request->attributes->add(['is_login'=>0]);
           echo '请先登录';
           exit;
       }
        return $next($request);
        
    }
}
