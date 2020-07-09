@extends('layouts.base')
@section('title', '充值设置')
@section('content')

    <div class="rightlist">

        @include('Yunshop\Love::Backend.tabs')

        <form action="{{ yzWebUrl('plugin.love.Backend.Controllers.recharge-set.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>
                <div class='panel-heading'>充值设置</div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">充值开关：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[recharge]" value="1" @if ($love['recharge'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[recharge]" value="0" @if ($love['recharge'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::base_set.off') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div id='love_recharge' @if(empty($love['recharge']))style="display:none"@endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">充值比例:</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group-addon" style="width: 110px;">充值比例</div>
                                    <input type="text" name="love[recharge_rate_money]" class="form-control" value="{{ $love['recharge_rate_money'] or 1 }}" placeholder=""/>
                                    <div class="input-group-addon">元</div>
                                    <input type="text" name="love[recharge_rate_love]" class="form-control" value="{{ $love['recharge_rate_love'] or 1 }}" placeholder=""/>
                                    <div class="input-group-addon">{{ LOVE_NAME }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='panel-heading'>充值类型</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">充值类型：</label>
                            <div class="col-sm-4 col-xs-6">
                                <label class="radio-inline">
                                    <input type="radio" name="love[recharge_type]" value="1" @if (empty($love['recharge_type']) || $love['recharge_type'] == 1) checked="checked" @endif />
                                    可用
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="love[recharge_type]" value="2" @if ($love['recharge_type'] == 2) checked="checked" @endif />
                                    冻结
                                </label>
                                <div class="help-block">
                                    会员充值类型：可用 充值到会员可用{{ LOVE_NAME }}，冻结 充值到会员冻结{{ LOVE_NAME }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='panel-heading'>充值奖励</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">充值奖励:</label>
                            <div class="col-sm-4 col-xs-6">
                                <label class="radio-inline">
                                    <input type="radio" name="love[recharge_award]" value="1" @if ($love['recharge_award'] == 1) checked="checked" @endif />
                                    开启
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="love[recharge_award]" value="0" @if ($love['recharge_award'] == 0) checked="checked" @endif />
                                    关闭
                                </label>
                                <div class="help-block">
                                    会员充值，支持上一级、二级赠送可用{{ LOVE_NAME }}比例，小于等于0不赠送，默认奖励可用{{ LOVE_NAME }}。
                                    只有前端充值才能获得奖励！
                                </div>
                            </div>
                        </div>
                        <div id='rechargeAward' @if(empty($love['recharge_award']))style="display:none"@endif>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="input-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">一级奖励比例</div>
                                            <input type="text" name="love[recharge_award_first]" class="form-control" value="{{ $love['recharge_award_first'] or 0}}" />
                                            <div class="input-group-addon">%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="input-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">二级奖励比例</div>
                                            <input type="text" name="love[recharge_award_second]" class="form-control" value="{{ $love['recharge_award_second'] or 0}}" />
                                            <div class="input-group-addon">%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(app('plugins')->isEnabled('commission'))
                        <div class='panel-heading'>充值限制</div>
                        <div class='panel-body'>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">充值限制:</label>
                                <div class="col-sm-4 col-xs-6">
                                    <label class="radio-inline">
                                        <input type="radio" name="love[recharge_condition]" value="1" @if ($love['recharge_condition'] == 1) checked="checked" @endif />
                                        {{ trans('Yunshop\Love::base_set.on') }}
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="love[recharge_condition]" value="0" @if ($love['recharge_condition'] == 0) checked="checked" @endif />
                                        {{ trans('Yunshop\Love::base_set.off') }}
                                    </label>
                                    <div class="help-block">
                                        推客等级：勾选，允许该等级会员充值，不勾选，不允许该等级会员充值
                                    </div>
                                </div>
                            </div>
                            <div id='love_recharge_condition' @if(empty($love['recharge_condition']))style="display:none"@endif>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-sm-9 col-xs-12">
                                        @foreach($commissionLevels as $key => $commissionLevel)
                                            <label class="radio-inline">
                                                <input type="checkbox" name="love[recharge_commission_level][]"
                                                       value="{{ $commissionLevel->id }}"
                                                       @if(in_array($commissionLevel->id, unserialize($love['recharge_commission_level']))) checked="checked" @endif/>
                                                {{ $commissionLevel->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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
            $(":radio[name='love[recharge]']").click(function () {
                if ($(this).val() == 1) {
                    $("#love_recharge").show();
                } else {
                    $("#love_recharge").hide();
                }
            });
            $(":radio[name='love[recharge_condition]']").click(function () {
                if ($(this).val() == 1) {
                    $("#love_recharge_condition").show();
                } else {
                    $("#love_recharge_condition").hide();
                }
            });
            $(":radio[name='love[recharge_award]']").click(function () {
                if ($(this).val() == 1) {
                    $("#rechargeAward").show();
                } else {
                    $("#rechargeAward").hide();
                }
            });
        })
    </script>

@endsection
