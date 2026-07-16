var prompt_key = 0;
function promptSuccess(title,message,time){
    var tmp = '    <div class="prompt" id="prompt-'+prompt_key+'">\n' +
        '        <p class="title success">'+title+'</p>\n' +
        '        <p class="desc">'+message+'</p>\n' +
        '    </div>';
    $('body').append(tmp);
    promptClose("#prompt-"+prompt_key,time);
    prompt_key++;
}

function promptError(title,message,time){
    var tmp = '    <div class="prompt" id="prompt-'+prompt_key+'">\n' +
        '        <p class="title error">'+title+'</p>\n' +
        '        <p class="desc">'+message+'</p>\n' +
        '    </div>';
    $('body').append(tmp);
    promptClose("#prompt-"+prompt_key,time);
    prompt_key++;
}

function promptClose(elem,time){
    if(!time){
        time = 2000;
    }
    setTimeout(function(){
        $(elem).remove();
    },time);

}


function addLoadingActionBtn(elem){
    var load = '<svg xmlns="http://www.w3.org/2000/svg" class="mx-auto block" style="width:50px;height:11px;" viewBox="0 0 120 30" fill="currentColor"><circle cx="15" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite"></animate></circle><circle cx="60" cy="15" r="9" fill-opacity="0.3"><animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite"></animate></circle><circle cx="105" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite"></animate></circle></svg>'
    $(elem).attr('data-tmp-text',$(elem).html());
    $(elem).html(load);
    $(elem).addClass('loading-action-btn-disabled');
    $(elem).attr('disabled','disabled');
}

function closeLoadingActionBtn(elem){
    $(elem).removeClass('loading-action-btn-disabled')
    $(elem).removeAttr('disabled');
    $(elem).html($(elem).attr('data-tmp-text'));
}

function textAnimation(elem){
    var text = $(elem).text();
    var textArr = text.split('');
    var html = '<span class="text-animation-main"><span class="splitting -aos-active"><span class="word">';
    for(j = 0; j < textArr.length; j++) {
        html += '<span class="char" style="--char-index:'+j+';">'+textArr[j]+'</span>';
    }
    html += '</span></span></span>';
    $(elem).html(html);
}
