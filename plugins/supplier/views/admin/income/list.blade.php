@extends('layouts.base')

@section('content')
@section('title', '提成明细')
<script src="https://cdn.static.runoob.com/libs/angular.js/1.4.6/angular.min.js"></script>
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">提成明细</a></li>
    </ul>
</div>

<div class='panel panel-default'>
    <form action="" method="post" class="form-horizontal" id="form1">
        <div class="panel panel-info">
            <div class="panel-body">

                <div class="form-group col-xs-12 col-sm-3">
                    <input class="form-control" name="search[id]" type="text"
                           value="{{$search['id']}}" placeholder="提成ID">
                </div>

                <div class="form-group col-xs-12 col-sm-3">
                    <input class="form-control" name="search[uid]" type="text"
                           value="{{$search['uid']}}" placeholder="会员ID">
                </div>

                <div class="form-group col-xs-12 col-sm-3">
                    <input class="form-control" name="search[member]"  type="text"
                           value="{{$search['member']}}" placeholder="会员昵称/姓名/手机号">
                </div>

                <div class="form-group col-xs-12 col-sm-3">
                    <input class="form-control" name="search[order_sn]"  type="text"
                           value="{{$search['order_sn']}}" placeholder="订单编号">
                </div>

                <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                    <div class="">
                        <button class="btn btn-success ">
                            <i class="fa fa-search"></i>
                            搜索
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<body ng-app="">
<div class='panel panel-default'>
    <div class='panel-heading'>
        总数：{{$list->total()}}个
    </div>
    <div class='panel-body'>
        <table class="table table-hover" style="overflow:visible;">
            <thead>
            <tr>
                <th style='width:6%;text-align: center;'>ID</th>
                <th style='width:16%;text-align: center;'>时间</th>
                <th style='width:16%;text-align: center;'>供应商</th>
                <th style='width:10%;text-align: center;'>订单编号</th>
                <th style='width:8%;text-align: center;'>订单金额</th>
                <th style='width:8%;text-align: center;'>提成金额</th>
                <th style='width:8%;text-align: center;'>提现状态</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
                <tr>
                    <td style="text-align: center;">{{$row->id}}</td>
                    <td style="text-align: center;">{{$row->created_at}}</td>
                    <td style="text-align: center;">
                        <a target="_blank"
                           href="{{yzWebUrl('member.member.detail',['id' => $row->member_id])}}">
                            <img src="{{tomedia($row->member->avatar)}}"
                                 style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                            </br>
                            昵称:{{$row->member->nickname}}<br>
                        </a>
                        账号:{{$row->supplier->username}}
                    </td>
                    <td style="text-align: center;">
                        <a target="_blank"
                           href="{{yzWebUrl('plugin.supplier.admin.controllers.order.supplier-order-detail',['id' => $row->order_id])}}">
                            {{$row->order->order_sn}}
                        </a>
                    </td>
                    <td style="text-align: center;">{{$row->order->price}}元</td>
                    <td style="text-align: center;">{{$row->supplier_profit}}元</td>
                    <td style="text-align: center;">{{$row->status_name}}</td>
                </tr>

            @endforeach
            </tbody>
        </table>

        {!! $pager !!}
    </div>
</div>

</body>
<div style="width:100%;height:150px;"></div>
@endsection