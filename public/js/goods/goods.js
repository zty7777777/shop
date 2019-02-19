$("#add_cart_btn").click(function(e){
    e.preventDefault();
    var num = $("#goods_num").val();
    var goods_id = $("#goods_id").val();



   $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url     :   '/cartadd',
        type    :   'post',
        data    :   {goods_id:goods_id,num:num},
        dataType:   'json',
        success :   function(res){
            if(res.error==301){
                window.location.href=d.url;
            }else{
                alert(res.msg);
                if(res.error==0){
                   window.location.href="/cartlist";
                }
            }
        }
    });
});

//搜索
$("#search").click(function(e){
    e.preventDefault();
    var info=$('#info').val();
    console.log(info);

})