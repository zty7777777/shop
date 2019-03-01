<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //echo date('Y-m-d H:i:s');
      return view('welcome');
});

Route::get('/adduser','User\UserController@add');

//路由跳转
Route::redirect('/hello1','/world1',301);
Route::get('/world1','Test\TestController@world1');

Route::get('hello2','Test\TestController@hello2');
Route::get('world2','Test\TestController@world2');


//路由参数
Route::get('/user/{uid}','User\UserController@user');
Route::get('/month/{m}/date/{d}','Test\TestController@md');
Route::get('/name/{str?}','Test\TestController@showName');



// View视图路由
Route::view('/mvc','mvc');
Route::view('/error','error',['code'=>403]);


//模板引入静态文件
Route::get('/mvc/test1','Mvc\MvcController@test1');
Route::get('/mvc/bst','Mvc\MvcController@bst');


// Query Builder
Route::get('/query/get','Test\TestController@query1');
Route::get('/query/where','Test\TestController@query2');


Route::get('/view/test2','Test\TestController@viewTest2');

//用户注册
Route::get('/userreg','User\UserController@reg');
Route::post('/userreg','User\UserController@doreg');

//用户登录
Route::get('/userlogin','User\UserController@login');
Route::post('/userlogin','User\UserController@checklogin');
Route::get('/usercenter','User\UserController@center')->middleware('check.login.token');
Route::get('/updatepwd','User\UserController@pwdview')->middleware('check.login.token');


//商品
Route::get('/goodslist','Goods\GoodsController@goodslist');
Route::get('/goodsdetail/{goods_id?}','Goods\GoodsController@goodsdetail');
Route::get('/uploadindex','Goods\GoodsController@uploadindex');
Route::post('/goods/upload/do','Goods\GoodsController@upload');


//购物车
Route::get('/cartlist','Cart\IndexController@cartlist');
Route::get('/cartaddtest/{goods_id?}','Cart\IndexController@addtest')->middleware('check.login.token');//购物车添加
Route::post('/cartadd','Cart\IndexController@add');    //购物车添加
Route::post('/cartdel','Cart\IndexController@del');    //删除


//订单
Route::get('/ordercreate/{cart_id?}','Order\OrderController@ordercreate')->middleware('check.login.token');
Route::get('/orderlist','Order\OrderController@orderlist')->middleware('check.login.token');


//支付
Route::get('/pay/alipay/test','Pay\AlipayController@test')->middleware('check.login.token');  //测试 订单支付
Route::get('/pay/o/{oid}','Pay\AlipayController@orderPay')->middleware('check.login.token');   //订单支付
Route::get('/pay/alipay/return','Pay\AlipayController@aliReturn');       //支付宝支付 同步通知回调
Route::post('/pay/alipay/notify','Pay\AlipayController@aliNotify');      //支付宝支付 异步通知回调


//微信

//微信公众号
Route::get('/weixin/valid','Weixin\WeixinController@validToken');
Route::get('/weixin/valid1','Weixin\WeixinController@validToken1');   //access_token
Route::post('/weixin/valid1','Weixin\WeixinController@wxEvent');        //接收微信服务器事件推送
Route::post('/weixin/valid','Weixin\WeixinController@validToken');

Route::get('/weixin/create_menu','Weixin\WeixinController@createMenu');     //创建菜单

//微信支付
Route::get('/weixin/pay/index/{oid}','Weixin\PayController@index');
Route::post('/weixin/pay/notice','Weixin\PayController@notice');     //微信支付通知回调
Route::get('/weixin/pay/success/{order_sn}','Weixin\PayController@success');     //微信支付通知回调

//微信扫码登录
Route::get('/weixin/login','Weixin\WeixinController@login');
Route::get('/weixin/getcode','Weixin\WeixinController@getCode');       //接收code

//微信JSSDK
Route::get('/weixin/jssdk','Weixin\WeixinController@jssdk');






//测试
Route::get('/test/check_cookie','Test\TestController@checkCookie')->middleware('check.cookie');
Route::get('/Order','Ce\CeController@ce');
Route::get('/weixin/testsub','Weixin\WeixinController@testsub');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


