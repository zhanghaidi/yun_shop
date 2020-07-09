<div class='panel panel-default'>
    <div class="'panel-body">
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">供应商管理背景图</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline"><input type="radio" class="" name="setdata[banner_status]" value="0" @if($set['banner_status'] == 0) checked="checked"@endif /> 关闭</label>
                <label class="radio-inline"><input type="radio" class="" name="setdata[banner_status]" value="1" @if($set['banner_status'] == 1) checked="checked"@endif /> 开启</label>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">背景banner一</label>
            <div class="col-sm-9 col-xs-12">
                {!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[banner_1]',
                yz_tomedia($set['banner_1']))!!}
                <span class="help-block">建议尺寸:375 * 180</span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">背景banner二</label>
            <div class="col-sm-9 col-xs-12">
                {!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[banner_2]',
                yz_tomedia($set['banner_2']))!!}
                <span class="help-block">建议尺寸:375 * 180</span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">背景banner三</label>
            <div class="col-sm-9 col-xs-12">
                {!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[banner_3]',
                yz_tomedia($set['banner_3']))!!}
                <span class="help-block">建议尺寸:375 * 180</span>
            </div>
        </div>
    </div>
</div>
