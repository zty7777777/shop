<?php

namespace App\Admin\Controllers\Weixin;

use App\Model\WeixinUser;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Redis;

class WeixinController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('微信')
            ->description('用户列表')
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


        $data=Redis::hGetAll('userinfo');
        foreach ($data as $k =>$v){
            $data[]=json_decode($v,true);
        }

        $grid = new Grid(new WeixinUser);
        $grid->openid('Openid');
        $grid->add_time('Add time')->display(function ($time){
            return date('Y-m-d H:i:s',$time);
        });;
        $grid->nickname('Nickname');
        $grid->sex('Sex')->display(function ($sex){
            if($sex==1){
                return '男';
            }else{
                return '女';
            }


        });
        $grid->headimgurl('Headimgurl')->display(function ($lmg_url){
            return '<img src="'.$lmg_url.'">';
        });
        /*  $grid->openid('发送消息')->display(function ($openid){
              return "<a href='/userchat?openid=".$openid."'>fasong</a>";
          });*/
        $grid->subscribe_time('Subscribe time')->display(function ($time){
            return date('Y-m-d H:i:s',$time);
        });
        $grid->hei('状态')->display(function ($status){
           if($status==0){
               return '未拉黑';
           } else{
               return '已拉黑';
           }

        });

        return $grid;

    /*  $info=[
        'userinfo'=>$uesrinfo
      ];
//print_r($uesrinfo);exit;
        return view('weixin.user',$info);*/

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

        $form->text('openid', 'Openid');
        $form->number('add_time', 'Add time');
        $form->text('nickname', 'Nickname');
        $form->number('sex', 'Sex');
        $form->text('headimgurl', 'Headimgurl');
        $form->number('subscribe_time', 'Subscribe time');
        $form->number('hei', '状态');

        return $form;
    }


    public function hei(){
        $openid=$_GET['openid'];

     /*   $info=Redis::hGetAll('userinfo');
        foreach ($info as $k =>$v){
            $uesrinfo[]=json_decode($v,true);
            foreach ($uesrinfo as $k=>$v){
                if($v['hei']==0){
                    $v['hei']=1;
                }
            }
        }*/


        $data=Redis::hGet('userinfo',$openid);
        print_r(json_decode($data));exit;
        foreach ($data as $k=> $v){
            $userinfo[]=json_decode($v);
            $userinfo['hei']=1;
        }
        print_r($userinfo);

        $data=json_encode($uesrinfo);
        Redis::hSet('userinfo',$openid,$data);

    }
}
