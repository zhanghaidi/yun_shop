<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$pluginName}}启用</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline">
            <input type="radio" name="setdata[is_clock_in]" value="0"
                   @if($set['is_clock_in'] == 0) checked="checked" @endif /> 禁用</label>
        <label class="radio-inline">
            <input type="radio" name="setdata[is_clock_in]" value="1"
                   @if($set['is_clock_in'] == 1) checked="checked" @endif /> 启用</label>
        <span class='help-block'>{{$pluginName}}关闭后，不会获得{{$pluginName}}奖励。</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义名称</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="setdata[plugin_name]" class="form-control" value="{{$set['plugin_name']}}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">参与{{$pluginName}}支付金额</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input type='text' name='setdata[amount]' class="form-control"
                   value="{{$set['amount']}}"/>
            <div class='input-group-addon'>元</div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">参与{{$pluginName}}打卡时间</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <select name='setdata[starttime]' class='form-control'>
                @foreach($hourData as $hour)
                    <option value='{{$hour['key']}}'
                            @if($set['starttime'] == $hour['key']) selected @endif>{{$hour['name']}}</option>
                @endforeach
            </select>
            <div class='input-group-addon'> 至</div>
            <select name='setdata[endtime]' class='form-control'>
                @foreach($hourData as $hour)
                    <option value='{{$hour['key']}}'
                            @if($set['endtime'] == $hour['key']) selected @endif>{{$hour['name']}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">奖金比例</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input type='text' name='setdata[rate]' class="form-control"
                   value="{{$set['rate']}}"/>
            <div class='input-group-addon'>%</div>
        </div>
        <span class='help-block'>每期总发放奖金金额=前一周期（前一天）支付金额*百分比；</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">前三名获得金额</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <div class='input-group-addon'>第一名</div>
            <input type='text' name='setdata[first]' class="form-control"
                   value="{{$set['first']}}"/>
            <div class='input-group-addon'>元</div>
        </div>
        <div class='input-group'>
            <div class='input-group-addon'>第二名</div>
            <input type='text' name='setdata[second]' class="form-control"
                   value="{{$set['second']}}"/>
            <div class='input-group-addon'>元</div>
        </div>
        <div class='input-group'>
            <div class='input-group-addon'>第三名</div>
            <input type='text' name='setdata[third]' class="form-control"
                   value="{{$set['third']}}"/>
            <div class='input-group-addon'>元</div>
        </div>
        <span class='help-block'>不设置前三名获得金额，将会参与瓜分奖金池金额。</span>
    </div>
</div>

@if ($isOpenCommission)
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
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销层级</label>
            <div class="col-sm-4">
                <select class="form-control" name="setdata[level]">
                    <option value="1" @if(isset($set['level']) && $set['level']==1) selected @endif>一级分销
                    </option>
                    <option value="2" @if(isset($set['level']) && $set['level']==2) selected @endif>二级分销
                    </option>
                    {{--<option value="3" @if(isset($set['level']) && $set['level']==3) selected @endif>三级分销--}}
                    {{--</option>--}}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销比例</label>
            <div class="col-sm-6 col-xs-6">
                <div class='input-group'>
                    <div class='input-group-addon'>一级分销比例</div>
                    <input type='text' name='setdata[first_level]' class="form-control"
                           value="{{$set['first_level']}}"/>
                    <div class='input-group-addon'>%</div>
                </div>
                <div class='input-group'>
                    <div class='input-group-addon'>二级分销比例</div>
                    <input type='text' name='setdata[second_level]' class="form-control"
                           value="{{$set['second_level']}}"/>
                    <div class='input-group-addon'>%</div>
                </div>
                {{--<div class='input-group'>--}}
                    {{--<div class='input-group-addon'>三级分销比例</div>--}}
                    {{--<input type='text' name='setdata[third_level]' class="form-control"--}}
                           {{--value="{{$set['third_level']}}"/>--}}
                    {{--<div class='input-group-addon'>%</div>--}}
                {{--</div>--}}
                <span class='help-block'>只能填写正整数，其他无效。</span>
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
            </div>
        </div>

        @foreach($agent_levels as $level)
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$level->name}}</label>
                <div class="col-sm-6 col-xs-6">
                    <div class='input-group'>
                        <div class='input-group-addon'>一级分销比例</div>
                        <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='setdata[commission_level][{{$level->id}}][first_level]' class="form-control"
                               value="{{$set['commission_level'][$level->id]['first_level']}}"/>
                        <div class='input-group-addon'>%</div>
                    </div>
                    <div class='input-group'>
                        <div class='input-group-addon'>二级分销比例</div>
                        <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='setdata[commission_level][{{$level->id}}][second_level]' class="form-control"
                               value="{{$set['commission_level'][$level->id]['second_level']}}"/>
                        <div class='input-group-addon'>%</div>
                    </div>
                    {{--<div class='input-group'>--}}
                        {{--<div class='input-group-addon'>三级分销比例</div>--}}
                        {{--<input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='setdata[commission_level][{{$level->id}}][third_level]' class="form-control"--}}
                               {{--value="{{$set['commission_level'][$level->id]['third_level']}}"/>--}}
                        {{--<div class='input-group-addon'>%</div>--}}
                    {{--</div>--}}
                </div>
            </div>
        @endforeach

    </div>
@endif
@if ($isOpenTeam)
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">经销商管理分红</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline">
                    <input type="radio" name="setdata[is_team_dividend]" value="0"
                           @if($set['is_team_dividend'] == 0)
                           checked="checked" @endif />
                    关闭</label>
                <label class="radio-inline">
                    <input type="radio" name="setdata[is_team_dividend]" value="1"
                           @if($set['is_team_dividend'] == 1)
                           checked="checked" @endif />
                    开启</label>
            </div>
        </div>
    </div>
    @foreach($team_levels as $level)
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$level->level_name}}</label>
            <div class="col-sm-6 col-xs-6">
                <div class='input-group'>
                    <div class='input-group-addon'>提成比例</div>
                    <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='setdata[team_level][{{$level->id}}][dividend_ratio]' class="form-control"
                           value="{{$set['team_level'][$level->id]['dividend_ratio']}}"/>
                    <div class='input-group-addon'>%</div>
                </div>
                {{--<div class='input-group'>
                    <div class='input-group-addon'>平级层级</div>
                    <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='setdata[team_level][{{$level->id}}][award_hierarchy]' class="form-control"
                           value="{{$set['team_level'][$level->id]['award_hierarchy']}}"/>
                    <div class='input-group-addon'>层</div>
                </div>
                <div class='input-group'>
                    <div class='input-group-addon'>平级比例</div>
                    <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='setdata[team_level][{{$level->id}}][award_ratio]' class="form-control"
                           value="{{$set['team_level'][$level->id]['award_ratio']}}"/>
                    <div class='input-group-addon'>%</div>
                </div>--}}
            </div>
        </div>
    @endforeach
@endif


























