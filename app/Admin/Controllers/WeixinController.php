<?php

namespace App\Admin\Controllers;

use App\Model\WeixinMsg;
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
            return "<a href='/admin/userchat?openid=".$openid."'>".$openid."</a>";
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
            ->body(view('/weixin/chat',['openid'=>$data['openid']]));//($this->chatview($data));
    }
    /**
     * 客服私聊视图
     */

    public function chatview($data){
        $form = new Form(new WeixinUser);

        //$form->setView('/weixin/chat',['openid'=>$data['openid']]);


        $form->text('content','输入聊天内容')->placeholder('请输入');
        $form->hidden('openid')->value($data['openid']);
        return $form;
        return view('/weixin/chat',['openid'=>$data['openid']]);
    }

    /**
     * 接收处理消息 dochat
     */
    public function dochat(Request $request){
        $openid=$request->input('openid');
        $msg=$request->input('msg');

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
        if ($res_arr['errcode'] == 0) {
            return "发送成功";
        } else {
            echo "发送失败";
            echo '</br>';
            echo $res_arr['errmsg'];

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

    /**
     *
     * 获取消息
     */
    public function getChatMsg()
    {
        $openid = $_GET['openid'];  //用户openid
        $pos = $_GET['pos'];        //上次聊天位置

        $msg = WeixinMsg::where(['openid'=>$openid])->where('id','>',$pos)->first();

        //$msg = WeixinChatModel::where(['openid'=>$openid])->where('id','>',$pos)->get();
        if($msg){
            $response = [
                'errno' => 0,
                'data'  => $msg->toArray()
            ];

        }else{
            $response = [
                'errno' => 50001,
                'msg'   => '服务器异常4，请联系管理员'
            ];
        }

        die( json_encode($response));

    }


}
