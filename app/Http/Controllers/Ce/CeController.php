<?php
namespace App\Http\Controllers\Ce;
use App\Model\Test;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class CeController extends Controller {
    public function ce(){
        $url='http://zty.52self.cn/';
        $client=new Client(['base_uri'=>$url,'timeout'=>2.0,]);
        $response=$client->request('GET','/index.php');
        echo $response->getBody();
    }

    public function ce2(){
        $url='http://zty.52self.cn/';
        $client=new Client(['base_uri'=>$url,'timeout'=>2.0,]);
        $response=$client->request('GET','/index.php');
        echo $response->getBody();
    }

    public function ce23(){
        $url='http://zty.52self.cn/';
        $client=new Client(['base_uri'=>$url,'timeout'=>2.0,]);
        $response=$client->request('GET','/index.php');
        echo $response->getBody();
    }
}
?>
