<?php
namespace App\Http\Controllers\Curl;

use App\Http\Controllers\Controller;

class CurlController extends Controller{

    // 加载公钥
    public $pub_key = './key/openssl_pub.key';
    public  function test()
    {
        $time=$_GET['time'];

        $method='AES-128-CBC';
        $key='pass';
        $salt='aaaaa';
        $iv=substr(md5($time.$salt),5,16);

        //接收加密数据
        $post_data=base64_decode($_POST['data']);

        //解密
        $de_str=openssl_decrypt($post_data,$method,$key,OPENSSL_RAW_DATA,$iv);
        //echo '<pre>';print_r($_POST);echo '</pre>';echo '<hr>';die;
        //echo $de_str;
        if(1){
            $now=time();
            $response=[
                'error'=>0,
                'msg'=>'ok',
                'data'=>'this is secret'
            ];
            $iv2=substr(md5($now.$salt),5,16);

            //加密传输
            $en_str=openssl_encrypt(json_encode($response),$method,$key,OPENSSL_RAW_DATA,$iv2);

            $arr=[
              't'=>$now,
              'data'=>base64_encode($en_str)
            ];
            echo json_encode($arr);
        }

    }
    /**
     *接收数据
     */
    public function  sign()
    {
        $sign=$_POST['sign'];

        //echo '<pre>';print_r($_POST);echo '</pre>';echo '<hr>';

        // 加载公钥 并转为openssl格式
        $pub_key = openssl_pkey_get_public(file_get_contents($this->pub_key));

        // 验签
        $verify=$this->verify($sign,$pub_key);
        //var_dump($verify); // int(1)表示验签成功
        if($verify==1){
            $post=$_POST['data'];

            $decrypted='';
            openssl_public_decrypt(base64_decode($post),$decrypted,$pub_key);
            var_dump($decrypted);
        }else{
            echo 'sign fail';
        }
    }

    //验签
    function verify($sign,$pub_key)
    {
        $data='hello word';
        // 摘要及签名的算法，同上面一致
        $method ='sha256';
        $algo = OPENSSL_ALGO_SHA1;

        // 生成摘要
        $digest = openssl_digest($data, $method);
        $verify = openssl_verify($digest, base64_decode($sign), $pub_key, $algo);
        return $verify;
    }

    /**
     * 移动app测试
     */
    public function app()
    {
        $data=[
            'info'=>'this is response'
          ];
        echo json_encode($data);
    }

}