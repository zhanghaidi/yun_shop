@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <form id="dataform" action="" method="post" class="form-horizontal form">

                <div class="right-titpos">
                    <ul class="add-snav">
                        <li class="active"><a href="#">
                                @if ($function == 'taobao')
                                    淘宝商品快速导入
                                @elseif ($function == 'jingdong')
                                    京东商品快速导入
                                @elseif ($function == 'alibaba')
                                    阿里巴巴商品快速导入
                                @elseif ($function == 'yzGoods')
                                    商品快速导入
                                @endif
                            </a></li>
                    </ul>
                </div>

                <div class="panel panel-default">
                <!--<div class="panel-heading">
                        @if ($function == 'taobao')
                    淘宝商品快速导入
                @elseif ($function == 'jingdong')
                    京东商品快速导入
                @elseif ($function == 'alibaba')
                    阿里巴巴商品快速导入
                @endif
                        </div>-->
                    <div class="panel-body">

                        <div class='alert-danger'>尽量在服务器空闲时间来操作，会占用大量内存与带宽，在获取过程中，请不要进行任何操作!</div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 链接</label>
                            <div class="col-sm-9">
                                <textarea style="width:600px;height:200px" id="url" name="url"
                                          class="form-control"></textarea>
                                @if ($function == 'taobao')

                                    <span class="help-block">商品链接, 例如: https://item.taobao.com/item.htm?id=xxxxxx 或 https://detail.tmall.com/item.htm?id=xxxxx</span>
                                    <span class="help-block">商品itemID, 上面链接中的 xxxxxxx</span>
                                    <span class="help-block">每一行一个itemID 或链接</span>

                                @elseif ($function == 'jingdong')

                                    <span class="help-block">例如商品链接为: https://item.jd.com/1856582.html,直接输入商品链接或输入商品ID:1856582</span>
                                    <span class="help-block">每行仅限输入一个链接或一个商品ID可多行输入</span>

                                @elseif ($function == 'alibaba')

                                    <span class="help-block">例如商品链接为: https://detail.1688.com/offer/527995131518.html,直接输入商品链接或输入商品ID:527995131518</span>
                                    <span class="help-block">每行仅限输入一个链接或一个商品ID可多行输入</span>
                                @elseif ($function == 'yzGoods')

                                    <span class="help-block">直接输入商品链接或输入商品ID:600</span>
                                    <span class="help-block">每行仅限输入一个链接或一个商品ID可多行输入</span>
                                @endif

                            </div>
                        </div>
                        @if ($function == 'taobao')
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>商品类型</label>
                            <div class="col-sm-8 col-xs-12">
                                <input type="radio" name="goodsType" value="taobao" />淘宝商品
                                <input type="radio" name="goodsType" value="tmall" />天猫商品
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>商品分类</label>
                            <div class="col-sm-8 col-xs-12">

                                {!!$catetory_menus!!}

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"> </label>
                            <div class="col-sm-9">
                                <span class="help-block">此分类读取的是商城的商品分类, 设置默认抓取商品的分类</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"> </label>
                    <div class="col-sm-9">
                        <input id="btn_submit" type="button" value="立即采集" class="btn btn-primary"
                               onclick="formcheck()"/>
                        <input id="function" type="hidden"
                               value="@if($function == 'taobao') {!!  yzWebUrl('plugin.goods-assistant.admin.import.taobao')  !!} @elseif($function == 'jingdong'){!!  yzWebUrl('plugin.goods-assistant.admin.import.jingdong')  !!} @elseif($function == 'alibaba'){!! yzWebUrl('plugin.goods-assistant.admin.import.alibaba') !!}@elseif($function == 'yzGoods'){!! yzWebUrl('plugin.goods-assistant.admin.import-yz.get-yz-goods') !!}@endif"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        var len = 0;
        var urls = [];
        var total = 0;
        var posturl = $("#function").val();
        function formcheck() {

            if ($(":input[name='url']").val() == '') {
                alert('请输入商品链接或itemId');
                return;
            }
            @if ($shopset['cat_level'] == 3)
            if ($('#category_third').val() == '0') {
                alert('请选择完整宝贝分类');
                return;
            }
            @else
            if ($('#category_child').val() == '0') {
                alert('请选择完整宝贝分类');
                return;
            }
            @endif
            $("#dataform").attr("disabled", "true");
            $("#btn_submit").val("正在获取中...").removeClass("btn-primary").attr("disabled", "true");

            urls = $("#url").val().split('\n');
            total = urls.length;
            $("#btn_submit").val("检测到需要获取 " + total + " 个宝贝, 请等待开始....");
            fetch_next();
            return;
        }
        function fetch_next() {
            var postdata = {
                url: urls[len],
                parentId: $("select[name='category[parentid]']").val(),
                childId: $("select[name='category[childid]']").val(),
                goodsType: $(":input[name='goodsType']:checked").val(),
            };
            //console.log(postdata);
            @if ($shopset['cat_level'] == 3)
                postdata.thirdId = $("select[name='category[thirdid]']").val();
            @endif
            $.post(posturl,
                postdata,
                function (data) {
                    len++;
                    if (data.result == 0) {
                        if (confirm('第' + len + '个链接未采集到内容,请确认采集地址的正确!')) {
                            location.reload();
                        }
                        return false;
                    }
                    $("#btn_submit").val("已经获取  " + len + " / " + total + " 个宝贝, 请等待....");

                    if (len >= total) {
                        $("#btn_submit").val("立即获取").addClass("btn-primary").removeAttr("disabled");
                        if (confirm('商品已经获取成功')) {
                            location.reload();
                        } else {
                            location.reload();
                        }
                    } else {
                        fetch_next();
                    }

                }, "json");
        }

    </script>
@endsection

