$(".xiangxi").click(function(){
    var action = $(this).attr("data-action");
    if(action == 1){
        $(this).parents("td").find(".user_agent").show();
        $(this).parents('td').find('.order_device').hide();
        $(this).attr('data-action',2)
        $('.column-order_device').css('width','300px');
        $(this).text("收起")
    }else{
        $(this).parents('td').find('.user_agent').hide();
        $(this).parents('td').find('.order_device').show();
        $(this).attr('data-action',1)
        $('.column-order_device').css('width','100px');
        $(this).text("詳細")
    }
});

$('.locus').click(function(){
    layer.open({
        title:"活動軌跡",
        type: 2,
        area: ['900px', '650px'],
        fixed: false, //不固定
        maxmin: true,
        content: '/hhrUIsBl/locus?id='+$(this).attr('data-id'),
    });
});
