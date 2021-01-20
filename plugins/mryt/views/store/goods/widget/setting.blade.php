<div class='panel panel-default'>
    <div class='panel-heading'>
        门店设置
    </div>
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">平台手续费独立规则</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline">
                    <input type="radio" name="widgets[store_goods_setting][is_open]" value="0"
                           @if(!$item['is_open']) checked="checked" @endif /> 关闭</label>
                <label class="radio-inline">
                    <input type="radio" name="widgets[store_goods_setting][is_open]" value="1"
                           @if($item['is_open'] == '1') checked="checked" @endif /> 开启</label>
            </div>
        </div>
        <input type="hidden" name="widgets[store_goods_setting][store_id]" value="{{$store_id}}">

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">手续费类型</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline">
                    <input type="radio" name="widgets[store_goods_setting][commission_type]" value="1"
                           @if($item['commission_type'] == 1) checked="checked" @endif /> 百分比</label>
                <label class="radio-inline">
                    <input type="radio" name="widgets[store_goods_setting][commission_type]" value="2"
                           @if($item['commission_type'] == 2) checked="checked" @endif /> 固定金额</label>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">手续费</label>
            <div class="col-sm-6 col-xs-6">
                <div class='input-group'>
                    <input type='text' onkeyup="this.value= this.value.match(/\d+(\.\d{0,2})?/) ? this.value.match(/\d+(\.\d{0,2})?/)[0] : ''" name='widgets[store_goods_setting][value]' class="form-control"
                           value="{{$item['value']}}"/>
                </div>
                <span class='help-block'>门店启用独立平台手续费比例：手续费始终按照商品现价x设置比例，而门店提成金额则按照门店插件基础设置提成结算方式减去平台手续费
例如：商品现价设置为110，实际支付了100元，手续费还是按照110计算，门店提成金额按照设置的计算方式减去手续费</span>
            </div>
        </div>

    </div>
    <div class="form-group"></div>

</div>

