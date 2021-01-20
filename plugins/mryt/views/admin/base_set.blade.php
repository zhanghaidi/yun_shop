<div class="panel-heading">基础设置</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">MRYT</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline">
            <input type="radio" name="set[switch]" value="1"
                   @if ($set['switch'] == 1)
                   checked
                    @endif> 开启
        </label>

        <label class="radio-inline">
            <input type="radio" name="set[switch]" value="0"
                   @if (empty($set['switch']) || $set['switch'] == 0)
                   checked
                   @endif> 关闭
        </label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单实付金额为0</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline">
            <input type="radio" name="set[is_award]" value="1"
                   @if($set['is_award'] == 1) checked="checked" @endif /> 奖励</label>
        <label class="radio-inline">
            <input type="radio" name="set[is_award]" value="0"
                   @if(!$set['is_award']) checked="checked" @endif /> 不奖励</label>
        <span style="" class='help-block'>
            当订单实付金额为0时,开关控制是否进行直推奖奖励<br />
            该订单指会员获得推广资格时的订单,其他订单无效
        </span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">默认级别奖励</label>
    <div class="input-group col-xs-2 col-sm-2 col-md-2">
        <span class="input-group-addon unit">{{ $set['referral_name'] }}</span>
        <input type="text" name="set[push_prize]" class="form-control" value="{{$set['push_prize']}}" autocomplete="off">
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="input-group recharge-item" style="margin-top:5px;width: 60%">
        <span class="input-group-addon unit">成为奖励优惠券</span>
        <input type="hidden" name="set[coupon][coupon_id]" value="{{$set['coupon']['coupon_id']}}" class="">
        <input type="text" maxlength="30" class="form-control" name="set[coupon][coupon_name]" readonly="" value="{{$set['coupon']['coupon_name']}}">
        <div class="input-group-addon"><button type="button" class="input-group-add">选择</button></div>
        <input type="text" class="form-control" name="set[coupon][coupon_several]" placeholder="请输入奖励优惠券数量（正整数）" value="{{$set['coupon']['coupon_several']}}">
        <span class="input-group-addon unit">张</span>
        <span class="help-block"></span>
    </div>
</div>

<div class="panel-heading">消息</div>
<div class="panel-body">
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">升级通知</label>
        <div class="col-sm-8 col-xs-12">
            <select name='set[mryt_upgrate_message]' class='form-control diy-notice'>
                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['mryt_upgrate_message'])) value="{{$set['mryt_upgrate_message']}}"
                        selected @else value="" @endif>
                    默认消息模版
                </option>
                @foreach ($temp_list as $item)
                    <option value="{{$item['id']}}"
                            @if($set['mryt_upgrate_message'] == $item['id'])
                            selected
                            @endif>{{$item['title']}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-2 col-xs-6">
            <input class="mui-switch mui-switch-animbg" id="mryt_upgrate_message" type="checkbox"
                   @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['mryt_upgrate_message']))
                   checked
                   @endif
                   onclick="message_default(this.id)"/>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">奖励通知</label>
        <div class="col-sm-8 col-xs-12">
            <select name='set[mryt_award_message]' class='form-control diy-notice'>
                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['mryt_award_message'])) value="{{$set['mryt_award_message']}}"
                        selected @else value="" @endif>
                    默认消息模版
                </option>
                @foreach ($temp_list as $item)
                    <option value="{{$item['id']}}"
                            @if($set['mryt_award_message'] == $item['id'])
                            selected
                            @endif>{{$item['title']}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-2 col-xs-6">
            <input class="mui-switch mui-switch-animbg" id="mryt_award_message" type="checkbox"
                   @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['mryt_award_message']))
                   checked
                   @endif
                   onclick="message_default(this.id)"/>
        </div>
    </div>

    <div class="form-group">
        <div class='panel-heading'>
            数据同步
        </div>
        <div class='panel-body'>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员数据同步</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="button" onclick="dataIdentical()" value="数据同步">
                    <span class='help-block'>已是代理商但在会员管理中查询不到会员，则需要同步数据。其他情况无需同步!</span>
                </div>
            </div>
        </div>
    </div>
</div>