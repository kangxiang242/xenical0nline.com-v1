document.addEventListener("DOMContentLoaded", function() {
    gsap.registerPlugin(ScrollTrigger);

    $('.model-scroll').each(function(){
        var _this = $(this);
        gsap.to(_this, {
            scrollTrigger: {
                trigger: _this,
                toggleActions: "onEnter onLeave onEnterBack onLeaveBack",
                onEnter:function(){
                    modelScroll(_this,true);
                },
                onLeave: function(){
                    //modelScroll(_this,false);

                }, // 滚动到退出
                onEnterBack: function(){
                    modelScroll(_this,true);
                }, // 滚动到进入位置
                onLeaveBack: function(){
                    //modelScroll(_this,false);
                }, // 滚动到退出位置

            }

        });
    })


});

function modelScroll(_this,open){
    var _text = _this.find('.text');
    var _line = _this.find('.target-ls');
    if(open === true){

        gsap.to(_text, {
            y:0,
            opacity:1,
            duration: 1
        });

        gsap.to(_line, {
            x:0,
            opacity:1,
            duration: 1.5
        });
    }else{
        gsap.to(_text, {
            y:-25,
            opacity:0,
            duration: 0
        });

        gsap.to(_line, {
            x:-15,
            opacity:0,
            duration: 0
        });
    }

}