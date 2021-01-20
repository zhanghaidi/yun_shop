<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启收银台</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][is_open]' value='1'
                   @if($store->hasOneCashier->hasOneCashierGoods->is_open == 1) checked @endif
            /> 是
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][is_open]' value='0'
                   @if($store->hasOneCashier->hasOneCashierGoods->is_open == 0) checked @endif
            /> 否
        </label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">用户付款是否需要填写信息</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][is_write_information]' value='1'
                   @if($store->hasOneCashier->hasOneCashierGoods->is_write_information == 1) checked @endif
            /> 是
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][is_write_information]' value='0'
                   @if($store->hasOneCashier->hasOneCashierGoods->is_write_information == 0) checked @endif
            /> 否
        </label>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启语音通知</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][audio_open]' value='1'
                   @if($store->hasOneCashier->hasOneCashierGoods->audio_open == 1) checked @endif
            /> 是
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][audio_open]' value='0'
                   @if($store->hasOneCashier->hasOneCashierGoods->audio_open == 0) checked @endif
            /> 否
        </label>
    </div>
</div>