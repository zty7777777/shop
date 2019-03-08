<?php

namespace App\Http\Controllers\Weixin;

use App\Model\UsersModel;
use App\Model\WeixinMedia;
use App\Model\WeixinMsg;
use App\Model\WxRegisterUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redis;

use App\Model\WeixinUser;

use GuzzleHttp;
use Illuminate\Support\Facades\Storage;
use function Psy\bin;

class WeixinController2 extends Controller
{

    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token

    /**
     * 接收微信服务器事件推送
     */
    public function wxEvent()
    {
        $return_data = file_get_contents("php://input");


        //解析XML
        $xml = simplexml_load_string($return_data);        //将 xml字符串 转换成对象

        //记录日志
        $log_str = date('Y-m-d H:i:s') . "\n" . $return_data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);

        $event = $xml->Event;                       //事件类型
        $openid = $xml->FromUserName;               //用户openid



                if($event=='subscribe'){                        //扫码关注事件
                    $sub_time = $xml->CreateTime;               //扫码关注时间
                    //获取用户信息
                    $user_info = $this->getUserInfo($openid);

                    //保存用户信息
                    $u = json_decode(Redis::hGet('userinfo',$openid));

                    $xml_response = '<xml>
                            <ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName>
                            <CreateTime>'.time().'</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content>
                                <![CDATA['. 'welcome zty 公众号'. date('Y-m-d H:i:s') .']]>
                            </Content>
                        </xml>';
                    echo $xml_response;

                    if($u){       //用户不存在
                        //echo '用户已存在';
                    }else{
                        $user_data = [
                            'openid'            => $openid,
                            'add_time'          => time(),
                            'nickname'          => $user_info['nickname'],
                            'sex'               => $user_info['sex'],
                            'headimgurl'        => $user_info['headimgurl'],
                            'subscribe_time'    => $sub_time,
                            'hei'               =>0
                        ];

                        $data=json_encode($user_data);

                        Redis::hSet('userinfo',$openid,$data);

                    }
                }
    }


    /**
     * 接收事件推送
     */
    public function validToken()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        //echo $_GET['echostr'];
        $data = file_get_contents("php://input");
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }

    /**
     * 获取微信AccessToken
     */
    public function getWXAccessToken()
    {

        //获取缓存
        $token = Redis::get($this->redis_weixin_access_token);
        if(!$token){        // 无缓存 请求微信接口
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WEIXIN_APPID').'&secret='.env('WEIXIN_APPSECRET');
            $data = json_decode(file_get_contents($url),true);

            //记录缓存
            $token = $data['access_token'];
            Redis::set($this->redis_weixin_access_token,$token);
            Redis::setTimeout($this->redis_weixin_access_token,3600);
        }
        return $token;

    }

    /**
     * 获取用户信息
     * @param $openid
     */
    public function getUserInfo($openid)
    {
        //$openid = 'oLreB1jAnJFzV_8AGWUZlfuaoQto';
        $access_token = $this->getWXAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

        $data = json_decode(file_get_contents($url),true);
        //echo '<pre>';print_r($data);echo '</pre>';
        return $data;
    }



    /** 测试 */
    public  function testsub(){
        $user_data = [
            'openid'            => 1,
            'add_time'          => time(),
            'nickname'          => 'aaa1',
            'sex'               => '男1',
            'hei'               =>0
        ];

        $data=json_encode($user_data);

            Redis::hSet('userinfo','openid1',$data);




        $data=Redis::hGet('userinfo','openid1');
      print_r(json_decode($data));exit;
        foreach ($data as $k=> $v){
           $userinfo[]=json_decode($v);
        }
        print_r($userinfo);
    }




}
