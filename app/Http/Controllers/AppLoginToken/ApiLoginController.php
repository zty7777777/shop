<?php
namespace App\Http\Controllers\AppLoginToken;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiLoginController extends  Controller{
    /**
     * 接收app登录信息
     */
  public function loginInfo()
  {
      $account=$_POST['account'];
      $password=$_POST['password'];
      $ip=$_POST['ip'];

      $data=[
        'account'=>$account,
        'password'=>$password,
          'ip'=>$ip
      ];
      $url="http://passport.zty77.com/new/login";
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_URL,$url);
      curl_setopt($ch,CURLOPT_POST,true);//文件上传
      curl_setopt($ch,CURLOPT_POSTFIELDS,$data); //文件上传
      curl_setopt($ch,CURLOPT_HEADER,0);//不返回头部信息
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//
    //抓取url传给浏览器
      $rs=curl_exec($ch);
      return $rs;
  }

    /**
     * 检测异地登录
     */
    public function islogin()
    {
        $account=$_POST['account'];
        $ip=$_POST['ip'];
        $area=$_POST['area'];

        $data=[
            'account'=>$account,
            'ip'=>$ip,
            'area'=>$area
        ];
        $url="http://passport.zty77.com/new/islogin";
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,true);//文件上传
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data); //文件上传
        curl_setopt($ch,CURLOPT_HEADER,0);//不返回头部信息
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//
        //抓取url传给浏览器
        $rs=curl_exec($ch);
        return $rs;
    }

  public function test(){
      echo mt_rand(90,100);
  }
}