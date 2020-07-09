@extends('layouts.base')

@section('content')
@section('title', trans($pluginName.'- 队列'))
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">{{$pluginName}} - 队列</a></li>
        <li class="active"><a href="{{yzWebUrl('plugin.clock-in.admin.clock-in-queue.export')}}">{{$pluginName}}队列导出</a>
        </li>
    </ul>
</div>

<div class='panel panel-default'>
    <div class='panel-body'>
        <table class="table table-hover" style="overflow:visible;">
            <thead>
            <tr>
                <th style='width:5%;'>ID</th>
                <th style='width:10%;'>时间</th>
                <th style='width:10%;'>前一天奖金池总金额</th>
                <th style='width:8%;'>奖金发放比例</th>
                <th style='width:8%;'>总发放金额</th>
                <th style='width:8%;'>前一天支付人数</th>
                <th style='width:8%;'>打卡人数</th>
                <th style='width:10%;'>未打卡人数</th>
                <th style='width:10%;'>操作</th>
            </tr>

            </thead>
            <tbody>
            @foreach($list as $row)
                <tr>
                    <td>{{$row['id']}}</td>
                    <td>{{$row['created_at']}}</td>
                    <td>{{$row['day_before_amount']}}</td>
                    <td>{{$row['rate']}}%</td>
                    <td>{{$row['amount']}}</td>
                    <td>{{$row['pay_num']}}人</td>
                    <td>{{$row['clock_in_num']}}人</td>
                    <td>{{$row['not_clock_in_num']}}人</td>
                    <td>
                        <a href="{{yzWebUrl('plugin.clock-in.admin.clock-in-pay-log.index',['search[queue_id]'=>$row['id']])}}">
                            查看详情
                        </a>
                    </td>
                </tr>

            @endforeach
            </tbody>
        </table>

        {!! $pager !!}
    </div>
</div>
<div style="width:100%;height:150px;"></div>

@endsection