<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">推送标题</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="poster[response_title]" class="form-control" value="{{$poster['response_title']}}" />
            <span class="help-block">如果这里设置为空, 则用户扫描推荐者的海报二维码后, 系统不推送内容</span>
        </div>
    </div>

    <div class="form-group respthumb">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">推送封面</label>
        <div class="col-sm-9 col-xs-12">
            {!!tpl_form_field_image('poster[response_thumb]',$poster['response_thumb'])!!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">推送描述</label>
        <div class="col-sm-9 col-xs-12">
            <textarea name="poster[response_desc]" class='form-control'>{{$poster['response_desc']}}</textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">推送链接</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="poster[response_url]" class="form-control" value="{{$poster['response_url']}}" />
            <span class='help-block'>默认为商城首页链接</span>
        </div>
    </div>
</div>