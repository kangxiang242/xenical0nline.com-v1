function xoTrackFail(code) {
    if (window.XenicalTracker) XenicalTracker.markCheckoutValidationFail(code);
}
function xoTrackPurchase(redirect, goodsId) {
    if (!window.XenicalTracker) return;
    var no = '';
    if (redirect) {
        var parts = String(redirect).replace(/\/$/, '').split('/');
        no = parts[parts.length - 1] || '';
    }
    XenicalTracker.conversion('purchase', { order_no: no, product_id: goodsId || $("input[name='goods_id']").val() });
}

function orderStore(){

    var name = $("input[name='name']").val();
    var phone = $("input[name='phone']").val();
    var email = $("input[name='email']").val();
    var city = $("select[name='city']").val();
    var county = $("select[name='county']").val();
    var street = $("select[name='street']").val();
    var address = $("input[name='address']").val();
    var goods_id = $("input[name='goods_id']").val();
    var order_type = $("input[name='order_type']:checked").val();
    var store_id = $("input[name='store_id']:checked").val();
    if(!name){
        xoTrackFail('name_required');
        $('input[name="name"]').focus().addClass('red-error');
        $('input[name="name"]').contip({
            align: 'right',
            html: '請填寫收件人姓名',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 3000,
            repeat:false,
        }).show();

        return false;
    }
    if(!phone){
        xoTrackFail('phone_required');
        $('input[name="phone"]').focus().addClass('red-error');
        $('input[name="phone"]').contip({
            align: 'right',
            html: '請填寫收件人聯絡電話',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 3000,
            repeat:false,
        }).show();
        return false;
    }
    if(!(/^09\d{8}$/.test(phone))){
        xoTrackFail('phone_invalid');
        $('input[name="phone"]').focus().addClass('red-error');
        $('input[name="phone"]').contip({
            align: 'right',
            html: '請填寫正確的聯絡電話',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 2000,
            repeat:false,
        }).show();
        return false;
    }

    if(!email){
        xoTrackFail('email_required');
        $('input[name="email"]').focus().addClass('red-error');
        $('input[name="email"]').contip({
            align: 'right',
            html: '請填寫收件人郵箱',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 2000,
            repeat:false,
        }).show();
        return false;
    }

    if(email.search(/^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.(?:com|cn|tw|info|net)$/) == -1){
        xoTrackFail('email_invalid');
        $('input[name="email"]').focus();
        $('input[name="email"]').contip({
            align: 'right',
            html: '請填寫正確的郵箱',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 2000,
            repeat:false,
        }).show();
        return false;
    }

    if(!city){
        xoTrackFail('city_required');
        $("select[name='city']").addClass('red-error');
        $("select[name='city']").contip({
            align: 'bottom',
            html: '請選擇縣市',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 2000,
            repeat:false,
        }).show();
        return false;
    }

    if(!county){
        xoTrackFail('county_required');
        $("select[name='county']").addClass('red-error');
        $("select[name='county']").contip({
            align: 'bottom',
            html: '請選擇地區',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 2000,
            repeat:false,
        }).show();
        return false;
    }
    if(!street){
        xoTrackFail('street_required');
        $("select[name='street']").addClass('red-error');
        $("select[name='street']").contip({
            align: 'bottom',
            html: '請選擇路段',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 2000,
            repeat:false,
        }).show();
        return false;
    }
    if(order_type > 0){

        if(!store_id){
            xoTrackFail('store_required');
            var $storeInput = $('#form-store-row input[name="store_id"]').first();
            if ($storeInput.length) {
                $storeInput.focus();
                $storeInput.contip({
                    align: 'bottom',
                    html: '請選擇取貨門市',
                    fade: 360,
                    opacity:0.6,
                    delay_out:100,
                    trigger:'focus',
                    auto_close: 2000,
                    repeat:false,
                }).show();
            } else {
                $('#form-store-row').contip({
                    align: 'bottom',
                    html: '請先選擇縣市、地區與路段以載入門市',
                    fade: 360,
                    opacity:0.6,
                    delay_out:100,
                    trigger:'focus',
                    auto_close: 2000,
                    repeat:false,
                }).show();
            }
            return false;
        }

    }else{

        if(!address){
            xoTrackFail('address_required');
            $('input[name="address"]').focus();
            $('input[name="address"]').contip({
                align: 'right',
                html: '請填寫詳細地址',
                fade: 360,
                opacity:0.6,
                delay_out:100,
                trigger:'focus',
                auto_close: 2000,
                repeat:false,
            }).show();
            return false;
        }
    }
    if(!goods_id){

        return false;
    }

    addLoadingActionBtn('.submit-btn');

    // 确保门店资料随订单提交
    var storeExtra = '';
    var $checkedStore = $('input[name="store_id"]:checked');
    if ($checkedStore.length) {
        storeExtra = '&store_name=' + encodeURIComponent($checkedStore.data('name') || '')
                   + '&store_address=' + encodeURIComponent($checkedStore.data('address') || '');
    }

    $.ajax({
        type: $('#order-form').attr('method'),
        url: $('#order-form').attr('action'),
        data: $('#order-form').serialize() + storeExtra,
        dataType: "json",
        success: function(data){
            xoTrackPurchase(data.redirect, $("input[name='goods_id']").val());
            window.location.href = data.redirect;
        },
        error:function(jqXHR, textStatus, errorThrown){
            var errMsg = 'server_error';
            var httpStatus = jqXHR.status || 0;
            try { var resp = JSON.parse(jqXHR.responseText); errMsg = resp.msg || resp.message || errMsg; } catch(e) {}
            if (window.XenicalTracker) XenicalTracker.conversion('submit_fail', { error_code: errMsg, http_status: httpStatus, product_id: $("input[name='goods_id']").val() });
            Swal.fire({
                title: '訂單提交失敗!',
                text: '',
                icon: 'error',
                confirmButtonText: '我知道了'
            })
            closeLoadingActionBtn('.submit-btn');
        }
    });
    return false;
}

function orderCheck(){

    if($('.form-btn').attr('disabled')){
        return false;
    }

    var email = $("input[name='email']").val();
    var phone = $("input[name='phone']").val();
    var captcha_code = $("input[name='captcha_code']").val();
    if(!phone){
        if (window.XenicalTracker) XenicalTracker.conversion('order_check_submit', { status: 'fail', error_code: 'phone_required' }, 'order_check');
        $("input[name='phone']").focus();
        return false;
    }
    if(!(/^09\d{8}$/.test(phone))){
        $("input[name='phone']").focus();
        return false;
    }
    if(!email){
        $("input[name='email']").focus();
        return false;
    }
    if(email.search(/^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.(?:com|cn|tw|info|net)$/) == -1){
        $("input[name='email']").focus();
        return false;
    }
    if(!captcha_code){
        $("input[name='captcha_code']").focus()
        return false;
    }

    addLoadingActionBtn('.form-btn');

    $.ajax({
        type: $('#check-form').attr('method'),
        url: $('#check-form').attr('action'),
        data: $('#check-form').serialize(),
        dataType: "json",
        success: function(data){
            if(data.code == 200){
                if (window.XenicalTracker) XenicalTracker.conversion('order_check_submit', { status: 'success' }, 'order_check');
                promptSuccess(data.message,'正在為您跳轉中,請稍後...');
                window.location.href = data.jump;
            }else{
                if (window.XenicalTracker) XenicalTracker.conversion('order_check_submit', { status: 'fail', error_code: 'query_failed' }, 'order_check');
                promptError("查詢失敗",data.message);
            }
            closeLoadingActionBtn('.form-btn');
            $("input[name='email']").val('');
            $("input[name='phone']").val('');
            $("input[name='captcha_code']").val('');
            $("img.captcha").click()
        },
        error:function(jqXHR, textStatus, errorThrown){
            if (window.XenicalTracker) XenicalTracker.conversion('order_check_submit', { status: 'fail', error_code: 'server_error', http_status: jqXHR.status }, 'order_check');
            var response = JSON.parse(jqXHR.responseText)
            promptError("查詢失敗",response.message);
            closeLoadingActionBtn('.form-btn');
        }
    });
    return false;

}

function messageStore(){
    var name = $("input[name='name']").val();
    var phone = $("input[name='phone']").val();
    var email = $("input[name='email']").val();
    var content = $("textarea[name='content']").val();
    if(!name){
        if (window.XenicalTracker) XenicalTracker.conversion('message_submit', { status: 'fail', error_code: 'name_required' }, 'message');
        $('input[name="name"]').focus();

        return false;
    }
    if(!phone){
        $("input[name='phone']").focus();

        return false;
    }
    if(!(/^09\d{8}$/.test(phone))){
        $("input[name='phone']").focus();

        return false;
    }
    if(!email){
        $("input[name='email']").focus();

        return false;
    }
    if(email.search(/^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.(?:com|cn|tw|info|net)$/) == -1){
        $("input[name='email']").focus();

        return false;
    }

    if(!content){
        $("textarea[name='content']").focus()
        return false;
    }

    if($('.form-btn').attr('disabled')){
        return false;
    }

    addLoadingActionBtn('.form-btn');

    $.ajax({
        type: $('#message-form').attr('method'),
        url: $('#message-form').attr('action'),
        data: $('#message-form').serialize(),
        dataType: "json",
        success: function(data){
            if (data.status == true){
                if (window.XenicalTracker) XenicalTracker.conversion('message_submit', { status: 'success' }, 'message');
                promptSuccess(data.title,data.message);
                $("input[name='name']").val('');
                $("input[name='phone']").val('');
                $("input[name='email']").val('');
                $("textarea[name='content']").val('');
            }else{
                if (window.XenicalTracker) XenicalTracker.conversion('message_submit', { status: 'fail', error_code: 'server_reject' }, 'message');
                promptError(data.title,data.message);
            }
            closeLoadingActionBtn('.form-btn');
        },
        error:function(jqXHR, textStatus, errorThrown){
            if (window.XenicalTracker) XenicalTracker.conversion('message_submit', { status: 'fail', error_code: 'server_error', http_status: jqXHR.status }, 'message');
            var response = JSON.parse(jqXHR.responseText)
            promptError("留言失敗",response.message);
            closeLoadingActionBtn('.form-btn');
        }
    });
    return false;
}

function warnInfo(elem,text){
    var left = parseInt(elem.parent().css('width'))+10;
    elem.css('color',"rgb(239, 60, 60)");
    elem.text(text);
}


$("input,textarea").blur(function(){
    $(this).attr('placeholder',$(this).attr('x-placeholder'));
    $(this).removeAttr('x-placeholder');
});

$("input,textarea").focus(function(){
    $(this).attr('x-placeholder',$(this).attr('placeholder'));
    $(this).removeAttr('placeholder');
})


function submit(elem,options={}){
    var _this = $(elem);
    if(!options.dataType){
        options.dataType = 'json';
    }

    _this.ajaxForm({
        url: _this.attr('action'), //提交地址：默认是form的action,如果申明,则会覆盖
        type: _this.attr('method'),   //默认是form的method（get or post），如果申明，则会覆盖
        beforeSerialize: function() {
            if(options.before){
                options.before();
            }
        },
        beforeSubmit: function(){

            var validate_result = true;
            var error_validate_message;
            var error_validate_elem;
            if(options.validate){
                validate_result = options.validate()
            }
            var request_data = {};
            _this.find('input,select,textarea').each(function () {
                var $this = $(this);
                var validate = $this.attr('data-validate');

                //检查条件
                var condition = $this.attr('data-validate-condition');
                var is_adopt_condition = true;
                if(condition){
                    var wheres = condition.split(':');
                    if(wheres[0]){
                        var where_val = request_data[wheres[0]];
                        if(where_val != wheres[1]){
                            is_adopt_condition = false;
                        }
                    }
                }

                var name = $this.attr('name');
                var val
                if($this.attr('type') ==='radio'){
                    val = _this.find('input[name="'+name+'"]:checked').val();
                }else{
                    val = $this.val()
                }
                request_data[name] = val;

                if(validate && is_adopt_condition && validate_result === true){




                    var rules = validate.split('|');
                    for(var i = 0;i < rules.length;i++){



                        var rule_untreated = rules[i];
                        if(rule_untreated && validate_result === true){
                            var rule_compose = rule_untreated.split(':')
                            var rule = rule_compose[0]

                            switch (rule) {
                                case "required":
                                    if(!val){
                                        if(rule_compose[1]){
                                            error_validate_message = rule_compose[1];
                                        }else{
                                            error_validate_message = "必填字段不能爲空"
                                        }
                                        validate_result = false;

                                    }
                                    break;
                                case "mobile":
                                    if(!(/^09\d{8}$/.test(val))){
                                        if(rule_compose[1]){
                                            error_validate_message = rule_compose[1];
                                        }else{
                                            error_validate_message = "聯絡電話格式錯誤"
                                        }
                                        validate_result = false;
                                    }
                                    break
                                case "email":
                                    if(val.search(/^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.(?:com|cn|tw|info|net)$/) == -1){
                                        if(rule_compose[1]){
                                            error_validate_message = rule_compose[1];
                                        }else{
                                            error_validate_message = "郵箱格式錯誤"
                                        }
                                        validate_result = false;
                                    }
                                    break
                                default:
                            }
                            error_validate_elem = $this;
                        }

                    }
                }

            });


            if(validate_result === false){
                if(error_validate_elem){
                    $(error_validate_elem).focus()
                }

                Swal.fire({
                    icon:'error',
                    iconColor:'#fff',
                    text: error_validate_message,
                    color:'#fff',
                    background:'rgba(0,0,0,0.7)',
                    width:'auto',
                    backdrop:false,
                    timer:1000,
                    timerProgressBar:false,
                    showConfirmButton:false,
                })

                return false;
            }


            if(!options.before){
                Swal.fire({
                    width:"50%",
                    text: "正在處理中",
                    backdrop:true,
                    allowOutsideClick:false,
                    didOpen: () => {
                        Swal.showLoading()
                    },
                })
            }

            _this.find('button').attr('disabled','disabled')

        }, //提交前的回调函数
        success: function(result){

            if(result.redirect){

                window.location.href = result.redirect;
            }else if(result.jump){
                window.location.href = result.jump;
            }else{
                if(options.success){
                    options.success(result);
                }else{
                    var swal_options = {};
                    swal_options.iconColor = "#fff";
                    if(result.title){
                        swal_options.title = result.title;
                    }
                    if(result.message){
                        swal_options.text = result.message;
                    }
                    swal_options.background = "rgba(0,0,0,1)";
                    swal_options.width = "auto";
                    swal_options.backdrop = "false";
                    swal_options.timer = 2000;
                    swal_options.showConfirmButton = false;
                    swal_options.color = "#fff";
                    if(result.status == true){
                        swal_options.icon = 'success'
                    }else{
                        swal_options.icon = 'error'
                    }
                    Swal.fire(swal_options)
                    _this.clearForm()
                }
            }

        },
        error:function(XMLHttpRequest){
            var error = XMLHttpRequest.responseJSON;
            Swal.fire({
                icon:'error',
                iconColor:'#fff',
                text: error.message,
                color:'#fff',
                background:'rgba(0,0,0,0.7)',
                width:'auto',
                backdrop:false,
                timer:1000,
                timerProgressBar:false,
                showConfirmButton:false,
            })
        },
        complete:function(XMLHttpRequest){
            _this.find('button').removeAttr('disabled')
            var res = XMLHttpRequest.responseJSON;
            _this.find("input[name='_token']").val(res._token)
            _this.find("img.captcha").click()

        },
        dataType: options.dataType, //html(默认), xml, script, json...接受服务端返回的类型
        //clearForm: true,  //成功提交后，是否清除所有表单元素的值
    })
    return false;

}

