@extends('layouts.base')

@section('content')
@section('title', trans('商品列表'))
    <div class="w1200 ">


        <script type="text/javascript" src="./resource/js/lib/jquery-ui-1.10.3.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{{static_url('css/font-awesome.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>

        <div id="goods-index" class=" rightlist ">
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">门店商品列表</a></li>
                </ul>
            </div>

            <div class="right-addbox"><!-- 此处是右侧内容新包一层div -->

                <div class="panel panel-info">
                    <div class="panel-body">
                        <form action="" method="post" class="form-horizontal" role="form">
                            <input type="hidden" name="c" value="site"/>
                            <input type="hidden" name="a" value="entry"/>
                            <input type="hidden" name="m" value="yun_shop"/>
                            <input type="hidden" name="do" value="shop"/>
                            <input type="hidden" name="p" value="goods"/>
                            @section('search')
                                <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                                    <div class="">
                                        <input class="form-control" placeholder="门店ID" name="store_id" id=""
                                               type="text" value="{{$store_id}}" />
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                                    <div class="">
                                        <input class="form-control" placeholder="请输入关键字" name="search[keyword]" id=""
                                               type="text" value="{{$requestSearch['keyword']}}" />
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                                    <div class="">
                                        <select name="search[status]" class='form-control'>
                                            <option value="">状态不限</option>

                                            <option value="1"
                                                    @if($requestSearch['status'] == '1') selected @endif>{{$lang['putaway']}}</option>
                                            <option value="0"
                                                    @if($requestSearch['status'] == '0') selected @endif>{{$lang['soldout']}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                                    <div class="">
                                        <select name="search[sell_stock]" class='form-control'>
                                            <option value="">售中库存</option>

                                            <option value="1"
                                                    @if($requestSearch['sell_stock'] == '1') selected @endif>{{$lang['yes_stock']}}</option>
                                            <option value="0"
                                                    @if($requestSearch['sell_stock'] == '0') selected @endif>{{$lang['no_stock']}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-8 col-lg-5">
                                    <div class="col-sm-12 col-xs-12">
                                        {!!$catetory_menus!!}
                                    </div>
                                </div>

                                <div class="form-group col-xs-12 col-sm-8 col-lg-3">
                                    <div class="col-sm-8 col-xs-12">
                                        <select name="search[brand_id]" id="brand">
                                            <option value="">请选择品牌</option>
                                            @if(!empty($brands))
                                                @foreach($brands as $brand)
                                                    <option value="{{$brand['id']}}"
                                                            @if($requestSearch['brand_id'] == $brand['id']) selected @endif>{{$brand['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class='form-input col-xs-12 col-sm-8 col-lg-6'>
                                    <p class="input-group-addon price">价格区间</p>
                                    <input class="form-control price" name="search[min_price]" id="minprice" type="text"
                                           value="" onclick="value='';" />
                                    <p class="line">—</p>
                                    <input class="form-control price" name="search[max_price]" id="max_price"
                                           type="text" value="" onclick="value='';" />
                                </div>

                                <div class="form-group col-xs-12 col-sm-8 col-lg-5 goods-type">
                                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label goods-type">商品类型</label>

                                    <div class="col-xs-12 col-sm-8 col-lg-9">
                                        @foreach($product_attr_list as $product_attr_key => $product_attr_name)
                                            <label for="{$product_attr_key}">
                                                <input type="checkbox"
                                                       @if(@in_array($product_attr_key, $product_attr)) checked="checked"
                                                       @endif name="search[product_attr][]"
                                                       value="{{$product_attr_key}}" id="{{$product_attr_key}}"/>
                                                {{$product_attr_name}}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @show
                            <div class="form-group col-xs-8 col-sm-8 col-lg-1">
                                <button class="btn btn-block btn-success"><i class="fa fa-search"></i> 搜索</button>
                            </div>

                        </form>
                    </div>
                </div>

                <style type="text/css">

                </style>
                <form id="goods-list" action="" method="post">
                    <div class="panel panel-default">
                        <div class="panel-body table-responsive">
                            <label class="btn btn-success checkall">全选</label>
                            <label class="btn btn-info batchenable">批量上架</label>
                            <label class="btn batchdisable">批量下架</label>
                            <table class="table table-hover">
                                <thead class="navbar-inner">
                                <tr>
                                    <th width="3%">选择</th>
                                    <th width="6%">ID</th>
                                    <th width="6%">排序</th>
                                    <th width="5%">{{$lang['good']}}</th>
                                    <th width="26%">&nbsp;</th>
                                    <th width="12%">{{$lang['price']}}<br/>{{$lang['repertory']}}</th>

                                    <th width="10%">销量</th>

                                    <th width="16%">@section('status')状态@show</th>

                                    <th width="18%">操作</th>
                                </tr>
                                </thead>
                                <tbody>

                                @section('foreach')
                                @foreach($list as $item)

                                    <tr>
                                        <td width="3%"><input type="checkbox" name="check1" value="{{$item['id']}}"></td>
                                        <td width="6%">{{$item['id']}}</td>
                                        <td width="6%">
                                            {{$item['display_order']}}
                                        </td>
                                        <td width="6%" title="{{$item['title']}}">
                                            <img src="{{tomedia($item['thumb'])}}"
                                                 style="width:40px;height:40px;padding:1px;border:1px solid #ccc;"/>
                                        </td>
                                        <td  width="26%">
                                            {{$item['title']}}

                                        </td>
                                        <td  width="16%">
                                            {{$item['price']}}
                                            <br/>
                                            {{$item['stock']}}
                                        </td>

                                        <td>{{$item['real_sales']}}</td>
                                        <td>

                                            <label data='{{$item['status']}}'
                                                   class='label  label-default @if($item['status']==1) label-info @endif'
                                                   onclick="setProperty(this, {{$item['id']}},'status')">
                                                @if($item['status']==1)
                                                    {{$lang['putaway']}}
                                                @else
                                                    {{$lang['soldout']}}
                                                @endif
                                            </label>
                                            <br>
                                            <label class='label  label-default label-info'>
                                                门店:{{$item['store_name']}}<br>
                                                ID:{{$item['store_id']}}
                                            </label>
                                        </td>

                                        <td style="position:relative; overflow:visible;" width="20%">
                                            <!-- yitian_add::商品链接二维码 2017-02-07 qq:751818588 -->
                                            <a class="btn btn-sm btn-default umphp" title="商品二维码"
                                               data-url="{{yzAppFullUrl('goods/'.$item['id'].'/o2o/'.$item['store_id'].'&i='.\YunShop::app()->uniacid.'&mid='.$item['store_uid'])}}"
                                               data-goodsid="{{$item['id']}}">
                                                <div class="img">
                                                    {!! QrCode::size(120)->generate(yzAppFullUrl('goods/'.$item['id'].'/o2o/'.$item['store_id'].'&i='.\YunShop::app()->uniacid.'&mid='.$item['store_uid'])) !!}
                                                </div>
                                                <i class="fa fa-qrcode"></i>
                                            </a>

                                            <a download="{{$item['img_name']}}" href="{{$item['download_url']}}" title="下载二维码" class="btn btn-default btn-sm js-clip"><i class="fa  fa-file-image-o"></i></a>

                                        </td>
                                    </tr>
                                    @endforeach
                                    @show
                                    </tr>
                                </tbody>
                            </table>
                            <label class="btn btn-success checkall">全选</label>
                            <label class="btn btn-info batchenable">批量上架</label>
                            <label class="btn batchdisable">批量下架</label>
                            {!!$pager!!}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
<script>
    $(function(){
        $(".checkall").click(function(){
            //全选
            if($(this).html() == '全选') {
                $(this).html('全不选');
                $('[name=check1]:checkbox').prop('checked',true);
            } else {
                $(this).html('全选');
                $('[name=check1]:checkbox').prop('checked',false);
            }
        });
        $(".checkrev").click(function(){
            //反选
            $('[name=check1]:checkbox').each(function(){
                this.checked=!this.checked;
            });
        });

        var arr = new Array();
        var url = "{!! yzWebUrl('goods.goods.batchSetProperty') !!}"

        $(".batchenable").click(function () {
            $(this).html('上架中...');
            $("[name=check1]:checkbox:checked").each(function(i){
                arr[i] = $(this).val();
            });
            $.post(url, {ids: arr, data: 1}
                , function (d) {
                    if (d.result) {
                        $(".batchenable").html('上架成功');
                        setTimeout(location.reload(), 3000);
                    }
                } , "json"
            );
        });
        $(".batchdisable").click(function () {
            $(this).html('下架中...');
            $("[name=check1]:checkbox:checked").each(function(i){
                arr[i] = $(this).val();
            });
            $.post(url, {ids: arr, data: 0}
                , function (d) {
                    if (d.result) {
                        $(".batchdisable").html('下架成功');
                        setTimeout(location.reload(), 3000);
                    }
                } , "json"
            );
        });

        $(".batchdel").click(function () {
            $(this).html('删除中...');
            $("input[type='checkbox']:checked").each(function(i){
                arr[i] = $(this).val();
            });
            $.post("{!! yzWebUrl('goods.goods.batchDestroy') !!}", {ids: arr}
                , function (d) {
                    if (d.result) {
                        $(".batchdel").html('删除成功');
                        setTimeout(location.reload(), 3000);
                    }
                } , "json"
            );
        })

    });
</script>
    <script type="text/javascript">
        $('.js-clip').each(function () {
            util.clip(this, $(this).attr('data-url'));
        });
        //鼠标划过显示商品链接二维码
        $('.umphp').hover(function () {
                    var url = $(this).attr('data-url');
                    $(this).addClass("selected");
                },
                function () {
                    $(this).removeClass("selected");
                })
        function fastChange(id, type, value) {
            $.ajax({
                url: "{!! yzWebUrl('plugin.store-cashier.admin.goods.change') !!}",
                type: "post",
                data: {id: id, type: type, value: value},
                cache: false,
                success: function ($data) {
                    //console.log($data);
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
                    if (type == 'title' && val.length >= '40') {
                        Tip.show(group.find(':input'), '名称不能大于40字符!');
                        return;
                    }
                    group.prev().show().find('span').html(val);
                    group.hide();
                    fastChange(goodsid, type, val);
                });
            })
        })
        @section('supplier_js')
        function setProperty(obj, id, type) {
            $(obj).html($(obj).html() + "...");
            $.post("{!! yzWebUrl('plugin.store-cashier.admin.goods.setProperty') !!}", {id: id, type: type, data: obj.getAttribute("data")}
                    , function (d) {
                        console.log(d);
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
        @show
        require(['select2'], function () {
            $('#brand').select2();
        })
    </script>

@endsection('content')