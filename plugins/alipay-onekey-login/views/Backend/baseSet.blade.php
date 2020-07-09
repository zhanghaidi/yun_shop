@extends('layouts.base')
@section('title', trans('Yunshop\Love::base_set.title'))
@section('content')

    <div class="rightlist">

        @include('Yunshop\Love::Backend.tabs')

        <form action="{{ yzWebUrl('plugin.love.Backend.Controllers.base-set.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>
                <div class='panel-heading'>{{ trans('Yunshop\Love::base_set.name_set') }}</div>
                <div class='panel-body'>
                    <div class="form-group" style="padding-top:20px;">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::base_set.name_set_subtitle') }}</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="love[name]" class="form-control" value="{{ $love['name'] }}" placeholder="{{ trans('Yunshop\Love::base_set.name_set_hint') }}" style="width: 250px;"/>
                            <div class="help-block">{{ trans('Yunshop\Love::base_set.name_set_introduce') }}</div>
                        </div>
                    </div>
                </div>

                <div class='panel-heading'>会员中心显示</div>
                <div class='panel-body'>
                    <div class="form-group" style="padding-top:20px;">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示可用{{ $love['name'] ?: '爱心值' }}</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[member_center_show]" value="1" @if ($love['member_center_show'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[member_center_show]" value="0" @if ($love['member_center_show'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.off') }}
                            </label>
                        </div>
                    </div>
                    <div class="form-group" style="padding-top:20px;">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">可用{{ $love['name'] ?: '爱心值' }}名称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="love[usable_name]" class="form-control" value="{{ $love['usable_name'] }}"  style="width: 250px;"/>
                            <div class="help-block">空白默认为{{ $love['name'] ?: '爱心值' }},仅在会员中心显示</div>
                        </div>
                    </div>
                </div>


                <div class='panel-body'>
                    <div class="form-group" style="padding-top:20px;">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示冻结{{ $love['name'] ?: '爱心值' }}</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[member_center_unable_show]" value="1" @if ($love['member_center_unable_show'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[member_center_unable_show]" value="0" @if ($love['member_center_unable_show'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.off') }}
                            </label>
                        </div>
                    </div>
                    <div class="form-group" style="padding-top:20px;">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">冻结{{ $love['name'] ?: '爱心值' }}名称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="love[unable_name]" class="form-control" value="{{ $love['unable_name'] }}"  style="width: 250px;"/>
                            <div class="help-block">空白默认为白{{ $love['name'] ?: '爱心值' }},仅在会员中心显示</div>
                        </div>
                    </div>

                </div>

                <div class='panel-heading'>{{ trans('Yunshop\Love::base_set.goods_detail_show') }}</div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::base_set.display_style') }}
                            ：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[goods_detail_show_love]" value="1"
                                       @if ($love['goods_detail_show_love'] == 1 ) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.daily_bargaining') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[goods_detail_show_love]" value="2"
                                       @if ($love['goods_detail_show_love'] == 2 ) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.shopping_gift') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[goods_detail_show_love]" value="0"
                                       @if ($love['goods_detail_show_love'] == 0 ) checked="checked" @endif />
                                关闭
                            </label>
                            <div class="help-block">

                            </div>
                        </div>
                    </div>

                </div>

                <div class='panel-heading'>{{ trans('Yunshop\Love::base_set.order_deduction') }}</div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::base_set.order_deduction_love') }}：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[order_love_deduction]" value="1" @if ($love['order_love_deduction'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[order_love_deduction]" value="0" @if ($love['order_love_deduction'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.off') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class='panel-heading'>{{ trans('Yunshop\Love::base_set.transfer_set') }}</div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::base_set.transfer_set_subtitle') }}：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[transfer]" value="1" @if ($love['transfer'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[transfer]" value="0" @if ($love['transfer'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.off') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div id='love_transfer' @if(empty($love['transfer']))style="display:none"@endif>
                    @if ($teamDividend)
                        <div  class="form-group">
                            <label style="margin-left: 10px;" class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::base_set.team_dividend_on') }}：</label>
                            <div class="col-sm-4 col-xs-6">
                                <label class="radio-inline">
                                    <input type="radio" name="love[team_dividend_transfer]" value="1" @if ($love['team_dividend_transfer'] == 1) checked="checked" @endif />
                                    {{ trans('Yunshop\Love::base_set.on') }}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="love[team_dividend_transfer]" value="0" @if ($love['team_dividend_transfer'] == 0) checked="checked" @endif />
                                    {{ trans('Yunshop\Love::base_set.off') }}
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div style="margin-left:10px;" class="col-sm-6 text-center help-block">
                                {{ trans('Yunshop\Love::base_set.team_dividend_transfer_range') }}
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group-addon" style="width: 110px;">{{ trans('Yunshop\Love::base_set.transfer_poundage') }}</div>
                                    <input type="text" name="love[transfer_poundage]" class="form-control" value="{{ $love['transfer_poundage'] }}" placeholder=""/>
                                    <div class="input-group-addon">%</div>
                                </div>
                            </div>
                            <div class="help-block">
                                {{ trans('Yunshop\Love::base_set.transfer_poundage_introduce') }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group-addon" style="width: 110px;">{{ trans('Yunshop\Love::base_set.transfer_fetter') }}</div>
                                    <input type="text" name="love[transfer_fetter]" class="form-control" value="{{ $love['transfer_fetter'] }}" placeholder=""/>
                                </div>
                            </div>
                            <div class="help-block">
                                {{ trans('Yunshop\Love::base_set.transfer_fetter_introduce') }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group-addon" style="width: 110px;">{{ trans('Yunshop\Love::base_set.transfer_multiple') }}</div>
                                    <input type="text" name="love[transfer_multiple]" class="form-control" value="{{ $love['transfer_multiple'] }}" placeholder=""/>
                                </div>
                            </div>
                            <div class="help-block">
                                {{ trans('Yunshop\Love::base_set.transfer_multiple_introduce') }}
                            </div>
                        </div>
                    </div>
                </div>


                <div class='panel-heading'>{{ trans('Yunshop\Love::base_set.deduction_set') }}</div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::base_set.deduction_set_subtitle') }}：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[deduction]" value="1" @if ($love['deduction'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[deduction]" value="0" @if ($love['deduction'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.off') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div id='love_deduction' @if(empty($love['deduction']))style="display:none"@endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">{{ trans('Yunshop\Love::base_set.deduction_proportion_low') }}</div>
                                        <input type="text" name="love[deduction_proportion_low]" class="form-control" value="{{ $love['deduction_proportion_low'] }}" placeholder=""/>
                                        <div class="input-group-addon">%</div>
                                        <div class="input-group-addon">{{ trans('Yunshop\Love::base_set.deduction_proportion') }}</div>
                                        <input type="text" name="love[deduction_proportion]" class="form-control" value="{{ $love['deduction_proportion'] }}" placeholder=""/>
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </div>
                                <div class="help-block">
                                    {{ trans('Yunshop\Love::base_set.deduction_proportion_introduce') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group-addon">{{ trans('Yunshop\Love::base_set.deduction_exchange') }}</div>
                                    <input type="text" name="love[deduction_exchange]" class="form-control" value="{{ $love['deduction_exchange'] }}" placeholder=""/>
                                    <div class="input-group-addon">%</div>
                                </div>
                            </div>
                            <div class="help-block">
                                {{ trans('Yunshop\Love::base_set.deduction_exchange_introduce') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::base_set.deduction_freight_title') }}：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[deduction_freight]" value="1" @if ($love['deduction_freight'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[deduction_freight]" value="0" @if ($love['deduction_freight'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.off') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class='panel-heading'>收入提现奖励设置</div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::award_set.withdraw_award_title') }}
                            ：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[withdraw_award]" value="1"
                                       @if ($love['withdraw_award'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::award_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[withdraw_award]" value="0"
                                       @if ($love['withdraw_award'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::award_set.off') }}
                            </label>
                            <div class="help-block">
                                {{ trans('Yunshop\Love::award_set.withdraw_award_introduce') }}
                            </div>
                        </div>
                    </div>
                </div>
                {{--<div class='panel-heading'>劳务税设置</div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">劳务税：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[poundage]" value="1" @if ($love['poundage'] == 1) checked="checked" @endif />
                                开启
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[poundage]" value="0" @if ($love['poundage'] == 0) checked="checked" @endif />
                                关闭
                            </label>
                        </div>
                    </div>
                </div>
                <div id='love_poundage' @if(empty($love['poundage']))style="display:none"@endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group-addon">劳务税</div>
                                    <input type="text" name="love[give_proportion]" id="commission2_pay" class="form-control" value="{{ $love['give_proportion'] }}" placeholder=""/>
                                    <div class="input-group-addon">%</div>
                                </div>
                            </div>
                            <div class="help-block">
                                按照【提现的收入 - 手续费】为基数，乘以设置的百分比扣除劳务税。
                            </div>
                        </div>
                    </div>
                </div>--}}

                <div class='panel-heading'>{{ trans('Yunshop\Love::base_set.explain_set') }}</div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::base_set.explain_title') }}</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="love[explain_title]" class="form-control" value="{{$love['explain_title']}}" placeholder="爱心值说明"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::base_set.explain_content') }}</label>
                    <div class="col-sm-9 col-xs-12">
                        <textarea name="love[explain_content]" rows="5" class="form-control">{{ $love['explain_content'] }}</textarea>
                    </div>
                </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9">
                    <input type="submit" name="submit" value="{{ trans('Yunshop\Love::base_set.submit') }}" class="btn btn-primary col-lg-1" onclick='return formcheck()'/>
                </div>
            </div>

    </div>
        </form>
    </div>
    <script language="javascript">
        $(function () {
            $(":radio[name='love[deduction]']").click(function () {
                if ($(this).val() == 1) {
                    $("#love_deduction").show();
                }
                else {
                    $("#love_deduction").hide();
                }
            });
            $(":radio[name='love[recharge]']").click(function () {
                if ($(this).val() == 1) {
                    $("#love_recharge").show();
                }
                else {
                    $("#love_recharge").hide();
                }
            });
            $(":radio[name='love[recharge_condition]']").click(function () {
                if ($(this).val() == 1) {
                    $("#love_recharge_condition").show();
                }
                else {
                    $("#love_recharge_condition").hide();
                }
            });
            $(":radio[name='love[transfer]']").click(function () {
                if ($(this).val() == 1) {
                    $("#love_transfer").show();
                } else if ($(this).val() == 0) {
                    $("#love_transfer").hide();
                }
            });
            $(":radio[name='love[poundage]']").click(function () {
                if ($(this).val() == 1) {
                    $("#love_poundage").show();
                }
                else {
                    $("#love_poundage").hide();
                }
            });
        })
    </script>

@endsection

