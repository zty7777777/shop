@extends('layouts.bst');

@section('content')

    <table class="table table-bordered">
        <h2>商品展示</h2>
        <div>
            <input type="text" id="info">
            <button id="search">搜索</button>
        </div>
        <tr>
            <th>id</th>
            <th>商品名称</th>
            <th>库存</th>
            <th>价格</th>
        </tr>
        @foreach($goodsInfo as $v)
        <tr>
            <td>{{$v['goods_id']}}</td>
            <td><a href="/goodsdetail/{{$v['goods_id']}}">{{$v['goods_name']}}</a></td>
            <td>{{$v['store']}}</td>
            <td>{{$v['price']/100}}</td>
        </tr>
        @endforeach
    </table>
@endsection
@section('footer')
    @parent
    <script src="{{URL::asset('/js/goods/goods.js')}}"></script>
@endsection