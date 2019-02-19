@extends('layouts.bst')

@section('content')

    <table  class="table table-bordered">
        <thead>
        <tr>
            <td>选择</td>
            <td>id</td>
            <td>商品名称</td>
            <td>购买数量</td>
            <td>添加时间</td>
            <td>操作</td>
        </tr>
        </thead>
        <tbody >
        @foreach($cartInfo as $v)
            <tr>
                <td><input type="checkbox" class="box" cart_id="{{$v['id']}}"></td>
                <td>{{$v['id']}}</td>
                <td>{{$v['goods_name']}}</td>
                <td>{{$v['num']}}</td>
                <td>{{date('Y-m-d H:i:s',$v['add_time'])}}</td>
                <td><a href="" class="cartDel">删除</a></td>
            </tr>
        @endforeach

        <tr>
         {{--   <td><input type="checkbox">全选</td>--}}
            <td colspan="6" align="right">
                <button class="btn btn-danger " id="settlement">去 结 算</button>
            </td>
        </tr>
        </tbody>
    </table>

@endsection

@section('footer')
    @parent
    <script src="{{URL::asset('/js/cart/cart.js')}}"></script>
@endsection