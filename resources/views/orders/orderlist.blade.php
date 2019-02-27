@extends('layouts.bst')

@section('content')
<div class="container" >
    <h3>订单：</h3>
    <table class="table table-bordered">
        <tr>
            <td>订单ID</td>
            <td>订单号</td>
            <td>订单总金额</td>
            <td>下单时间</td>
            <td>支付状态</td>
            <td>操作</td>
        </tr>
        @foreach($list as $k=>$v)
        <tr>
            <td>{{$v['oid']}}</td>
            <td>{{$v['order_sn']}}</td>
            <td>¥{{$v['order_amount'] / 100}}</td>
            <td>{{date('Y-m-d H:i:s',$v['add_time'])}}</td>
            <td>
                    @if($v['is_pay']==0)
                        未支付
                    @elseif($v['is_pay']==1)
                        已支付
                    @endif

            </td>
            <td>
                @if($v['is_pay']==0){{--/pay/o/{{$v['oid']}}--}}
                    <a href="/pay/o/{{$v['oid']}}" class="btn btn-info btn-sm">支付宝支付</a>
                    <a href="/weixin/pay/index/{{$v['order_sn']}}" class="btn btn-info btn-sm">  微信支付</a>
                @elseif($v['is_pay']==1)
                    <a href="javascript:void(0)" class="btn btn-danger btn-sm">查看物流</a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection