@extends('layouts.base')

@section('content')
@section('title', trans('套餐详情'))
<script type="text/javascript">

    window.optionchanged = false;
    require(['bootstrap'], function () {
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
    });

    require(['util'], function (u) {
        $('#cp').each(function () {
            u.clip(this, $(this).text());
        });
    });

    function formCheck() {
        var checkResult = true;

        var reg = /(^[-+]?[1-9]\d*(\.\d{1,2})?$)|(^[-+]?[0]{1}(\.\d{1,2})?$)/; //金额字段验证,后两位小数
        var numerictype = /^(0|[1-9]\d*)$/; //整数验证
        var thumb = /\.(gif|jpg|jpeg|png|GIF|JPG|PNG)$/;
        var datetime = /(\d{2}|\d{4})(?:\-)?([0]{1}\d{1}|[1]{1}[0-2]{1})(?:\-)?([0-2]{1}\d{1}|[3]{1}[0-1]{1})(?:\s)?([0-1]{1}\d{1}|[2]{1}[0-3]{1})(?::)?([0-5]{1}\d{1})(?::)?([0-5]{1}\d{1})/;

        //套餐标题不能为空
        if ($(':input[name="package[title]"]').val() == '') {
            $('#myTab a[href="#tab_basic"]').tab('show');
            alert("请输入套餐名称!");
            Tip.focus("#package_title", "请输入套餐名称!");
            checkResult = false;
            return checkResult;
        }
        //套餐标题不能超过40字符
        if ($(':input[name="package[title]"]').val().length >= '50') {
            $('#myTab a[href="#tab_basic"]').tab('show');
            alert("套餐名称不能超过50个字符!");
            Tip.focus("#package_title", "套餐名称不能超过50个字符!");
            checkResult = false;
            return checkResult;
        }

        //图片如果不为空则类型必须为图片
        if ($.trim($(':input[name="package[thumb]"]').val()) != '') {
            if (!thumb.test($.trim($(':input[name="package[thumb]"]').val()))) {
                $('#myTab a[href="#tab_basic"]').tab('show');
                alert("图片类型必须是.gif,jpeg,jpg,png中的一种.");
                Tip.focus(':input[name="package[thumb]"]', '图片类型必须是.gif,jpeg,jpg,png中的一种.');
                checkResult = false;
                return checkResult;
            }
        }
        //套餐商品必须
        if ($(':input[name="package[category][sort][]"]').length <= 0 || $(':input[name="package[category][cate_name][]"]').length <= 0 || $(':input[name="package[category][goods_ids][]"]').length <= 0) {
            $('#myTab a[href="#tab_basic"]').tab('show');
            alert("请选择套餐商品!");
            checkResult = false;
            return checkResult;
        }
        else{
            //遍历每一个排序，必须填写并且为数字
            $(':input[name="package[category][sort][]"]').each(function(){
                if ($(this).val() == '') {
                    $('#myTab a[href="#tab_basic"]').tab('show');
                    alert("请输入栏目排序!");
                    Tip.focus($(this), "请输入栏目排序!");
                    checkResult = false;
                    return checkResult;
                }
                if (!numerictype.test($(this).val())) {
                    $('#myTab a[href="#tab_basic"]').tab('show');
                    alert("栏目排序必须为数字!");
                    Tip.focus($(this), "栏目排序必须为数字!");
                    checkResult = false;
                    return checkResult;
                }
            });
            if(!checkResult) {
                return checkResult;
            }
            //栏目
            $(':input[name="package[category][cate_name][]"]').each(function(){
                if ($(this).val() == '') {
                    $('#myTab a[href="#tab_basic"]').tab('show');
                    alert("栏目不能为空!");
                    Tip.focus($(this), "栏目不能为空!");
                    checkResult = false;
                    return checkResult;
                }
                if ($(this).val().length >= '50') {
                    $('#myTab a[href="#tab_basic"]').tab('show');
                    alert("栏目名称不能超过50个字符!");
                    Tip.focus($(this), "栏目名称不能超过50个字符!");
                    checkResult = false;
                    return checkResult;
                }
            });
            if(!checkResult) {
                return checkResult;
            }
            //商品id
            $(':input[name="package[category][goods_ids][]"]').each(function(){
                if ($(this).val() == '') {
                    $('#myTab a[href="#tab_basic"]').tab('show');
                    alert("请选择商品!");
                    Tip.focus($(this), "商品为空,请选择商品!");
                    checkResult = false;
                    return checkResult;
                }
            });
            if (!checkResult) {
                return checkResult;
            }
        }

        //优惠价格如果填写，则必须数字
        if ($.trim($(':input[name="package[on_sale_price]"]').val()) != '') {
            if (!reg.test($(':input[name="package[on_sale_price]"]').val())) {
                $('#myTab a[href="#tab_basic"]').tab('show');
                alert("套餐优惠价格格式错误,必须为数字并且最多两位小数!");
                Tip.focus(':input[name="package[on_sale_price]"]', '套餐优惠价格格式错误,最多两位小数.');
                checkResult = false;
                return checkResult;
            }
        }

        //搭配，如果开启则必须有其他套餐选择，关闭则不需要
        if ($(':input[name="package[other_package_status]"]').get(0).checked) {
            if($(':input[name="package[other_package_ids][]"]').length == 0) {
                $('#myTab a[href="#tab_basic"]').tab('show');
                alert("请选择搭配套餐!");
                checkResult = false;
                return checkResult;
            } else {
                //对每一个id都要验证
                $(':input[name="package[other_package_ids][]"]').each(function(){
                    if($(this).val() == '') {
                        $('#myTab a[href="#tab_basic"]').tab('show');
                        alert("搭配套餐不能为空!");
                        checkResult = false;
                        return checkResult;
                    }
                });
            }
        }
        //幻灯片，如果有，则它的排序必须为数字
        if ($('.carousel_is_open').length > 0) {
            $('.carousel_sort').each(function(){
                if ($(this).val() == '') {
                    $('#myTab a[href="#tab_carousel"]').tab('show');
                    alert("幻灯片排序必须填写并且为数字!");
                    checkResult = false;
                    return checkResult;
                }
                if (!numerictype.test($(this).val())) {
                    $('#myTab a[href="#tab_carousel"]').tab('show');
                    alert("幻灯片排序必须是数字!");
                    checkResult = false;
                    return checkResult;
                }
            });
        }
        return checkResult;
    }

</script>

<script type="text/javascript">
    //鼠标划过显示商品链接二维码
    $('.umphp').hover(function () {
                var url = $(this).attr('data-url');
                var goodsid = $(this).attr('data-goodsid');
                $.post("{!! yzWebUrl('shop.goods') !!}"
                        , {'op': 'goods_qrcode', id: goodsid, url: url}
                        , function (qr) {
                            if (qr.img) {
                                var goodsqr = qr.img;
                                var element = document.getElementById(goodsid);
                                element.src = goodsqr;
                            }
                        }
                        , "json"
                );
                $(this).addClass("selected");
            },
            function () {
                $(this).removeClass("selected");
            })
    function fastChange(id, type, value) {
        $.ajax({
            url: "{!! yzWebUrl('shop.goods') !!}",
            type: "post",
            data: {op: 'change', id: id, type: type, value: value},
            cache: false,
            success: function () {
                location.reload();
            }
        })
    }
    $(function () {
        $("form").keypress(function (e) {
            if (e.which == 13) {
                return false;
            }
        });

        $('.tdedit input').keydown(function (event) {
            if (event.keyCode == 13) {
                var group = $(this).closest('.input-group');
                var type = group.find('button').data('type');
                var goodsid = group.find('button').data('goodsid');
                var val = $.trim($(this).val());
                if (type == 'title' && val == '') {
                    return;
                }
                group.prev().show().find('span').html(val);
                group.hide();
                fastChange(goodsid, type, val);
            }
        })
        $('.tdedit').mouseover(function () {
            $(this).find('.fa-pencil').show();
        }).mouseout(function () {
            $(this).find('.fa-pencil').hide();
        });
        $('.fa-edit-item').click(function () {
            var group = $(this).closest('span').hide().next();

            group.show().find('button').unbind('click').click(function () {
                var type = $(this).data('type');
                var goodsid = $(this).data('goodsid');
                var val = $.trim(group.find(':input').val());
                if (type == 'title' && val == '') {
                    Tip.show(group.find(':input'), '请输入名称!');
                    return;
                }
                group.prev().show().find('span').html(val);
                group.hide();
                fastChange(goodsid, type, val);
            });
        })
    })


</script>


<link rel="stylesheet" type="text/css" href="{{static_url('css/font-awesome.min.css')}}">
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#"><i class="fa fa-circle-o" style="color: #33b5d2;"></i>套餐信息</a></li>
    </ul>
</div>
<form id="goods-edit" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
    <div class="panel-default panel-center">

        @if(!empty($package['id']))
            <input type="hidden" name="package[id]"  value="{{ $package['id'] }}" />
            <input type="hidden" name="route" value="plugin.goods-package.admin.package.edit" id="route" />
        @else
            <input type="hidden" name="route" value="plugin.goods-package.admin.package.create" id="route" />
        @endif
        <div class="top">
            <ul class="add-shopnav" id="myTab">
                <li class="active"><a href="#tab_basic">基本信息</a></li>
                <li><a href="#tab_share">分享设置</a></li>
                <li><a href="#tab_carousel">幻灯片</a></li>
                <li><a href="#tab_desc">套餐描述</a></li>
            </ul>
        </div>
        <div class="info">
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane  active" id="tab_basic">@include('Yunshop\GoodsPackage::admin.detail.basic')</div>
                    <div class="tab-pane" id="tab_share">@include('Yunshop\GoodsPackage::admin.detail.share')</div>
                    <div class="tab-pane" id="tab_carousel">@include('Yunshop\GoodsPackage::admin.carousel.carousel_list')</div>
                    <div class="tab-pane" id="tab_desc">@include('Yunshop\GoodsPackage::admin.detail.desc')</div>
                </div>
                <div class="form-group col-sm-12 mrleft40 border-t">
                    <input type="submit" name="submit" value="提交" onclick="return formCheck()"  class="btn btn-success"/> <!--  -->
                    <input type="hidden" name="token" value="{{$var['token']}}"/>
                    @section('back')
                        <a href="{{yzWebUrl('plugin.goods-package.admin.package.index')}}"><input type="button" name="back" style='margin-left:10px;' value="返回列表" class="btn btn-default" /></a>
                    @show
                </div>
            </div>
        </div>
    </div>
</form>
{{--</div>--}}

@endsection('content')
