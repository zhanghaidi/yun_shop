    @extends('Yunshop\Supplier::supplier.layouts.base')

@section('isputaway')

@endsection

@section('content')
@section('title', trans('商品详情'))
<script type="text/javascript">
    window.type = "{{$goods['type']}}";
    window.virtual = "{{$goods['virtual']}}";

    $(function () {

        $(':radio[name=type]').click(function () {
            window.type = $("input[name='type']:checked").val();
            window.virtual = $("#virtual").val();
            if (window.type == '1') {
                $('#dispatch_info').show();
            } else {
                $('#dispatch_info').hide();
            }
            if (window.type == '3') {
                if ($('#virtual').val() == '0') {
                    $('.choosetemp').show();
                }
            }
        })

        $("input[name='back']").click(function () {
            location.href = "{!! yzWebUrl('plugin.supplier.supplier.controllers.goods.supplier-goods-list.index') !!}";
        });
    })

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
    })

    function formcheck() {

        window.type = $("input[name='goods[type]']:checked").val();
        window.virtual = $("#virtual").val();
        var reg = /(^[-+]?[1-9]\d*(\.\d{1,2})?$)|(^[-+]?[0]{1}(\.\d{1,2})?$)/; //金额字段验证,后两位小数
        var numerictype = /^(0|[1-9]\d*)$/; //整数验证
        var thumb = /\.(gif|jpg|jpeg|png|GIF|JPG|PNG)$/;
        var datetime = /(\d{2}|\d{4})(?:\-)?([0]{1}\d{1}|[1]{1}[0-2]{1})(?:\-)?([0-2]{1}\d{1}|[3]{1}[0-1]{1})(?:\s)?([0-1]{1}\d{1}|[2]{1}[0-3]{1})(?::)?([0-5]{1}\d{1})(?::)?([0-5]{1}\d{1})/;
        if ($(':input[name="goods[title]"]').val() == '') {
            $('#myTab a[href="#tab_basic"]').tab('show');
            Tip.focus("#goodsname", "请输入商品名称!");
            return false;
        }
        if ($(':input[name="goods[title]"]').val().length >= '40') {
            $('#myTab a[href="#tab_basic"]').tab('show');
            Tip.focus("#goodsname", "商品名称不能超过40个字符!");
            return false;
        }
        if ($(':input[name="category[parentid][]"]').val() == 0) {
            $('#myTab a[href="#tab_basic"]').tab('show');
            Tip.focus(':input[name="category[parentid][]"]', "请选择一级分类!");
            return false;
        }
        if ($(':input[name="category[childid][]"]').val() == 0) {
            $('#myTab a[href="#tab_basic"]').tab('show');
            Tip.focus(':input[name="category[childid][]"]', "请选择二级分类!");
            return false;
        }
        @if($shopset['cat_level'] == 3)
        if ($(':input[name="category[thirdid][]"]').val() == 0) {
            $('#myTab a[href="#tab_basic"]').tab('show');
            Tip.focus(':input[name="category[thirdid][]"]', "请选择三级分类!");
            return false;
        }
        @endif
        if ($(':input[name="goods[sku]"]').val() == '') {
            $('#myTab a[href="#tab_basic"]').tab('show');
            Tip.focus(':input[name="goods[sku]"]', "请输入商品单位!");
            return false;
        }

        @if (empty($id))
        if ($.trim($(':input[name="goods[thumb]"]').val()) == '') {
            $('#myTab a[href="#tab_basic"]').tab('show');
            Tip.focus(':input[name="goods[thumb]"]', '请上传缩略图.');
            return false;
        } else {
            if(!thumb.test($.trim($(':input[name="goods[thumb]"]').val())))
            {
                $('#myTab a[href="#tab_basic"]').tab('show');
                Tip.focus(':input[name="goods[thumb]"]', '图片类型必须是.gif,jpeg,jpg,png中的一种.');
                return false;
            }
        }

        @endif

        // if ($.trim($(':input[name="goods[price]"]').val()) == '') {
        //     $('#myTab a[href="#tab_basic"]').tab('show');
        //     Tip.focus(':input[name="goods[price]"]', '请填写价格.');
        //     return false;
        // } else {
        //     if (!reg.test($(':input[name="goods[price]"]').val())) {
        //         $('#myTab a[href="#tab_basic"]').tab('show');
        //         Tip.focus(':input[name="goods[price]"]', '价格格式错误,最多两位小数.');
        //         return false;
        //     }
        // }
        // if ($.trim($(':input[name="goods[market_price]"]').val()) == '') {
        //     $('#myTab a[href="#tab_basic"]').tab('show');
        //     Tip.focus(':input[name="goods[market_price]"]', '请填写价格.');
        //     return false;
        // } else {
        //     if (!reg.test($(':input[name="goods[market_price]"]').val())) {
        //         $('#myTab a[href="#tab_basic"]').tab('show');
        //         Tip.focus(':input[name="goods[market_price]"]', '价格格式错误,最多两位小数.');
        //         return false;
        //     }
        // }
        // if ($.trim($(':input[name="goods[cost_price]"]').val()) == '') {
        //     $('#myTab a[href="#tab_basic"]').tab('show');
        //     Tip.focus(':input[name="goods[cost_price]"]', '请填写价格.');
        //     return false;
        // } else {
        //     if (!reg.test($(':input[name="goods[cost_price]"]').val())) {
        //         $('#myTab a[href="#tab_basic"]').tab('show');
        //         Tip.focus(':input[name="goods[cost_price]"]', '价格格式错误,最多两位小数.');
        //         return false;
        //     }
        // }

        if ($(':input[name="goods[stock]"]').val() == '') {
            $('#myTab a[href="#tab_basic"]').tab('show');
            Tip.focus(':input[name="goods[stock]"]', "请输入库存!");
            return false;
        } else {
            if (!numerictype.test($(':input[name="goods[stock]"]').val())) {
                $('#myTab a[href="#tab_basic"]').tab('show');
                Tip.focus(':input[name="goods[stock]"]', '库存格式错误,只能为非负整数.');
                return false;
            }
        }

        if ($(':input[name="widgets[dispatch][dispatch_price]"]').val() == '') {
            $('#myTab a[href="#tab_dispatch"]').tab('show');
            Tip.focus(':input[name="widgets[dispatch][dispatch_price]"]', "请输入统一邮费金额!");
            return false;
        } else {
            if (!reg.test($(':input[name="widgets[dispatch][dispatch_price]"]').val())) {
                $('#myTab a[href="#tab_dispatch"]').tab('show');
                Tip.focus(':input[name="widgets[dispatch][dispatch_price]"]', '统一邮费金额数值格式错误,最多两位小数.');
                return false;
            }
        }

        var full = true;
        if (window.type == '3') {
            if (window.virtual != '0') {  //如果单规格，不能有规格
                if ($('#hasoption').get(0).checked) {
                    $('#myTab a[href="#tab_option"]').tab('show');
                    util.message('您的商品类型为：虚拟物品(卡密)的单规格形式，需要关闭商品规格！');
                    return false;
                }
            }
            else {

                var has = false;
                $('.spec_item_virtual').each(function () {
                    has = true;
                    if ($(this).val() == '' || $(this).val() == '0') {
                        $('#myTab a[href="#tab_option"]').tab('show');
                        Tip.focus($(this).next(), '请选择虚拟物品模板!');
                        full = false;
                        return false;
                    }
                });
                if (!has) {
                    $('#myTab a[href="#tab_option"]').tab('show');
                    util.message('您的商品类型为：虚拟物品(卡密)的多规格形式，请添加规格！');
                    return false;
                }
            }
        }
        if (!full) {
            return false;
        }

        full = checkoption();
        if (!full) {
            return false;
        }
        if (optionchanged) {
            $('#myTab a[href="#tab_option"]').tab('show');
            alert('规格数据有变动，请重新点击 [刷新规格项目表] 按钮!');
            return false;
        }
        var discountway = $('input:radio[name=discountway]:checked').val();
        var discounttype = $('input:radio[name=discounttype]:checked').val();
        var returntype = $('input:radio[name=returntype]:checked').val();
        var marketprice = $('input:text[name=marketprice]').val();
        var isreturn = false;

        // Tip.focus("#goodsname", "请输入商品名称!");
        // 		return false;

        if (discountway == 1) {
            if (discounttype == 1) {
                $(".discounts").each(function () {
                    if (parseFloat($(this).val()) <= 0 || parseFloat($(this).val()) >= 10) {
                        $(this).val('');
                        isreturn = true;
                        alert('请输入正确折扣！');
                        return false;
                    }
                });
            } else {
                $(".discounts2").each(function () {
                    if (parseFloat($(this).val()) <= 0 || parseFloat($(this).val()) >= 10) {
                        $(this).val('');
                        isreturn = true;
                        alert('请输入正确折扣！');
                        return false;
                    }
                });
            }


        } else {
            if (discounttype == 1) {
                $(".discounts").each(function () {
                    if (parseFloat($(this).val()) < 0 || parseFloat($(this).val()) >= parseFloat(marketprice)) {
                        $(this).val('');
                        isreturn = true;
                        alert('请输入正确折扣金额！');
                        return false;
                    }
                });
            } else {
                $(".discounts2").each(function () {
                    if (parseFloat($(this).val()) < 0 || parseFloat($(this).val()) >= parseFloat(marketprice)) {
                        $(this).val('');
                        isreturn = true;
                        alert('请输入正确折扣金额！');
                        return false;
                    }
                });
            }


        }
        if (returntype == 1) {
            $(".returns").each(function () {
                if (parseFloat($(this).val()) < 0 || parseFloat($(this).val()) >= parseFloat(marketprice)) {
                    $(this).val('');
                    isreturn = true;
                    alert('请输入正确返现金额！');
                    return false;
                }
            });
        } else {
            $(".returns2").each(function () {
                if (parseFloat($(this).val()) < 0 || parseFloat($(this).val()) >= parseFloat(marketprice)) {
                    $(this).val('');
                    isreturn = true;
                    alert('请输入正确返现金额！');
                    return false;
                }
            });
        }


        if (isreturn) {
            return false;
        }
        return true;

    }

    function checkoption() {

        var full = true;
        if ($("#hasoption").get(0).checked) {
            $(".spec_title").each(function (i) {
                if ($(this).isEmpty()) {
                    $('#myTab a[href="#tab_option"]').tab('show');
                    Tip.focus(".spec_title:eq(" + i + ")", "请输入规格名称!", "top");
                    full = false;
                    return false;
                }
            });
            $(".spec_item_title").each(function (i) {
                if ($(this).isEmpty()) {
                    $('#myTab a[href="#tab_option"]').tab('show');
                    Tip.focus(".spec_item_title:eq(" + i + ")", "请输入规格项名称!", "top");
                    full = false;
                    return false;
                }
            });
        }
        if (!full) {
            return false;
        }
        return full;
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
    function setProperty(obj, id, type) {
        $(obj).html($(obj).html() + "...");
        $.post("{!! yzWebUrl('goods.goods.index') !!}"
                , {'op': 'setgoodsproperty', id: id, type: type, plugin: "", data: obj.getAttribute("data")}
                , function (d) {
                    $(obj).html($(obj).html().replace("...", ""));
                    if (type == 'type') {
                        $(obj).html(d.data == '1' ? '实体物品' : '虚拟物品');
                    }
                    if (type == 'status') {
                        $(obj).html(d.data == '1' ? '{{$lang['putaway']}}' : '{{$lang['soldout']}}');
                    }
                    $(obj).attr("data", d.data);
                    if (d.result == 1) {
                        $(obj).toggleClass("label-info text-pinfo");
                    }
                }
                , "json"
        );
    }

</script>




<link rel="stylesheet" type="text/css" href="{{static_url('css/font-awesome.min.css')}}">
<link href="{{static_url('yunshop/goods/goods.css')}}" media="all" rel="stylesheet" type="text/css"/>
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">商品编辑</a></li>
    </ul>
</div>
{{--<div class="main rightlist">--}}


<form id="goods-edit"  action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
    <div class="panel-default panel-center">

        <!--             <div class="panel-heading">
                        {if empty($goods['id'])}添加商品{else}编辑商品{/if}
                    </div> -->

        <div  class="top">
            <ul class="add-shopnav" id="myTab">
                <li class="active" ><a href="#tab_basic">基本信息</a></li>
                <li><a href="#tab_des">{{$lang['shopdesc']}}</a></li>
                <li><a href="#tab_param">属性</a></li>
                <li><a href="#tab_sale">营销</a></li>
                <li><a href="#tab_option">{{$lang['shopoption']}}</a></li>
                <li><a href="#tab_supplier_dispatch">供应商配送</a></li>
                <li><a href="#tab_supplier_notice">消息通知</a></li>
                <li><a href="#tab_share">分享关注</a></li>

                @foreach(\app\common\modules\widget\Widget::current()->getItem('goods') as $key=>$value)
                    @if ($key == 'tb_limitbuy')
                        <li><a href="#{{$key}}">{{$value['title']}}</a></li>
                    @endif
                @endforeach
            </ul>
        </div>
        <div class="info" >
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane  active" id="tab_basic">@include('goods.basic')</div>
                    <div class="tab-pane" id="tab_des">@include('goods.des')</div>
                    <div class="tab-pane" id="tab_param">@include('goods.tpl.param')</div>
                    <div class="tab-pane" id="tab_sale">{!! widget('Yunshop\Supplier\supplier\controllers\goods\SaleWidget', ['goods_id'=> $goods->id])!!}</div>
                    <div class="tab-pane" id="tab_option">@include('goods.tpl.option')</div>
                    <div class="tab-pane" id="tab_supplier_dispatch">{!! widget('Yunshop\Supplier\supplier\controllers\goods\GoodsWidget', ['goods_id'=> $goods->id])!!}</div>
                    <div class="tab-pane" id="tab_supplier_notice">{!! widget('Yunshop\Supplier\supplier\controllers\goods\NoticeWidget', ['goods_id'=> $goods->id])!!}</div>
                    <div class="tab-pane" id="tab_share">{!! widget('app\backend\widgets\goods\ShareWidget', ['goods_id'=> $goods->id])!!}</div>
                    @if (app('plugins')->isEnabled('merchant'))
                        <div class="tab-pane" id="merchant">
                            {!! widget('Yunshop\Merchant\widgets\MerchantGoodsWidget', ['goods_id'=> $goods->id])!!}
                        </div>
                    @endif
                    @foreach(\app\common\modules\widget\Widget::current()->getItem('goods') as $key=>$value)
                        @if ($key == 'tb_limitbuy')
                            <div class="tab-pane"
                                 id="{{$key}}">{!! widget($value['class'], ['goods_id'=> $goods->id])!!}</div>
                        @endif
                        @if ($key == 'tab_supplier_commission')
                            <div class="tab-pane"
                                 id="{{$key}}">{!! widget($value['class'], ['goods_id'=> $goods->id])!!}</div>
                        @endif
                        @if ($key == 'tab_supplier_team_dividend')
                            <div class="tab-pane"
                                 id="{{$key}}">{!! widget($value['class'], ['goods_id'=> $goods->id])!!}</div>
                        @endif
                        @if ($key == 'tab_supplier_area_dividend')
                            <div class="tab-pane"
                                 id="{{$key}}">{!! widget($value['class'], ['goods_id'=> $goods->id])!!}</div>
                        @endif
                        @if ($key == 'merchant')
                            <div class="tab-pane"
                                 id="{{$key}}">{!! widget($value['class'], ['goods_id'=> $goods->id])!!}</div>
                        @endif
                        @if ($key == 'tab_room')
                            <div class="tab-pane"
                                 id="{{$key}}">{!! widget($value['class'], ['goods_id'=> $goods->id])!!}</div>
                        @endif
                    @endforeach
                </div>
                <div class="form-group col-sm-12 mrleft40 border-t" >
                    <input type="submit" name="submit" value="{{$lang['shopsubmit']}}" class="btn btn-success" onclick="return formcheck()"  />
                    <input type="hidden" name="token" value="{{$var['token']}}" />
                    <input type="button" name="back" value="返回列表" class="btn btn-default back"/>
                </div>
            </div>
        </div>
    </div>
</form>
{{--</div>--}}

@endsection('content')
