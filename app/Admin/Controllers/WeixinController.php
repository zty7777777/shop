<?php

namespace App\Admin\Controllers;

use App\Model\WeixinUser;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use GuzzleHttp;
use Illuminate\Support\Facades\Redis;

class WeixinController extends Controller
{
    use HasResourceActions;
    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WeixinUser);

        $grid->id('Id');
        $grid->uid('Uid');
        $grid->openid('Openid')->display(function ($openid){
            return "<a href='/admin/userchat/send?openid=".$openid."'>".$openid."</a>";
        });
        $grid->add_time('Add time');
        $grid->nickname('Nickname');
        $grid->sex('Sex');
        $grid->headimgurl('Headimgurl')->display(function ($lmg_url){
            return '<img src="'.$lmg_url.'">';
        });
      /*  $grid->openid('发送消息')->display(function ($openid){
            return "<a href='/userchat?openid=".$openid."'>fasong</a>";
        });*/
        $grid->subscribe_time('Subscribe time')->display(function ($time){
            return date('Y-m-d H:i:s',$time);
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WeixinUser::findOrFail($id));

        $show->id('Id');
        $show->uid('Uid');
        $show->openid('Openid');
        $show->add_time('Add time');
        $show->nickname('Nickname');
        $show->sex('Sex');
        $show->headimgurl('Headimgurl');
        $show->subscribe_time('Subscribe time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WeixinUser);

        $form->number('uid', 'Uid');
        $form->text('openid', 'Openid');
        $form->number('add_time', 'Add time');
        $form->text('nickname', 'Nickname');
        $form->number('sex', 'Sex');
        $form->text('headimgurl', 'Headimgurl');
        $form->number('subscribe_time', 'Subscribe time');

        return $form;
    }



    /**
     * 客服私聊
     */
    public function chatindex(Content $content)
    {
        $openid=$_GET['openid'];
        $data=WeixinUser::where('openid',$openid)->first();
        return $content
            ->header($data['nickname'])
            ->description("聊天")
            ->row("<img src='".$data['headimgurl']."' width='70px'>")
            ->body($this->chatview($data));
    }
    /**
     * 客服私聊视图
     */

    public function chatview($data){
        $form = new Form(new WeixinUser);
        $form->textarea('content','聊天内容');
       // $form->textarea('','聊天内容')->value($this->returnmsg($data['openid']));
        $form->hidden('openid')->value($data['openid']);
        return $form;
    }

    /**
     * 接收处理消息 dochat
     */
    public function dochat(Request $request){
        $msg=$request->input('content');
        $openid=$request->input('openid');
        //获取access_token
        $access_token=$this->getWXAccessToken();
        //拼接url
        $url="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$access_token";
        //请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $data=[
            'touser'=>$openid,
            "msgtype"=>"text",
            "text"=>["content"=>$msg],
        ];
        $res=$client->request('POST', $url, ['body' => json_encode($data,JSON_UNESCAPED_UNICODE)]);
        $res_arr=json_decode($res->getBody(),true);
        if($res_arr){
        }
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

    //接收用户信息
    public function returnmsg($openid){
        $return_data = file_get_contents("php://input");

       //var_dump($return_data);exit;

        //解析XML
        $xml = simplexml_load_string($return_data);        //将 xml字符串 转换成对象

        //事件类型$event = $xml->Event;
        //$openid = $xml->FromUserName;               //用户openid


        // 处理用户发送消息
                $msg = $xml->Content;
                $xml_response = '<xml>
                                    <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                    <FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName>
                                    <CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType>
                                    <Content><![CDATA['. $msg.  date('Y-m-d H:i:s') .']]></Content>
                                  </xml>';
                echo $xml_response;
    }


}
