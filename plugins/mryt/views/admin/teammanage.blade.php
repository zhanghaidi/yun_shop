@extends('layouts.base')
@section('title', '团队管理奖记录')
@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-body">
                <form action="" method="post" class="form-horizontal" role="form" id="form1">
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <div class="">
                            <input type="text" placeholder="会员uid" class="form-control"  name="search[uid]" value="{{$search['uid']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <div class="">
                            <input type="text" placeholder="会员信息" class="form-control"  name="search[member]" value="{{$search['member']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <div class="">
                            <input type="text" placeholder="销售佣金id" class="form-control"  name="search[log_id]" value="{{$search['log_id']}}"/>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <input type="text" class="form-control"  name="search[log_member]" value="{{$search['log_member']}}" placeholder="销售佣金信息"/>
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
                            {{--<button type="submit" name="export" value="1" id="export" class="btn btn-default excel back ">导出Excel</button>
                            <input type="hidden" name="token" value="{{$var['token']}}" />--}}
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
                    累计分红金额: {{ $amount_total }}元
                </div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:3%;text-align: center;'>ID</th>
                            <th style='width:16%;text-align: center;'>分红时间</th>
                            <th style='text-align: center'>会员信息</th>
                            <th style='text-align: center'>等级</th>
                            <th style='text-align: center'>类型</th>
                            <th style='width:16%; text-align: center'>销售佣金ID<br>订单号</th>
                            <th style='text-align: center'>获得销售佣金会员<br>店主</th>
                            <th style='text-align: center'>销售佣金金额<br>订单金额</th>
                            <th style='text-align: center'>分红比例</th>
                            <th style='text-align: center'>下级会员提成比例</th>
                            <th style='text-align: center'>奖励金额</th>
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
                                    <img src='{{ yz_tomedia($item->hasOneMember->avatar) }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    <a href="{!! yzWebUrl('member.member.detail',['id' => $item->uid])!!}">
                                        {{ $item->hasOneMember->nickname }}
                                    </a>
                                    <br>
                                    {{ $item->hasOneMember->mobile }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $item->hasOneLevel->level_name }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $item->type == 1 ? '销售佣金' : '门店订单' }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $item->log_id }}
                                </td>
                                <td style="text-align: center;">
                                    <img src='{{ $item->hasOneSourceMember->avatar }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    <a href="{!! yzWebUrl('member.member.detail',['id' => $item->log_uid])!!}">
                                        {{ $item->hasOneSourceMember->nickname }}
                                    </a>
                                    <br>
                                    {{ $item->hasOneSourceMember->mobile }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $item->log_amount }}元
                                </td>
                                <td style="text-align: center;">
                                    {{ $item->award_ratio }}%
                                </td>
                                <td style="text-align: center;">
                                    {{ $item->lower_award_ratio }}%
                                </td>
                                <td style="text-align: center;">
                                    {{ $item->amount }}元
                                </td>
                                <td style="text-align: center;">
                                    {{ $item->status_name }}
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
                $('#form1').submit();
            });
        });
    </script>
@endsection