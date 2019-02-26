@extends('layouts.bst')

@section('content')


  <input type="hidden" value="{{$code_url}}" id="code_url">
    <div id="code" align="center"></div>

@endsection
@section('footer')

<script src="{{URL::asset('/js/jquery-3.2.1.min.js')}}"></script>
<script src="{{URL::asset('/js/jquery.qrcode.min.js')}}"></script>
<script>
    $(function(){
        var code_url=$('#code_url').val()
        console.log(code_url)
        $("#code").qrcode({
            render: "canvas", //table方式
            width: 200, //宽度
            height:200, //高度
            text:code_url //任意内容
        });
    })

</script>

@endsection