<?php
namespace App\Http\Controllers\Cart;

use App\Http\Controllers\User\UserController;
use App\Model\CartModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\GoodsModel;

class IndexController extends  Controller{

    public $uid;
    public function __construct()
    {
        $this->middleware(function($request,$next){
            $this->uid=session()->get('u_id');
            return $next($request);
        });
        $this->middleware('auth');
    }


    /** 购物车展示 */
    public function cartlist(Request $request)
    {
        $u_id=session()->get('uid');
        //echo $u_id;exit;
          $cartInfo=CartModel::join('p_goods','p_goods.goods_id','=','p_cart.goods_id')
              ->where(['u_id'=>$u_id])
              ->get()->toArray();

          $data=[
              'cartInfo'=>$cartInfo
          ];
          return view('cart.cartlist',$data);
     /*  $goods=session()->get('cart_goods');
       //var_dump($goods);
       if(empty($goods)){
           echo '购物车为空';
       }else{
           foreach ($goods as $v=>$k){
               //echo $k."<br/>";
               $cartInfo=GoodsModel::where(['goods_id'=>$k])->first()->toArray();
               echo 'GOODS_ID：   ' .$cartInfo['goods_id']."----";
               echo 'GOODS_NAME： '.$cartInfo['goods_name']."<br/>";
               echo '<hr>';
           }
       }*/
    }

    /**  添加商品到购物车 test*/
    public function addtest($goods_id){
        $cart_goods = session()->get('cart_goods');
       //echo '<pre>';print_r($cart_goods);echo '</pre>';exit;

        //是否已在购物车中
        if(!empty($cart_goods)){
            if(in_array($goods_id,$cart_goods)){
                echo '已存在购物车中';
                exit;
            }
        }

        //商品添加到session
        session()->push('cart_goods',$goods_id);

        //减库存
        $where=[
            'goods_id'=>$goods_id
        ];
        $store = GoodsModel::where($where)->value('store');

        if($store<=0){
            echo '库存不足';
            exit;
        }
        $rs = GoodsModel::where(['goods_id'=>$goods_id])->decrement('store');
        if($rs){
            echo '添加成功';
        }
    }

    /** 添加商品到购物车*/
    public function add(Request $request){
       // echo session()->get('uid');exit;

        $goods_id = $request->input('goods_id');
        $num = $request->input('num');

        //检查库存
        $store_num = GoodsModel::where(['goods_id'=>$goods_id])->value('store');
        if($store_num<=0){
            $response = [
                'error' => 5001,
                'msg'   => '库存不足'
            ];
            return $response;
        }

        //检查购物车重复商品
        $cart_goods = CartModel::where(['u_id'=>session()->get('uid')])->get()->toArray();
        if($cart_goods){
            $goods_id_arr = array_column($cart_goods,'goods_id');

            if(in_array($goods_id,$goods_id_arr)){
                $response = [
                    'error' => 5002,
                    'msg'   => '商品已在购物车中，请勿重复添加'
                ];
                return $response;
            }
        }

        //写入购物车表
        $data = [
            'goods_id'  => $goods_id,
            'num'       => $num,
            'add_time'  => time(),
            'u_id'       => session()->get('uid'),
            'session_token' => session()->get('u_token')
        ];

        $cid = CartModel::insertGetId($data);
        if(!$cid){
            $response = [
                'error' => 5002,
                'msg'   => '添加购物车失败，请重试',
            ];
            return $response;
        }


        $response = [
            'error' => 0,
            'msg'   => '添加成功',
        ];
        return $response;
    }


    /**
     * 删除商品
     */
    public function del(Request $request)
    {
        $cart_id=$request->input('cart_id');
        $where=[
            //'u_id'=>session()->get('uid'),
            'id'=>$cart_id
        ];

        $rs = CartModel::where($where)->delete();
        $response = [
            'error' => 5001,
            'msg'   => '商品ID:  '.$cart_id . ' 删除成功1'
        ];
        return $response;


    }
}