<?php
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use DemeterChain\C;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Grid;
use Encore\Admin\Form;

use App\Model\GoodsModel;

class GoodsController extends Controller{

    public function index(Content $content){
        return $content
            ->header('商品管理')
            ->description('商品列表')
            ->body($this->grid());
    }

    public function grid(){
        $grid=new Grid(new GoodsModel());

        $grid->goods_id('商品id');
        $grid->goods_name('商品名称');
        $grid->price('商品价格');
        $grid->store('库存');
       /* $grid->add_time('添加时间')->display(function($time){
           return date('Y-m-d H:i:s',$time);
        });*/
        $grid->created_at('添加时间');
        return $grid;

    }

    /** 商品添加 */
    public function create(Content $content){
       return  $content
           ->header('商品管理')
           ->description('添加')
           ->body($this->form());

    }

    public function store()
    {
       // echo '<pre>';print_r($_POST);echo '</pre>';
        $data=[
            'goods_name'=>$_POST['goods_name'],
            'price'=>$_POST['price']*100,
            'cat_id'=>$_POST['cat_id'],
            'store'=>$_POST['store'],

        ];
        GoodsModel::insert($data);
    }

    //删除
    public function destroy($id)
    {

        GoodsModel::where(['goods_id'=>$id])->delete();
        $response = [
            'status' => true,
            'message'   => 'ok'
        ];

        return $response;
    }


    protected function form(){
        $form=new Form(new GoodsModel());

        $form->display('goods_id','商品ID');
        $form->text('goods_name','商品名称');
        $form->currency('price', '价格')->symbol('¥');
        $form->number('cat_id','分类ID')->value('1');
        $form->number('store','库存');

        return $form;
    }

}