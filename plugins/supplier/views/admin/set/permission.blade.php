<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">供应商订单退款操作</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline"><input type="radio" name="setdata[supplier_order_refund_right]" value="0" @if(empty($set['supplier_order_refund_right']) || $set['supplier_order_refund_right'] == 0 ) checked="checked"@endif /> 禁止</label>
        <label class="radio-inline"><input type="radio" name="setdata[supplier_order_refund_right]" value="1" @if($set['supplier_order_refund_right'] == 1) checked="checked"@endif /> 允许</label>
    </div>
</div>