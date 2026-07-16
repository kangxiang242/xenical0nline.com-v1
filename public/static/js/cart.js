var max_cart_num = 99;
var min_cart_num = 1;
var cookie_key = 'carts';
var added_cookie_key = 'added_carts';
var is_show_cart = 0;
$('body').on('click',".cart-action[data-ac='increase']",function(){

    var input = $(this).attr('data-input');
    var origin_val = parseInt($(input).val());

    if(origin_val+1 == max_cart_num){
        $(this).addClass('disable');
    }
    if(origin_val+1 > max_cart_num){
        return false;
    }

    $(input).val(origin_val+1);

    $(this).siblings(".cart-action[data-ac='reduce']").removeClass('disable');

    var callback = $(this).attr('data-callback');

    if(callback){
        if(callback == 'increaseCallCart'){
            increaseCallCart($(this));
        }else if(callback == 'increaseCallAddedCart'){
            increaseCallAddedCart($(this));
        }

    }

});



$('body').on('click',".cart-action[data-ac='reduce']",function(){

    var input = $(this).attr('data-input');
    var origin_val = parseInt($(input).val());

    if(origin_val-1 == min_cart_num){

        $(this).addClass('disable');
    }

    if(origin_val-1 < min_cart_num){
        return false;
    }

    $(input).val(origin_val-1);

    $(this).siblings(".cart-action[data-ac='increase']").removeClass('disable');

    var callback = $(this).attr('data-callback');
    if(callback){
        if(callback == 'increaseCallCart'){
            increaseCallCart($(this));
        }else if(callback == 'increaseCallAddedCart'){
            increaseCallAddedCart($(this));
        }

    }

});

function increaseCallCart(obj){
    var id = obj.attr('data-id');
    var input = obj.attr('data-input');
    var val = parseInt($(input).val());
    fixedNumCart(id,val);
    censusCart();
}

function increaseCallAddedCart(obj){
    var id = obj.attr('data-id');
    var pid = obj.attr('data-pid');
    var input = obj.attr('data-input');
    var val = parseInt($(input).val());
    fixedNumAddedCart(id,pid,val);
    censusCart();
}


$('body').on('click',".to-cart",function(){
    var num = 1;
    var id = $(this).attr('data-id');
    if(id <= 0){
        return false;
    }
    if($(this).attr('data-num')){
        var adc = $(this).attr('data-num');
        if(adc > 0){
            num = adc;
        }else{
            var v = $(adc).val();
            if(v > 0){
                num = v;
            }
        }
    }
    var added = [];
    $('.added-item').each(function(){
        var checkbox = $(this).find("input[type='checkbox']");
        if(checkbox.is(':checked')){
            var id = checkbox.val();
            var pid = checkbox.attr('data-pid');
            var num = $(this).find('.added-num').val();
            var tmp_added = {id:id,num:num,pid:pid};
            added.push(tmp_added);

        }
    });
    addCart(id,num,added);
    //showCartMain();


    if(typeof(toCartCallback) && typeof(toCartCallback)=='function'){
        toCartCallback(id,num,$(this));
    }
    bottomTips("加入購物車成功");
});


function addCart(id,num,added){
    if (id <= 0){
        return false
    }

    num = num < 1?1:num;

    added = added?added:[];

    var new_cart = [];
    var origin_cart = $.cookie(cookie_key)
    if(origin_cart){
        origin_cart = JSON.parse(origin_cart)

        for(var p in origin_cart){
            if(origin_cart[p].id != id){
                new_cart.push(origin_cart[p])
                continue;
            }
            num = parseInt(origin_cart[p].num) + parseInt(num);
        }

        var wait = {
            id:id,
            num:num,
        };

    }else{
        var wait = {
            id:id,
            num:num,
        };

    }
    new_cart.push(wait);
    $.cookie(cookie_key, JSON.stringify(new_cart), { expires: 7,path: '/' });
    addedCart(added);
}

function addedCart(added){
    if(!added){
        return false;
    }
    var added_origin_cart = $.cookie(added_cookie_key);
    if(added_origin_cart){
        added_origin_cart = JSON.parse(added_origin_cart);
    }

    var new_added_cart = [];

    if(added_origin_cart && added_origin_cart.length > 0){

        for(var p in added_origin_cart){
            var wait = [];
            var is_exist = true;
            for (var x in added){
                //存在相同
                if(added_origin_cart[p].pid == added[x].pid && added_origin_cart[p].id == added[x].id){

                    added_origin_cart[p].num = parseInt(added_origin_cart[p].num) + parseInt(added[x].num);
                    new_added_cart.push(added_origin_cart[p]);
                    delete added_origin_cart[p];
                    delete added[x];
                    break;

                }else{
                    is_exist = false;
                }

            }


            if(!is_exist){


                if(added_origin_cart[p]){
                    new_added_cart.push(added_origin_cart[p]);
                }

            }

        }

        if(added.length > 0){
            for (var x in added){
                new_added_cart.push(added[x]);
            }
        }


    }else{

        new_added_cart = added;
    }
    if(new_added_cart && new_added_cart.length>0){
        $.cookie(added_cookie_key, JSON.stringify(new_added_cart), { expires: 7,path: '/' });
    }
}

function deleteCart(id){
    if(!id){
        return false;
    }
    var origin_cart = $.cookie(cookie_key)
    if(origin_cart){
        origin_cart = JSON.parse(origin_cart);

        var added_origin_cart = $.cookie(added_cookie_key);
        if(added_origin_cart){
            added_origin_cart = JSON.parse(added_origin_cart);
        }

        for(var p in origin_cart){
            if(origin_cart[p].id == id){

                if (added_origin_cart ){

                    for (var x in added_origin_cart){
                        if(origin_cart[p].id == added_origin_cart[x].pid){
                            delete added_origin_cart[x];
                        }
                    }
                }
                delete origin_cart[p];
                break;
            }

        }

        var new_cart = [];
        for(var p in origin_cart){
            if(origin_cart[p]){
                new_cart.push(origin_cart[p])
            }
        }


        $.cookie(cookie_key, JSON.stringify(new_cart), { expires: 7,path: '/' });


        var new_added_cart = [];
        for(var p in added_origin_cart){
            if(added_origin_cart[p]){
                new_added_cart.push(added_origin_cart[p])
            }
        }
        $.cookie(added_cookie_key, JSON.stringify(new_added_cart), { expires: 7,path: '/' });

    }
}

function deleteAddedCart(id,pid){
    if(!id || !pid){
        return false;
    }
    var added_origin_cart = $.cookie(added_cookie_key);
    if(added_origin_cart){
        added_origin_cart = JSON.parse(added_origin_cart);

        if(added_origin_cart){
            for (var x in added_origin_cart){
                if(added_origin_cart[x].pid == pid && added_origin_cart[x].id == id){

                    delete added_origin_cart[x];
                    break;
                }
            }

            var new_added_cart = [];
            for(var p in added_origin_cart){
                if(added_origin_cart[p]){
                    new_added_cart.push(added_origin_cart[p])
                }
            }
            $.cookie(added_cookie_key, JSON.stringify(new_added_cart), { expires: 7,path: '/' });

        }
    }

}

/**
 * 固定数量修改购物车，用于购物车内加减
 * @param id
 * @param num
 */
function fixedNumCart(id,num){

    if(id && num>0){
        var origin_cart = $.cookie(cookie_key)
        if(origin_cart) {
            origin_cart = JSON.parse(origin_cart);
            if(origin_cart){
                for(var p in origin_cart){
                    if(origin_cart[p].id == id){
                        origin_cart[p].num = parseInt(num);
                        break;
                    }

                }
                $.cookie(cookie_key, JSON.stringify(origin_cart), { expires: 7,path: '/' });
            }

        }

    }

}

/**
 * 固定数量修改购物车，用于购物车内加减
 * @param id
 * @param pid
 * @param num
 */
function fixedNumAddedCart(id,pid,num){

    if(id && pid && num>0){
        var added_origin_cart = $.cookie(added_cookie_key);
        if(added_origin_cart){
            added_origin_cart = JSON.parse(added_origin_cart);
            if(added_origin_cart){
                for (var x in added_origin_cart){
                    if(added_origin_cart[x].pid == pid && added_origin_cart[x].id == id){
                        added_origin_cart[x].num = parseInt(num);
                        break;
                    }
                }
                $.cookie(added_cookie_key, JSON.stringify(added_origin_cart), { expires: 7,path: '/' });
            }
        }

    }

}

function getCartCount(){
    var num = 0;

    var origin_cart = $.cookie(cookie_key)

    var added_origin_cart = $.cookie(added_cookie_key);
    if(origin_cart){
        origin_cart = JSON.parse(origin_cart);
        for(var p in origin_cart){
            num += parseInt(origin_cart[p].num);
        }
    }

    if(added_origin_cart){
        added_origin_cart = JSON.parse(added_origin_cart);
        for(var p in added_origin_cart){
            num += parseInt(added_origin_cart[p].num);
        }
    }

    return num?num:0;
}

function headCartCount(){
    var num = getCartCount();

    $('#header-cart-count').text(num);
    if(num>0){
        $('#header-cart-count').css('opacity',1);
    }else{
        $('#header-cart-count').css('opacity',0);
    }
    $('#cart-total-count').text(num);
}

/**
 * 统计购物车内数据
 */
function censusCart(){
    var total_count = 0

    var total_price = 0;
    $('.cart-product .item').each(function(){
        var quantity_num = parseInt($(this).find('.quantity input').val());
        var quantity_price = parseInt($(this).find('.quantity input').attr('data-price'));
        total_count += quantity_num;
        total_price += quantity_price*quantity_num;

        var added = $(this).find('.added')
        if(added){
            var added_count = 0;
            added.find('.added-product .added-item').each(function(){
                var added_quantity_num = parseInt($(this).find('.quantity input').val());
                var added_quantity_price = parseInt($(this).find('.quantity input').attr('data-price'));
                added_count += added_quantity_num;
                total_price += added_quantity_price*added_quantity_num;

            });
            added.find('#added-total-count').text("("+added_count+")");
        }
    })

    $('#total-price').text(total_price);
    if(total_price>=free_shipping_where){
        $("#freight-price").text(0);
    }else{
        $("#freight-price").text(free_shipping_freight);
    }
    headCartCount()
}

function showCartMain(){
    is_show_cart = 1;
    $('#cart-main').removeClass('cart-close')
    cartLoading("#cart-main");
    showShade();
    $.ajax({
        type: "GET",
        url: "/get/cart",
        data: {},
        dataType: "html",
        success: function(data){
            $('#cart-main').html(data);
            censusCart();
        }
    });
}

function hideCartMain(){
    $('#cart-main').addClass('cart-close')
}

function cartLoading(elm){
    var tmp = '<div class="dcat-loading" style="background:transparent;z-index:999991014;text-align: center;position: absolute;left: 50%;top: 50%;transform: translate(-50%,-50%);"><svg xmlns="http://www.w3.org/2000/svg" class="mx-auto block" style="width:58px;{svg_style}" viewBox="0 0 120 30" fill="#bacad6"><circle cx="15" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite"></animate></circle><circle cx="60" cy="15" r="9" fill-opacity="0.3"><animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite"></animate></circle><circle cx="105" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite"></animate></circle></svg></div>';
    $(elm).html(tmp);
}


/**
 * 购物车关闭
 */
$("#cart-main").on('click','.close',function(){
    is_show_cart = 0;
    hideCartMain();
    closeShade();
})

/**
 * 头部点击打开购物车
 */
$('.show-cart').hover(function(){
    if(is_show_cart == 0){
        showCartMain();
    }
});

$('#cart-main').hover(function(){

},function(){
    is_show_cart = 0;
    hideCartMain();
    closeShade();
})


/**
 * 购物车移除附加商品
 */
$("#cart-main").on('click','.added-remove',function(){

    var parent = $(this).parents('.added-item');

    if(parent.siblings().length>0){

    }else{
        parent = $(this).parents('.added')
    }
    parent.animate({
        opacity:0,
        height:0,
    },500,function(){
        $(this).remove();
        censusCart();
    });

    var id = parseInt($(this).attr('data-id'));
    var pid = parseInt($(this).attr('data-pid'));
    deleteAddedCart(id,pid)
})

/**
 * 购物车移除商品
 */
$("#cart-main").on('click','.product-remove',function(){
    var parent = $(this).parents('.item');

    parent.animate({
        opacity:0,
        height:0,
        marginBottom:0,
        paddingBottom:0,
    },500,function(){
        $(this).remove();
        if($('.product-remove').length<1){
            var tmp_html = '<div class="empty-cart">\n' +
                '        <a class="close" href="javascript:;"><i class="iconfont">&#xeca0;</i></a>\n' +
                '        <div><i class="iconfont">&#xe6b5;</i></div>\n' +
                '        <p class="empty-text">\n' +
                '            購物車還是空的，挑幾件商品吧！\n' +
                '        </p>\n' +
                '    </div>';
            $('#cart-main').html(tmp_html);
        }
        censusCart();
    });
    var id = parseInt($(this).attr('data-id'));
    deleteCart(id)
})

headCartCount();


var bottom_tip_key = 0;
function bottomTips(text){
    var id = "bottom-message-"+bottom_tip_key;
    var tmp = '<div id="'+id+'" class="bottom-message">\n' +
        '    <p class="text">'+text+'</p>\n' +
        '</div>';

    $('body').append(tmp);
    $('#'+id).animate({height:60},500,function(){
        var _this = $(this);
        setTimeout(function() {
            _this.find('.text').animate({opacity:0},500);
            _this.animate({height:0});
        }, 3000);
    });
    $('#'+id).find('.text').animate({opacity:1},1500);
}
