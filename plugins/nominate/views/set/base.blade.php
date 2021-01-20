<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
        推荐奖励
    </label>
    <div class="col-sm-6 col-xs-6">
        <label class="radio-inline">
            <input type="radio" name="setdata[is_open]" value="0"
                   @if($set['is_open'] != 1) checked="checked" @endif /> 关闭</label>
        <label class="radio-inline">
            <input type="radio" name="setdata[is_open]" value="1"
                   @if($set['is_open'] == 1) checked="checked" @endif /> 开启</label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
        任务中心
    </label>
    <div class="col-sm-6 col-xs-6">
        <label class="radio-inline">
            <input type="radio" name="setdata[is_open_task]" value="0"
                   @if($set['is_open_task'] != 1) checked="checked" @endif /> 关闭</label>
        <label class="radio-inline">
            <input type="radio" name="setdata[is_open_task]" value="1"
                   @if($set['is_open_task'] == 1) checked="checked" @endif /> 开启</label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
        自定义名称
    </label>
    <div class="col-sm-6 col-xs-6">
        <input type='text' class="form-control" name="setdata[plugin_name]" value="{!! $set['plugin_name']?:'推荐奖励' !!}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
        直推奖名称
    </label>
    <div class="col-sm-6 col-xs-6">
        <input type='text' class="form-control" name="setdata[nominate_prize_name]" value="{!! $set['nominate_prize_name']?:'直推奖' !!}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
        直推极差奖名称
    </label>
    <div class="col-sm-6 col-xs-6">
        <input type='text' class="form-control" name="setdata[nominate_poor_prize_name]" value="{!! $set['nominate_poor_prize_name']?:'直推极差奖' !!}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
        团队奖名称
    </label>
    <div class="col-sm-6 col-xs-6">
        <input type='text' class="form-control" name="setdata[team_prize_name]" value="{!! $set['team_prize_name']?:'团队奖' !!}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
        团队业绩奖名称
    </label>
    <div class="col-sm-6 col-xs-6">
        <input type='text' class="form-control" name="setdata[team_manage_prize_name]" value="{!! $set['team_manage_prize_name']?:'团队业绩奖' !!}"/>
    </div>
</div>
