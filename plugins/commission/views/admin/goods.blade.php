<div class='panel panel-default'>
    <div class='panel-heading'>
        分销设置
    </div>
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启分销</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline">
                    <input type="radio" name="widgets[commission][is_commission]" value="0"
                           @if($item['is_commission'] == '0') checked="checked" @endif /> 关闭</label>
                <label class="radio-inline">
                    <input type="radio" name="widgets[commission][is_commission]" value="1"
                           @if($item['is_commission'] == '1') checked="checked" @endif /> 开启</label>
                <span class='help-block'>如果不开启分销，则不产生分销佣金</span>
            </div>
        </div>

        {{--<div class="form-group">--}}
            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">显示"我要分销"按钮</label>--}}
            {{--<div class="col-sm-9 col-xs-12">--}}

                {{--<label class="radio-inline">--}}
                    {{--<input type="radio" value="0" name="widgets[commission][show_commission_button]"--}}
                           {{--@if($item['show_commission_button'] == '0') checked="checked" @endif /> 显示--}}
                {{--</label>--}}
                {{--<label class="radio-inline">--}}
                    {{--<input type="radio" value="1" name="widgets[commission][show_commission_button]"--}}
                           {{--@if($item['show_commission_button'] == '1') checked="checked" @endif /> 隐藏--}}
                {{--</label>--}}
                {{--<span class="help-block">如果隐藏了按钮，在参与分销的情况下，按钮只是隐藏，分享其他人购买后依然产生分销佣金</span>--}}

            {{--</div>--}}
        {{--</div>--}}

        {{--<div class="form-group">--}}
            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">海报图片</label>--}}
            {{--<div class="col-sm-9 col-xs-12">--}}
                {{--{!! app\common\helpers\ImageHelper::tplFormFieldImage('widgets[commission][poster_picture]', $item['poster_picture'])!!}--}}
                {{--<span class='help-block'>尺寸: 640*640，如果为空默认缩略图片</span>--}}

            {{--</div>--}}
        {{--</div>--}}

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">独立规则</label>
            <div class="col-sm-9 col-xs-12">
                <label class="checkbox-inline">
                    <input type="checkbox" id="hascommission" value="1" name="widgets[commission][has_commission]"
                           @if($item['has_commission'] == '1') checked="checked" @endif />启用独立佣金比例
                </label>
                <span class="help-block">启用独立佣金设置，此商品拥有独自的佣金比例,不受分销商等级比例及默认设置限制</span>
            </div>
        </div>
        <div id="commission_div" @if($item['has_commission'] != '1') style="display:none" @endif >

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <div class='panel-body'>
                        <div class="table-responsive ">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 10%">等级名称</th>
                                    @if($set['level']>=1)
                                        <th style="text-align: center;">一级分销</th>@endif
                                    @if($set['level']>=2)
                                        <th style="text-align: center;">二级分销</th>@endif
                                    @if($set['level']>=3)
                                        <th style="text-align: center;">三级分销</th>@endif
                                </tr>
                                </thead>
                                <tbody>


                                <tr>
                                    <td> {{$defaultLevel}} </td>
                                    @if($set['level']>=1)
                                        <td>
                                            <div class="input-group">
                                                <input type="text" name="widgets[commission][rule][level_0][first_level_rate]"
                                                       class="form-control"
                                                       value="{{$item->rule['level_0']['first_level_rate']}}"/>
                                                <div class="input-group-addon">% 固定</div>
                                                <input type="text" name="widgets[commission][rule][level_0][first_level_pay]"
                                                       class="form-control"
                                                       value="{{$item->rule['level_0']['first_level_pay']}}"/>
                                                <div class="input-group-addon">元</div>
                                            </div>
                                        </td>
                                    @endif
                                    @if($set['level']>=2)
                                        <td>
                                            <div class="input-group">
                                                <input type="text" name="widgets[commission][rule][level_0][second_level_rate]"
                                                       class="form-control"
                                                       value="{{$item->rule['level_0']['second_level_rate']}}"/>
                                                <div class="input-group-addon">% 固定</div>
                                                <input type="text" name="widgets[commission][rule][level_0][second_level_pay]"
                                                       class="form-control"
                                                       value="{{$item->rule['level_0']['second_level_pay']}}"/>
                                                <div class="input-group-addon">元</div>
                                            </div>
                                        </td>
                                    @endif
                                    @if($set['level']>=3)
                                        <td>
                                            <div class="input-group">
                                                <input type="text" name="widgets[commission][rule][level_0][third_level_rate]"
                                                       class="form-control"
                                                       value="{{$item->rule['level_0']['third_level_rate']}}"/>
                                                <div class="input-group-addon">% 固定</div>
                                                <input type="text" name="widgets[commission][rule][level_0][third_level_pay]"
                                                       class="form-control"
                                                       value="{{$item->rule['level_0']['third_level_pay']}}"/>
                                                <div class="input-group-addon">元</div>
                                            </div>
                                        </td>
                                    @endif
                                </tr>


                                @foreach($levels as $level)
                                    <tr>
                                        <td>{{$level->name}}</td>
                                        @if($set['level']>=1)
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" name="widgets[commission][rule][level_{{$level->id}}][first_level_rate]"
                                                           class="form-control"
                                                           value="{{$item->rule['level_'.$level->id]['first_level_rate']}}"/>
                                                    <div class="input-group-addon">% 固定</div>
                                                    <input type="text" name="widgets[commission][rule][level_{{$level->id}}][first_level_pay]"
                                                           class="form-control"
                                                           value="{{$item->rule['level_'.$level->id]['first_level_pay']}}"/>
                                                    <div class="input-group-addon">元</div>
                                                </div>
                                            </td>
                                        @endif
                                        @if($set['level']>=2)
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" name="widgets[commission][rule][level_{{$level->id}}][second_level_rate]"
                                                           class="form-control"
                                                           value="{{$item->rule['level_'.$level->id]['second_level_rate']}}"/>
                                                    <div class="input-group-addon">% 固定</div>
                                                    <input type="text" name="widgets[commission][rule][level_{{$level->id}}][second_level_pay]"
                                                           class="form-control"
                                                           value="{{$item->rule['level_'.$level->id]['second_level_pay']}}"/>
                                                    <div class="input-group-addon">元</div>
                                                </div>
                                            </td>
                                        @endif
                                        @if($set['level']>=3)
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" name="widgets[commission][rule][level_{{$level->id}}][third_level_rate]"
                                                           class="form-control"
                                                           value="{{$item->rule['level_'.$level->id]['third_level_rate']}}"/>
                                                    <div class="input-group-addon">% 固定</div>
                                                    <input type="text" name="widgets[commission][rule][level_{{$level->id}}][third_level_pay]"
                                                           class="form-control"
                                                           value="{{$item->rule['level_'.$level->id]['third_level_pay']}}"/>
                                                    <div class="input-group-addon">元</div>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            {{--@if($set['level']>=1)--}}
            {{--<div class="form-group">--}}
            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">一级分销</label>--}}
            {{--<div class="col-sm-4 col-xs-12">--}}
            {{--<div class="input-group">--}}
            {{--<input type="text" name="widgets[commission][first_level_rate]" id="commission1_rate"--}}
            {{--class="form-control"--}}
            {{--value="{{$item['first_level_rate']}}"/>--}}
            {{--<div class="input-group-addon">% 固定</div>--}}
            {{--<input type="text" name="widgets[commission][first_level_pay]" id="commission1_pay"--}}
            {{--class="form-control"--}}
            {{--value="{{$item['first_level_pay']}}"/>--}}
            {{--<div class="input-group-addon">元</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@endif--}}
            {{--@if($set['level']>=2)--}}
            {{--<div class="form-group">--}}
            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">二级分销</label>--}}
            {{--<div class="col-sm-4 col-xs-12">--}}
            {{--<div class="input-group">--}}
            {{--<input type="text" name="widgets[commission][second_level_rate]" id="commission2_rate"--}}
            {{--class="form-control"--}}
            {{--value="{{$item['second_level_rate']}}"/>--}}
            {{--<div class="input-group-addon">% 固定</div>--}}
            {{--<input type="text" name="widgets[commission][second_level_pay]" id="commission2_pay"--}}
            {{--class="form-control"--}}
            {{--value="{{$item['second_level_pay']}}"/>--}}
            {{--<div class="input-group-addon">元</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@endif--}}
            {{--@if($set['level']>=3)--}}
            {{--<div class="form-group">--}}
            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">三级分销</label>--}}
            {{--<div class="col-sm-4 col-xs-12">--}}
            {{--<div class="input-group">--}}
            {{--<input type="text" name="widgets[commission][third_level_rate]" id="commission3_rate"--}}
            {{--class="form-control"--}}
            {{--value="{{$item['third_level_rate']}}"/>--}}
            {{--<div class="input-group-addon">% 固定</div>--}}
            {{--<input type="text" name="widgets[commission][third_level_pay]" id="commission3_pay"--}}
            {{--class="form-control"--}}
            {{--value="{{$item['third_level_pay']}}"/>--}}
            {{--<div class="input-group-addon">元</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@endif--}}

            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
            <div class="col-sm-9 col-xs-12">
                <span class="help-block">如果比例为空或等于0，则使用固定规则，如果都为空或等于0则无分销佣金</span>
            </div>
        </div>

    </div>


    <div class="form-group"></div>

</div>
<script language="javascript">
    $(function () {
        $("#hascommission").click(function () {
            var obj = $(this);
            if (obj.get(0).checked) {
                $("#commission_div").show();
            } else {
                $("#commission_div").hide();
            }
        });
    })
</script>