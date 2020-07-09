@extends('order.index')

@section('search_bar')
    <div class="form-group  col-md-2 col-sm-6">
        <select name="search[ambiguous][field]" id="ambiguous-field"
                class="form-control">
            <option value="order"
                    @if(array_get($requestSearch,'ambiguous.field','') =='order')  selected="selected"@endif >
                订单号/支付号
            </option>
            <option value="member"
                    @if( array_get($requestSearch,'ambiguous.field','')=='member')  selected="selected"@endif>
                用户姓名/ID/昵称/手机号
            </option>

            <option value="address"
                    @if( array_get($requestSearch,'ambiguous.field','')=='address')  selected="selected"@endif>
                收货地址/姓名/手机号
            </option>

            <option value="goods_id"{{--order_goods--}}
            @if( array_get($requestSearch,'ambiguous.field','')=='goods_id')  selected="selected"@endif>
                商品名称/ID
            </option>
            {{--<option value="goods_id"--}}
            {{--@if( array_get($requestSearch,'ambiguous.field','')=='goods_id')  selected="selected"@endif>--}}
            {{--商品ID--}}
            {{--</option>--}}
            <option value="dispatch"
                    @if( array_get($requestSearch,'ambiguous.field','')=='dispatch')  selected="selected"@endif>
                快递单号
            </option>
        </select>
    </div>
    <div class='form-group col-sm-4 col-lg-3 col-xs-12'>

        <input class="form-control" name="search[ambiguous][string]" type="text"
               value="{{array_get($requestSearch,'ambiguous.string','')}}"
               placeholder="订单号/支付单号" id="string">


        <div class="form-group notice" id="goods_name">
            <div >
                <div class='input-group'>
                    <input type="hidden" id="plugin_id" name="plugin_id" value="@if(!empty($list['plugin_id'])) {{$list['plugin_id']}} @else 0 @endif">
                    <input type="text" name="search[ambiguous][name]" maxlength="30" value="{{array_get($requestSearch,'ambiguous.name','')}}" id="saler" class="form-control" readonly />
                    <div class='input-group-btn'>
                        <button class="btn btn-default" type="button" onclick="popwin = $('#modal-module-menus-notice').modal();">选择商品</button>
                        <button class="btn btn-danger" type="button" onclick="$('#noticeopenid').val('');$('#saler').val('');$('#saleravatar').hide()">清除选择</button>
                    </div>
                </div>
                <div id="modal-module-menus-notice"  class="modal fade" tabindex="-1">
                    <div class="modal-dialog" style='width: 920px;'>
                        <div class="modal-content">
                            <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择商品名称</h3></div>
                            <div class="modal-body" >
                                <div class="row">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="keyword" value="" id="search-kwd-notice" placeholder="请输入商品名称" />
                                        <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_members();">搜索</button></span>
                                    </div>
                                </div>
                                <div id="module-menus-notice" style="padding-top:5px;"></div>
                            </div>
                            <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="form-group form-group col-sm-8 col-lg-2 col-xs-12">
        <!-- 注意，由于属于支付宝支付的支付方式有好几种，包括app支付宝支付方式，支付宝-YZ方式
        等，所以进行了分组,支付选项传入的支付方式是支付方式组的id，并不是支付方式的id -->
        <select name="search[pay_type]" class="form-control">
            <option value=""
                    @if( array_get($requestSearch,'pay_type',''))  selected="selected"@endif>
                全部支付方式
            </option>
            <option value="1"
                    @if( array_get($requestSearch,'pay_type','') == '1')  selected="selected"@endif>
                微信支付
            </option>
            <option value="2"
                    @if( array_get($requestSearch,'pay_type','') == '2')  selected="selected"@endif>
                支付宝支付
            </option>
            <option value="3"
                    @if( array_get($requestSearch,'pay_type','') == '3')  selected="selected"@endif>
                余额支付
            </option>
            <option value="4"
                    @if( array_get($requestSearch,'pay_type','') == '4')  selected="selected"@endif>
                后台付款
            </option>
        </select>
    </div>

    <div class="form-group col-sm-12 col-lg-12 col-xs-12"></div>
    <div class="form-group col-sm-8 col-lg-5 col-xs-12">

        <select name="search[time_range][field]" class="form-control form-time" >
            <option value=""
                    @if( array_get($requestSearch,'time_range.field',''))selected="selected"@endif >
                操作时间
            </option>
            <option value="create_time"
                    @if( array_get($requestSearch,'time_range.field','')=='create_time')  selected="selected"@endif >
                下单
            </option>
            <option value="pay_time"
                    @if( array_get($requestSearch,'time_range.field','')=='pay_time')  selected="selected"@endif>
                付款
            </option>
            <option value="send_time"
                    @if( array_get($requestSearch,'time_range.field','')=='send_time')  selected="selected"@endif>
                发货
            </option>
            <option value="finish_time"
                    @if( array_get($requestSearch,'time_range.field','')=='finish_time')  selected="selected"@endif>
                完成
            </option>
        </select>
        {!!
            app\common\helpers\DateRange::tplFormFieldDateRange('search[time_range]', [
    'starttime'=>array_get($requestSearch,'time_range.start',0),
    'endtime'=>array_get($requestSearch,'time_range.end',0),
    'start'=>0,
    'end'=>0
    ], true)!!}

    </div>
    <div class='input-group'>
        <select name="search[supplier]" class="form-control">
            <option value="">选择供应商</option>
            @foreach($all_supplier as $supplier)
            <option value="{{$supplier['id']}}" @if ($requestSearch['supplier'] == $supplier['id'])) selected="selected" @endif>账号：{{$supplier['username']}}/{{$supplier['id']}}</option>
            @endforeach
        </select>
    </div>
@endsection

    {{--@foreach ($list['data'] as $val)
        @section('shop_name',' <label class="label label-primary">所属供应商：'.$val['be_longs_to_supplier']['username'].'</label>')


    @endforeach--}}
@section('is_plugin')
@foreach ($list['data'] as $order_index => $order)
    <div class="order-info">
        <table class='table order-title' >
            <tr>
                <td class="left" colspan='8' >
                    <b>订单编号:</b> {{$order['order_sn']}}
                    @if($order['status']>\app\common\models\Order::WAIT_PAY && isset($order['has_one_order_pay']))
                        <b>支付单号:</b> {{$order['has_one_order_pay']['pay_sn']}}
                    @endif
                    <b>下单时间: </b>{{$order['create_time']}}
                    @if( $order['has_one_refund_apply'] == \app\common\models\refund\RefundApply::WAIT_RECEIVE_RETURN_GOODS)<label class='label label-primary'>客户已经寄出快递</label>@endif

                    <label class="label label-primary">所属供应商：{{$order['be_longs_to_supplier']['username']}}</label>

                    @if(!empty($order['has_one_refund_apply']))
                        <label class="label label-danger">{{$order['has_one_refund_apply']['refund_type_name']}}:{{$order['has_one_refund_apply']['status_name']}}</label>
                @endif
            </tr>
        </table>
        <table class='table order-main' >
            @foreach( $order['has_many_order_goods'] as $order_goods_index => $order_goods)
                <tr class='trbody'>
                    <td class="goods_info">
                        <img src="{{tomedia($order_goods['thumb'])}}">
                    </td>

                    <td class="top" valign='top' >
                        <a style="font-size: 16px" href="{{yzWebUrl('plugin.supplier.admin.controllers.goods.goods-operation.edit', array('id' => $order_goods['goods_id']))}}">{{$order_goods['title']}}</a>
                        @if( !empty($order_goods['goods_option_title']))<br/>
                        {{--<span--}}
                                {{--class="label label-primary sizebg">{{$order_goods['goods_option_title']}}</span>--}}
                        <span style="font-size: 15px;color: #AEB9C0">{{$order_goods['goods_option_title']}}</span>
                        @endif
                        <br/><span style="font-size: 15px;color: #AEB9C0">{{$order_goods['goods_sn']}}</span>
                    </td>
                    <td class="price">
                        原价: {{ number_format($order_goods['goods_price']/$order_goods['total'],2)}}
                        <br/>应付: {{ number_format($order_goods['price']/$order_goods['total'],2) }}
                        <br/>数量: {{$order_goods['total']}}
                    </td>


                    @if( $order_goods_index == 0)
                        <td rowspan="{{count($order['has_many_order_goods'])}}">
                            <a href="{!! yzWebUrl('member.member.detail',array('id'=>$order['belongs_to_member']['uid'])) !!}"> {{$order['belongs_to_member']['nickname']}}</a>
                            <br/>
                            {{$order['belongs_to_member']['realname']}}
                            <br/>{{$order['belongs_to_member']['mobile']}}
                        </td>

                        <td rowspan="{{count($order['has_many_order_goods'])}}">
                            <label class='label label-info'>{{$order['pay_type_name']}}</label>
                            <br/>

                            {{$order['has_one_dispatch_type']['name']}}
                            @if( 0&&$order['addressid']!=0 && $order['statusvalue']>=2)<br/>
                            <button type='button' class='btn btn-default btn-sm'
                                    onclick='express_find(this,"{{$order['id']}}")'>查看物流
                            </button>
                            @endif
                        </td>
                        <td rowspan="{{count($order['has_many_order_goods'])}}" style='width:18%;'>
                            <table class="goods-price" >
                                <tr>
                                    <td style=''>商品小计：</td>
                                    <td style=''>￥{!! number_format(
                                                $order['goods_price'] ,2) !!}
                                    </td>
                                </tr>

                                <tr>
                                    <td style=''>运费：</td>
                                    <td style=''>￥{!! number_format(
                                                $order['dispatch_price'],2) !!}
                                    </td>
                                </tr>
                                @if($order['change_price'] != 0)
                                    <tr>
                                        <td style=''>卖家改价：</td>
                                        <td style='color:green'>￥{!! number_format(
                                                $order['change_price'] ,2) !!}
                                        </td>
                                    </tr>
                                @endif
                                @if($order['change_dispatch_price'] != 0)
                                    <tr>
                                        <td style=''>卖家改运费：</td>
                                        <td style='color:green'>￥{{ number_format(
                                                $order['change_dispatch_price'] ,2) }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style=''>应收款：</td>
                                    <td style='color:green'>￥{!! number_format(
                                                $order['price'] ,2) !!}
                                    </td>
                                </tr>
                                @if($order['status'] == 0)
                                    <tr>
                                        <td ></td>
                                        <td style='color:green;'>
                                            <a href="javascript:;" class="btn btn-link "
                                               onclick="changePrice('{{$order['id']}}')">修改价格</a>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                        <td rowspan="{{count($order['has_many_order_goods'])}}"><label
                                    class='label label-info'>{{$order['status_name']}}</label><br/>
                            <a href="{!! yzWebUrl($detail_url,['id'=>$order['id']])!!}">查看详情</a>
                        </td>
                        <td rowspan="{{count($order['has_many_order_goods'])}}" width="10%">

                            @include($include_ops)

                        </td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>
@endforeach
@endsection