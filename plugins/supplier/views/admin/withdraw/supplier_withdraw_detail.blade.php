@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        <form action="" method='post' class='form-horizontal' id="form">
            <input type="hidden" name="c" value="site" />
            <input type="hidden" name="a" value="entry" />
            <input type="hidden" name="m" value="yun_shop" />
            <input type="hidden" name="do" value="{{random(4)}}" />
            <input type="hidden" name="route" value="plugin.supplier.admin.controllers.withdraw.withdraw-operation.pay" />
            <input type="hidden" name="id" value="{{$withdraw->id}}" />
        <div class="panel panel-default">
            <div class='panel-heading'>
                提现者信息
            </div>
            <div class='panel-body'>
                <div style='height:auto;width:120px;float:left;'>
                    <img src='{{$withdraw->hasOneMember->avatar}}' style='width:100px;height:100px;border:1px solid #ccc;padding:1px' />
                </div>
                <div style='float:left;height:auto;overflow: hidden'>
                    <p>
                        <b>昵称:</b> {{$withdraw->hasOneMember->nickname}}
                        <b>姓名:</b> {{$withdraw->hasOneMember->realname}}
                        <b>手机号:</b> {{$withdraw->hasOneMember->mobile}}
                    </p>
                    <p>
                        @if ($withdraw->type == 1 && $withdraw->status == 2)
                            <b>银行账号:</b> {{$withdraw->hasOneSupplier->company_bank}}
                            <b>开户人姓名:</b> {{$withdraw->hasOneSupplier->bank_username}}
                            <b>开户行:</b> {{$withdraw->hasOneSupplier->bank_of_accounts}}
                            <b>开户支行:</b> {{$withdraw->hasOneSupplier->opening_branch}}
                            <b>微信账号:</b> {{$withdraw->hasOneSupplier->wechat}}<br>
                            <b>企业支付宝账号:</b> {{$withdraw->hasOneSupplier->company_ali}}
                            <b>企业支付宝用户名:</b> {{$withdraw->hasOneSupplier->company_ali_username}}
                            <b>支付宝账号:</b> {{$withdraw->hasOneSupplier->ali}}
                            <b>支付宝用户名:</b> {{$withdraw->hasOneSupplier->ali_username}}
                        @endif
                    </p>
                    <p>
                        <b>申请佣金: </b><span style='color:red'>{{$withdraw->apply_money}}</span> 元
                        <b>手续费: </b><span style='color:red'>{{$withdraw->apply_money - $withdraw->money}}</span> 元
                        <b>应打款佣金: </b><span style='color:red'>{{$withdraw->money}}</span> 元
                        <b>打款方式: </b>
                        @if ($withdraw->type == 1)
                            <label type="button" class="label label-primary">
                        @elseif($withdraw->type == 2)
                            <label type="button" class="label label-success">
                        @elseif($withdraw->type == 3)
                            <label type="button" class="label label-warning">
                        @elseif($withdraw->type == 5)
                            <label type="button" class="label label-info">
                        @endif
                            {{$withdraw->type_name}}
                        </label>
                    </p>
                    <p>
                        <b>状态: </b>
                        @if ($withdraw->status == 1)
                            <span class='label label-primary'>申请中</span>
                        @elseif ($withdraw->status == 2)
                            <span class='label label-success'>审核完毕，准备打款</span>
                        @elseif ($withdraw->status == 4)
                            <span class='label label-success'>打款中</span>
                        @elseif ($withdraw->status == 3)
                            <span class='label label-warning'>已打款</span>
                        @elseif ($withdraw->status == -1)
                            <span class='label label-warning'>驳回</span>
                        @endif
                        @if ($withdraw->status == 1)
                            <b>申请时间: </b> {{$withdraw['created_at']}}
                        @endif
                        @if ($withdraw->status == 2)
                            <b>审核时间: </b> {{$withdraw['updated_at']}}
                        @endif
                        @if ($withdraw->status == 3)
                            <b>打款时间: </b> {{date('Y-m-d H:i', $withdraw['pay_time'])}}
                        @endif
                    </p>
                </div>
            </div>

            <div class='panel-heading'>
                提现申请订单信息 共计 <span style="color:red; ">{{$order_count}}</span> 个订单
                佣金总计 <span style="color:red; ">{{$withdraw->money}}</span> 元
                @if ($withdraw->status == 1)
                <a href="{{yzWebUrl('plugin.supplier.admin.controllers.withdraw.withdraw-operation.index', ['withdraw_id' => $withdraw->id, 'type' => 2])}}" class="btn btn-primary">审核通过</a>

                <a href="{{yzWebUrl('plugin.supplier.admin.controllers.withdraw.withdraw-operation.index', ['withdraw_id' => $withdraw->id, 'type' => -1])}}" class="btn btn-danger">审核不通过</a>
                @endif
            </div>
            <div class='panel-body'>
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style='width:24%;'>订单号</th>
                        <th style='width:10%;'>总金额</th>
                        <th style='width:10%;'>商品金额</th>
                        <th style='width:10%;'>运费</th>
                        <th style='width:10%;'>成本+运费</th>
                        <th style='width:10%;'>付款方式</th>
                        <th style='width:24%;'>下单时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($withdraw->belongsToManyOrder as $row)
                        <tr  style="background: #eee">
                            <td>{{$row->order_sn}}</td>
                            <td>{{$row->price}}</td>
                            <td>{{$row->goods_price}}</td>
                            <td>运费：{{$row->dispatch_price}}</td>
                            <td>{{$row->profit}}</td>
                            <td>
                                <span class="label label-danger">{{$row->pay_type_name}}</span>
                            </td>

                            <td>{{$row->create_time}}</td>
                        </tr>
                        <tr >
                            <td colspan="6">
                                <table width="100%">
                                    <thead class="navbar-inner">
                                    <tr>
                                        <th style='width:12%;'>商品</th>
                                        <th style='width:5%;'>名称</th>
                                        <th style='width:5%;'>单价</th>
                                        <th style='width:5%;'>数量</th>
                                        <th style='width:5%;'>总价</th>
                                        <th style='width:11%;'></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($row->hasManyOrderGoods as $g)
                                        <tr>
                                            <td style='height:60px;'><img src="{{tomedia($g->thumb)}}" style="width: 50px; height: 50px;border:1px solid #ccc;padding:1px;"></td>
                                            <td><span>{{$g->title}}</span></span>
                                            </td>
                                            <td>原价: {!! $g->goods_price/$g->total !!}<br/>折扣后:{!! $g->price/$g->total !!}</td>
                                            <td>{{$g->total}}</td>
                                            <td>原价<br/><strong>{{$g->goods_price}}</strong><br/>折扣后<br/><strong>{{$g->price}}</strong></td>
                                        </tr>
                                    @endforeach
                                    </tbody></table>
                            </td></tr>
                    @endforeach
                </table>
            </div>

            @if ($withdraw->status == 2)
            <div class='panel-heading'>
                打款信息
            </div>
            <div class='panel-body'>
                此次佣金总额:  <span style='color:red'>{{$withdraw->money}}</span> 元
                应该打款：<span style='color:red'>{{$withdraw->money}}</span> 元
            </div>
            @endif

        </div>
        <div class="form-group col-sm-12">
            {{--@if ($withdraw->status == 1)
            <input type="submit" name="submit_check" value="提交审核" class="btn btn-primary col-lg-1" onclick='return check()'/>
            @endif

            @if ($withdraw->status == 2)
            <input type="submit" name="submit_cancel" value="重新审核" class="btn btn-default col-lg-1"  onclick='return cancel()'/>
            @endif

            {{--@if ($withdraw->type == 1)--}}
            {{--<input type="submit" name="submit_pay" value="打款到银行卡" class="btn btn-primary col-lg-1"  style='margin-left:10px;' onclick='return pay_credit()'/>--}}
            {{--@elseif ($withdraw->type == 2)--}}
            {{--<input type="submit" name="submit_pay" value="打款到微信" class="btn btn-primary col-lg-1" style='margin-left:10px;' onclick='return pay_weixin()'/>--}}
            {{--@endif--}}
            @if ($withdraw->status == 2)
                @if ($withdraw->type == 1)
                    <input type="submit" name="submit_pay" value="手动提现" class="btn btn-success col-lg"  style='margin-left:10px;'/>
                @elseif ($withdraw->type == 2)
                    <input type="submit" name="submit_pay" value="打款到微信" class="btn btn-success col-lg" style='margin-left:10px;'/>
                @elseif ($withdraw->type == 3)
                    <input type="submit" name="submit_pay" value="打款到支付宝" class="btn btn-success col-lg" style='margin-left:10px;'/>
                @elseif ($withdraw->type == 4)
                    <input type="submit" name="submit_pay" value="打款到易宝" class="btn btn-success col-lg" style='margin-left:10px;'/>
                @elseif ($withdraw->type == 5)
                    <input type="submit" name="submit_pay" value="打款到汇聚" class="btn btn-success col-lg" style='margin-left:10px;'/>
                @endif
            @endif

            <input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回" style='margin-left:10px;' />
            <input type="hidden" name="token" value="{{$var['token']}}" />
        </div>
        </form>
    </div>
@endsection