@extends('layouts.base')
@section('title', trans('Yunshop\Love::award_set.title'))
@section('content')

    <div class="rightlist">

        @include('Yunshop\Love::Backend.tabs')

        <form action="{{ yzWebUrl('plugin.love.Backend.Controllers.award-set.store') }}" method="post"
              class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>

                <div class='panel-heading'>{{ trans('Yunshop\Love::award_set.award_type') }}</div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::award_set.award_type_title') }}
                            ：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[award_type]" value="usable"
                                       @if ($love['award_type'] == 'usable') checked="checked" @endif />
                                {{ trans('Yunshop\Love::award_set.award_type_usable') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[award_type]" value="froze"
                                       @if ($love['award_type'] == 'froze' || !$love['award_type']) checked="checked" @endif />
                                {{ trans('Yunshop\Love::award_set.award_type_froze') }}
                            </label>
                            <div class="help-block">
                                {{ trans('Yunshop\Love::award_set.award_type_introduce') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class='panel-heading'>奖励时间</div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                            <span>奖励时间：</span>
                        </label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[order_status]" value="0" @if (empty($love['order_status'])) checked="checked" @endif />
                                <span>订单完成</span>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[order_status]" value="1" @if ($love['order_status'] == '1') checked="checked" @endif />
                                <span>订单支付</span>
                            </label>
                            <div class="help-block">
                                <span>请勿随意更改奖励方式，修改过后导致的问题由客户承担。</span>
                                <br />
                                <span>①奖励方式由完成后改为支付后，仅针对修改后的未支付订单为准。支付了的订单不会再进行奖励。</span>
                                <br />
                                <span>②奖励方式由支付后改为完成后，之前已按支付后奖励爱心值的订单订单完成时会再一次的奖励，即一笔订单会奖励两次爱心值。</span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class='panel-heading'>{{ trans('Yunshop\Love::award_set.award_set') }}</div>

                <div class='panel-body'>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::award_set.shopping-award_set') }}</label>
                        <div class="col-sm-8 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[reward_rule]" value="1"
                                       @if ($love['reward_rule'] == 1 || $love['reward_rule'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::award_set.actual') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[reward_rule]" value="2"
                                       @if ($love['reward_rule'] == 2) checked="checked" @endif />
                                {{ trans('Yunshop\Love::award_set.present') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[reward_rule]" value="3"
                                       @if ($love['reward_rule'] == 3) checked="checked" @endif />
                                {{ trans('Yunshop\Love::award_set.cost') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[reward_rule]" value="4"
                                       @if ($love['reward_rule'] == 4) checked="checked" @endif />
                                {{ trans('Yunshop\Love::award_set.profit') }}
                            </label>
                            <div class="help-block">
                                {{ trans('Yunshop\Love::award_set.award_rule') }}
                            </div>


                        </div>
                    </div>
                    <div class="form-group" id="profit_reward_proportion_title" @if($love['reward_rule'] != 4 )style="display:none"@endif>
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                            {{ trans('Yunshop\Love::award_set.profit_reward_proportion_title') }}
                        </label>
                        <div class="col-sm-4 col-xs-6">
                            <div class='input-group' style="width: 286px;">
                                <input type='text' name='love[profit_award_proportion]' class="form-control"
                                       value="{{ $love['profit_award_proportion'] }}" />
                                <div class="input-group-addon">%</div>

                            </div>
                            <div class="help-block">
                                {{ trans('Yunshop\Love::award_set.profit_reward_proportion_explain') }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::award_set.award_set_title') }}
                            ：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[award]" value="1"
                                       @if ($love['award'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::award_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[award]" value="0"
                                       @if ($love['award'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::award_set.off') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div id='love_award' @if(empty($love['award']))style="display:none"@endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group-addon">{{ trans('Yunshop\Love::award_set.award_set_hint') }}</div>
                                    <input type="text" name="love[award_proportion]" id="commission2_pay"
                                           class="form-control" value="{{ $love['award_proportion'] }}" placeholder=""/>
                                    <div class="input-group-addon">%</div>
                                </div>
                            </div>
                            <div class="help-block">
                                {{ trans('Yunshop\Love::award_set.award_set_introduce') }}
                            </div>
                        </div>
                    </div>
                </div>


                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::award_set.parent_award_set_title') }}
                            ：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[parent_award]" value="0"
                                       @if ($love['parent_award'] == 0) checked="checked" @endif />
                                {{--{{ trans('Yunshop\Love::award_set.off') }}--}}
                                关闭
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[parent_award]" value="1"
                                       @if ($love['parent_award'] == 1) checked="checked" @endif />
                                {{--{{ trans('Yunshop\Love::award_set.on') }}--}}
                                会员
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[parent_award]" value="2"
                                       @if ($love['parent_award'] == 2) checked="checked" @endif />
                                {{--{{ trans('Yunshop\Love::award_set.off') }}--}}
                                分销商
                            </label>
                        </div>
                    </div>
                </div>

                <div id='love_parent_award' @if($love['parent_award'] != 1)style="display:none"@endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group-addon">{{ trans('Yunshop\Love::award_set.one_parent_award_set_hint') }}</div>
                                    <input type="text" name="love[parent_award_proportion]" class="form-control"
                                           value="{{ $love['parent_award_proportion'] }}" placeholder=""/>
                                    <div class="input-group-addon">%</div>
                                </div>
                            </div>
                            <div class="help-block">
                                {{ trans('Yunshop\Love::award_set.one_parent_award_set_introduce') }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group-addon">{{ trans('Yunshop\Love::award_set.two_parent_award_set_hint') }}</div>
                                    <input type="text" name="love[second_award_proportion]" class="form-control"
                                           value="{{ $love['second_award_proportion'] }}" placeholder=""/>
                                    <div class="input-group-addon">%</div>
                                </div>
                            </div>
                            <div class="help-block">
                                {{ trans('Yunshop\Love::award_set.two_parent_award_set_introduce') }}
                            </div>
                        </div>
                    </div>
                    {{--<div class="form-group">--}}
                        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
                        {{--<div class="col-sm-9 col-xs-12">--}}
                            {{--<div class="input-group">--}}
                                {{--<div class="input-group">--}}
                                    {{--<div class="input-group-addon">{{ trans('Yunshop\Love::award_set.third_parent_award_set_hint') }}</div>--}}
                                    {{--<input type="text" name="love[third_award_proportion]" class="form-control"--}}
                                           {{--value="{{ $love['third_award_proportion'] }}" placeholder=""/>--}}
                                    {{--<div class="input-group-addon">%</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="help-block">--}}
                                {{--{{ trans('Yunshop\Love::award_set.third_parent_award_set_introduce') }}--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>

                @if($pluginCommission)
                {{--<div class='panel-body'>--}}
                    {{--<div class="form-group">--}}
                        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::award_set.commission_level_superior_give') }}--}}
                            {{--：</label>--}}
                        {{--<div class="col-sm-4 col-xs-6">--}}
                            {{--<label class="radio-inline">--}}
                                {{--<input type="radio" name="love[commission_level_give]" value="1"--}}
                                       {{--@if ($love['commission_level_give'] == 1) checked="checked" @endif />--}}
                                {{--{{ trans('Yunshop\Love::award_set.on') }}--}}
                            {{--</label>--}}
                            {{--<label class="radio-inline">--}}
                                {{--<input type="radio" name="love[commission_level_give]" value="0"--}}
                                       {{--@if ($love['commission_level_give'] == 0) checked="checked" @endif />--}}
                                {{--{{ trans('Yunshop\Love::award_set.off') }}--}}
                            {{--</label>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div id="commission_level_give_status" class="form-group" @if($love['parent_award'] != 2)style="display: none" @endif>
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div class='panel-body'>
                                <div class="table-responsive ">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th style="width: 10%">{{ trans('Yunshop\Love::award_set.level_name') }}</th>
                                            {{--@if($set['level']>=1)--}}
                                                <th style="text-align: center;">{{ trans('Yunshop\Love::award_set.first_level_commission') }}</th>
                                            {{--@endif--}}
                                            {{--@if($set['level']>=2)--}}
                                                <th style="text-align: center;">{{ trans('Yunshop\Love::award_set.second_level_commission') }}</th>
                                            {{--@endif--}}
                                            {{--@if($set['level']>=3)--}}
                                                {{--<th style="text-align: center;">{{ trans('Yunshop\Love::award_set.third_level_commission') }}</th>--}}
                                            {{--@endif--}}
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <tr>
                                            <td>{{ trans('Yunshop\Love::award_set.default_level') }}</td>
                                            {{--@if($set['level']>=1)--}}
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="love[commission][rule][level_0][first_level_rate]"
                                                               class="form-control"
                                                               value="{{ number_format($love_commission_set['rule']['level_0']['first_level_rate']?:0, 2) }}"/>
                                                        <div class="input-group-addon">%</div>
                                                    </div>
                                                </td>
                                            {{--@endif--}}
                                            {{--@if($set['level']>=2)--}}
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="love[commission][rule][level_0][second_level_rate]"
                                                               class="form-control"
                                                               value="{{ number_format($love_commission_set['rule']['level_0']['second_level_rate']?:0, 2) }}"/>
                                                        <div class="input-group-addon">%</div>
                                                    </div>
                                                </td>
                                            {{--@endif--}}
                                            {{--@if($set['level']>=3)--}}
                                                {{--<td>--}}
                                                    {{--<div class="input-group">--}}
                                                        {{--<input type="text" name="love[commission][rule][level_0][third_level_rate]"--}}
                                                               {{--class="form-control"--}}
                                                               {{--value="{{ number_format($love_commission_set['rule']['level_0']['third_level_rate']?:0, 2) }}"/>--}}
                                                        {{--<div class="input-group-addon">%</div>--}}
                                                    {{--</div>--}}
                                                {{--</td>--}}
                                            {{--@endif--}}
                                        </tr>

                                        @foreach($levels as $level)
                                            <tr>
                                                <td>{{$level->name}}</td>
                                                {{--@if($set['level']>=1)--}}
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="text" name="love[commission][rule][level_{{$level->id}}][first_level_rate]"
                                                                   class="form-control"
                                                                   value="{{ number_format($love_commission_set['rule']['level_'.$level->id]['first_level_rate']?:0, 2) }}"/>
                                                            <div class="input-group-addon">%</div>
                                                        </div>
                                                    </td>
                                                {{--@endif--}}
                                                {{--@if($set['level']>=2)--}}
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="text" name="love[commission][rule][level_{{$level->id}}][second_level_rate]"
                                                                   class="form-control"
                                                                   value="{{ number_format($love_commission_set['rule']['level_'.$level->id]['second_level_rate']?:0, 2) }}"/>
                                                            <div class="input-group-addon">%</div>
                                                        </div>
                                                    </td>
                                                {{--@endif--}}
                                                {{--@if($set['level']>=3)--}}
                                                    {{--<td>--}}
                                                        {{--<div class="input-group">--}}
                                                            {{--<input type="text" name="love[commission][rule][level_{{$level->id}}][third_level_rate]"--}}
                                                                   {{--class="form-control"--}}
                                                                   {{--value="{{ number_format($love_commission_set['rule']['level_'.$level->id]['third_level_rate']?:0, 2) }}"/>--}}
                                                            {{--<div class="input-group-addon">%</div>--}}
                                                        {{--</div>--}}
                                                    {{--</td>--}}
                                                {{--@endif--}}
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                {{--</div>--}}
                @endif

                @if($pluginCommission)
                    <div class='panel-heading'>{{ trans('Yunshop\Love::award_set.commission_award_set') }}</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                {{ trans('Yunshop\Love::award_set.commission_award_set_title') }}
                            </label>
                            <div class="col-sm-4 col-xs-6">
                                <label class="radio-inline">
                                    <input type="radio" name="love[commission_award]" value="1"
                                           @if ($love['commission_award'] == 1) checked="checked" @endif />
                                    {{ trans('Yunshop\Love::award_set.on') }}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="love[commission_award]" value="0"
                                           @if ($love['commission_award'] == 0) checked="checked" @endif />
                                    {{ trans('Yunshop\Love::award_set.off') }}
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                {{ trans('Yunshop\Love::award_set.commission_award_proportion_title') }}
                            </label>
                            <div class="col-sm-4 col-xs-6">
                                <div class='input-group' style="width: 286px;">
                                    <input type='text' name='love[commission_award_proportion]' class="form-control"
                                           value="{{ $love['commission_award_proportion'] }}" />
                                    <div class="input-group-addon">{{\Yunshop\Love\Common\Services\SetService::getLoveName()}}</div>
                                </div>

                                <div class="help-block">
                                    {{ trans('Yunshop\Love::award_set.commission_award_set_introduce') }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                {{trans('Yunshop\Love::award_set.commission_award_times')}}
                            </label>
                            <div class="col-sm-6 col-xs-6">
                                <div class='input-group'>
                                    <label class="radio-inline">
                                        <input type="radio" name="love[commission_award_times]" value="0"
                                               @if($love['commission_award_times'] == 0) checked="checked" @endif />
                                        {{trans('Yunshop\Love::award_set.commission_every_day')}}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-6 col-xs-6" style="width: 248px;">
                                <select name='love[commission_every_day]' class='form-control'>
                                    @foreach($hourData as $hour)
                                        <option value='{{$hour['key']}}'
                                                @if($love['commission_every_day'] == $hour['key']) selected @endif>{{$hour['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                @endif


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="{{ trans('Yunshop\Love::award_set.submit') }}"
                               class="btn btn-primary col-lg-1" onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script language="javascript">
        $(function () {
            $(":radio[name='love[award]']").click(function () {
                if ($(this).val() == 1) {
                    $("#love_award").show();
                }
                else {
                    $("#love_award").hide();
                }
            });
            $(":radio[name='love[parent_award]']").click(function () {
                if ($(this).val() == 1) {
                    $("#love_parent_award").show();
                } else {
                    $("#love_parent_award").hide();
                }
            });
            $(":radio[name='love[parent_award]']").click(function () {
                if ($(this).val() == 2) {
                    $("#commission_level_give_status").show();
                }
                else {
                    $("#commission_level_give_status").hide();
                }
            });

            $(":radio[name='love[reward_rule]']").click(function () {
                if ($(this).val() == 4) {
                    $("#profit_reward_proportion_title").show();
                }
                else {
                    $("#profit_reward_proportion_title").hide();
                }
            });

        })
    </script>

@endsection

