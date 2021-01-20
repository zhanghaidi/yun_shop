
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="package[share_title]" id="package[share_title]" class="form-control"
               value="{{ $package['share_title'] }}"/>
        <span class='help-block'>如果不填写，默认为套餐标题</span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图标</label>
    <div class="col-sm-9 col-xs-12">
        {!! app\common\helpers\ImageHelper::tplFormFieldImage('package[share_thumb]', $package['share_thumb']) !!}
        <span class='help-block'>如果不选择，默认为套餐缩略图片</span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享描述</label>
    <div class="col-sm-9 col-xs-12">
        <textarea name="package[share_desc]" class="form-control">{{ $package['share_desc'] }}</textarea>
        <span class='help-block'>如果不填写，默认为店铺名称</span>
    </div>
</div>