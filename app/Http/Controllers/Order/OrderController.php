<?php

namespace App\Http\Controllers\Order;

use App\Model\CartModel;
use App\Model\GoodsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\OrderModel;

class OrderController extends Controller{

    /** 生成订单 */
    public function orderCreate($cart_id){
      /*  $where=[
            ['u_id'=>session()->get('uid')],
            ['id'=>$cart_id],
        ];
        print_r($where);exit;*/


        $uid=session()->get('uid');
        echo $uid;

        exit;
        $cart_id=explode(',',$cart_id);

        $cartInfo=CartModel::whereIn('id',$cart_id)->where('u_id',$uid)->get()->toArray();

        if(empty($cartInfo)){
            echo '您的购物车没有该商品 1不能结算';exit;
        }


        $order_amount=0;
        foreach($cartInfo as $k=>$v){
            $goods_info = GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
            $goods_info['num'] = $v['num'];
            $list[] = $goods_info;

            //计算订单价格 = 商品数量 * 单价
            $order_amount += $goods_info['price'] * $v['num'];
        }

        $order_sn = OrderModel::generateOrderSN();

        $data = [
            'order_sn'      => $order_sn,
            'u_id'           => $uid,
            'add_time'      => time(),
            'order_amount'  => $order_amount
        ];

        $oid = OrderModel::insertGetId($data);
        if(!$oid){
            echo '生成订单失败';
        }

        echo '下单成功,订单号：'.$oid .' 跳转支付';

        //清空购物车
        CartModel::whereIn('id',$cart_id)->where('u_id',$uid)->delete();
    }

    /** 订单列表 */
    public function orderList(){

        $orderInfo=OrderModel::where(['u_id'=>session()->get('uid')])
                               ->orderBy('oid','desc')->get()->toArray();
        $data = [
            'list'  => $orderInfo
        ];
        return view('orders.orderlist',$data);

    }

}