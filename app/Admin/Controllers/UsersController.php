<?php
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Grid;
use Encore\Admin\Form;

use App\Model\UserModel;

class UsersController extends Controller{
    public function index(Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('商品列表')
            ->body($this->grid());
    }

    /** 用户展示 */
    protected function grid()
    {
        $grid = new Grid(new UserModel());

        $grid->id('ID');
        $grid->name('昵称');
        //$grid->age('年龄');
        $grid->email('邮箱');
        $grid->reg_time('注册时间')->display(function($time){
            return date('Y-m-d H:i:s',$time);
        });

        return $grid;
    }


    public function edit($id)
    {
       // echo __METHOD__;
        $form=new Form(new UserModel());

        $form->text('nick_name', '昵称');
        $form->resource();

        return $form;
    }
}