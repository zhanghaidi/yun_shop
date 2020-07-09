$(function(){
    require(['bootstrap'], function () {
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
    });

    $('form').submit(function(){
        return true;
    })
});

$('form').submit(function(){
    var data = [];
    $('.drag').each(function(){
        var obj = $(this);
        var type = obj.attr('type');
        var left = obj.css('left'),top = obj.css('top');
        var d= {left:left,top:top,type:obj.attr('type'),width:obj.css('width'),height:obj.css('height')};
        if(type=='nickname' || type=='time'){

            d.size = obj.find('.text').css('font-size');
            d.color = rgb2hex(obj.find('.text').css('color'));
        } else if(type=='qr'){
            d.size = obj.attr('size');
        } else if(type=='qr_shop'){
            d.size = obj.attr('size');
        } else if(type=='img'){
            d.src = obj.find('img').attr('src');
        }
        data.push(d);
    });
    if(data.length == 0){
        $(':input[name=data]').val(''); //如果没有样式数据, 则设为空, 否则字符串形式的空数组'[]'会通过required的表单验证
    } else{
        $(':input[name=data]').val( JSON.stringify(data));
//                return false;
    }

    return true;
});

//将 RGB 转换为 16 进制颜色
function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ("0" + parseInt(x).toString(16)).slice(-2);
    }
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

function bindEvents(obj){
    var index = obj.attr('index');

    var rs = new Resize(obj, { Max: true, mxContainer: "#poster" });
    rs.Set($(".rRightDown",obj), "right-down");
    rs.Set($(".rLeftDown",obj), "left-down");
    rs.Set($(".rRightUp",obj), "right-up");
    rs.Set($(".rLeftUp",obj), "left-up");
    rs.Set($(".rRight",obj), "right");
    rs.Set($(".rLeft",obj), "left");
    rs.Set($(".rUp",obj), "up");
    rs.Set($(".rDown",obj), "down");
    rs.Scale = true;
    var type = obj.attr('type');
    if(type=='nickname' || type=='img' || type=='time'){
        rs.Scale = false;
    }
    new Drag(obj, { Limit: true, mxContainer: "#poster" });
    $('.drag .remove').unbind('click').click(function(){
        $(this).parent().remove();
    })
    require(['contextMenu'],function(){
        $.contextMenu({
            selector: '.drag[index=' + index + ']',
            callback: function(key, options) {
                var index = parseInt($(this).attr('z-index'));

                if(key=='next'){
                    var nextdiv = $(this).next('.drag');
                    if(nextdiv.length>0 ){
                        nextdiv.insertBefore($(this));
                    }
                } else if(key=='prev'){
                    var prevdiv = $(this).prev('.drag');
                    if(prevdiv.length>0 ){
                        $(this).insertBefore(prevdiv);
                    }
                } else if(key=='last'){
                    var len = $('.drag').length;
                    if(index >=len-1){
                        return;
                    }
                    var last = $('#poster .drag:last');
                    if(last.length>0){
                        $(this).insertAfter(last);
                    }
                }else if(key=='first'){
                    var index = $(this).index();
                    if(index<=1){
                        return;
                    }
                    var first = $('#poster .drag:first');
                    if(first.length>0){
                        $(this).insertBefore(first);
                    }
                }else if(key=='delete'){
                    $(this).remove();
                }
                var n =1 ;
                $('.drag').each(function(){
                    $(this).css("z-index",n);
                    n++;
                })
            },
            items: {
                "next": {name: "调整到上层"},
                "prev": {name: "调整到下层"},
                "last": {name: "调整到最顶层"},
                "first": {name: "调整到最低层"},
                "delete": {name: "删除元素"}
            }
        });
    })

    obj.unbind('click').click(function(){
        bind($(this));
    })

}
var imgsettimer = 0;
var nametimer = 0;
var bgtimer = 0 ;
function bindType(type){
    $("#goodsparams").hide();
    $(".type4").hide();
    if(type=='4'){
        $(".type4").show();
    } else if(type=='3'){
        $("#goodsparams").show();
    }
}
function clearTimers(){
    clearInterval(imgsettimer);
    clearInterval(nametimer);
    clearInterval(bgtimer);

}
function getImgUrl(val){

    if(val.indexOf('http://')==-1){
        var attachurl = window.sysinfo['attachurl'];
        if (attachurl.charAt(attachurl.length-1) != '/') { // 新框架下可能出现末尾没有反斜杠的情况，原因未知，所以要添加该判断
            attachurl = attachurl + '/';
        }
        val = attachurl + val;
        // val = window.sysinfo['attachurl'] + val;
    }
    return val;
}
function bind(obj){
    var imgset = $('#imgset'), nameset = $("#nameset");
    imgset.hide(),nameset.hide();
    clearTimers();
    var type = obj.attr('type');
    if(type=='img'){
        imgset.show();
        var src = obj.find('img').attr('src');
        var input = imgset.find('input');
        var img = imgset.find('img');
        if(typeof(src)!='undefined' && src!=''){
            input.val(src);
            img.attr('src',getImgUrl(src));
        }

        imgsettimer = setInterval(function(){
            if(input.val()!=src && input.val()!=''){
                var url = getImgUrl(input.val());

                obj.attr('src',input.val()).find('img').attr('src',url);
            }
        },10);

    } else if(type=='nickname' || type=='time'){

        nameset.show();
        var nickobj = obj.find('.text');

        var color = rgb2hex(nickobj.css('color')) || "#000";
        var size = nickobj.css('fontSize') || "16";

        var input = nameset.find('input:first');
        var namesize = nameset.find('#namesize');
        var picker = nameset.find('.sp-preview-inner');
        input.val(color);
        namesize.val(size.replace("px",""));
        picker.css( {'background-color':color,'font-size':size});

        nametimer = setInterval(function(){
            obj.attr('color',input.val()).find('.text').css('color',input.val());
            obj.attr('size',namesize.val() +"px").find('.text').css('font-size',namesize.val() +"px");
        },10);
    }
}

$(function(){
    $('.drag').each(function(){
        bindEvents($(this));
    })

    $(':radio[name=type]').click(function(){
        var type = $(this).val();
        bindType(type);
    })
    //改变背景
    $('#bgset').find('button:first').click(function(){
        var oldbg = $(':input[name="poster[background]"]').val();
        bgtimer = setInterval(function(){
            var bg = $(':input[name="poster[background]"]').val();
            if(oldbg!=bg){
                bg = getImgUrl(bg);

                $('#poster .bg').remove();
                var bgh = $("<img src='" + bg + "' class='bg' />");
                var first = $('#poster .drag:first');
                if(first.length>0){
                    bgh.insertBefore(first);
                } else{
                    $('#poster').append(bgh);
                }

                oldbg = bg;
            }
        },2000);
    });

    $('.btn-com').click(function(){
        var pathname = window.location.pathname;

        if (pathname == '/admin/shop') {
            webroot = '/';
        } else {
            webroot = '../addons/yun_shop/';
        }


        var imgset = $('#imgset'), nameset = $("#nameset");
        imgset.hide(),nameset.hide();
        clearTimers();

        if($('#poster img').length<=0){
            alert('请选择背景图片!');
            return;
        }
        var type = $(this).data('type');
        var img = "";
        switch (type){
            case 'qr':
                img = '<img src="' + webroot + 'plugins/poster/assets/img/qr.png" />';
                break;
            case 'qr_shop':
                img = '<img src="' + webroot + 'plugins/poster/assets/img/qr_shop.png" />';
                break;
            case 'qr_app_share':
                img = '<img src="' + webroot + 'plugins/poster/assets/img/qr_app_share.png" />';
                break;
            case 'head':
                img = '<img src="' + webroot + 'plugins/poster/assets/img/head.jpg" />';
                break;
            case 'img':
                img = '<img src="' + webroot + 'plugins/poster/assets/img/img.jpg" />';
                break;
            case 'nickname':
                img = '<div class=text>昵称</div>';
                break;
            case 'time':
                img = '<div class=text>失效时间</div>';
                break;
        }

        var index = $('#poster .drag').length+1;
        var obj = $('<div class="drag" type="' + type +'" index="' + index +'" style="z-index:' + index+'">' + img+'<div class="rRightDown"> </div><div class="rLeftDown"> </div><div class="rRightUp"> </div><div class="rLeftUp"> </div><div class="rRight"> </div><div class="rLeft"> </div><div class="rUp"> </div><div class="rDown"></div></div>');

        $('#poster').append(obj);
        bindEvents(obj);
    });

    $('.drag').click(function(){
        bind($(this));
    })

})

var currentCouponType = null;
function selectCoupon(type){
    currentCouponType = type;
    $('#modal-module-menus-coupon').modal();
}

function select_coupon(o) {
    $("input[data-name=" + currentCouponType +"id]").val(o.id);
    $("input[data-name=" + currentCouponType +"name]").val(o.name);
    $("."+currentCouponType + "group").find('button').html( "[" + o.id + "] " + o.name );
    $("#modal-module-menus-coupon .close").click();

}