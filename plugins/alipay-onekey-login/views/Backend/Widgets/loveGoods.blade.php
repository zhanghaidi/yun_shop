<div class='panel panel-default'>

    <div class='panel-heading'>{{ trans('Yunshop\Love::love_goods.award_set') }}</div>
    <div class='panel-body'>
        @if($set['award'] == 1)
            <div class='panel-body'>

                {{--<div class="form-group">--}}
                    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::award_set.shopping-award_set') }}</label>--}}
                    {{--<div class="col-sm-4 col-xs-6">--}}
                        {{--<label class="radio-inline">--}}
                            {{--<input type="radio" name="love[rule]" value="1"--}}
                                   {{--@if ($love['rule'] == 1) checked="checked" @endif />--}}
                            {{--{{ trans('Yunshop\Love::award_set.present') }}--}}
                        {{--</label>--}}
                        {{--<label class="radio-inline">--}}
                            {{--<input type="radio" name="love[rule]" value="0"--}}
                                   {{--@if ($love['rule'] == 0) checked="checked" @endif />--}}
                            {{--{{ trans('Yunshop\Love::award_set.actual') }}--}}
                        {{--</label>--}}
                        {{--<div class="help-block">--}}
                            {{--{{ trans('Yunshop\Love::award_set.award_set_introduce') }}--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::love_goods.award_set_title') }}：</label>
                    <div class="col-sm-4 col-xs-6">
                        <label class="radio-inline">
                            <input type="radio" name="widgets[love][award]" value="1" @if ($goods['award'] == 1) checked="checked" @endif />
                            {{ trans('Yunshop\Love::love_goods.on') }}
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="widgets[love][award]" value="0" @if ($goods['award'] == 0) checked="checked" @endif />
                            {{ trans('Yunshop\Love::love_goods.off') }}
                        </label>
                    </div>
                </div>
            </div>
            <div id='love_goods_award' @if(empty($goods['award']))style="display:none"@endif>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <div class="input-group">
                                <div class="input-group-addon">{{ trans('Yunshop\Love::love_goods.award_set_hint') }}</div>
                                <input type="text" name="widgets[love][award_proportion]" class="form-control" value="{{ $goods['award_proportion'] }}" placeholder="0.00"/>
                                <div class="input-group-addon">%</div>
                            </div>
                        </div>
                        <div class="help-block">
                            {{ trans('Yunshop\Love::love_goods.award_set_introduce') }}
                        </div>
                     </div>
                </div>
            </div>
        @else
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12" style="padding-bottom: 30px; padding-top: 15px;">
                    <input type="hidden" name="widgets[love][award_proportion]" value="{{ $goods['award_proportion'] }}">
                    {{ trans('Yunshop\Love::love_goods.award_off_hint') }}
                    “<a href="{{ yzWebUrl('plugin.love.Backend.Controllers.award-set.see') }}">{{ trans('Yunshop\Love::love_goods.award_off_url') }}</a>”
                </div>
            </div>
        @endif
    </div>

    <div class='panel-heading'>{{ trans('Yunshop\Love::love_goods.parent_award_set_title') }}</div>
    <div class='panel-body'>
        @if($set['parent_award'] == 1)
            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::love_goods.parent_award_set_title') }}：</label>
                    <div class="col-sm-4 col-xs-6">
                        <label class="radio-inline">
                            <input type="radio" name="widgets[love][parent_award]" value="1" @if ($goods['parent_award'] == 1) checked="checked" @endif />
                            {{ trans('Yunshop\Love::love_goods.on') }}
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="widgets[love][parent_award]" value="0" @if ($goods['parent_award'] == 0) checked="checked" @endif />
                            {{ trans('Yunshop\Love::love_goods.off') }}
                        </label>
                    </div>
                </div>
            </div>
            {{--<div id='love_goods_parent_award' @if($goods['parent_award'] != 1) style="display:none" @endif>--}}
            <div id='love_goods_parent_award' @if($goods['parent_award'] != 1) style="display:none" @endif>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <div class="input-group">
                                <div class="input-group-addon">{{ trans('Yunshop\Love::love_goods.one_parent_award_set_hint') }}</div>
                                <input type="text" name="widgets[love][parent_award_proportion]" class="form-control" value="{{ $goods['parent_award_proportion'] }}" placeholder="0.00"/>
                                <div class="input-group-addon">%，或固定值</div>
                                <input type="text" name="widgets[love][parent_award_fixed]" class="form-control" value="{{ $goods['parent_award_fixed'] }}" placeholder="0.00"/>
                                <div class="input-group-addon">{{ trans('Yunshop\Love::love_goods.love_name') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <div class="input-group">
                                <div class="input-group-addon">{{ trans('Yunshop\Love::love_goods.two_parent_award_set_hint') }}</div>
                                <input type="text" name="widgets[love][second_award_proportion]" class="form-control" value="{{ $goods['second_award_proportion'] }}" placeholder="0.00"/>
                                <div class="input-group-addon">%，或固定值</div>
                                <input type="text" name="widgets[love][second_award_fixed]" class="form-control" value="{{ $goods['second_award_fixed'] }}" placeholder="0.00"/>
                                <div class="input-group-addon">{{ trans('Yunshop\Love::love_goods.love_name') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--<div class="form-group">--}}
                    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
                    {{--<div class="col-sm-9 col-xs-12">--}}
                        {{--<div class="input-group">--}}
                            {{--<div class="input-group">--}}
                                {{--<div class="input-group-addon">{{ trans('Yunshop\Love::love_goods.third_parent_award_set_hint') }}</div>--}}
                                {{--<input type="text" name="widgets[love][third_award_proportion]" class="form-control" value="{{ $goods['third_award_proportion'] }}" placeholder="0.00"/>--}}
                                {{--<div class="input-group-addon">%，或固定值</div>--}}
                                {{--<input type="text" name="widgets[love][third_award_fixed]" class="form-control" value="{{ $goods['third_award_fixed'] }}" placeholder="0.00"/>--}}
                                {{--<div class="input-group-addon">{{ trans('Yunshop\Love::love_goods.love_name') }}</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="help-block">--}}
                            {{--{{ trans('Yunshop\Love::love_goods.third_parent_award_set_introduce') }}--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            </div>
        @elseif($set['parent_award'] == 2)
            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::award_set.commission_level_superior_give') }}
                        ：</label>
                    <div class="col-sm-4 col-xs-6">
                        <label class="radio-inline">
                            <input type="radio" name="widgets[love][commission_level_give]" value="1"
                                   @if ($goods['commission_level_give'] == 1) checked="checked" @endif />
                            {{ trans('Yunshop\Love::award_set.on') }}
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="widgets[love][commission_level_give]" value="0"
                                   @if ($goods['commission_level_give'] == 0) checked="checked" @endif />
                            {{ trans('Yunshop\Love::award_set.off') }}
                        </label>
                    </div>
                </div>
            </div>
            <div id="commission_level_give_status" class="form-group" @if($goods['commission_level_give'] !=1 ) style="display:none" @endif>
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <div class='panel-body'>
                        <div class="table-responsive ">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 10%">{{ trans('Yunshop\Love::award_set.level_name') }}</th>
                                    {{--@if($commission_set['level']>=1)--}}
                                    <th style="text-align: center;">{{ trans('Yunshop\Love::award_set.first_level_commission') }}</th>
                                    {{--@endif--}}
                                    {{--@if($commission_set['level']>=2)--}}
                                    <th style="text-align: center;">{{ trans('Yunshop\Love::award_set.second_level_commission') }}</th>
                                    {{--@endif--}}
                                    {{--@if($commission_set['level']>=3)--}}
                                    {{--<th style="text-align: center;">{{ trans('Yunshop\Love::award_set.third_level_commission') }}</th>--}}
                                    {{--@endif--}}
                                </tr>
                                </thead>
                                <tbody>

                                <tr>
                                    <td>{{ trans('Yunshop\Love::award_set.default_level') }}</td>
                                    {{--@if($commission_set['level']>=1)--}}
                                    <td>
                                        <div class="input-group">
                                            <input type="text" name="widgets[love][commission][rule][level_0][first_level_rate]"
                                                   class="form-control"
                                                   value="{{ number_format($goods['commission']['rule']['level_0']['first_level_rate']?:0, 2) }}"/>
                                            <div class="input-group-addon">%或固定值</div>
                                            <input type="text" name="widgets[love][commission][rule][level_0][first_level_fixed]"
                                                   class="form-control"
                                                   value="{{ number_format($goods['commission']['rule']['level_0']['first_level_fixed']?:0, 2) }}"/>
                                        </div>
                                    </td>
                                    {{--@endif--}}
                                    {{--@if($commission_set['level']>=2)--}}
                                    <td>
                                        <div class="input-group">
                                            <input type="text" name="widgets[love][commission][rule][level_0][second_level_rate]"
                                                   class="form-control"
                                                   value="{{ number_format($goods['commission']['rule']['level_0']['second_level_rate']?:0, 2) }}"/>
                                            <div class="input-group-addon">%或固定值</div>
                                            <input type="text" name="widgets[love][commission][rule][level_0][second_level_fixed]"
                                                   class="form-control"
                                                   value="{{ number_format($goods['commission']['rule']['level_0']['second_level_fixed']?:0, 2) }}"/>
                                        </div>
                                    </td>
                                    {{--@endif--}}
                                    {{--@if($commission_set['level']>=3)--}}
                                    {{--<td>--}}
                                        {{--<div class="input-group">--}}
                                            {{--<input type="text" name="widgets[love][commission][rule][level_0][third_level_rate]"--}}
                                                   {{--class="form-control"--}}
                                                   {{--value="{{ number_format($goods['commission']['rule']['level_0']['third_level_rate']?:0, 2) }}"/>--}}
                                            {{--<div class="input-group-addon">%或固定值</div>--}}
                                            {{--<input type="text" name="widgets[love][commission][rule][level_0][third_level_fixed]"--}}
                                                   {{--class="form-control"--}}
                                                   {{--value="{{ number_format($goods['commission']['rule']['level_0']['third_level_fixed']?:0, 2) }}"/>--}}
                                        {{--</div>--}}
                                    {{--</td>--}}
                                    {{--@endif--}}
                                </tr>

                                @foreach($levels as $level)
                                    <tr>
                                        <td>{{$level->name}}</td>
                                        {{--@if($commission_set['level']>=1)--}}
                                        <td>
                                            <div class="input-group">
                                                <input type="text" name="widgets[love][commission][rule][level_{{$level->id}}][first_level_rate]"
                                                       class="form-control"
                                                       value="{{ number_format($goods['commission']['rule']['level_'.$level->id]['first_level_rate']?:0, 2) }}"/>
                                                <div class="input-group-addon">%或固定值</div>
                                                <input type="text" name="widgets[love][commission][rule][level_{{$level->id}}][first_level_fixed]"
                                                       class="form-control"
                                                       value="{{ number_format($goods['commission']['rule']['level_'.$level->id]['first_level_fixed']?:0, 2) }}"/>
                                            </div>
                                        </td>
                                        {{--@endif--}}
                                        {{--@if($commission_set['level']>=2)--}}
                                        <td>
                                            <div class="input-group">
                                                <input type="text" name="widgets[love][commission][rule][level_{{$level->id}}][second_level_rate]"
                                                       class="form-control"
                                                       value="{{ number_format($goods['commission']['rule']['level_'.$level->id]['second_level_rate']?:0, 2) }}"/>
                                                <div class="input-group-addon">%或固定值</div>
                                                <input type="text" name="widgets[love][commission][rule][level_{{$level->id}}][second_level_fixed]"
                                                       class="form-control"
                                                       value="{{ number_format($goods['commission']['rule']['level_'.$level->id]['second_level_fixed']?:0, 2) }}"/>
                                            </div>
                                        </td>
                                        {{--@endif--}}
                                        {{--@if($commission_set['level']>=3)--}}
                                        {{--<td>--}}
                                            {{--<div class="input-group">--}}
                                                {{--<input type="text" name="widgets[love][commission][rule][level_{{$level->id}}][third_level_rate]"--}}
                                                       {{--class="form-control"--}}
                                                       {{--value="{{ number_format($goods['commission']['rule']['level_'.$level->id]['third_level_rate']?:0, 2) }}"/>--}}
                                                {{--<div class="input-group-addon">%或固定值</div>--}}
                                                {{--<input type="text" name="widgets[love][commission][rule][level_{{$level->id}}][third_level_fixed]"--}}
                                                       {{--class="form-control"--}}
                                                       {{--value="{{ number_format($goods['commission']['rule']['level_'.$level->id]['third_level_fixed']?:0, 2) }}"/>--}}
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
        @else
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12" style="padding-bottom: 30px; padding-top: 15px;">
                    <input type="hidden" name="widgets[love][parent_award_proportion]" value="{{ $goods['parent_award_proportion'] }}">
                    {{ trans('Yunshop\Love::love_goods.parent_award_off_hint') }}
                    “<a href="{{ yzWebUrl('plugin.love.Backend.Controllers.award-set.see') }}">{{ trans('Yunshop\Love::love_goods.award_off_url') }}</a>”
                </div>
            </div>
        @endif
    </div>

    {{--<div class='panel-heading'>--}}
        {{--{{ trans('Yunshop\Love::love_goods.deduction_set') }}--}}
    {{--</div>--}}
    {{--<div class='panel-body'>--}}

            {{--<div class='panel-body'>--}}
                {{--<div class="form-group">--}}
                    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::love_goods.deduction_set_title') }}：</label>--}}
                    {{--<div class="col-sm-4 col-xs-6">--}}
                        {{--<label class="radio-inline">--}}
                            {{--<input type="radio" name="widgets[love][deduction]" value="1" @if ($goods['deduction'] == 1) checked="checked" @endif />--}}
                            {{--{{ trans('Yunshop\Love::love_goods.on') }}--}}
                        {{--</label>--}}
                        {{--<label class="radio-inline">--}}
                            {{--<input type="radio" name="widgets[love][deduction]" value="0" @if ($goods['deduction'] == 0) checked="checked" @endif />--}}
                            {{--{{ trans('Yunshop\Love::love_goods.off') }}--}}
                        {{--</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div id='love_goods_deduction' @if(empty($goods['deduction']))style="display:none"@endif>--}}
                {{--<div class="form-group">--}}
                    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
                    {{--<div class="col-sm-9 col-xs-12">--}}
                        {{--<div class="input-group">--}}
                            {{--<div class="input-group">--}}
                                {{--<div class="input-group-addon">{{ trans('Yunshop\Love::love_goods.deduction_set_hint') }}</div>--}}
                                {{--<input type="text" name="widgets[love][deduction_proportion]" class="form-control" value="{{ $goods['deduction_proportion'] }}" placeholder="0.00"/>--}}
                                {{--<div class="input-group-addon">%</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="help-block">--}}
                            {{--{{ trans('Yunshop\Love::love_goods.deduction_set_introduce') }}--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="form-group">--}}
                {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
                {{--<div class="col-sm-9 col-xs-12" style="padding-bottom: 30px; padding-top: 15px;">--}}
                    {{--<input type="hidden" name="widgets[love][deduction_proportion]" value="{{ $goods['deduction_proportion'] }}">--}}
                    {{--{{ trans('Yunshop\Love::love_goods.deduction_off_hint') }}--}}
                    {{--“<a href="{{ yzWebUrl('plugin.love.Backend.Controllers.base-set.see') }}">{{ trans('Yunshop\Love::love_goods.deduction_off_url') }}</a>”--}}
                {{--</div>--}}
            {{--</div>--}}
       {{----}}
    {{--</div>--}}

    <div class='panel-heading'>{{ trans('Yunshop\Love::love_goods.activation_love') }}</div>
    <div class='panel-body'>
        @if ($set['activation_state'])
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">激活状态</label>
            <div class="col-sm-4 col-xs-6">
                <label class="radio-inline">
                    <input type="radio" name="widgets[love][activation_state]" value="1"
                           @if ($goods['activation_state'] == '1') checked="checked" @endif />
                    {{ trans('Yunshop\Love::love_goods.on') }}
                </label>
                <label class="radio-inline">
                    <input type="radio" name="widgets[love][activation_state]" value="0"
                           @if ($goods['activation_state'] == '0') checked="checked" @endif />
                    {{ trans('Yunshop\Love::love_goods.off') }}
                </label>
                <div class="help-block">

                </div>
            </div>

        </div>
        <div class='panel-body' id="love_accelerate">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <div class="input-group">
                        <div class="input-group">
                            <div class="input-group-addon">{{ trans('Yunshop\Love::love_goods.love_accelerate') }}</div>
                            <input type="text" name="widgets[love][love_accelerate]" class="form-control" value="{{ $goods['love_accelerate'] }}" placeholder=""/>
                            <div class="input-group-addon">%</div>
                        </div>
                    </div>
                    <div class="help-block">
                        {{ trans('Yunshop\Love::love_goods.activation_instructions') }}
                    </div>
                </div>
            </div>
        </div>
        @else
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12" style="padding-bottom: 30px; padding-top: 15px;">
                    <input type="hidden" name="widgets[love][deduction_proportion]" value="{{ $goods['deduction_proportion'] }}">
                    {{ trans('Yunshop\Love::love_goods.activation_off') }}
                    “<a href="{{ yzWebUrl('plugin.love.Backend.Controllers.base-set.see') }}">{{ trans('Yunshop\Love::love_goods.deduction_off_url') }}</a>”
                </div>
            </div>
        @endif
    </div>


    <div class='panel-heading'>{{ trans('Yunshop\Love::love_goods.deduction_set') }}</div>
    <div class='panel-body'>
            @if ($set['deduction'])
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::love_goods.deduction') }}</label>
            <div class="col-sm-4 col-xs-6">
                <label class="radio-inline">
                    <input type="radio" name="widgets[love][deduction]" value="1"
                           @if ($goods['deduction'] == '1') checked="checked" @endif />
                    {{ trans('Yunshop\Love::love_goods.on') }}
                </label>
                <label class="radio-inline">
                    <input type="radio" name="widgets[love][deduction]" value="0"
                           @if ($goods['deduction'] == '0' || $goods['deduction'] != 1) checked="checked" @endif />
                    {{ trans('Yunshop\Love::love_goods.off') }}
                </label>
                <div class="help-block">

                </div>
            </div>
        </div>

            {{--<div class="form-group" id="deduction">--}}
                {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
                {{--<div class="col-sm-9 col-xs-12">--}}
                    {{--<div class="input-group">--}}
                        {{--<div class="input-group">--}}
                            {{--<div class="input-group">--}}
                                {{--<div class="input-group-addon">{{ trans('Yunshop\Love::base_set.deduction_proportion_low') }}</div>--}}
                                {{--<input type="text" name="widgets[love][deduction_set_min]" class="form-control" value="{{ $goods['deduction_set_min'] }}" placeholder=""/>--}}
                                {{--<div class="input-group-addon">%</div>--}}
                                {{--<div class="input-group-addon">{{ trans('Yunshop\Love::base_set.deduction_proportion') }}</div>--}}
                                {{--<input type="text" name="widgets[love][deduction_set]" class="form-control" value="{{ $goods['deduction_set'] }}" placeholder=""/>--}}
                                {{--<div class="input-group-addon">%</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="help-block">--}}
                            {{--商品最低抵扣比例-商品最高抵扣比例--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

            <div class='panel-body' id="deduction" @if ($goods['deduction'] == '1') style="display:block" @else style="display:none" @endif>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::love_goods.deduction_set') }}</label>
                    <div class="col-sm-4">
                        <div class='input-group'>
                            <span class='input-group-addon'>{{ trans('Yunshop\Love::love_goods.deduction_proportion_low') }}</span>
                            <input type="text" name="widgets[love][deduction_proportion_low]" value="{{$goods['deduction_proportion_low']}}"
                                   class="form-control"/>
                            <span class='input-group-addon'>%</span>
                        </div>
                        <div class="help-block">
                            <span class='help-block'>商品抵扣比例，如:1~100</span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class='input-group'>
                            <span class='input-group-addon'>{{ trans('Yunshop\Love::love_goods.deduction_proportion') }}</span>
                            <input type="text" name="widgets[love][deduction_proportion]" value="{{$goods['deduction_proportion']}}"
                                   class="form-control"/>
                            <span class='input-group-addon'>%</span>
                        </div>

                    </div>

                </div>


            </div>

        @else
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12" style="padding-bottom: 30px; padding-top: 15px;">
                    <input type="hidden" name="widgets[love][deduction_proportion]" value="{{ $goods['deduction_proportion'] }}">
                    {{ trans('Yunshop\Love::love_goods.deduction_off') }}
                    “<a href="{{ yzWebUrl('plugin.love.Backend.Controllers.base-set.see') }}">{{ trans('Yunshop\Love::love_goods.deduction_off_url') }}</a>”
                </div>
            </div>
        @endif
    </div>



    <script language="javascript">
        $(function () {


            $(":radio[name='widgets[love][deduction]']").click(function () {
                if ($(this).val() == 1) {
                    $("#deduction").show();
                }
                else {
                    $("#deduction").hide();
                }
            });
            $(":radio[name='widgets[love][award]']").click(function () {
                if ($(this).val() == 1) {
                    $("#love_goods_award").show();
                }
                else {
                    $("#love_goods_award").hide();
                }
            });
            $(":radio[name='widgets[love][parent_award]']").click(function () {
                if ($(this).val() == 1) {
                    $("#love_goods_parent_award").show();
                }
                else {
                    $("#love_goods_parent_award").hide();
                }
            });
            $(":radio[name='widgets[love][activation_state]']").click(function () {
                if ($(this).val() == 0) {
                    $("#love_accelerate").hide();
                    $(":input[name='widgets[love][love_accelerate]']").val();
                }
                else {
                    $("#love_accelerate").show();
                }
            });
            $(":radio[name='widgets[love][commission_level_give]']").click(function () {
                if ($(this).val() == 1) {
                    $("#commission_level_give_status").show();
                }
                else {
                    $("#commission_level_give_status").hide();
                }
            });

            if ("{{$goods['activation_state']}}" == 0){
                $("#love_accelerate").hide();
                $(":input[name='widgets[love][love_accelerate]']").val();
            }

            $(":radio[name='widgets[love][deduction]']").click(function () {
                if ($(this).val() == 0) {
                    $("#deduction").hide();
                    $(":input[name='widgets[love][deduction_proportion]']").val();
                    $(":input[name='widgets[love][deduction_proportion_low]']").val();
                }
                else {
                    $("#deduction").show();
                }
            });
            if ("{{$goods['deduction']}}" == 0){
                $(":input[name='widgets[love][deduction_proportion]']").val();
                $(":input[name='widgets[love][deduction_proportion_low]']").val();
            }
        })
    </script>



</div>