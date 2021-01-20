@extends('layouts.base')
@section('title', trans('基础设置'))
@section('content')
    <script type="text/javascript">
        window.optionchanged = false;
        require(['bootstrap'], function () {
            $('#myTab a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
            })
        });
    </script>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div  class="panel panel-info">
                <ul class="add-shopnav" id="myTab">
                    <li class="active" ><a href="#tab_baseset">基础设置</a></li>
                    <li><a href="#tab_diyname">自定义名称</a></li>
                    {{--<li><a href="#tab_cash_back">返现设置</a></li>--}}
                </ul>
            </div>
            <form action="" method="post" class="form-horizontal"  id="form1">
                <div class="panel panel-info">
                    <div class="panel panel-default">
                        {{--<div class="panel-heading">基础设置</div>--}}
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane  active" id="tab_baseset">@include('Yunshop\Mryt::admin.base_set')</div>
                                <div class="tab-pane" id="tab_diyname">@include('Yunshop\Mryt::admin.diy_name')</div>
                                {{--<div class="tab-pane" id="tab_cash_back">@include('Yunshop\Mryt::admin.tpl.cash_back')</div>--}}
                            </div>
                        </div>


                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="submit" name="submit" value="保存设置" class="btn btn-primary" data-original-title="" title="">
                                    <input type="hidden" name="token" value="{$_W['token']}">
                                </div>
                            </div>

                    </div>
                </div>
            </form>
        </div>
    </div>


    <div id="modal-module-menus-coupon" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>选择优惠券</h3></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="input-group">
                            <input type="text" class="form-control" name="keyword" value="" id="search-kwd-coupon" placeholder="请输入优惠券名称"/>
                            <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_coupons();">搜索</button>
                        </span>
                        </div>
                    </div>
                    <div id="module-menus-coupon"></div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>

        </div>
    </div>
    <script>
        function dataIdentical(){
            $.get("{!! yzWebUrl('plugin.mryt.admin.data-identical.index') !!}", function(data) {
                alert(data);
            });
        }

        //优惠券模态框
        function showCouponModel(obj) {
            $('#modal-module-menus-coupon').modal();
        }

        //关闭优惠券模态框
        function removeRechargeItem(obj) {
            $(obj).closest('.recharge-item').remove();
        }

        //优惠券搜索
        function search_coupons() {
            if ($('#search-kwd-coupon').val() == '') {
                Tip.focus('#search-kwd-coupon', '请输入关键词');
                return;
            }
            $("#module-menus-coupon").html("正在搜索....");
            $.get("{!! yzWebUrl('coupon.coupon.get-search-coupons') !!}", {
                keyword: $.trim($('#search-kwd-coupon').val())
            }, function (dat) {
                $('#module-menus-coupon').html(dat);
            });
        }

        //选择优惠券
        function select_coupon(o) {
            //$("#coupon_id").val(o.id);
            //$("#coupon").val(o.name);
            $('.select_coupon_id').val(o.id);
            $('.select_coupon_name').val(o.name);
            $("#modal-module-menus-coupon .close").click();
            //console.log($(document).find('.recharge-item'));
            $(document).find('input').removeClass('select_coupon_id');
            $(document).find('input').removeClass('select_coupon_name');
        }

        $(function(){
            $(document).on('click', '.input-group-add', function() {
                showCouponModel($(this).get(0));
                $(this).parents('.recharge-item').find('input[name="set[coupon][coupon_id]"]').addClass('select_coupon_id');
                $(this).parents('.recharge-item').find('input[name="set[coupon][coupon_name]"]').addClass('select_coupon_name');
            });
        });
    </script>

    <script>
        function message_default(name) {
            var id = "#" + name;
            var setting_name = "plugin.mryt_set";
            var select_name = "select[name='set[" + name + "]']"
            var url_open = "{!! yzWebUrl('setting.default-notice.index') !!}"
            var url_close = "{!! yzWebUrl('setting.default-notice.cancel') !!}"
            var postdata = {
                notice_name: name,
                setting_name: setting_name
            };
            if ($(id).is(':checked')) {
                //开
                $.post(url_open,postdata,function(data){
                    if (data.result == 1) {
                        $(select_name).find("option:selected").val(data.id)
                        showPopover($(id),"开启成功")
                    } else {
                        showPopover($(id),"开启失败，请检查微信模版")
                        $(id).attr("checked",false);
                    }
                }, "json");
            } else {
                //关
                $.post(url_close,postdata,function(data){
                    $(select_name).val('');
                    showPopover($(id),"关闭成功")
                }, "json");
            }
        }
        function showPopover(target, msg) {
            target.attr("data-original-title", msg);
            $('[data-toggle="tooltip"]').tooltip();
            target.tooltip('show');
            target.focus();
            //2秒后消失提示框
            setTimeout(function () {
                    target.attr("data-original-title", "");
                    target.tooltip('hide');
                }, 2000
            );
        }
    </script>
    <script>
        $('.diy-notice').select2();
    </script>
@endsection
