@if(array_key_exists('single-return', $exist_plugins))
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">消费赠送</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                <input type="hidden" name="widgets[single_return][is_single_return]" value="1">
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[single_return][return_rate]' class="form-control discounts_value"
                       value="{{$exist_plugins['single-return']['single_return_goods']['return_rate']?$exist_plugins['single-return']['single_return_goods']['return_rate']:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
@endif

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">消费满额赠送</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[full-return][is_open]' value='1'
                   @if($exist_plugins['full-return']['is_open'] == 1) checked @endif
            /> 开启
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[full-return][is_open]' value='0'
                   @if($exist_plugins['full-return']['is_open'] == 0) checked @endif
            /> 关闭
        </label>
    </div>
</div>