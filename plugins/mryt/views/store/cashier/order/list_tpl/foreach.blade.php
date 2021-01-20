@foreach ($list as $order)
    <div class="order-info">
        <table class='table order-title'>
            <tr>
                <td class="left" colspan='8'>
                    <b>订单编号:</b> {{$order->order_sn}}
                    @if($order->status>\app\common\models\Order::WAIT_PAY && isset($order->hasOneOrderPay))
                        <b>支付单号:</b> {{$order->hasOneOrderPay->pay_sn}}
                    @endif
                    <b>下单时间: </b>{{$order->create_time}}

                    <label class="label label-info">收银台</label>
                    @if(!empty($order->hasOneRefundApply))
                        <label class="label label-danger">{{$order->hasOneRefundApply['refund_type_name']}}
                            :{{$order->hasOneRefundApply['status_name']}}</label>
                    @endif


                <td class="right">
                    @if(empty($order->status))
                        <a class="btn btn-default btn-sm" href="javascript:;"
                           onclick="$('#modal-close').find(':input[name=order_id]').val('{{$order->id}}')"
                           data-toggle="modal" data-target="#modal-close">关闭订单</a>
                    @endif
                </td>
            </tr>
        </table>
        <table class='table order-main'>
            <tr class='trbody'>
                <td class="goods_info">
                    <img src="{{tomedia($order->store_thumb)}}">
                </td>
                <td class="top" valign='top'>
                    {{$order->store_name}}
                </td>
                <td class="price">
                    订单金额: {{$order->price}}
                </td>
                <td rowspan="{{count($order)}}">
                    <a href="{!! yzWebUrl('member.member.detail',array('id'=>$order->belongsToMember->uid)) !!}"> {{$order->belongsToMember->nickname}}</a>
                    <br/>
                    {{$order->belongsToMember->realname}}
                    <br/>{{$order->belongsToMember->mobile}}
                    {{--@if($identity[$order->belongsToMember->uid])
                        @if($identity[$order->belongsToMember->uid]['merchant'])
                            <br><span class="label label-danger">
                                {{$identity[$order->belongsToMember->uid]['merchant']}}
                            </span>
                        @endif
                        @if($identity[$order->belongsToMember->uid]['commission'])
                            <br><span class="label label-warning">
                                {{$identity[$order->belongsToMember->uid]['commission']}}
                            </span>
                        @endif
                    @else
                        <br><span class="label label-default">普通会员</span>
                    @endif--}}
                </td>

                <td rowspan="{{count($order)}}">
                    <label class='label label-info'>{{$order->pay_type_name}}</label>
                    <br/>
                </td>
                <td rowspan="{{count($order)}}">
                    <label class='label label-info'>
                        {{$order->status_name}}
                    </label>
                    <br/>
                    <a href="{!! yzWebUrl(\Yunshop\StoreCashier\admin\OrderController::DETAIL_URL,['order_id'=>$order->id])!!}">查看详情</a>
                </td>
                <td rowspan="{{count($order)}}" width="10%">
                    @include('Yunshop\StoreCashier::admin.order.list_tpl.ops')
                </td>
            </tr>
        </table>
    </div>
@endforeach