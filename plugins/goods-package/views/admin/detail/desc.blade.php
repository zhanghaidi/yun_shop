
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">图片描述</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="package[description_title]" id="package[description_title]" class="form-control" value="{{ $package['description_title'] }}"/>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">描述图片</label>
    <div class="col-sm-9 col-xs-12">
        {!! app\common\helpers\ImageHelper::tplFormFieldImage('package[description_thumb]', $package['description_thumb']) !!}
        <span class='help-block'>建议尺寸640*640,或正方形图片</span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">描述详情</label>
    <div class="col-sm-9 col-xs-12">
        <textarea name="package[description_desc]" class="form-control" rows="10">{{ $package['description_desc'] }}</textarea>
    </div>
</div>