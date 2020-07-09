@extends('layouts.base')

@section('content')
@section('title', trans('分销基础设置'))
    <section class="content">

        <form id="setform" action="" method="post" class="form-horizontal form">

            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">分销设置</a></li>
                </ul>
            </div>
            @include('Yunshop\Commission::admin.tabs')

            <div class='panel panel-default'>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启分销</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[is_commission]" value="0"
                                       @if($set['is_commission'] == 0)
                                checked="checked" @endif />
                                关闭</label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[is_commission]" value="1"
                                       @if($set['is_commission'] == 1)
                                checked="checked" @endif />
                                开启</label>
                            <span class='help-block'>开启分销商需要在会员设置中开启会员关系链</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">等级升级设置</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[is_with]" value="0"
                                       @if($set['is_with'] == 0)
                                       checked="checked" @endif />
                                或</label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[is_with]" value="1"
                                       @if($set['is_with'] == 1)
                                       checked="checked" @endif />
                                与</label>
                            <span class='help-block'><b>[或]</b>满足任意条件都可以升级<br><b>[与]</b>满足所有条件才可以升级(ps:其中下级粉丝人数和下级分销商人数是上级分销商的升级依据,并隐藏<b>[购买指定商品]</b><b>[购买指定商品之一]</b>升级方式)</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销层级</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="setdata[level]" id="commission_level" onchange="commissionLevel()">
                                <option value="1" @if(isset($set['level']) && $set['level']==1) selected @endif>一级分销
                                </option>
                                <option value="2" @if(isset($set['level']) && $set['level']==2) selected @endif>二级分销
                                </option>
                                {{--<option value="3" @if(isset($set['level']) && $set['level']==3) selected @endif>三级分销--}}
                                {{--</option>--}}
                            </select>
                        </div>
                    </div>
                    @if($set['level']>=1)
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级分销比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="setdata[first_level]" class="form-control"
                                       value="@if(isset($set['first_level'])){{$set['first_level']}}@endif"/>
                            </div>
                        </div>
                    @endif
                    @if($set['level']>=2)
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">二级分销比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="setdata[second_level]" class="form-control"
                                       value="@if(isset($set['second_level'])){{$set['second_level']}}@endif"/>
                            </div>
                        </div>
                    @endif
                    {{--@if($set['level']>=3)--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">三级分销比例</label>--}}
                            {{--<div class="col-sm-9 col-xs-12">--}}
                                {{--<input type="text" name="setdata[third_level]" class="form-control"--}}
                                       {{--value="@if(isset($set['third_level'])){{$set['third_level']}}@endif"/>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--@endif--}}

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">限制提现</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[no_withdraw]" value="0"
                                       @if($set['no_withdraw'] == 0)
                                       checked="checked" @endif />
                                关闭</label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[no_withdraw]" value="1"
                                       @if($set['no_withdraw'] == 1)
                                       checked="checked" @endif />
                                开启</label>
                            <span class='help-block'>开启则默认等级不可提现</span>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销内购</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[self_buy]" value="0"
                                       @if($set['self_buy'] == 0) checked="checked" @endif />
                                关闭
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[self_buy]" value="1"
                                       @if(isset($set['self_buy']) && $set['self_buy'] == 1) checked="checked" @endif/>
                                开启
                            </label>
                            {{--<span class='help-block'>开启分销内购，分销商自己购买商品，享受一级佣金，上级享受二级佣金，上上级享受三级佣金</span>--}}
                            <span class='help-block'>开启分销内购，分销商自己购买商品，享受一级佣金，上级享受二级佣金</span>
                        </div>
                    </div>

                </div>

                <div class='panel-heading'>
                    结算设置
                </div>
                <div class='panel-body'>

                    {{--<div class="form-group">--}}
                        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">提现额度</label>--}}
                        {{--<div class="col-sm-9 col-xs-12">--}}
                            {{--<input type="text" name="setdata[roll_out_limit]" class="form-control"--}}
                                   {{--value="@if(isset($set['roll_out_limit'])){{$set['roll_out_limit']}}@endif"/>--}}
                            {{--<span class="help-block">当前代理的佣金达到此额度时才能提现</span>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算类型</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[settlement_model]" value="0"
                                       @if($set['settlement_model'] == 0) checked="checked" @endif />自动结算</label>
                            <label class="radio-inline" style="margin-left: 35px">
                                <input type="radio" name="setdata[settlement_model]" value="1"
                                       @if($set['settlement_model'] == 1) checked="checked" @endif /> 手动结算</label>
                            <span style="" class='help-block'>
                                自动结算：订单完成后，根据结算期时间来加入到提现<br />
                                手动结算：订单完成后，需要进入推广中心手动领取才可以提现
                            </span>

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算选项</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[settlement_option]" value="0"
                                       @if($set['settlement_option'] == 0) checked="checked" @endif />收入提现</label>
                            <label class="radio-inline" style="margin-left: 35px">
                                <input type="radio" name="setdata[settlement_option]" value="1"
                                       @if($set['settlement_option'] == 1) checked="checked" @endif /> 转入积分</label>
                            <span style="" class='help-block'>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算事件</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[settlement_event]" value="0"
                                       @if(!$set['settlement_event'])
                                       checked="checked" @endif />
                                订单完成后</label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[settlement_event]" value="1"
                                       @if($set['settlement_event'] == 1)
                                       checked="checked" @endif />
                                订单支付后</label>
                            <span class='help-block'>默认[订单完成后]分销订单进入结算计算(ps:计算结算期)</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算天数</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="setdata[settle_days]" class="form-control"
                                   value="@if(isset($set['settle_days'])){{$set['settle_days']}}@endif"/>
                            <span class="help-block">当订单完成后的n天后，佣金才能申请提现</span>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">佣金计算方式-增加项目</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="checkbox" name="setdata[culate_method_plus][actual]" value="1"
                                       @if(isset($set['culate_method_plus']['actual']) && $set['culate_method_plus']['actual']) checked="checked" @endif/>
                                商品实际支付金额
                            </label>
                            <label class="radio-inline">
                                <input type="checkbox" name="setdata[culate_method_plus][freight]" value="1"
                                       @if(isset($set['culate_method_plus']['freight']) && $set['culate_method_plus']['freight']) checked="checked" @endif/>
                                运费
                            </label>
                            {{--<label class="radio-inline">--}}
                                {{--<input type="checkbox" name="setdata[culate_method_plus][point]" value="1"--}}
                                       {{--@if(isset($set['culate_method_plus']['point']) && $set['culate_method_plus']['point']) checked="checked" @endif/>--}}
                                {{--积分抵扣--}}
                            {{--</label>--}}
                            {{--<label class="radio-inline">--}}
                                {{--<input type="checkbox" name="setdata[culate_method_plus][balance]" value="1"--}}
                                       {{--@if(isset($set['culate_method_plus']['balance']) && $set['culate_method_plus']['balance']) checked="checked" @endif/>--}}
                                {{--余额抵扣--}}
                            {{--</label>--}}
                            <label class="radio-inline">
                                <input type="checkbox" name="setdata[culate_method_plus][market_price]" value="1"
                                       @if(isset($set['culate_method_plus']['market_price']) && $set['culate_method_plus']['market_price']) checked="checked" @endif/>
                                商品现价
                            </label>
                            <label class="radio-inline">
                                <input type="checkbox" name="setdata[culate_method_plus][price]" value="1"
                                       @if(isset($set['culate_method_plus']['price']) && $set['culate_method_plus']['price']) checked="checked" @endif/>
                                商品原价
                            </label>
                            <label class="radio-inline">
                                <input type="checkbox" name="setdata[culate_method_plus][cost_price]" value="1"
                                       @if(isset($set['culate_method_plus']['cost_price']) && $set['culate_method_plus']['cost_price']) checked="checked" @endif/>
                                商品成本
                            </label>
                            @if(YunShop::plugin()->get('coin'))
                                <label class="radio-inline">
                                    <input type="checkbox" name="setdata[culate_method_plus][coin_deduction]" value="1"
                                           @if(isset($set['culate_method_plus']['coin_deduction']) && $set['culate_method_plus']['coin_deduction']) checked="checked" @endif/>
                                    {{trans('Yunshop\Coin::coin.name')}}抵扣
                                </label>
                            @endif
                            <span class='help-block'>
                                ===佣金计算方式说明===<br>
                                商品实际支付金额不包含运费
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">佣金计算方式-减少项目</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="checkbox" name="setdata[culate_method_minus][freight]" value="1"
                                       @if(isset($set['culate_method_minus']['freight']) && $set['culate_method_minus']['freight']) checked="checked" @endif/>
                                运费
                            </label>
                            {{--<label class="radio-inline">--}}
                                {{--<input type="checkbox" name="setdata[culate_method_minus][point]" value="1"--}}
                                       {{--@if(isset($set['culate_method_minus']['point']) && $set['culate_method_minus']['point']) checked="checked" @endif/>--}}
                                {{--奖励积分--}}
                            {{--</label>--}}
                            {{--<label class="radio-inline">--}}
                                {{--<input type="checkbox" name="setdata[culate_method_minus][bonus]" value="1"--}}
                                       {{--@if(isset($set['culate_method_minus']['bonus']) && $set['culate_method_minus']['bonus']) checked="checked" @endif/>--}}
                                {{--奖励红包--}}
                            {{--</label>--}}
                            <label class="radio-inline">
                                <input type="checkbox" name="setdata[culate_method_minus][cost]" value="1"
                                       @if(isset($set['culate_method_minus']['cost']) && $set['culate_method_minus']['cost']) checked="checked" @endif/>
                                成本
                            </label>

                            <span class='help-block'>===佣金计算方式说明===</span>
                        </div>
                    </div>

                </div>

                <div class='panel-heading'>商品详情活动</div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">可获得佣金</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio-inline">
                            <input type="radio" name="setdata[goods_detail]" value="1" @if($set['goods_detail'] == 1)checked="checked" @endif />
                            显示
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="setdata[goods_detail]" value="0" @if($set['goods_detail'] == 0)checked="checked" @endif />
                            隐藏
                        </label>
                        <span class='help-block'>在商品活动中显示可获得佣金，隐藏时：商品详情活动中不显示可获得佣金（备：佣金正常计算）</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示层级</label>
                    <div class="col-sm-4">
                        <select class="form-control" name="setdata[goods_detail_level]" id="goods_detail_level">
                            <option value="1" id="goods_detail_level1" @if(isset($set['goods_detail_level']) && $set['goods_detail_level']==1) selected @endif>
                                一级
                            </option>

                            <option value="2" id="goods_detail_level2" @if(isset($set['goods_detail_level']) && $set['goods_detail_level']==2) selected @endif>
                                二级
                            </option>

                            {{--<option value="3" id="goods_detail_level3" @if(isset($set['goods_detail_level']) && $set['goods_detail_level']==3) selected @endif>--}}
                                {{--三级--}}
                            {{--</option>--}}
                        </select>
                    </div>
                </div>

                <div class='panel-heading'>
                    分销中心
                </div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销商品/订单详情</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[open_order_detail]" value="0"
                                       @if(isset($set['open_order_detail']) &&
                                       $set['open_order_detail'] == 0)
                                       checked="checked" @endif />
                                关闭</label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[open_order_detail]" value="1"
                                       @if(isset($set['open_order_detail']) &&
                                       $set['open_order_detail'] == 1)
                                       checked="checked" @endif />
                                开启</label>
                            <span class='help-block'>分销中心分销订单是否显示商品详情</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销订单购买者详情</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[open_order_buyer]" value="0"
                                       @if(isset($set['open_order_buyer']) &&
                                       $set['open_order_buyer'] == 0)
                                       checked="checked" @endif />
                                关闭</label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[open_order_buyer]" value="1"
                                       @if(isset($set['open_order_buyer']) &&
                                       $set['open_order_buyer'] == 1)
                                       checked="checked" @endif />
                                开启</label>
                            <span class='help-block'>分销中心分销订单是否显示购买者</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销订单收货人信息</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[open_order_buyer_info]" value="0"
                                       @if(isset($set['open_order_buyer_info']) &&
                                       $set['open_order_buyer_info'] == 0)
                                       checked="checked" @endif />
                                关闭</label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[open_order_buyer_info]" value="1"
                                       @if(isset($set['open_order_buyer_info']) &&
                                       $set['open_order_buyer_info'] == 1)
                                       checked="checked" @endif />
                                开启</label>
                            <span class='help-block'>分销中心分销订单是否显示收货人信息</span>
                        </div>
                    </div>

                </div>


                <div class='panel-heading'>
                    数据同步
                </div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员数据同步</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="button" onclick="dataIdentical()" value="同步">
                            <span class='help-block'>已是推广员但在分销管理中查询不到会员，则需要同步数据。其他情况无需同步!</span>
                        </div>
                    </div>
                </div>

                <div class="form-group"></div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"
                               onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </section><!-- /.content -->
@endsection
<script type="text/javascript" src="./resource/js/lib/jquery-ui-1.10.3.min.js"></script>
<script language='javascript'>



    window.onload=function(){
        var option=$("#commission_level option:selected").val();

        if (option < 3) {
            $("#goods_detail_level3").hide();
        }
        if (option < 2) {
            $("#goods_detail_level2").hide();
        }
        if (option < 1) {
            $("#goods_detail_level1").hide();
        }
    }

    require(['select2'], function () {
        $('.diy-notice').select2();
    })

    function dataIdentical(){
        $.get("{!! yzWebUrl('plugin.commission.admin.data-identical.index') !!}", function(data) {
            alert(data);
        });
    }

    function commissionLevel(){
        var selectdIndex = $("#commission_level").get(0).selectedIndex;

        $("#goods_detail_level option").attr("selected", false);
        $("#goods_detail_level option").eq(selectdIndex).prop("selected", true);


        if (selectdIndex == 0) {
            $("#goods_detail_level1").show();
            $("#goods_detail_level2").hide();
            $("#goods_detail_level3").hide();
        }
        if (selectdIndex == 1) {
            $("#goods_detail_level1").show();
            $("#goods_detail_level2").show();
            $("#goods_detail_level3").hide();
        }
        if (selectdIndex == 2) {
            $("#goods_detail_level1").show();
            $("#goods_detail_level2").show();
            $("#goods_detail_level3").show();
        }

        //var goods_detail = $("#goods_detail_level option:selected").val();

       // alert(goods_detail);
    }
    function formcheck() {
        var numerictype = /^(0|[1-9]\d*)$/; //整数验证
        var reg = /^(([1-9]+)|([0-9]+\.[0-9]{1,2}))$/; //小数验证
        var nr = /^(0|[1-9][0-9]*)+(\.\d{1,2})?$/; // Yy 整数或小数
        var level = "{{$set['level']}}";

        if (level >= '1') {
            if (!nr.test($(':input[name="setdata[first_level]"]').val())) {
                Tip.focus(':input[name="setdata[first_level]"]', '只能是整数.');
                return false;
            }
        }
        if (level >= '2') {
            if (!nr.test($(':input[name="setdata[second_level]"]').val())) {
                Tip.focus(':input[name="setdata[second_level]"]', '只能是整数.');
                return false;
            }
        }

        // if (level >= '3') {
        //     if (!nr.test($(':input[name="setdata[third_level]"]').val())) {
        //         Tip.focus(':input[name="setdata[third_level]"]', '只能是整数.');
        //         return false;
        //     }
        // }

        if (!numerictype.test($(':input[name="setdata[settle_days]"]').val())) {
            Tip.focus(':input[name="setdata[settle_days]"]', '只能是整数.');
            return false;
        }
    }
</script>