@extends('layouts.base')
@section('title', trans('Yunshop\Love::withdraw_set.title'))
@section('content')

    <div class="rightlist">

        @include('Yunshop\Love::Backend.tabs')

        <form action="{{ yzWebUrl('plugin.love.Backend.Controllers.withdraw-set.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>
                <div class='panel-heading'>
                    <span>{{ trans('Yunshop\Love::withdraw_set.withdraw_set') }}:</span>
                </div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                            <span>{{ trans('Yunshop\Love::withdraw_set.withdraw_set_title') }}：</span>
                        </label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[withdraw_status]" value="1" @if ($love['withdraw_status'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::withdraw_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[withdraw_status]" value="0" @if ($love['withdraw_status'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::withdraw_set.off') }}
                            </label>
                        </div>
                    </div>
                    <div id='love_withdraw' @if(empty($love['withdraw_status']))style="display:none"@endif>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="input-group">
                                    <div class="input-group">
                                        <div class="input-group-addon" style="width: 150px;">{{ trans('Yunshop\Love::withdraw_set.withdraw_multiple_hint') }}</div>
                                        <input type="text" name="love[withdraw_multiple]" id="commission2_pay"
                                               class="form-control" value="{{ $love['withdraw_multiple'] }}" placeholder=""/>
                                    </div>
                                </div>
                                <div class="help-block">
                                    {{ trans('Yunshop\Love::withdraw_set.withdraw_multiple_introduce') }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="input-group">
                                    <div class="input-group">
                                        <div class="input-group-addon" style="width: 150px;">{{ trans('Yunshop\Love::withdraw_set.withdraw_scale_hint') }}</div>
                                        <input type="text" name="love[withdraw_scale]" id="commission2_pay" class="form-control" value="{{ $love['withdraw_scale'] }}" placeholder=""/>
                                    </div>
                                </div>
                                <div class="help-block">
                                    {{ trans('Yunshop\Love::withdraw_set.withdraw_scale_introduce') }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="input-group">
                                    <div class="input-group">
                                        <div class="input-group-addon" style="width: 150px;">{{ trans('Yunshop\Love::withdraw_set.withdraw_poundage_hint') }}</div>
                                        <input type="text" class="form-control" value="{{ $love['withdraw_proportion'] }}" disabled="disabled"/>
                                        <div class="input-group-addon">@if($love['poundage_type'] == 0) % @else 元 @endif </div>
                                    </div>
                                </div>
                                <div class="help-block">
                                    {{ trans('Yunshop\Love::withdraw_set.withdraw_poundage_introduce') }}
                                    <a href="{{ yzWebUrl('finance.withdraw-set.see') }}">【点我前往】</a>>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($integralPluginStatus)
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::withdraw_set.withdraw_in_consumption_integral') }}：</label>
                            <div class="col-sm-4 col-xs-6">
                                <label class="radio-inline">
                                    <input type="radio" name="love[integral_withdraw_status]" value="1" @if ($love['integral_withdraw_status'] == 1) checked="checked" @endif />
                                    {{ trans('Yunshop\Love::withdraw_set.on') }}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="love[integral_withdraw_status]" value="0" @if ($love['integral_withdraw_status'] == 0) checked="checked" @endif />
                                    {{ trans('Yunshop\Love::withdraw_set.off') }}
                                </label>
                            </div>
                        </div>
                        <div id='love_integral_withdraw' @if(empty($love['integral_withdraw_status']))style="display:none"@endif>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="input-group">
                                        <div class="input-group">
                                            <div class="input-group-addon" style="width: 150px;">{{ trans('Yunshop\Love::withdraw_set.withdraw_in_consumption_integral_proportion') }}</div>
                                            <input type="text" name="love[integral_withdraw_scale]" id="commission2_pay" class="form-control" value="{{ $love['integral_withdraw_scale'] }}" placeholder=""/>
                                        </div>
                                    </div>
                                    <div class="help-block">
                                        {{ trans('Yunshop\Love::withdraw_set.withdraw_scale_introduce') }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="input-group">
                                        <div class="input-group">
                                            <div class="input-group-addon" style="width: 150px;">{{ trans('Yunshop\Love::withdraw_set.withdraw_poundage_integral_hint') }}</div>
                                            <input type="text" class="form-control" name="love[integral_withdraw_proportion]" value="{{ $love['integral_withdraw_proportion'] }}" />
                                            <div class="input-group-addon">%</div>
                                        </div>
                                        <div class="help-block">
                                            {{ trans('Yunshop\Love::withdraw_set.withdraw_scale_introduce_integral') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class='panel-heading'>
                    <span>提现限制:</span>
                </div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                            <span>{{ trans('Yunshop\Love::withdraw_set.proportion_switch') }}:</span>
                        </label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[proportion_switch]" value="1" @if ($love['proportion_switch'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::withdraw_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[proportion_switch]" value="0" @if ($love['proportion_switch'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::withdraw_set.off') }}
                            </label>
                            <div class="help-block">
                                提现至收入时，扣除预计手续等值{{ trans('Yunshop\Love::withdraw_set.pointName') }}，积分不足不允许提现
                                <br />
                                提现至{{ trans('Yunshop\Love::withdraw_set.integralName') }}时，扣除提现手续费等值的{{ trans('Yunshop\Love::withdraw_set.pointName') }}，{{ trans('Yunshop\Love::withdraw_set.pointName') }}不足不允许提现
                            </div>
                        </div>
                    </div>
                    @if($integralPluginStatus)
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                <span>提现扣除{{ trans('Yunshop\Love::withdraw_set.integralName') }}:</span>
                            </label>
                            <div class="col-sm-4 col-xs-6">
                                <label class="radio-inline">
                                    <input type="radio" name="love[reduce_integral]" value="1" @if ($love['reduce_integral'] == 1) checked="checked" @endif />
                                    {{ trans('Yunshop\Love::withdraw_set.on') }}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="love[reduce_integral]" value="0" @if ($love['reduce_integral'] == 0) checked="checked" @endif />
                                    {{ trans('Yunshop\Love::withdraw_set.off') }}
                                </label>
                                <div class="help-block">
                                    提现申请时，扣除比例{{ trans('Yunshop\Love::withdraw_set.integralName') }}，{{ trans('Yunshop\Love::withdraw_set.integralName') }}不足不允许提现
                                </div>
                            </div>
                        </div>
                        <div id='reduce_integral' @if(empty($love['reduce_integral']))style="display:none"@endif>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="input-group">
                                        <div class="input-group">
                                            <div class="input-group-addon" style="width: 150px;">
                                                扣除比例
                                            </div>
                                            <input type="text" name="love[reduce_integral_rate]" id="commission2_pay" class="form-control" value="{{ $love['reduce_integral_rate'] or 0}}" />
                                        </div>
                                    </div>
                                    <div class="help-block">
                                        计算公式：提现值 x 扣除比例 = 扣除{{ trans('Yunshop\Love::withdraw_set.integralName') }}数额
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($assetPluginStatus && !$digitizationList->isEmpty())
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                提现扣除{{ PLUGIN_ASSET_DIGITIZATION_NAME }}:
                            </label>
                            <div class="col-sm-4 col-xs-6">
                                <label class="radio-inline">
                                    <input type="radio" name="love[reduce_digitization]" value="1" @if ($love['reduce_digitization'] == 1) checked="checked" @endif />
                                    开启
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="love[reduce_digitization]" value="0" @if ($love['reduce_digitization'] == 0) checked="checked" @endif />
                                    关闭
                                </label>
                            </div>
                        </div>
                        <div id='reduce_digitization' @if(empty($love['reduce_digitization']))style="display:none"@endif>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-6 col-xs-6">
                                    <div class='input-group'>
                                        <label>
                                            <select name='love[reduce_digitization_id]' class='form-control' style="width: 188px;">
                                                @foreach($digitizationList as $key => $digitizationModel)
                                                    <option value='{{ $digitizationModel->asset_id }}' @if($digitizationModel->asset_id == $love['reduce_digitization_id']) selected @endif>{{ $digitizationModel->name }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <div class="help-block">
                                            每提现一笔，都要扣除1：1的所选{{ PLUGIN_ASSET_DIGITIZATION_NAME }}，如果没有足够的所选{{ PLUGIN_ASSET_DIGITIZATION_NAME }}不允许提现
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="{{ trans('Yunshop\Love::withdraw_set.submit') }}" class="btn btn-primary col-lg-1" onclick='return formcheck()'/>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script language="javascript">
        $(function () {
            $(":radio[name='love[withdraw_status]']").click(function () {
                if ($(this).val() == 1) {
                    $("#love_withdraw").show();
                }
                else {
                    $("#love_withdraw").hide();
                }
            });
            $(":radio[name='love[reduce_digitization]']").click(function () {
                if ($(this).val() == 1) {
                    $("#reduce_digitization").show();
                }
                else {
                    $("#reduce_digitization").hide();
                }
            });
            $(":radio[name='love[integral_withdraw_status]']").click(function () {
                if ($(this).val() == 1) {
                    $("#love_integral_withdraw").show();
                }
                else {
                    $("#love_integral_withdraw").hide();
                }
            });
            $(":radio[name='love[reduce_integral]']").click(function () {
                if ($(this).val() == 1) {
                    $("#reduce_integral").show();
                }
                else {
                    $("#reduce_integral").hide();
                }
            });
        })


    </script>

@endsection

