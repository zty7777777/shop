@extends('layouts.bst')

@section('content')


  <input type="hidden" value="{{$code_url}}" id="code_url">
  <input type="hidden" value="{{$order_sn}}" id="order_sn">
    <div id="code" align="center"></div>

@endsection
@section('footer')

<script src="{{URL::asset('/js/jquery-3.2.1.min.js')}}"></script>
<script src="{{URL::asset('/js/jquery.qrcode.min.js')}}"></script>
<script>
    $(function(){
        var code_url=$('#code_url').val()
        //console.log(code_url)
        $("#code").qrcode({
            render: "canvas", //table方式
            width: 200, //宽度
            height:200, //高度
            text:code_url //任意内容
        });
    })

    setInterval(function (){
        var order_sn=$('#order_sn').val()
        //console.log(code_url)
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/weixin/pay/success/'+order_sn,
            type: 'get',
            dataType: 'json',
            success: function (d) {
               if(d.msg=='yes'){
                alert('支付成功');
                location.href="/orderlist"
               }
            }
        });
    },3000)

</script>

@endsection