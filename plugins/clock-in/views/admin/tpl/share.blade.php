<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="setdata[share_title]" class="form-control" value="{{ $set['share_title'] }}" />
            <span class="help-block">不填写默认商城名称</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图标</label>
        <div class="col-sm-9 col-xs-12">
            {!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[share_icon]', $set['share_icon'])!!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享描述</label>
        <div class="col-sm-9 col-xs-12">
            <textarea style="height:100px;" name="setdata[share_desc]" class="form-control" cols="60">{{ $set['share_desc'] }}</textarea>
        </div>
    </div>
</div>