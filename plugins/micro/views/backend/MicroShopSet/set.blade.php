@extends('layouts.base')

@section('content')
@section('title', trans('基础设置'))
<script type="text/javascript">
    function formcheck() {
        /*if ($(':input[name="setdata[micro_title]"]').val() == '') {
            Tip.focus(':input[name="setdata[micro_title]"]', "请输入微店名称!");
            return false;
        }*/
        var reg = /(^[-+]?[1-9]\d*(\.\d{1,2})?$)|(^[-+]?[0]{1}(\.\d{1,2})?$)/; //金额字段验证,后两位小数

        if ($(':input[name="setdata[micro_thumb]"]').val() == '') {
            Tip.focus(':input[name="setdata[micro_thumb]"]', "请上传申请海报!");
            return false;
        }
        var agent_gold_level = $(':input[name="setdata[agent_gold_level]"]').val();
        var agent_bonus_level = $(':input[name="setdata[agent_bonus_level]"]').val();
        if (agent_gold_level != '0' || agent_bonus_level != '0') {
            if (agent_bonus_level >= '1') {
                if (!reg.test($(':input[name="setdata[bonus_first_level]"]').val())) {
                    Tip.focus(':input[name="setdata[bonus_first_level]"]', '格式错误,最多两位小数.');
                    return false;
                }
            }
            if (agent_bonus_level >= '2') {
                if (!reg.test($(':input[name="setdata[bonus_second_level]"]').val())) {
                    Tip.focus(':input[name="setdata[bonus_second_level]"]', '格式错误,最多两位小数.');
                    return false;
                }
            }
            if (agent_bonus_level == '3') {
                if (!reg.test($(':input[name="setdata[bonus_third_level]"]').val())) {
                    Tip.focus(':input[name="setdata[bonus_third_level]"]', '格式错误,最多两位小数.');
                    return false;
                }
            }
            if (agent_gold_level >= '1') {
                if (!reg.test($(':input[name="setdata[gold_first_level]"]').val())) {
                    Tip.focus(':input[name="setdata[gold_first_level]"]', '格式错误,最多两位小数.');
                    return false;
                }
            }
            if (agent_gold_level >= '2') {
                if (!reg.test($(':input[name="setdata[gold_second_level]"]').val())) {
                    Tip.focus(':input[name="setdata[gold_second_level]"]', '格式错误,最多两位小数.');
                    return false;
                }
            }
            if (agent_gold_level == '3') {
                if (!reg.test($(':input[name="setdata[gold_third_level]"]').val())) {
                    Tip.focus(':input[name="setdata[gold_third_level]"]', '格式错误,最多两位小数.');
                    return false;
                }
            }
        }
    }
</script>
    <div class="w1200 m0a">
        <div class="rightlist">
            <form id="setform" action="" method="post" class="form-horizontal form">
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        微店基础设置
                    </div>
                    <div class="form-group"></div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">微店</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="setdata[micro_title]" class="form-control" value="{{$set['micro_title']}}" />
                                <div class="help-block">前端自定义微店名称，默认微店</div>
                            </div>
                        </div>
                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启微店</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[is_open_miceo]" value="0" @if($set['is_open_miceo'] == 0) checked="checked"@endif /> 关闭</label>
                                <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[is_open_miceo]" value="1" @if($set['is_open_miceo'] == 1) checked="checked"@endif /> 开启</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>申请海报</label>
                        <div class="col-sm-9 col-xs-12 detail-logo">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[micro_thumb]', $set['micro_thumb']) !!}
                            <span class="help-block">建议尺寸: 300 * 200 </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>选取商品海报</label>
                        <div class="col-sm-9 col-xs-12 detail-logo">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[select_goods_thumb]', $set['select_goods_thumb']) !!}
                            <span class="help-block">建议尺寸: 300 * 200 </span>
                        </div>
                    </div>

                    {{--<div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">申请协议</label>
                        <div class="col-sm-9 col-xs-12">
                            <textarea  name="setdata[signature]" class="form-control" >{{$set['signature']}}</textarea>
                            <div class="help-block">
                                微店申请协议
                            </div>
                        </div>
                    </div>--}}

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">申请协议</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! tpl_ueditor('setdata[signature]', $set['signature']) !!}

                        </div>
                    </div>

                    <div class='panel-heading'>
                        微店分红设置
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启微店分红</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[is_open_bonus]" value="0" @if($set['is_open_bonus'] == 0) checked="checked"@endif /> 关闭</label>
                                <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[is_open_bonus]" value="1" @if($set['is_open_bonus'] == 1) checked="checked"@endif /> 开启</label>
                            </div>
                        </div>
                    </div>

                    <!-- <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">上级分红奖励</label>
                            <div class="col-xs-12 col-sm-9 col-md-10">
                                <div class='input-group'>
                                    <span class="input-group-addon">分红比例</span>
                                    <input type="text" name="setdata[agent_bonus_ratio]"  value="{{ $set['agent_bonus_ratio'] }}" class="form-control" />
                                    <span class="input-group-addon">%</span>
                                </div>
                                <span class="help-block">上级也是微店时，上级分红比例</span>
                            </div>
                        </div>
                    </div> -->

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">上级微店分红奖励</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="setdata[agent_bonus_level]">
                                <option value="0" @if(!$set['agent_bonus_level']) selected @endif>关闭分红
                                </option>
                                <option value="1" @if(isset($set['agent_bonus_level']) && $set['agent_bonus_level'] == 1) selected @endif>一级微店分红
                                </option>
                                <option value="2" @if(isset($set['agent_bonus_level']) && $set['agent_bonus_level']==2) selected @endif>二级微店分红
                                </option>
                                <option value="3" @if(isset($set['agent_bonus_level']) && $set['agent_bonus_level']==3) selected @endif>三级微店分红
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级微店分红比例</label>
                            <div class="col-xs-12 col-sm-9 col-md-10">
                                <div class='input-group'>
                                    <span class="input-group-addon">分红比例</span>
                                    <input type="text" name="setdata[bonus_first_level]"  value="{{ $set['bonus_first_level'] }}" class="form-control" />
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">二级微店分红比例</label>
                            <div class="col-xs-12 col-sm-9 col-md-10">
                                <div class='input-group'>
                                    <span class="input-group-addon">分红比例</span>
                                    <input type="text" name="setdata[bonus_second_level]"  value="{{ $set['bonus_second_level'] }}" class="form-control" />
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">三级微店分红比例</label>
                            <div class="col-xs-12 col-sm-9 col-md-10">
                                <div class='input-group'>
                                    <span class="input-group-addon">分红比例</span>
                                    <input type="text" name="setdata[bonus_third_level]"  value="{{ $set['bonus_third_level'] }}" class="form-control" />
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">上级微店金币奖励</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="setdata[agent_gold_level]">
                                <option value="0" @if(!$set['agent_gold_level']) selected @endif>关闭奖励
                                </option>
                                <option value="1" @if(isset($set['agent_gold_level']) && $set['agent_gold_level'] == 1) selected @endif>一级奖励
                                </option>
                                <option value="2" @if(isset($set['agent_gold_level']) && $set['agent_gold_level']==2) selected @endif>二级奖励
                                </option>
                                <option value="3" @if(isset($set['agent_gold_level']) && $set['agent_gold_level']==3) selected @endif>三级奖励
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级微店奖励比例</label>
                            <div class="col-xs-12 col-sm-9 col-md-10">
                                <div class='input-group'>
                                    <span class="input-group-addon">奖励比例</span>
                                    <input type="text" name="setdata[gold_first_level]"  value="{{ $set['gold_first_level'] }}" class="form-control" />
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">二级微店奖励比例</label>
                            <div class="col-xs-12 col-sm-9 col-md-10">
                                <div class='input-group'>
                                    <span class="input-group-addon">奖励比例</span>
                                    <input type="text" name="setdata[gold_second_level]"  value="{{ $set['gold_second_level'] }}" class="form-control" />
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">三级微店奖励比例</label>
                            <div class="col-xs-12 col-sm-9 col-md-10">
                                <div class='input-group'>
                                    <span class="input-group-addon">奖励比例</span>
                                    <input type="text" name="setdata[gold_third_level]"  value="{{ $set['gold_third_level'] }}" class="form-control" />
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算方式</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[bonus_type]" value="0" @if($set['bonus_type'] == 0) checked="checked"@endif /> 成交价</label>
                                <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[bonus_type]" value="1" @if($set['bonus_type'] == 1) checked="checked"@endif /> 利润</label>
                            </div>
                        </div>
                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算周期</label>
                            <div class="col-xs-12 col-sm-9 col-md-10">
                                <div class='input-group'>
                                    <span class="input-group-addon">订单退款限制时间{{$refund_days}}天 + </span>
                                    <input type="text" name="setdata[cycle]"  value="{{ $set['cycle'] }}" class="form-control" />
                                    <span class="input-group-addon">天</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现手续费</label>
                            <div class="col-sm-9 col-xs-12">
                                <a href="{{yzWebUrl('finance.withdraw-set.see')}}" class="alert-link">{{$set['fee']}}</a>
                            </div>

                        </div>
                    </div>

                    <div class='panel-heading'>
                        通知设置
                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">成为微店通知</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='setdata[micro_become_micro]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_become_micro'])) value="{{$set['micro_become_micro']}}"
                                            selected @else value="" @endif>
                                        默认消息模板
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['micro_become_micro'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input class="mui-switch mui-switch-animbg" id="micro_become_micro" type="checkbox"
                                   @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_become_micro']))
                                   checked
                                   @endif
                                   onclick="message_default(this.id)"/>
                        </div>
                    </div>

                    @if(YunShop::notice()->getNotSend('micro.upgrade_micro_title'))
                        <div class='panel-body'>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">微店升级通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='setdata[micro_micro_upgrade]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_micro_upgrade'])) value="{{$set['micro_micro_upgrade']}}"
                                                selected @else value="" @endif>
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['micro_micro_upgrade'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input class="mui-switch mui-switch-animbg" id="micro_micro_upgrade" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_micro_upgrade']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>
                        </div>
                    @endif

                    @if(YunShop::notice()->getNotSend('micro.bonus_order_title'))
                        <div class='panel-body'>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分红订单通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='setdata[micro_order_bonus]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_order_bonus'])) value="{{$set['micro_order_bonus']}}"
                                                selected @else value="" @endif>
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['micro_order_bonus'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input class="mui-switch mui-switch-animbg" id="micro_order_bonus" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_order_bonus']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>
                        </div>
                    @endif

                    @if(YunShop::notice()->getNotSend('micro.lower_bonus_order_title'))
                        <div class='panel-body'>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">下级微店分红通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='setdata[micro_lower_bonus]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_lower_bonus'])) value="{{$set['micro_lower_bonus']}}"
                                                selected @else value="" @endif>
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['micro_lower_bonus'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input class="mui-switch mui-switch-animbg" id="micro_lower_bonus" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_lower_bonus']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>
                        </div>
                    @endif

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">微店分红结算通知</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='setdata[micro_bonus_settlement]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_bonus_settlement'])) value="{{$set['micro_bonus_settlement']}}"
                                            selected @else value="" @endif>
                                        默认消息模板
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['micro_bonus_settlement'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input class="mui-switch mui-switch-animbg" id="micro_bonus_settlement" type="checkbox"
                                   @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_bonus_settlement']))
                                   checked
                                   @endif
                                   onclick="message_default(this.id)"/>
                        </div>
                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">上级微店分红结算通知</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='setdata[micro_agent_bonus_settlement]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_agent_bonus_settlement'])) value="{{$set['micro_agent_bonus_settlement']}}"
                                            selected @else value="" @endif>
                                        默认消息模板
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['micro_agent_bonus_settlement'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input class="mui-switch mui-switch-animbg" id="micro_agent_bonus_settlement" type="checkbox"
                                   @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_agent_bonus_settlement']))
                                   checked
                                   @endif
                                   onclick="message_default(this.id)"/>
                        </div>
                    </div>

                    {{--<div class='panel-body'>--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">上级店主金币奖励通知</label>--}}
                            {{--<div class="col-sm-8 col-xs-12">--}}
                                {{--<select name='setdata[micro_agent_gold]' class='form-control diy-notice'>--}}
                                    {{--<option value="{{$set['micro_agent_gold']}}" @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_agent_gold']))--}}
                                    {{--selected @endif>--}}
                                        {{--默认消息模板--}}
                                    {{--</option>--}}
                                    {{--@foreach ($temp_list as $item)--}}
                                        {{--<option value="{{$item['id']}}"--}}
                                                {{--@if($set['micro_agent_gold'] == $item['id'])--}}
                                                {{--selected--}}
                                                {{--@endif>{{$item['title']}}</option>--}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</div>--}}
                            {{--<input class="mui-switch mui-switch-animbg" id="micro_agent_gold" type="checkbox"--}}
                                   {{--@if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['micro_agent_gold']))--}}
                                   {{--checked--}}
                                   {{--@endif--}}
                                   {{--onclick="message_default(this.id)"/>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9">
                            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg"
                                   onclick='return formcheck()'/>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
<script>
    function message_default(name) {
        var id = "#" + name;
        var setting_name = "plugin.micro";
        var select_name = "select[name='setdata[" + name + "]']"
        var url_open = "{!! yzWebUrl('setting.default-notice.index') !!}"
        var url_close = "{!! yzWebUrl('setting.default-notice.cancel') !!}"
        var postdata = {
            notice_name: name,
            setting_name: setting_name
        };
        if ($(id).is(':checked')) {
            //开
            $.post(url_open,postdata,function(data){
                if (data.result == 1) {
                    $(select_name).find("option:selected").val(data.id)
                    showPopover($(id),"开启成功")
                } else {
                    showPopover($(id),"开启失败，请检查微信模版")
                    $(id).attr("checked",false);
                }
            }, "json");
        } else {
            //关
            $.post(url_close,postdata,function(data){
                $(select_name).val('');
                showPopover($(id),"关闭成功")
            }, "json");
        }
    }
    function showPopover(target, msg) {
        target.attr("data-original-title", msg);
        $('[data-toggle="tooltip"]').tooltip();
        target.tooltip('show');
        target.focus();
        //2秒后消失提示框
        setTimeout(function () {
                target.attr("data-original-title", "");
                target.tooltip('hide');
            }, 2000
        );
    }
</script>
<script type="text/javascript">
    require(['bootstrap'], function ($) {
        $(document).scroll(function () {
            var toptype = $("#edui1_toolbarbox").css('position');
            if (toptype == "fixed") {
                $("#edui1_toolbarbox").addClass('top_menu');
            }
            else {
                $("#edui1_toolbarbox").removeClass('top_menu');
            }
        });
    });
    $('.diy-notice').select2();
</script>
@endsection
