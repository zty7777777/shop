@extends('layouts.bst')

@section('content')
<div class="container" >
    <h3>wechat支付:{{$code_url}}</h3>
    <input type="hidden" id="code_url" value="">
    <div id="code"></div>

</div>
@endsection
<script src="{{URL::asset('/js/jquery-3.2.1.min.js')}}"></script>
<script src="{{URL::asset('/js/jquery.qrcode.min.js')}}"></script>
<script>
    var code_url=$("#code_curl").val();

    $("#code").qrcode({
        render: "canvas", //table方式
        width: 200, //宽度
        height:200, //高度
        text: code_url//任意内容
    });
</script>
