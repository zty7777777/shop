<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    $router->resource('/goods',Goods2Controller::class);
    $router->resource('/users',UsersController::class);
    $router->resource('/wxuser',WeixinController::class);
    $router->resource('/wxmedia',WeixinMediaController::class); //微信素材管理

    //微信永久素材
    $router->resource('/wxpmmedia',WeixinPmMediaController::class);
    $router->post('/wxpmmedia','WeixinPmMediaController@formTest');

    //群发信息
    $router->get('/auth/sendall','WeixinPmMediaController@sendgroup');
    $router->post('/auth','WeixinPmMediaController@group_content');

    $router->get('/userchat', 'WeixinController@chatindex');   //客服私聊
    $router->post('/userchat', 'WeixinController@dochat');   //客服私聊
    $router->get('/userchat/getmsg', 'WeixinController@getChatMsg');   //获取消息记录



});
