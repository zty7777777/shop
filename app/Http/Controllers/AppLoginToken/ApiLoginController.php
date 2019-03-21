<?php
namespace App\Http\Controllers\AppLoginToken;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiLoginController extends  Controller{

  public function loginInfo()
  {
      $account=$_POST['account'];
      $pwd=$_POST['password'];

      $data=[
        'account'=>$account,
        'pwd'=>$pwd
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
      var_dump($rs);


  }
}