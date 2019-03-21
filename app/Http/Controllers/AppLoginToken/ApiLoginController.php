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
      $client=new Client();
      $rs=$client->request('post',$url,$data);
      echo ($rs->getBody());

  }
}