<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <div class="alert alert-warning">
                商城未开启相关支付则收银台支付设置无效, 
                <a href="{{yzWebUrl('setting.shop.pay')}}" taget="_blank">
                    前往商城支付设置
                </a>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">平台提成比例</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[cashier][shop_commission]' class="form-control discounts_value"
                   value="{{$store->hasOneCashier->hasOneCashierGoods->shop_commission?$store->hasOneCashier->hasOneCashierGoods->shop_commission:0}}"/>
            <div class='input-group-addon waytxt'>%</div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算期</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[cashier][settlement_day]' class="form-control discounts_value"
                   value="{{$store->hasOneCashier->hasOneCashierGoods->settlement_day?$store->hasOneCashier->hasOneCashierGoods->settlement_day:0}}"/>
            <div class='input-group-addon waytxt'>天</div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信支付</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][payment_types][wechatPay]' value='1'
                   @if($store->hasOneCashier->hasOneCashierGoods->payment_types['wechatPay'] == 1) checked @endif
            /> 开启
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][payment_types][wechatPay]' value='0'
                   @if($store->hasOneCashier->hasOneCashierGoods->payment_types['wechatPay'] == 0 && isset($store->hasOneCashier->hasOneCashierGoods->payment_types['wechatPay'])) checked @endif
            /> 关闭
        </label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝支付</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][payment_types][alipay]' value='1'
                   @if($store->hasOneCashier->hasOneCashierGoods->payment_types['alipay'] == 1) checked @endif
            /> 开启
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][payment_types][alipay]' value='0'
                   @if($store->hasOneCashier->hasOneCashierGoods->payment_types['alipay'] == 0 && isset($store->hasOneCashier->hasOneCashierGoods->payment_types['alipay'])) checked @endif
            /> 关闭
        </label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额支付</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][payment_types][balance]' value='1'
                   @if($store->hasOneCashier->hasOneCashierGoods->payment_types['balance'] == 1) checked @endif
            /> 开启
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][payment_types][balance]' value='0'
                   @if($store->hasOneCashier->hasOneCashierGoods->payment_types['balance'] == 0 && isset($store->hasOneCashier->hasOneCashierGoods->payment_types['balance'])) checked @endif
            /> 关闭
        </label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">现金支付</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][is_cash_pay]' value='1'
                   @if($store->hasOneCashier->hasOneCashierGoods->is_cash_pay == 1) checked @endif
            /> 开启
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[cashier][is_cash_pay]' value='0'
                   @if($store->hasOneCashier->hasOneCashierGoods->is_cash_pay === 0) checked @endif
            /> 关闭
        </label>
    </div>
</div>