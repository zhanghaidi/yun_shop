<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝 :</label>
    <div class="col-sm-9 col-xs-12">
        <img src='{{$store_order->belongsToMember->avatar}}'
             style='width:100px;height:100px;padding:1px;border:1px solid #ccc'/>
        <a href="{!! yzWebUrl('member.member.detail',array('id'=>$store_order->belongsToMember->uid)) !!}"> {{$store_order->belongsToMember->nickname}}</a>

    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员信息 :</label>
    <div class="col-sm-9 col-xs-12">
        <div class='form-control-static'>ID:
                    姓名: {{$store_order->belongsToMember->realname}} /
            手机号: {{$store_order->belongsToMember->mobile}}</div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">姓名 :</label>
    <div class="col-sm-9 col-xs-12">
        <p class="form-control-static">{{$cashier_order->realname}} </p>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">电话 :</label>
    <div class="col-sm-9 col-xs-12">
        <p class="form-control-static">{{$cashier_order->mobile}} </p>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单编号 :</label>
    <div class="col-sm-9 col-xs-12">
        <p class="form-control-static">{{$store_order->order_sn}} </p>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单金额 :</label>
    <div class="col-sm-9 col-xs-12">
        <div class="form-control-static">
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <td style='border:none;text-align:right;'>商品小计：</td>
                    <td style='border:none;text-align:right;;'>
                        ￥{{$store_order->order_goods_price}}</td>
                </tr>
                <tr>
                    <td style='border:none;text-align:right;'>优惠：</td>
                    <td style='border:none;text-align:right;'>
                        ￥{{number_format( $store_order['discount_price'] ,2)}}</td>
                </tr>
                <tr>
                    <td style='border:none;text-align:right;'>抵扣：</td>
                    <td style='border:none;text-align:right;'>
                        ￥{{number_format( $store_order['deduction_price'] ,2)}}</td>
                </tr>
                <tr>
                    <td style='border:none;text-align:right;'>应收款：</td>
                    <td style='border:none;text-align:right;color:green;'>
                        ￥{{$store_order->price}}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单状态 :</label>
    <div class="col-sm-9 col-xs-12">
        <p class="form-control-static">
            <span class="label label-success">{{$store_order->status_name}}</span>
        </p>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付方式 :</label>
    <div class="col-sm-9 col-xs-12">
        <p class="form-control-static">
            <span class="label label-info">{{$store_order->pay_type_name}}</span>


                <a target="_blank" href="{{yzWebUrl('order.orderPay', array('order_id' => $store_order['id']))}}" class='btn btn-default'>查看支付记录</a>
            
        </p>

    </div>

</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">备注 :</label>
    <div class="col-sm-9 col-xs-12">
        <textarea style="height:150px;" class="form-control" id="remark" name="remark" cols="70">{{$store_order->hasOneOrderRemark->remark}}</textarea>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-9 col-xs-12">
        <br/>
        <button name='saveremark' onclick="sub()" class='btn btn-default'>保存备注</button>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">下单日期 :</label>
    <div class="col-sm-9 col-xs-12">
        <p class="form-control-static">{{$store_order->create_time}}</p>
    </div>
</div>
@if ($store_order['status'] >= 1)
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">付款时间 :</label>
        <div class="col-sm-9 col-xs-12">
            <p class="form-control-static">{{$store_order->pay_time}}</p>
        </div>
    </div>
@endif
@if ($store_order['status'] == 3)
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">完成时间 :</label>
        <div class="col-sm-9 col-xs-12">
            <p class="form-control-static">{{$store_order->finish_time}}</p>
        </div>
    </div>
@endif

@if (!empty($store_order->hasOneRefundApply))
    @include('refund.index')
@endif


@if (count($store_order->deductions))
    <div class="panel panel-default">
        <div class="panel-heading">
            抵扣信息
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th class="col-md-5 col-lg-3">名称</th>
                    <th class="col-md-5 col-lg-1">抵扣值</th>
                    <th class="col-md-5 col-lg-3">抵扣金额</th>
                </tr>
                </thead>
                @foreach ($store_order->deductions as $deduction)
                    <tr>
                        <td>{{$deduction->name}}</td>
                        <td>{{$deduction->qty}}</td>
                        <td>¥{{$deduction->amount}}</td>
                    </tr>

                @endforeach
            </table>
        </div>
    </div>
@endif
@if (count($store_order->coupons))
    <div class="panel panel-default">
        <div class="panel-heading">
            优惠券信息
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th class="col-md-5 col-lg-3">名称</th>
                    <th class="col-md-5 col-lg-3">优惠金额</th>
                </tr>
                </thead>
                @foreach ($store_order->coupons as $coupon)
                    <tr>
                        <td>{{$coupon->name}}</td>
                        <td>¥{{$coupon->amount}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endif
<style>
    .form-group {
        overflow: hidden;
        margin-bottom: 0 !important;
    }
</style>
<div class="panel panel-default">
    <div class="panel-heading">
        门店信息
    </div>
    <div class="panel-body table-responsive">
        <table class="table table-hover">
            <tr>
                <td style='width:20%;text-align: center;'><img src='{{tomedia($cashier_order->hasOneStore->thumb)}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /></td>
                <td style='width:20%;text-align: center;'>
                    {{$cashier_order->hasOneStore->store_name}}
                </td>
                <td style='width:40%;text-align: center;'><?php
                $province = app\common\models\Address::find($cashier_order->hasOneStore->province_id);
            $city = app\common\models\Address::find($cashier_order->hasOneStore->city_id);
            $district = app\common\models\Address::find($cashier_order->hasOneStore->district_id);
            $street = app\common\models\Street::find($cashier_order->hasOneStore->street_id);
            $address = $province['areaname'] . $city['areaname'] . $district['areaname'] . $street['areaname'] . '-' . $cashier_order->hasOneStore->address; ?>
                {{$address}}
                </td>
                <td style='width:20%;text-align: center;'>{{$cashier_order->hasOneStore->mobile}}</td>
            </tr>
            <tr>
                <td colspan="2">
                    @include('Yunshop\StoreCashier::admin.order.list_tpl.ops')
                    @include('order.modals')
                </td>
                <td colspan="8">
                </td>
            </tr>
        </table>
    </div>
</div>