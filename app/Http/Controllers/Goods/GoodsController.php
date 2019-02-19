<?php
namespace App\Http\Controllers\Goods;

use App\Model\CartModel;
use DemeterChain\C;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\GoodsModel;

class GoodsController extends  Controller{
    public function goodslist(){
        $goodsInfo=GoodsModel::get()->toArray();
        $data=[
            'goodsInfo'=>$goodsInfo
        ];
        return view('goods.goodslist',$data);
    }

    public function goodsdetail($goods_id){
        $goods = GoodsModel::where(['goods_id'=>$goods_id])->first();

        //商品不存在
        if(!$goods){
            header('Refresh:2;url=/goodslist');
            echo '商品不存在,正在跳转至首页';
            exit;
        }
        //print_r($goods->toArray());exit;

        $data = [
            'goods' => $goods
        ];
        return view('goods.goodsdetail',$data);

    }

    /** 文件上传 */
    public function upload(Request $request){
       $file=$request->file('goods_file');
       $ext=$file->extension();
       //var_dump($ext);
        if($ext != 'doc'){
            echo '请上传doc文件';exit;
        }
       $res=$file->storeAs(date('Ymd'),str_random(5).'.doc');
       if($res){
           echo '上传成功';
       }
    }

    public function uploadindex(){
        return view('goods.upload');
    }
}