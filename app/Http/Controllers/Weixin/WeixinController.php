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

class WeixinController extends Controller
{

    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token
    /**
     * 首次接入
     */
    public function validToken1()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        //echo $_GET['echostr'];
        echo 'Token: '. $this->getWXAccessToken();
    }

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


        // 处理用户发送消息
        if(isset($xml->MsgType)){
            if($xml->MsgType=='text'){            //用户发送文本消息
                $msg = $xml->Content;
                $xml_response = '<xml>
                                    <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                    <FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName>
                                    <CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType>
                                    <Content><![CDATA['. $msg. date('Y-m-d H:i:s') .']]></Content>
                                  </xml>';
               // echo $xml_response;

                //保存聊天记录到数据库

                $msgData=[
                    'openid'=>$openid,
                    'msg'=>$msg,
                    'addtime'=>time()
                ];

                WeixinMsg::insertGetId($msgData);
            }elseif($xml->MsgType=='image'){       //用户发送图片信息
                //视业务需求是否需要下载保存图片
                if(1){  //下载图片素材
                    $file_name =$this->dlWxImg($xml->MediaId);
                    $xml_response = '<xml>
                                           <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                           <FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName>
                                           <CreateTime>'.time().'</CreateTime>
                                           <MsgType><![CDATA[text]]></MsgType>
                                           <Content><![CDATA['.'发送成功'. date('Y-m-d H:i:s') .']]></Content>
                                      </xml>';

                    echo $xml_response;

                    //写入数据库
                    $data = [
                        'openid'    => $openid,
                        'add_time'  => time(),
                        'msg_type'  => 'image',
                        'media_id'  => $xml->MediaId,
                        'format'    => $xml->Format,
                        'msg_id'    => $xml->MsgId,
                        'local_file_name'   => $file_name
                    ];

                    $m_id = WeixinMedia::insertGetId($data);
                    var_dump($m_id);
                }
            }elseif($xml->MsgType=='voice'){        //处理语音信息
                $file_name=$this->dlVoice($xml->MediaId);
                //写入数据库
                $data = [
                    'openid'    => $openid,
                    'add_time'  => time(),
                    'msg_type'  => 'voice',
                    'media_id'  => $xml->MediaId,
                    'format'    => $xml->Format,
                    'msg_id'    => $xml->MsgId,
                    'local_file_name'   => $file_name
                ];

                $m_id = WeixinMedia::insertGetId($data);
            }elseif($xml->MsgType=='video'){        //处理视频
                $file_name=$this->dlVideo($xml->MediaId);
                $data = [
                    'openid'    => $openid,
                    'add_time'  => time(),
                    'msg_type'  => 'video',
                    'media_id'  => $xml->MediaId,
                    'format'    => $xml->Format,
                    'msg_id'    => $xml->MsgId,
                    'local_file_name'   => $file_name
                ];

                $m_id = WeixinMedia::insertGetId($data);
            }elseif($xml->MsgType=='event'){        //判断事件类型

                if($event=='subscribe'){                        //扫码关注事件
                    $sub_time = $xml->CreateTime;               //扫码关注时间
                    //获取用户信息
                    $user_info = $this->getUserInfo($openid);

                    //保存用户信息
                    $u = WeixinUser::where(['openid'=>$openid])->first();

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
                        ];

                        $id = WeixinUser::insertGetId($user_data);      //保存用户信息
                        //var_dump($id);
                    }
                }elseif($event=='CLICK'){               //click 菜单
                    if($xml->EventKey=='huamulan'){       // 根据 EventKey判断菜单
                        $this->huamulan($openid,$xml->ToUserName);
                    }
                }

            }

        }
    }

    /**
     * 客服处理
     * @param $openid   用户openid
     * @param $from     开发者公众号id 非 APPID
     */
    public function huamulan($openid,$from)
    {
        // 文本消息
        $xml_response = '<xml>
                            <ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$from.']]></FromUserName>
                            <CreateTime>'.time().'</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content>
                                <![CDATA['. '我木兰贼秀鸭, 现在时间'. date('Y-m-d H:i:s') .']]>
                            </Content>
                        </xml>';
        echo $xml_response;
    }

    /**
     * 下载图片素材
     * @param $media_id
     */
    public function dlWxImg($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;
        //echo $url;echo '</br>';

        //保存图片
        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //$h = $response->getHeaders();

        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_image_path = 'wx/images/'.$file_name;
        //保存图片
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功

        }else{      //保存失败

        }

        return $file_name;
    }

    /**
     * 下载语音文件
     * @param $media_id
     */
    public function dlVoice($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;

        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //$h = $response->getHeaders();
        //echo '<pre>';print_r($h);echo '</pre>';die;
        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_image_path = 'wx/voice/'.$file_name;
        //保存语音
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功

        }else{      //保存失败

        }
        return $file_name;
    }

    /**
     * 处理视频信息
     * @param $media_id
     */
    public  function dlVideo($media_id){
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;

        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //$h = $response->getHeaders();
        //echo '<pre>';print_r($h);echo '</pre>';die;
        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_image_path = 'wx/video/'.$file_name;
        //保存语音
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功

        }else{      //保存失败

        }
        return $file_name;
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

    /**
     * 创建服务号菜单
     */
    public function createMenu()
    {
        // 1 获取access_token 拼接请求接口
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->getWXAccessToken();
        //echo $url;echo '</br>';

        //2 请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);

        $data = [
            "button"    => [
                [
                    "name" =>'仙',
                    "sub_button"  =>[
                        [
                            "type"  => "view",
                            "name"  => "项目",
                            "url"   => "https://zty.tactshan.com"
                        ],
                        [
                            "type"  => "view",
                            "name"  => "2",
                            "url"   => "https://www.baidu.com"
                        ]
                    ]
                ],
                [
                    "type"  => "click",       // click类型
                    "name"  => "木兰首秀",
                    "key"   => "huamulan"
                ],
                [
                    "type"  => "view",      // view类型 跳转指定 URL
                    "name"  => "京东",
                    "url"   => "https://www.jd.com"
                ]
            ]
        ];


        $r = $client->request('POST', $url, [
            'body' => json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);

        // 3 解析微信接口返回信息

        $response_arr = json_decode($r->getBody(),true);
        //echo '<pre>';print_r($response_arr);echo '</  >';

        if($response_arr['errcode'] == 0){
            echo "菜单创建成功";
        }else{
            echo "菜单创建失败，请重试";echo '</br>';
            echo $response_arr['errmsg'];

        }


    }

    /**
     * 微信扫码登录
     */
    public function  login(){
     return view('weixin.login');
    }

    /**
     * 接收code
     */
    public function getCode()
    {
        //用code换取access_token 请求接口
        //echo '<pre>';print_r($_GET);echo '</pre>';
        $code = $_GET['code'];
        //echo 'code: '.$code;

        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxe24f70961302b5a5&secret=0f121743ff20a3a454e4a12aeecef4be&code='.$code.'&grant_type=authorization_code';

        $token_json=file_get_contents($token_url);
        $token_arr = json_decode($token_json,true);
        /*echo '<hr>';
        echo '<pre>';print_r($token_arr);echo '</pre>';*/
        //取出access_token
        $access_token=$token_arr['access_token'];
        $openid=$token_arr['openid'];

        // 3 携带token  获取用户信息
        $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_json = file_get_contents($user_info_url);

        $user_arr = json_decode($user_json,true);
        /*echo '<hr>';
        echo '<pre>';print_r($user_arr);echo '</pre>';*/

        //保存用户信息到数据库
        $this->insersDb( $user_arr);

    }

    /** 保存用户信息到数据库 */
    public function insersDb($user_arr){
        //根据unionid查询p_wx_users表里是否有该用户信息
        $res=WeixinUser::where(['unionid'=>$user_arr['unionid']])->first();
        if($res){
            $uid=WeixinUser::where(['unionid'=>$user_arr['unionid']])->value('uid');
            echo '登录成功>>>已有此用户';
        }else{
            //存入users主表
            $data=[
                'name'=>$user_arr['nickname'],
                'email'=>'',
                'password'=>'',
                'remember_token'=>''
               ];
            $uid=UsersModel::insertGetId($data);

            $wx_data=[
                'uid'=>$uid,
                'openid'=>$user_arr['openid'],
                'nickname'=>$user_arr['nickname'],
                'sex'=>$user_arr['sex'],
                'unionid'=>$user_arr['unionid'],
                'add_time'=>time(),
                'headimgurl'=>$user_arr['headimgurl'],
            ];

            $rs=WeixinUser::insert($wx_data);
            if($rs){
                echo '登录成功>>没有此用户';
            }

            //header('refresh:1;');
        }
      $token = substr(md5(time() . mt_rand(1, 99999)), 10, 10);
        setcookie('uid',$uid , time() + 86400, '/', 'shop.com', false, true);
        setcookie('token', $token, time() + 86400, '/user', '', false, true);

        session()->put('uid', $uid);
        session()->put('u_token', $token);


    }


    /** 测试 */
    public  function testsub(){
        $str="abc.jpgas";
        //echo substr($str,strpos($str,'.')+1);

        return redirect('/goodslist');//页面跳转。重定向
    }


}
