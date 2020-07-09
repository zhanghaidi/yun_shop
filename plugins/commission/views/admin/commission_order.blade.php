@extends('layouts.base')

@section('content')
@section('title', trans('分销订单管理'))
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">分销订单管理</a></li>
    </ul>
</div>
<form action="" method="post" class="form-horizontal" id="form1">
    <div class="panel panel-info">
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">订单ID</label>
                <div class="col-xs-12 col-sm-8 col-lg-9">
                    <input class="form-control" name="search[order]" type="text"
                           value="{{$search['order']}}" placeholder="订单ID/订单号">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">推荐人信息</label>
                <div class="col-xs-12 col-sm-8 col-lg-9">
                    <input class="form-control" name="search[member]" type="text"
                           value="{{$search['member']}}" placeholder="会员ID/昵称/手机">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">佣金状态</label>
                <div class="col-sm-8 col-lg-9 col-xs-12">
                    <select name='search[status]' class='form-control'>
                        <option value=''>所有状态</option>
                        <option value='0' @if($search['status'] == '0') selected @endif>预计佣金</option>
                        <option value='1' @if($search['status'] == '1') selected @endif>未结算</option>
                        <option value='2' @if($search['status'] == '2') selected @endif>已结算</option>
                        <option value='3' @if($search['withdraw'] == '0') selected @endif>未提现</option>
                        <option value='4' @if($search['withdraw'] == '1') selected @endif>已提现</option>
                        <option value='-1' @if($search['status'] == '-1') selected @endif>无效佣金</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">分销层级</label>
                <div class="col-sm-8 col-lg-9 col-xs-12">
                    <select name='search[hierarchy]' class='form-control'>
                        <option value=''>所有层级</option>
                        <option value='1' @if($search['hierarchy'] == '1') selected @endif>一级</option>
                        <option value='2' @if($search['hierarchy'] == '2') selected @endif>二级</option>
                        {{--<option value='3' @if($search['hierarchy'] == '3') selected @endif>三级</option>--}}
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">分销商等级</label>
                <div class="col-sm-8 col-lg-9 col-xs-12">
                    <select name='search[level]' class='form-control'>
                        <option value=''>所有等级</option>
                        @foreach($agent_levels as $level)
                            <option value='{{$level['id']}}'
                                    @if($search['level'] == $level->id) selected @endif> {{$level->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">统计</label>
                <div class="col-sm-8 col-lg-9 col-xs-12">
                    <select name='search[statistics]' class='form-control'>
                        <option value='2' @if($search['statistics'] == '2') selected @endif>不统计</option>
                        <option value='1' @if($search['statistics'] == '1') selected @endif>统计</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">支付状态</label>
                <div class="col-sm-8 col-lg-9 col-xs-12">
                    <select name='search[is_pay]' class='form-control'>
                        <option value='0' @if($search['is_pay'] == '0') selected @endif>全部</option>
                        <option value='1' @if($search['is_pay'] == '1') selected @endif>已支付</option>
                        <option value='2' @if($search['is_pay'] == '2') selected @endif>未支付</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
                <div class="col-sm-8 col-lg-9 col-xs-12">
                    <label class='radio-inline'>
                        <input type='radio' value='0' name='search[is_time]'
                               @if($search['is_time'] == '0') checked @endif>不搜索
                    </label>
                    <label class='radio-inline'>
                        <input type='radio' value='1' name='search[is_time]'
                               @if($search['is_time'] == '1') checked @endif>搜索
                    </label>

                    {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', ['starttime'=>$search['time']['start'],
                         'endtime'=>$search['time']['end'],
                         'start'=>$search['time']['start'],
                         'end'=>$search['time']['end']
                         ], true) !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-3 col-lg-3 control-label"> </label>
                <div class="col-xs-12 col-sm-3 col-lg-3">
                    {{--<input type="submit" class="btn btn-success" value="搜索">--}}
                    <input type="button" class="btn btn-success" id="scanRepetition" value="检测重复记录">

                    <input type="button" class="btn btn-success" id="export" value="导出">

                    <input type="button" class="btn btn-success pull-right" id="search" value="搜索">

                </div>
            </div>

        </div>
    </div>
</form>

<div class='panel panel-default'>
        <div class='panel-heading'>
            <div id="statistics" @if($search['statistics'] != 1) hidden="hidden" @endif>
                管理 (数量: {{$total}} 条) 累计佣金:{{$commission_total}} 分销订单金额:{{$count['order_amount']}}</br>
                累计未支付佣金:{{$count['unpaid']}}
                累计未结算佣金:{{$count['unliquidated']}}
                累计已结算佣金:{{$count['already_settled']}}
                累计未提现佣金:{{$count['not_present']}}
                累计已提现佣金:{{$count['withdraw']}}
                </br>
                注: 不能修改的佣金为已申请或已结算
            </div>
        </div>

    <div class='panel-body'>

        <table class="table table-hover" style="overflow:visible;">
            <thead class='panel panel-default'>
            <tr class='panel-heading'>
                <th style='width:5%;'>ID</th>
                <th style='width:15%;'>订单号</th>
                <th style='width:15%;'>购买者信息</th>
                <th style='width:10%;'>订单金额</th>
                <th style='width:15%;'>分销计算金额</br>计算方式</th>
                <th style='width:15%;'>推荐者信息</th>
                <th style='width:15%;'>推荐者分销等级</br>分销层级/佣金比例</th>
                <th style='width:10%;'>佣金金额</th>
                <th style='width:10%;'>佣金状态</th>
                <th style='width:10%;'> 操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
                <tr>
                    <td>{{$row['id']}}</td>
                    <td>
                        @if($row['ordertable_type'] != 'Yunshop\ClockIn\models\ClockPayLogModel')
                            <a href="{{yzWebUrl('order.detail',['id'=>$row['order']['id']])}}">{{$row['order']['order_sn']}}</a>
                        @else
                            {{$clock_name}}分销奖励
                        @endif
                    </td>
                    <td>
                        <img src="{{$row->hasOneMember->avatar}}"
                             style="width: 40px; height: 40px;border:1px solid #ccc;padding:1px;">
                        </br>
                        {{$row->hasOneMember->nickname}}
                    </td>
                    <td>
                        @if($row['ordertable_type'] != 'Yunshop\ClockIn\models\ClockPayLogModel')
                            {{$row['order']['price']}}
                        @else
                            {{$row['commission_amount']}}
                        @endif
                    </td>
                    <td> {{$row['commission_amount']}} </br> {{$row['formula']}}</td>
                    <td><img src="{{$row['parentMember']['avatar']}}"
                             style="width: 40px; height: 40px;border:1px solid #ccc;padding:1px;">
                        </br>
                        {{$row['parentMember']['nickname']}}
                    </td>
                    <td>
                        @if($row->agent['agentLevel']['name'] == '默认等级') 
                            {{$defaultLevelName}}
                        @else
                            {{$row->agent['agentLevel']['name']}}
                        @endif
                        </br>层级:{{$row['hierarchy']}} - 比例:{{$row['commission_rate']}}</td>
                    <td>
                        @if($row['status'] <= '1' && $row['status'] != '-1')
                            <a href="javascript:;" class="edit-commission">{{$row['commission']}} 修改 </a>
                            <input style="display: none" type="text" class="updated-commission"
                                   data-id="{{$row['id']}}" name="commission"
                                   value="{{$row['commission']}}">
                        @else
                            {{$row['commission']}}
                        @endif

                    </td>
                    <td>
                        @if($row['status'] == '-1')
                            无效佣金
                        @elseif($row['status'] == '0')
                            预计佣金
                        @elseif($row['status'] == '1')
                            未结算
                        @elseif($row['status'] == '2' && $row['withdraw'] == '0')
                            未提现
                        @elseif($row['status'] == '2' && $row['withdraw'] == '1')
                            已提现
                        @elseif($row['status'] == '2')
                            已结算
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-primary" href="{{ yzWebUrl('plugin.commission.admin.commission-order.details',['id' => $row['id']]) }}">查看详情</a>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>

        {!! $pager !!}
    </div>
</div>
<script>
    $('#export').click(function () {
        $('#form1').attr('action', '{!! yzWebUrl('plugin.commission.admin.commission-order.export') !!}');
        $('#form1').submit();
    });

    $('#search').click(function () {
        $('#form1').attr('action', '{!! yzWebUrl('plugin.commission.admin.commission-order.index') !!}');
        $('#form1').submit();
    });
    $('#scanRepetition').click(function () {
        $('#form1').attr('action', '{!! yzWebUrl('plugin.commission.admin.commission-order.index',['scan_repetition'=>1]) !!}');
        $('#form1').submit();
    });

    $(".edit-commission").click(function () {
        var _this = $(this);
        _this.hide();
        _this.next().show();

    });

    $(".updated-commission").blur(function () {
        var _this = $(this);
        var id;

        id = _this.data('id');
        $.ajax({
            url: '{!! yzWebUrl('plugin.commission.admin.commission-order.edit') !!}',
            dataType: 'json',
            data: {
                commission: $.trim(_this.val()),
                id: $.trim(id)
            }, success: function (query) {
                _this.hide();
                _this.prev().show();
                if (query.result) {
                    _this.prev().text(_this.val() + '修改');
                }
                alert(query.msg);
            }
        });
    });

    $(".updated-commission").keydown(function (e) {
        if (e.which == 13) {
            $(".updated-commission").blur();
        }
    });

    $('#statistics').change(function () {
        
    })
</script>
@endsection
