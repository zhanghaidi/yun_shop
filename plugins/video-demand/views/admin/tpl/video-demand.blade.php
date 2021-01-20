<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">视频点播</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline">
            <input type="radio" name="setdata[is_video_demand]" value="0"
                   @if($set['is_video_demand'] == 0) checked="checked" @endif /> 关闭</label>
        <label class="radio-inline">
            <input type="radio" name="setdata[is_video_demand]" value="1"
                   @if($set['is_video_demand'] == 1) checked="checked" @endif /> 开启</label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">课程聚合页</label>
    <div class="col-sm-9 col-xs-12">
        <a href="javascript:;"
           data-url="{!! \app\common\helpers\Url::absoluteApp('/member/courseindex') !!}"
           title="复制连接" class="btn btn-default btn-sm js-clip">课程聚合页
        </a>
    </div>
</div>


<div class='panel-heading'>
    讲师设置
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">讲师打赏</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline">
            <input type="radio" name="setdata[is_reward]" value="0"
                   @if($set['is_reward'] == 0) checked="checked" @endif /> 关闭</label>
        <label class="radio-inline">
            <input type="radio" name="setdata[is_reward]" value="1"
                   @if($set['is_reward'] == 1) checked="checked" @endif /> 开启</label>
    </div>
</div>

<div class='panel-heading'>
    结算设置
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算期</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input type='text' name='setdata[settle_days]' class="form-control discounts_value"
                   value="{{$set['settle_days']}}"/>
            <div class='input-group-addon waytxt'>天</div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">打赏提成比例</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input type='number' name='setdata[reward_pr]' min="0" max="100" step="0.01" class="form-control discounts_value"
                   value="{{$set['reward_pr']}}"/>
            <div class='input-group-addon waytxt'>%</div>
        </div>
    </div>
</div>
