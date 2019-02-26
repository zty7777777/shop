@extends('layouts.bst')

@section('content')
<div class="container" >
    <h3>wechat支付:</h3>
    <hidden id="code_curl">{{$code_url}}</hidden>
    <div id="code"></div>

</div>
@endsection
<script src="{{URL::asset('/js/jquery-3.2.1.min.js')}}"></script>
<script src="{{URL::asset('/js/jquery.qrcode.min.js')}}"></script>
<script>
    var code_curl=$("#code_curl").val();
    $("#code").qrcode({
        render: "canvas", //table方式
        width: 200, //宽度
        height:200, //高度
        text: code_curl//任意内容
    });
</script>
