@extends('layouts.base')
@section('title', "分红详情")
@section('content')
    <style>
        .form-horizontal .control-label{padding-bottom: 8px;padding-top: 10px}

        .panel {
            margin-bottom: 20px !important;
            background-color: #fff;
            border-radius: 4px;
            -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
            box-shadow: 0 1px 1px rgba(0,0,0,.05);
        }
        .panel-default>.panel-heading {
            color: #333;
            background-color: #f5f5f5;
            border-color: #ddd;
        }
        .panel-heading {
            padding: 10px 15px;
            border: 1px solid #ddd !important;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
        }
        .panel-body {
            border: 1px solid #ddd !important;
            padding: 15px;
        }
    </style>
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="{!! yzWebUrl('plugin.commission.admin.commission-order.index') !!}">订单管理 &nbsp; <i class="fa fa-angle-double-right"></i> &nbsp; 订单详情</a>
                </li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <form id="setform" action="" method="post" class="form-horizontal form">
            <div class="col-xs-12">
                <div class="panel panel-default col-xs-12  col-sm-6">
                <div class="panel-heading">分销商信息
                    <strong style="float:right;">
                        <a href="{!! yzWebUrl('member.member.detail',array('id'=>$commission->hasOneMember->uid)) !!}">查看详情 </a>
                    </strong>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销商头像 :</label>
                        <div class="col-sm-9 col-xs-12">
                            <a href="{!! yzWebUrl('member.member.detail',array('id'=>$commission->hasOneMember->uid)) !!}">
                                <img src='{{$commission->parentMember->avatar}}' style='width:100px;height:100px;padding:1px;border:1px solid #ccc'/>
                            </a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销商信息 :</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class='form-control-static'>
                                ID: {{$commission->parentMember->uid}} &nbsp;
                                微信昵称: {{$commission->parentMember->nickname}}  &nbsp;
                                姓名: {{$commission->parentMember->realname ? : '无'}}  &nbsp;
                                手机号: {{$commission->parentMember->mobile ? : '无'}}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销等级 :</label>
                        <div class="col-sm-9 col-xs-12">
                            <p class="form-control-static">{{$commission->agent['agentLevel']['name']}}</p>
                        </div>
                    </div>
                </div>
            </div>
                <div class="panel panel-default col-xs-12  col-sm-6">
                    <div class="panel-heading">购买者信息
                        <strong style="float:right;">
                            <a href="{!! yzWebUrl('member.member.detail',array('id'=>$commission->hasOneMember->uid)) !!}">查看详情 </a>
                        </strong>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">购买者头像 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <a href="{!! yzWebUrl('member.member.detail',array('id'=>$commission->hasOneMember->uid)) !!}">
                                    <img src='{{$commission->hasOneMember->avatar}}' style='width:100px;height:100px;padding:1px;border:1px solid #ccc'/>
                                </a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">购买者信息 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    ID: {{$commission->hasOneMember->uid}} &nbsp;
                                    微信昵称: {{$commission->hasOneMember->nickname}}  &nbsp;
                                    姓名: {{$commission->hasOneMember->realname ? : '无'}}  &nbsp;
                                    手机号: {{$commission->hasOneMember->mobile ? : '无'}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="panel panel-default col-xs-12  col-sm-6">
                    <div class="panel-heading">分销信息</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销计算金额 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">{{$commission->commission_amount}}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">计算方式 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">{{$commission->formula}}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销层级 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">
                                    @if($commission->hierarchy == 0)
                                        额外分红
                                    @else
                                        {{$commission->hierarchy}}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">佣金比例 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">{{$commission->commission_rate}}%</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">佣金金额 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">{{$commission->commission}}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">佣金状态 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">
                                <span class="label
                                @if ($commission['status'] == 2) label-success
                                @elseif ($commission['status'] == -1) label-default
                                @else label-info
                                @endif">

                                @if($commission['status'] == '-1')
                                        无效佣金
                                    @elseif($commission['status'] == '0')
                                        预计佣金
                                    @elseif($commission['status'] == '1')
                                        未结算
                                    @elseif($commission['status'] == '2' && $commission['withdraw'] == '0')
                                        未提现
                                    @elseif($commission['status'] == '2' && $commission['withdraw'] == '1')
                                        已提现
                                    @elseif($commission['status'] == '2')
                                        已结算
                                    @endif
                                </span>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算类型 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">
                                    @if($set['settlement_model'] == 0)
                                        自动结算
                                    @else
                                        手动结算
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算选项 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">
                                    @if($set['settlement_option'] == 0)
                                        收入提现
                                    @else
                                        转入积分
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算事件 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">
                                    @if($set['settlement_event'] == 0)
                                        订单完成后
                                    @else
                                        订单支付后
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($commission['status'] >= 1)

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算期 :</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">{{$commission->settle_days}}天</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算开始时间 :</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">{{$commission->recrive_at ? date('Y-m-d H:i:s', $commission->recrive_at) : '无'}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">结算时间 :</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">{{$commission->statement_at ? date('Y-m-d H:i:s', $commission->statement_at)  : '无'}}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="panel panel-default col-xs-12  col-sm-6">
                    <div class="panel-heading">订单信息
                        <strong style="float:right;">
                            <a href="{{yzWebUrl('order.detail',['id'=>$commission->order->id])}}">查看详情 </a>
                        </strong>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单编号 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    @if($commission->ordertable_type != 'Yunshop\ClockIn\models\ClockPayLogModel')
                                        <a href="{{yzWebUrl('order.detail',['id'=>$commission->order->id])}}">{{$commission->order->order_sn}}</a>
                                    @else
                                        {{$clock_name}}分销奖励
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单状态 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">
                                <span class="label
                                @if ($commission->order['status'] == 3) label-success
                                @elseif ($commission->order['status'] == -1) label-default
                                @else label-info
                                @endif">
                                    {{$commission->order['status_name']}}
                                </span>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">下单日期 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">{{$commission->order['create_time']}}</p>
                            </div>
                        </div>
                        @if ($commission->order['status'] >= 1)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">付款时间 :</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">{{$commission->order['pay_time']}}</p>
                                </div>
                            </div>
                        @endif
                        @if ($commission->order['status'] >= 2)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">发货时间 :</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">{{$commission->order['send_time']}}</p>
                                </div>
                            </div>
                        @endif
                        @if ($commission->order['status'] == 3)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">完成时间 :</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">{{$commission->order['finish_time']}}</p>
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单金额 :</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class="form-control-static">
                                    @if($commission['ordertable_type'] != 'Yunshop\ClockIn\models\ClockPayLogModel')
                                        {{$commission->order['price']}}
                                    @else
                                        {{$commission['commission_amount']}}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                商品信息
                            </div>
                            <div class="panel-body table-responsive">
                                <table class="table table-hover">
                                    <thead class="navbar-inner">
                                    <tr>
                                        <th class="col-md-1 col-lg-1">ID</th>
                                        <th class="col-md-3 col-lg-3">商品标题</th>
                                        <th class="col-md-2 col-lg-2">均摊支付金额</th>
                                        <th class="col-md-3 col-lg-3">现价/原价/成本价</th>
                                        <th class="col-md-3 col-lg-3">购买数量</th>
                                    </tr>
                                    </thead>
                                    @foreach ($commission->order->hasManyOrderGoods as $order_goods)

                                        <tr>
                                            <td>{{$order_goods['goods_id']}}</td>
                                            <td>
                                                <a href="{{yzWebUrl($edit_goods, array('id' => $order_goods['goods_id']))}}">{{$order_goods['title']}}</a>
                                            </td>
                                            <td>{{$order_goods['payment_amount']}}</td>
                                            <td>{{$order_goods['goods_price']}}
                                                /{{$order_goods['goods_market_price']}}
                                                /{{$order_goods['goods_cost_price']}}元
                                            </td>
                                            <td>{{$order_goods['total']}}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($lose)
                <div class="col-xs-12">
                    <div class="panel panel-default col-xs-12  col-sm-6">
                        <div class="panel-heading">封顶金额</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">封顶金额:</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">
                                        {{$lose->amount_seal}}
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">今日分销佣金:</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">
                                        {{$lose->today_commission}}
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">今日经销商奖励:</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">
                                        {{$lose->today_team_dividend}}
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">今日共享奖励:</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">
                                        {{$lose->today_share}}
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">应获得金额:</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">
                                        {{$lose->should_amount}}
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">实际奖励金额:</label>
                                <div class="col-sm-9 col-xs-12">
                                    <p class="form-control-static">
                                        {{$lose->reality_amount}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </form>

    </div>
@endsection