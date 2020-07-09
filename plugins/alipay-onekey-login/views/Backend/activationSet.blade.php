@extends('layouts.base')
@section('title', trans('Yunshop\Love::activation_set.title'))
@section('content')

    <div class="rightlist">

        @include('Yunshop\Love::Backend.tabs')

        <form action="{{ yzWebUrl('plugin.love.Backend.Controllers.activation-set.store') }}" method="post"
              class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>


                <div class='panel-heading'>激活时间</div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">激活类型</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="love[activation_time]" value="1"
                                       @if ($love['activation_time'] == '1') checked="checked" @endif />
                                每天激活
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[activation_time]" value="2"
                                       @if ($love['activation_time'] == '2') checked="checked" @endif />
                                每周激活
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="love[activation_time]" value="3"
                                       @if ($love['activation_time'] == '3') checked="checked" @endif />
                                每月激活
                            </label>
                            <div class="help-block">

                            </div>
                        </div>
                    </div>
                    <div id='activation_time_month' @if($love['activation_time'] != 3)style="display:none"@endif>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">每月激活</label>
                            <div class="col-sm-6 col-xs-6">
                                <div class='input-group'>
                                    <label class="radio-inline">
                                        <input type="text" class="form-control" placeholder=" 默认每月1号" disabled style="width: 187px;"/>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id='activation_time_week' @if($love['activation_time'] != 2)style="display:none"@endif>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">每周激活</label>
                            <div class="col-sm-6 col-xs-6">
                                <div class='input-group'>
                                    <label class="radio-inline">
                                        <select name='love[activation_time_week]' class='form-control' style="width: 188px;">

                                            @foreach($week_data as $key => $week)
                                                <option value='{{ $key }}'
                                                        @if($key == $love['activation_time_week']) selected @endif>{{ $week }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">激活时间</label>
                        <div class="col-sm-6 col-xs-6">
                            <div class='input-group'>
                                <label class="radio-inline">
                                    <select name='love[activation_time_hour]' class='form-control' style="width: 188px;">
                                        <option value='0' @if(empty($love['activation_time_hour'])) selected @endif>关闭激活</option>
                                        @foreach($day_data as $key => $week)
                                            <option value='{{ $key }}' @if($key == $love['activation_time_hour']) selected @endif>{{ $week }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <div class="help-block">
                                    &nbsp;&nbsp;&nbsp;激活时间请勿随意修改，修改注意：<br>
                                    &nbsp;&nbsp;&nbsp;修改时间++，时间顺延（如已经激活过，则会二次激活），修改时间 - -，可能会导致不激活
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <div class='panel-heading'>激活比例设置</div>
                <div class='panel-body'>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">固定激活比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="input-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">激活冻结值百分比</div>
                                        <input type="text" name="love[activation_proportion]" class="form-control"
                                               value="{{ $love['activation_proportion'] }}" placeholder=""/>
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </div>
                                <div class="help-block">
                                    激活当前会员冻结值的 N%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级会员下线激活比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="input-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">激活周期订单金额</div>
                                        <input type="text" name="love[level_one_proportion]" class="form-control"
                                               value="{{ $love['level_one_proportion'] }}" placeholder=""/>
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </div>
                                <div class="help-block">
                                    一级会员下线上周期完成订单金额的百分比，无激活上限<br>
                                    激活周期订单金额：激活时间中，激活类型设置类型控制，如 每天即昨天、每周即上周、每月即上个月
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">二、三级会员下线激活比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="input-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">激活周期期单金额</div>
                                        <input type="text" name="love[level_two_proportion]" class="form-control"
                                               value="{{ $love['level_two_proportion'] }}" placeholder=""/>
                                        <div class="input-group-addon">%，最高激活上限比例</div>
                                        <input type="text" name="love[level_two_fetter_proportion]" class="form-control"
                                               value="{{ $love['level_two_fetter_proportion'] }}" placeholder=""/>
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </div>
                                <div class="help-block">
                                    激活周期订单金额：激活时间中，激活类型设置类型控制，如 每天即昨天、每周即上周、每月即上个月<br>
                                    激活二、三级下线会员周期完成订单金额的百分比，最高激活上限比例为最后一次代理升级赠送值的百分比
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">团队激活比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="input-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">激活周期订单金额</div>
                                        <input type="text" name="love[team_proportion]" class="form-control"
                                               value="{{ $love['team_proportion'] }}" placeholder=""/>
                                        <div class="input-group-addon">%，最高激活上限比例：（使用二、三级会员下线最高激活上限比例）</div>
                                    </div>
                                </div>
                                <div class="help-block">
                                    团队周期完成订单金额的百分比，最高激活上限比例为最后一次代理升级赠送值的百分比<br>
                                    激活周期订单金额：激活时间中，激活类型设置类型控制，如 每天即昨天、每周即上周、每月即上个月
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">利润激活比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="input-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">激活周期订单利润</div>
                                        <input type="text" name="love[profit_proportion]" class="form-control" value="{{ $love['profit_proportion'] }}" placeholder=""/>
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </div>
                                <div class="help-block">
                                    激活周期订单利润：激活时间中，激活类型设置类型控制，如 每天即昨天、每周即上周、每月即上个月<br>
                                    计算规则：（客户持有冻结量 ÷ 总持有冻结量）×（平台上周期完成订单(完成时间)商品利润× 设定比例）= 周期内可激活爱心值
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class='panel-heading'>{{trans('Yunshop\Love::activation_set.accelerate')}}</div>
                    <div class='panel-body'>


                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{trans('Yunshop\Love::activation_set.accelerate_state')}}</label>
                                <div class="col-sm-4 col-xs-6">
                                    <label class="radio-inline">
                                        <input type="radio" name="love[activation_state]" value="1"
                                               @if ($love['activation_state'] == '1') checked="checked" @endif />
                                        {{trans('Yunshop\Love::activation_set.on')}}
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="love[activation_state]" value="0"
                                               @if ($love['activation_state'] == '0' || !$love['activation_state']) checked="checked" @endif />
                                        {{trans('Yunshop\Love::activation_set.off')}}
                                    </label>
                                    <div class="help-block">

                                    </div>
                                </div>
                            </div>

                        <div class='panel-body' id="love_accelerate">
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{trans('Yunshop\Love::activation_set.accelerate_proportion')}}</label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="input-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">{{trans('Yunshop\Love::activation_set.accelerate_proportion')}}</div>
                                            <input type="text" name="love[love_accelerate]" class="form-control"
                                                   value="{{ $love['love_accelerate'] }}" placeholder=""/>
                                            <div class="input-group-addon">%</div>
                                        </div>
                                    </div>
                                    <div class="help-block">
                                        {{trans('Yunshop\Love::activation_set.accelerated_description')}}
                                    </div>
                                </div>
                            </div>
                        </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9">
                            <input type="submit" name="submit" value="{{ trans('Yunshop\Love::base_set.submit') }}"
                                   class="btn btn-primary col-lg-1" onclick='return formcheck()'/>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
    <script language="javascript">
        $(function () {
            $(":radio[name='love[activation_time]']").click(function () {
                if ($(this).val() == 3) {
                    $("#activation_time_month").show();
                }
                else {
                    $("#activation_time_month").hide();
                }
                if ($(this).val() == 2) {
                    $("#activation_time_week").show();
                }
                else {
                    $("#activation_time_week").hide();
                }
            });

            $(":radio[name='love[activation_state]']").click(function () {
                if ($(this).val() == 0) {
                    $("#love_accelerate").hide();
                    $(":input[name='love[love_accelerate]']").val(0);
                }
                else {
                    $("#love_accelerate").show();
                }
            });
            if("{{$love['activation_state']}}" == 0){
                $("#love_accelerate").hide();
                $(":input[name='love[love_accelerate]']").val(0);
            }
        })
    </script>

@endsection

