@if(array_key_exists('commission', $exist_plugins))
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启分销</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='widgets[commission][is_commission]' value='1'
                       @if($exist_plugins['commission']['commission_goods']['is_commission'] == 1) checked @endif
                /> 是
            </label>
            <label class='radio-inline'>
                <input type='radio' name='widgets[commission][is_commission]' value='0'
                       @if($exist_plugins['commission']['commission_goods']['is_commission'] == 0) checked @endif
                /> 否
            </label>
        </div>
    </div>
    {{--  <input type="hidden" name="widgets[commission][is_commission]" value="1">  --}}
    <input type="hidden" name="widgets[commission][show_commission_button]" value="1">
    <input type="hidden" name="widgets[commission][has_commission]" value="1">

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销</label>
        <div class="col-sm-9 col-xs-12">
            <div class='panel-body'>
                <div class="table-responsive ">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style="width: 10%">等级名称</th>
                            <th style="text-align: center;">一级分销</th>
                            <th style="text-align: center;">二级分销</th>
                            <th style="text-align: center;">三级分销</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>默认等级</td>
                            <td>
                                <div class="input-group">
                                    <input onkeyup="value=value.replace(/[^\d.]/g,'')" type="text" name="widgets[commission][rule][level_0][first_level_rate]"
                                           class="form-control"
                                           value="{{$exist_plugins['commission']['commission_goods']->rule['level_0']['first_level_rate']}}"/>
                                    <div class="input-group-addon">%</div>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input onkeyup="value=value.replace(/[^\d.]/g,'')" type="text" name="widgets[commission][rule][level_0][second_level_rate]"
                                           class="form-control"
                                           value="{{$exist_plugins['commission']['commission_goods']->rule['level_0']['second_level_rate']}}"/>
                                    <div class="input-group-addon">%</div>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input onkeyup="value=value.replace(/[^\d.]/g,'')" type="text" name="widgets[commission][rule][level_0][third_level_rate]"
                                           class="form-control"
                                           value="{{$exist_plugins['commission']['commission_goods']->rule['level_0']['third_level_rate']}}"/>
                                    <div class="input-group-addon">%</div>
                                </div>
                            </td>
                        </tr>


                        @foreach($exist_plugins['commission']['commission_levels'] as $level)
                            <tr>
                                <td>{{$level->name}}</td>
                                <td>
                                    <div class="input-group">
                                        <input onkeyup="value=value.replace(/[^\d.]/g,'')" type="text" name="widgets[commission][rule][level_{{$level->id}}][first_level_rate]"
                                               class="form-control"
                                               value="{{$exist_plugins['commission']['commission_goods']->rule['level_'.$level->id]['first_level_rate']}}"/>
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input onkeyup="value=value.replace(/[^\d.]/g,'')" type="text" name="widgets[commission][rule][level_{{$level->id}}][second_level_rate]"
                                               class="form-control"
                                               value="{{$exist_plugins['commission']['commission_goods']->rule['level_'.$level->id]['second_level_rate']}}"/>
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input onkeyup="value=value.replace(/[^\d.]/g,'')" type="text" name="widgets[commission][rule][level_{{$level->id}}][third_level_rate]"
                                               class="form-control"
                                               value="{{$exist_plugins['commission']['commission_goods']->rule['level_'.$level->id]['third_level_rate']}}"/>
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

@if(array_key_exists('team-dividend', $exist_plugins))
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启经销商</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='widgets[team_dividend][is_dividend]' value='1'
                       @if($exist_plugins['team-dividend']['team_dividend_goods']['is_dividend'] == 1) checked @endif
                /> 是
            </label>
            <label class='radio-inline'>
                <input type='radio' name='widgets[team_dividend][is_dividend]' value='0'
                       @if($exist_plugins['team-dividend']['team_dividend_goods']['is_dividend'] == 0) checked @endif
                /> 否
            </label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">经销商提成</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                {{--  <input type="hidden" name="widgets[team_dividend][is_dividend]" value="1">  --}}
                <input type="hidden" name="widgets[team_dividend][has_dividend]" value="1">
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[team_dividend][has_dividend_rate]' class="form-control discounts_value"
                       value="{{$exist_plugins['team-dividend']['team_dividend_goods']['has_dividend_rate']?$exist_plugins['team-dividend']['team_dividend_goods']['has_dividend_rate']:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
@endif

@if(array_key_exists('area-dividend', $exist_plugins))
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启区域分红</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='widgets[area_dividend][is_dividend]' value='1'
                       @if($exist_plugins['area-dividend']['area_dividend_goods']['is_dividend'] == 1) checked @endif
                /> 是
            </label>
            <label class='radio-inline'>
                <input type='radio' name='widgets[area_dividend][is_dividend]' value='0'
                       @if($exist_plugins['area-dividend']['area_dividend_goods']['is_dividend'] == 0) checked @endif
                /> 否
            </label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">区域分红</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                {{--  <input type="hidden" name="widgets[area_dividend][is_dividend]" value="1">  --}}
                <input type="hidden" name="widgets[area_dividend][has_dividend]" value="1">
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[area_dividend][has_dividend_rate]' class="form-control discounts_value"
                       value="{{$exist_plugins['area-dividend']['area_dividend_goods']['has_dividend_rate']?$exist_plugins['area-dividend']['area_dividend_goods']['has_dividend_rate']:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
@endif

@if(array_key_exists('merchant', $exist_plugins))
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">招商员独立分红</label>
        <div class="col-sm-6 col-xs-12">
            <div class="input-group">

                <input type="hidden" name="setting[merchant][is_open_bonus_staff]" value="1">
                <input type="hidden" name="setting[merchant][is_open_bonus_center]" value="1">

                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type="text" name="widgets[merchant][staff_bonus]" class="form-control" value="{{$exist_plugins['merchant']['staff_bonus']?$exist_plugins['merchant']['staff_bonus']:0}}" />
                <span class="input-group-addon">%</span>
            </div>
        </div>
    </div>
    @foreach($exist_plugins['merchant_levels'] as $key => $level)
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">@if($key == 0)招商中心等级独立分红@endif</label>
            <div class="col-sm-6 col-xs-12">
                <div class='input-group'>
                    <span class="input-group-addon">{{$level->level_name}}</span>
                    <input onkeyup="value=value.replace(/[^\d.]/g,'')" type="text" name="widgets[merchant][level][{{$level->id}}]"  value="{{$exist_plugins['merchant']->level[$level->id]?$exist_plugins['merchant']->level[$level->id]:0}}" class="form-control" />
                    <span class="input-group-addon">%</span>
                </div>
            </div>
        </div>
    @endforeach
@endif