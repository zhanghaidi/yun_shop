@extends('layouts.base')

@section('content')
@section('title', trans($pluginName.' - 奖励记录'))
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">{{$pluginName}} - 奖励记录</a></li>
    </ul>
</div>

<div class='panel panel-default'>
    <form action="" method="post" class="form-horizontal" id="form1">
        <div class="panel panel-info">
            <div class="panel-body">
                <div class="form-group col-xs-12 col-sm-3">
                    <input class="form-control" name="search[log_id]" type="text"
                           value="{{$search['log_id']}}" placeholder="奖励记录ID">
                </div>

                <div class="form-group col-xs-12 col-sm-3">
                    <input class="form-control" name="search[member]" type="text"
                           value="{{$search['member']}}" placeholder="会员ID/昵称/姓名/手机">
                </div>

                <div class="form-group col-xs-12 col-sm-8">
                    <div class="col-sm-3">
                        <label class='radio-inline'>
                            <input type='radio' value='0' name='search[is_time]'
                                   @if($search['is_time'] == '0') checked @endif>不搜索
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' value='1' name='search[is_time]'
                                   @if($search['is_time'] == '1') checked @endif>搜索
                        </label>
                    </div>
                    {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', ['starttime'=>$search['time']['start'],
                         'endtime'=>$search['time']['end'],
                         'start'=>$search['time']['start'],
                         'end'=>$search['time']['end']
                         ], true) !!}
                </div>

                <div class="form-group col-xs-12 col-sm-4">
                    <input type="button" class="btn btn-success" id="export" value="导出">
                    <input type="button" class="btn btn-success pull-right" id="search" value="搜索">
                </div>

            </div>
        </div>
    </form>
</div>

<div class='panel panel-default'>

    <div class='panel-body'>
        <table class="table table-hover" style="overflow:visible;">
            <thead>
            <tr>
                <th style='width:5%;'>ID</th>
                <th style='width:10%;'>时间</th>
                <th style='width:10%;'>会员</th>
                <th style='width:8%;'>奖励金额（元）</th>
            </tr>
            </thead>

            <tbody>
            @foreach($list as $row)
                <tr>
                    <td>{{$row['id']}}</td>
                    <td>{{$row['created_at']}}</td>
                    <td>
                        <a target="_blank"
                           href="{{yzWebUrl('member.member.detail',['id'=>$row['hasOneMember']['uid']])}}">
                            <img src="{{tomedia($row['hasOneMember']['avatar'])}}"
                                 style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                            </br>
                            {{$row['hasOneMember']['nickname']}}
                        </a>
                    </td>
                    <td>{{$row['amount']}}元</td>


                </tr>

            @endforeach
            </tbody>
        </table>

        {!! $pager !!}
    </div>
</div>
<div style="width:100%;height:150px;"></div>
<script language='javascript'>
    $(function () {
        $('#export').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('plugin.clock-in.admin.clock-in-reward-log.export') !!}');
            $('#form1').submit();
        });
        $('#search').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('plugin.clock-in.admin.clock-in-reward-log.index') !!}');
            $('#form1').submit();
        });
    });
</script>
@endsection