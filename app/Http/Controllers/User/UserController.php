<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\UserModel;

class UserController extends Controller
{


    /** 注册页面 */
    public function reg()
    {
        return view('user.reg');
    }

    /** 注册 */
    public function doReg(Request $request)
    {
        //echo __METHOD__;
        //echo '<pre>';print_r($_POST);echo '</pre>';

        //验证用户名
        $name = $request->input('u_name');
        $checkName = UserModel::where(['name' => $name])->first();
        if (!empty($checkName)) {
            header('refresh:2;/userreg');
            exit('账号已存在');
        }


        //验证密码
        $pwd = $request->input('u_pwd');
        $pwd2 = $request->input('u_pwd2');

        if ($pwd != $pwd2) {
            echo '确认密码与密码不一致';
            exit;
        }

        $pwd = password_hash($pwd, PASSWORD_BCRYPT);

        $data = [
            'name' => $name,
            'email' => $request->input('u_email'),
            'pwd' => $pwd,
            'reg_time' => time(),
        ];

        $uid = UserModel::insertGetId($data);
        var_dump($uid);

        if ($uid) {
            echo '注册成功';
        } else {
            echo '注册失败';
        }
    }

    /** 登录页面 */
    public function login()
    {
        return view('user.login');
    }

    /** 检查登录 */
    public function checklogin(Request $request)
    {
        $email = $request->input('u_email');
        $use_pwd = $request->input('u_pwd');

        $u = UserModel::where(['email' => $email])->first();
        if (empty($u)) {
            echo '账号不存在';
            exit;
        }

        if (password_verify($use_pwd, $u->pwd) == false) {
            echo '账号或密码错误';
            header('refresh:2;/userlogin');
            exit;
        } else {
            $token = substr(md5(time() . mt_rand(1, 99999)), 10, 10);
            setcookie('uid', $u->id, time() + 86400, '/', 'shop.com', false, true);
            setcookie('token', $token, time() + 86400, '/user', '', false, true);

            $request->session()->put('uid', $u->id);
            $request->session()->put('u_token', $token);
            echo '登录成功';
            header('refresh:2;/usercenter');
        }


    }

    /** 用户展示 */
    public function center()
    {
        $info = UserModel::get();
        foreach ($info as $k => $v) {
            $info[$k]['reg_time'] = date('Y-m-d H:i:s', $v['reg_time']);
        }
        $data = [
            'info' => $info
        ];
        return view('user.center', $data);
    }


}