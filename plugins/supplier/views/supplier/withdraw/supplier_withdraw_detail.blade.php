@extends('Yunshop\Supplier::supplier.layouts.base')

@section('content')
    <div class="w1200 m0a">
        <form action="" method='post' class='form-horizontal' id="form">
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
                        @else
                            <label type="button" class="label label-default">
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
                佣金总计 <span style="color:red; ">{{$withdraw->apply_money}}</span> 元

            </div>
            <div class='panel-body'>
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th>订单号</th>
                        <th>总金额</th>
                        <th>商品金额</th>
                        <th>运费</th>
                        <th>付款方式</th>
                        <th>下单时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($withdraw->belongsToManyOrder as $row)
                    <tr  style="background: #eee">
                        <td>{{$row->order_sn}}</td>
                        <td>{{$row->price}}</td>
                        <td>{{$row->goods_price}}</td>
                        <td>运费：{{$row->dispatch_price}}</td>
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
                                    <th style='width:60px;'>商品</th>
                                    <th></th>
                                    <th>单价</th>
                                    <th>数量</th>
                                    <th>总价</th>
                                    <th>佣金</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($row->hasManyOrderGoods as $g)
                                <tr>
                                    <td style='height:60px;'><img src="{{tomedia($g->thumb)}}" style="width: 50px; height: 50px;border:1px solid #ccc;padding:1px;"></td>
                                    <td><span>{{$g->title}}</span>
                                    </td>
                                    <td>原价: {!! $g->goods_price/$g->total !!}<br/>折扣后:{!! $g->price/$g->total !!}</td>
                                    <td>{{$g->total}}</td>
                                    <td><strong>原价:{{$g->goods_price}}<br/>折扣后:{{$g->price}}</strong></td>
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
                此次佣金总额:  <span style='color:red'>{{$withdraw->apply_money}}</span> 元
                应该打款：<span style='color:red'>{{$withdraw->money}}</span> 元
            </div>
            @endif

        </div>
        <div class="form-group col-sm-12">
            {{--@if ($withdraw->status == 1)
            <input type="submit" name="submit_check" value="提交审核" class="btn btn-primary col-lg" onclick='return check()'/>
            @endif

            @if ($withdraw->status == 2)
            <input type="submit" name="submit_cancel" value="重新审核" class="btn btn-default col-lg"  onclick='return cancel()'/>
            @endif

            @if ($withdraw['type'] == 1)
            <input type="submit" name="submit_pay" value="打款到银行卡" class="btn btn-primary col-lg"  style='margin-left:10px;' onclick='return pay_credit()'/>
            @elseif ($withdraw['type'] == 2)
            <input type="submit" name="submit_pay" value="打款到微信" class="btn btn-primary col-lg" style='margin-left:10px;' onclick='return pay_weixin()'/>
            @endif--}}

            <input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回" style='margin-left:10px;' />
            <input type="hidden" name="token" value="{{$var['token']}}" />
        </div>
        </form>

        <script language='javascript'>
            function checkall(ischeck){
                var val =  ischeck?2:-1;

                $('.status1,.status2,.status3').each(function(){
                    $(this).closest('.input-group-addon').find(":radio[value='" + val + "']").get(0).checked = true;
                });
            }
            function check(){
                var pass  = true;
                $('.status1,.status2,.status3').each(function(){
                    if( !$(this).get(0).checked && !$(this).parent().next().find(':radio').get(0).checked){
                        Tip.focus( $(this),'请选择审核状态!' );
                        pass = false;
                        return false;
                    }
                });
                if(!pass){
                    return false;
                }
                return confirm('确认已核实成功并要提交?\r\n(提交后还可以撤销审核状态, 申请将恢复到申请状态)');
            }
            function cancel(){
                return confirm('确认撤销审核?\r\n( 所有状态恢复到申请状态)');
            }
            function pay_credit(){
                return confirm('确认打款到此用户的余额账户?');
            }
            function pay_weixin(){
                return confirm('确认打款到此用户的微信钱包?');
            }
            function pay_alipay(){
                $("#form").attr("target", "_blank");
                return confirm('确认打款到此用户的支付宝?');
            }
            function pay_alipay2(){
                $("#form").attr("target", "_blank");
                return   confirm('确认再次打款到此用户的支付宝?');
            }
        </script>
    </div>
@endsection