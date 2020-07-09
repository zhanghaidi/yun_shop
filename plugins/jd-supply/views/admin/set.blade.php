@extends('layouts.base')
@section('title', trans('基础设置'))
@section('content')
    <div class="w1200 m0a">
        <div class="main">
            <form id="baseform" method="post" class="form-horizontal form">
                <div class="rightlist">
                    <div class="right-titpos">
                        <ul class="add-snav">
                            <li class="active"><a href="#">基础设置</a></li>
                        </ul>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">appKey</label>
                                <div class="col-sm-6 col-xs-12">
                                    <input type="text" name="set[app_key]" class="form-control" value="{{$set['app_key']}}" autocomplete="off">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">appSecret</label>
                                <div class="col-sm-6 col-xs-12">
                                    @if(empty($set['app_secret']))
                                    <input type="text" name="set[app_secret]" class="form-control" value="{{$set['app_secret']}}" autocomplete="off">
                                        @else
                                        <label id="reset" class='radio-inline' onclick="showSecret()">点击重新设置</label>
                                    @endif
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">回调地址</label>
                                <div class="col-sm-6 col-xs-12">
                                    <span>{!! \app\common\helpers\Url::absoluteApi('plugin.jd-supply.frontend.message-api') !!}</span>
                                    <a href="javascript:;" data-clipboard-text="{!! \app\common\helpers\Url::absoluteApi('plugin.jd-supply.frontend.message-api') !!}" data-url="{!! \app\common\helpers\Url::absoluteApi('plugin.jd-supply.frontend.message-api') !!}" class="js-clip" title="复制链接">复制链接</a>
                                    <span style="" class='help-block'>
                                        推送消息实时处理（服务器压力大）<br/>
                                    复制此链接到(开放平台->应用管理->基本信息->回调地址)<br/>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">新回调地址</label>
                                <div class="col-sm-6 col-xs-12">
                                    <span>{!! $push_url !!}</span>
                                    <a href="javascript:;" data-clipboard-text="{!! $push_url !!}" data-url="{!! $push_url !!}" class="js-clip" title="复制链接">复制链接</a>
                                    <span style="" class='help-block'>
                                        推送消息一分钟集中处理一次（服务器压力小）<br/>
                                        复制此链接到(开放平台->应用管理->基本信息->回调地址)<br/>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">店铺名称</label>
                                <div class="col-sm-6 col-xs-12">
                                    <input type="text" name="set[shop_name]" class="form-control" value="{{$set['shop_name']}}" autocomplete="off">
                                    <span class="help-block">默认为聚合供应链</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">自动更新商品价格</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[is_close_auto_update]' value='0'
                                               @if(!$set['is_close_auto_update']) checked @endif
                                        /> 开启
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[is_close_auto_update]' value='1'
                                               @if($set['is_close_auto_update'] == 1) checked @endif
                                        /> 关闭
                                    </label>
                                    <span style="" class='help-block'>
                                        关闭则不更新商品售价
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">自动更新商品基本信息</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[is_close_auto_detail]' value='0'
                                               @if(!$set['is_close_auto_detail']) checked @endif
                                        /> 开启
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[is_close_auto_detail]' value='1'
                                               @if($set['is_close_auto_detail'] == 1) checked @endif
                                        /> 关闭
                                    </label>
                                    <span style="" class='help-block'>
                                        关闭则不更新商品名称和商品详情
                                    </span>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">去除图片水印</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[remove_logo]' value='1'
                                               @if($set['remove_logo'] == 1) checked @endif
                                        /> 开启
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[remove_logo]' value='0'
                                               @if($set['remove_logo'] == 0) checked @endif
                                        /> 关闭
                                    </label>
                                    <span style="" class='help-block'>

                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">导入时创建品牌</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[create_brand]' value='1'
                                               @if($set['create_brand'] == 1) checked @endif
                                        /> 开启
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[create_brand]' value='0'
                                               @if($set['create_brand'] == 0) checked @endif
                                        /> 关闭
                                    </label>
                                    <span style="" class='help-block'>
                                        开启后导入商品时将创建对应商品品牌并将商品绑入相应品牌
                                    </span>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">京东销售价 定价策略</label>
                                <div class="col-sm-9 col-xs-12">
                                        <label class='radio-inline'>
                                            <input type='radio' name='set[price_method]' value='0'
                                                   @if($set['price_method'] == 0) checked @endif
                                            /> 指导价 x 定价系数
                                            <input type="text" size="3" name="set[price_radio]" value="{{$set['price_radio']}}">
                                            <span>%</span>
                                        </label>
                                        <label class='radio-inline'>
                                        <input type='radio' name='set[price_method]' value='1'
                                               @if($set['price_method'] == 1) checked @endif
                                        /> 协议价 x 定价系数
                                        <input type="text" size="3" name="set[cost_price_radio]" value="{{$set['cost_price_radio']}}">
                                        <span>%</span>
                                        </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[price_method]' value='2'
                                               @if($set['price_method'] == 2) checked @endif
                                        /> 营销价 x 定价系数
                                        <input type="text" size="3" name="set[market_price_radio]" value="{{$set['market_price_radio']}}">
                                        <span>%</span>
                                    </label>

                                    <span style="" class='help-block'>
                                        举例：协议价50元 指导价100元 </br>
                                        协议价 x 定价系数 即 50 x 130% = 65 元</br>
                                        指导价 x 定价系数 即 100 x 80% = 80 元</br>
                                        默认为指导价 x 100%（指导价不变，协议价上涨可能导致亏损!）</br>
                                        没有营销价的商品按指导价计算
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">京东成本价 定价策略</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[cost_price_method]' value='0'
                                               @if($set['cost_price_method'] == 0) checked @endif
                                        /> 协议价 x 定价系数
                                        <input type="text" size="3" name="set[cost_price_radio_cost]" value="{{$set['cost_price_radio_cost']}}">
                                        <span>%</span>
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[cost_price_method]' value='1'
                                               @if($set['cost_price_method'] == 1) checked @endif
                                        /> 营销价 x 定价系数
                                        <input type="text" size="3" name="set[market_price_radio_cost]" value="{{$set['market_price_radio_cost']}}">
                                        <span>%</span>
                                    </label>
                                    <span style="" class='help-block'>
                                        没有营销价的商品按指导价计算
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">阿里销售价 定价策略</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[ali_price_method]' value='0'
                                               @if($set['ali_price_method'] == 0) checked @endif
                                        /> 指导价 x 定价系数
                                        <input type="text" size="3" name="set[ali_price_radio]" value="{{$set['ali_price_radio']}}">
                                        <span>%</span>
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[ali_price_method]' value='1'
                                               @if($set['ali_price_method'] == 1) checked @endif
                                        /> 协议价 x 定价系数
                                        <input type="text" size="3" name="set[ali_cost_price_radio]" value="{{$set['ali_cost_price_radio']}}">
                                        <span>%</span>
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[ali_price_method]' value='2'
                                               @if($set['ali_price_method'] == 2) checked @endif
                                        /> 营销价 x 定价系数
                                        <input type="text" size="3" name="set[ali_market_price_radio]" value="{{$set['ali_market_price_radio']}}">
                                        <span>%</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">阿里成本价 定价策略</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[ali_cost_price_method]' value='0'
                                               @if($set['ali_cost_price_method'] == 0) checked @endif
                                        /> 协议价 x 定价系数
                                        <input type="text" size="3" name="set[ali_cost_price_radio_cost]" value="{{$set['ali_cost_price_radio_cost']}}">
                                        <span>%</span>
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[ali_cost_price_method]' value='1'
                                               @if($set['ali_cost_price_method'] == 1) checked @endif
                                        /> 营销价 x 定价系数
                                        <input type="text" size="3" name="set[ali_market_price_radio_cost]" value="{{$set['ali_market_price_radio_cost']}}">
                                        <span>%</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">天猫销售价 定价策略</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[tm_price_method]' value='0'
                                               @if($set['tm_price_method'] == 0) checked @endif
                                        /> 指导价 x 定价系数
                                        <input type="text" size="3" name="set[tm_price_radio]" value="{{$set['tm_price_radio']}}">
                                        <span>%</span>
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[tm_price_method]' value='1'
                                               @if($set['tm_price_method'] == 1) checked @endif
                                        /> 协议价 x 定价系数
                                        <input type="text" size="3" name="set[tm_cost_price_radio]" value="{{$set['tm_cost_price_radio']}}">
                                        <span>%</span>
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[tm_price_method]' value='2'
                                               @if($set['tm_price_method'] == 2) checked @endif
                                        /> 营销价 x 定价系数
                                        <input type="text" size="3" name="set[tm_market_price_radio]" value="{{$set['tm_market_price_radio']}}">
                                        <span>%</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">天猫成本价 定价策略</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[tm_cost_price_method]' value='0'
                                               @if($set['tm_cost_price_method'] == 0) checked @endif
                                        /> 协议价 x 定价系数
                                        <input type="text" size="3" name="set[tm_cost_price_radio_cost]" value="{{$set['tm_cost_price_radio_cost']}}">
                                        <span>%</span>
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[tm_cost_price_method]' value='1'
                                               @if($set['tm_cost_price_method'] == 1) checked @endif
                                        /> 营销价 x 定价系数
                                        <input type="text" size="3" name="set[tm_market_price_radio_cost]" value="{{$set['tm_market_price_radio_cost']}}">
                                        <span>%</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">风控策略</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[control_method]' value='0'
                                               @if($set['control_method'] == 0) checked @endif
                                        /> 产品售价 < 协议价
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[control_method]' value='1'
                                               @if($set['control_method'] == 1) checked @endif
                                        /> 利润率 < 设定利润率
                                        <input type="text" size="3" name="set[profit_radio]" value="{{$set['profit_radio']}}">
                                        <span>%</span>
                                    </label>

                                    <span style="" class='help-block'>
                                        利润率 = (售价-成本价)/成本价<br>
                                        满足条件的商品会执行下架
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">邮费计算</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[freight_method]' value='0'
                                               @if(!$set['freight_method']) checked @endif
                                        /> 协议价
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='set[freight_method]' value='1'
                                               @if($set['freight_method'] == 1) checked @endif
                                        /> 售价
                                    </label>
                                    <span style="" class='help-block'>
                                        该设置只对京东商品有效<br>
                                        订单金额<49元，运费8元<br>
                                        订单金额<99元，运费6元<br>
                                        订单金额>99元，免运费
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="panel panel-footer">
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

    <script type="text/javascript">
        $('.diy-notice').select2();

        $("input[name='set[source][]']").click(function () {
            var obj = $(this);
            if (obj.is(':checked')) {
                obj.parent().addClass('is-checked');
                obj.parent().parent().addClass('is-checked');
            } else {
                obj.parent().removeClass('is-checked');
                obj.parent().parent().removeClass('is-checked');
            }
        });
        function showSecret() {
            $('#reset').after('<input type="text" name="set[app_secret]" class="form-control" value="" autocomplete="off">\n').remove();
        }

    </script>

@endsection