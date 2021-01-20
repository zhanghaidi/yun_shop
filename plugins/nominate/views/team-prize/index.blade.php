@extends('layouts.base')
@section('title', '团队奖记录')
@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-body">
                <form action="" method="post" class="form-horizontal" role="form" id="form1">
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <div class="">
                            <input type="text" placeholder="会员ID" class="form-control"  name="search[uid]" value="{{$search['uid']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <div class="">
                            <input type="text" placeholder="会员信息" class="form-control"  name="search[member]" value="{{$search['member']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <div class="">
                            <input type="text" placeholder="新进VIP-UID" class="form-control"  name="search[source_id]" value="{{$search['source_id']}}"/>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <input type="text" class="form-control"  name="search[source_member]" value="{{$search['source_member']}}" placeholder="新进VIP-信息"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-6">

                        <div class="time">

                            <select name='search[search_time]' class='form-control'>
                                <option value='0' @if($search['search_time']=='0') selected @endif>不搜索奖励时间</option>
                                <option value='1' @if($search['search_time']=='1') selected @endif>搜索奖励时间</option>
                            </select>
                        </div>
                        <div class="search-select">
                            {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                            'starttime'=>date('Y-m-d H:i', strtotime($search['time']['start']) ?: strtotime('-1 month')),
                            'endtime'=>date('Y-m-d H:i',strtotime($search['time']['end']) ?: time()),
                            'start'=>0,
                            'end'=>0
                            ], true) !!}
                        </div>
                    </div>

                    <div class="form-group  col-xs-12 col-sm-5 col-lg-4">
                        <div class="">
                            <button type="button" name="export" value="1" id="export" class="btn btn-default excel back ">导出Excel</button>
                            <input type="hidden" name="token" value="{{$var['token']}}" />
                            <button class="btn btn-success "><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">
                    总数：{{ $list->total() }}
                    <br>
                    累计{{ $set['team_prize_name']?:'团队奖' }}金额: {{ $amountTotal }}元
                </div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:6%;text-align: center;'>ID</th>
                            <th style='width:16%;text-align: center;'>时间</th>
                            <th style='text-align: center'>会员信息</th>
                            <th style='width:16%;text-align: center;'>会员等级</th>
                            <th style='text-align: center'>新进VIP会员</th>
                            <th style='text-align: center'>{{ $set['team_prize_name']?:'团队奖' }}金额</th>
                            <th style='text-align: center'>状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $item)
                            <tr style="text-align: center">
                                <td style="text-align: center;">
                                    {{ $item->id }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $item->created_at }}
                                </td>
                                <td style="text-align: center;">
                                    <a href="{!! yzWebUrl('member.member.detail', ['id'=>$item->uid]) !!}"><img src='{{ yz_tomedia($item->member->avatar) }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                        {{ $item->member->nickname }}
                                        <br>
                                        {{ $item->member->mobile }}</a>
                                </td>
                                <td style="text-align: center;">
                                    {{ $item->memberLevel->level_name }}
                                </td>
                                <td style="text-align: center;">
                                    <a href="{!! yzWebUrl('member.member.detail', ['id'=>$item->source_id]) !!}"><img src='{{ yz_tomedia($item->sourceMember->avatar) }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                        {{ $item->sourceMember->nickname }}
                                        <br>
                                        {{ $item->sourceMember->mobile }}</a>
                                </td>
                                <td style="text-align: center;">
                                    {{ $item->amount }}元
                                </td>
                                <td style="text-align: center;">
                                    已完成
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $pager !!}

                </div>
            </div>
        </div>
    </div>
    <script language='javascript'>
        $(function () {
            $('#export').click(function(){
                $('#form1').get(0).action="{!! yzWebUrl('plugin.nominate.admin.team-prize.export') !!}";
                $('#form1').submit();
                $('#form1').get(0).action="{!! yzWebUrl('plugin.nominate.admin.team-prize.index') !!}";
            });
        });
    </script>
@endsection