
//删除购物车商品
$(".cartDel").click(function(e){
    e.preventDefault();
    var cart_id=$(this).parents('tr').children().first().text();


    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url     :   '/cartdel',
        type    :   'post',
        data    :   {cart_id:cart_id},
        dataType:   'json',
        success :   function(res){
           if(res.error==301){
                window.location.href=res.url;
            }else{
                alert(res.msg);
               window.location.href="/cartlist";
            }
        }
    });
})

//结算
$("#settlement").click(function (e) {
    e.preventDefault();
    var cart_id='';
    $('.box').each(function(index){
        if($(this).prop('checked')==true){
            cart_id+=','+$(this).attr('cart_id');
        }
    })

    if(cart_id==''){
        alert('请选择要结算的商品');
        return false;
    }
    cart_id=cart_id.substr(1);
    window.location.href="/ordercreate/"+cart_id;
})