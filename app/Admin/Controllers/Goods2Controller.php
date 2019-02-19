<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Model\GoodsModel;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class Goods2Controller extends Controller
{
    use HasResourceActions;

    /** 商品首页 */
    public function index(Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('商品列表2')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('商品详情')
            ->body($this->detail($id));
    }

    /** 商品编辑*/
    public function edit($id, Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }
    /** 商品添加 */
    public function create(Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('商品添加')
            ->body($this->form());
    }

    /** 执行添加 */
    public function store()
    {
        $data=[
            'goods_name'=>$_POST['goods_name'],
            'price'=>$_POST['price'],
            'cat_id'=>$_POST['cat_id'],
            'store'=>$_POST['store'],

        ];
        GoodsModel::insert($data);
    }

    /** 执行修改 */
   /* public function update($id)
    {
        $data=[
            'goods_name'=>$_POST['goods_name'],
            'price'=>$_POST['price']*100,
            'cat_id'=>$_POST['cat_id'],
            'store'=>$_POST['store'],

        ];
    }*/

    /**  */
    protected function grid()
    {

        $grid = new Grid(new GoodsModel());


        $grid->goods_id('ID')->sortable();

        $grid->goods_name('商品名称');
        $grid->price('价格');
        $grid->store('库存');
        $grid->created_at('添加时间');
        $grid->expandFilter();

        $grid->filter(function ($filter) {

            $filter->like('goods_name');
        });

        $grid->paginate(5);
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(GoodsModel::findOrFail($id));

        $show->goods_id('ID');
        $show->goods_name('商品名称');
        $show->price('商品价格');
        $show->store('库存');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**    */
    protected function form()
    {
        $form = new Form(new GoodsModel());

        $form->display('id', 'ID');
        $form->text('goods_name', '商品名称');
        $form->currency('price', '商品价格')->symbol('¥');
        $form->number('cat_id','分类ID')->value('1');
        $form->number('store','库存');

        return $form;
    }
}
