<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">供应商订单下单通知(通知供应商)</label>
        <div class="col-sm-8 col-xs-12">
            <select name='setdata[supplier_order_create]' class='form-control diy-notice'>
                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['supplier_order_create'])) value="{{$set['supplier_order_create']}}"
                        selected @else value="" @endif>
                    默认消息模板
                </option>
                @foreach ($temp_list as $item)
                    <option value="{{$item['id']}}"
                            @if($set['supplier_order_create'] == $item['id'])
                            selected
                            @endif>{{$item['title']}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">供应商订单支付通知(通知供应商)</label>
        <div class="col-sm-8 col-xs-12">
            <select name='setdata[supplier_order_pay]' class='form-control diy-notice'>
                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['supplier_order_pay'])) value="{{$set['supplier_order_pay']}}"
                        selected @else value="" @endif>
                    默认消息模板
                </option>
                @foreach ($temp_list as $item)
                    <option value="{{$item['id']}}"
                            @if($set['supplier_order_pay'] == $item['id'])
                            selected
                            @endif>{{$item['title']}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">供应商订单发货通知(通知供应商)</label>
        <div class="col-sm-8 col-xs-12">
            <select name='setdata[supplier_order_send]' class='form-control diy-notice'>
                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['supplier_order_send'])) value="{{$set['supplier_order_send']}}"
                        selected @else value="" @endif>
                    默认消息模板
                </option>
                @foreach ($temp_list as $item)
                    <option value="{{$item['id']}}"
                            @if($set['supplier_order_send'] == $item['id'])
                            selected
                            @endif>{{$item['title']}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">供应商订单完成通知(通知供应商)</label>
        <div class="col-sm-8 col-xs-12">
            <select name='setdata[supplier_order_finish]' class='form-control diy-notice'>
                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['supplier_order_finish'])) value="{{$set['supplier_order_finish']}}"
                        selected @else value="" @endif>
                    默认消息模板
                </option>
                @foreach ($temp_list as $item)
                    <option value="{{$item['id']}}"
                            @if($set['supplier_order_finish'] == $item['id'])
                            selected
                            @endif>{{$item['title']}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现通知</label>
        <div class="col-sm-8 col-xs-12">
            <select name='setdata[supplier_withdraw_apply]' class='form-control diy-notice'>
                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['supplier_withdraw_apply'])) value="{{$set['supplier_withdraw_apply']}}"
                        selected @else value="" @endif>
                    默认消息模板
                </option>
                @foreach ($temp_list as $item)
                    <option value="{{$item['id']}}"
                            @if($set['supplier_withdraw_apply'] == $item['id'])
                            selected
                            @endif>{{$item['title']}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

@if(YunShop::notice()->getNotSend('supplier.withdraw_ok_title'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">审核通过通知</label>
            <div class="col-sm-8 col-xs-12">
                <select name='setdata[supplier_withdraw_pass]' class='form-control diy-notice'>
                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['supplier_withdraw_pass'])) value="{{$set['supplier_withdraw_pass']}}"
                            selected @else value="" @endif>
                        默认消息模板
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['supplier_withdraw_pass'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif

@if(YunShop::notice()->getNotSend('supplier.withdraw_no_title'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">审核驳回通知</label>
            <div class="col-sm-8 col-xs-12">
                <select name='setdata[supplier_withdraw_reject]' class='form-control diy-notice'>
                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['supplier_withdraw_reject'])) value="{{$set['supplier_withdraw_reject']}}"
                            selected @else value="" @endif>
                        默认消息模板
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['supplier_withdraw_reject'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif

@if(YunShop::notice()->getNotSend('supplier.withdraw_pay_title'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现打款通知</label>
            <div class="col-sm-8 col-xs-12">
                <select name='setdata[supplier_withdraw_play]' class='form-control diy-notice'>
                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['supplier_withdraw_play'])) value="{{$set['supplier_withdraw_play']}}"
                            selected @else value="" @endif>
                        默认消息模板
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['supplier_withdraw_play'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif

@if(YunShop::notice()->getNotSend('supplier.apply_reject_title'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">供应商申请驳回通知</label>
            <div class="col-sm-8 col-xs-12">
                <select name='setdata[supplier_apply_reject]' class='form-control diy-notice'>
                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['supplier_apply_reject'])) value="{{$set['supplier_apply_reject']}}"
                            selected @else value="" @endif>
                        默认消息模板
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['supplier_apply_reject'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif

<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">供应商申请通过通知</label>
        <div class="col-sm-8 col-xs-12">
            <select name='setdata[supplier_apply_pass]' class='form-control diy-notice'>
                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['supplier_apply_pass'])) value="{{$set['supplier_apply_pass']}}"
                        selected @else value="" @endif>
                    默认消息模板
                </option>
                @foreach ($temp_list as $item)
                    <option value="{{$item['id']}}"
                            @if($set['supplier_apply_pass'] == $item['id'])
                            selected
                            @endif>{{$item['title']}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>