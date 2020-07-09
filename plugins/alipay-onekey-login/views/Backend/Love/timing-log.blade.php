@extends('layouts.base')
@section('title', trans('定期充值记录'))
@section('content')
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">定期充值记录</a></li>
        </ul>
    </div>

    <div class='panel panel-default'>
        <form action="" method="get" class="form-horizontal" id="form1">

            <input type="hidden" name="c" value="site"/>
            <input type="hidden" name="a" value="entry"/>
            <input type="hidden" name="m" value="yun_shop"/>
            <input type="hidden" name="do" value="sign" id="form_do"/>
            <input type="hidden" name="route" value="plugin.love.Backend.Modules.Love.Controllers.timing-log.index" id="route"/>

            <div class="panel panel-info">
                <div class="panel-body">

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <div class="">
                            <input type="text" placeholder="会员信息" class="form-control" name="search[member]"
                                   value="{{$search['member']}}"/>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <select name='search[member_level]' class='form-control'>
                                <option value=''>会员等级</option>

                                @foreach($memberLevels as $item)
                                    <option value='{{ $item['id'] }}'
                                            @if($search['member_level'] == $item['id']) selected @endif>{{ $item['level_name'] }}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <select name='search[member_group]' class='form-control'>
                                <option value=''>会员分组</option>
                                @foreach($memberGroups as $item)
                                    <option value='{{ $item['id'] }}'
                                            @if($search['member_group'] == $item['id']) selected @endif>{{ $item['group_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
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
                       {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                            'starttime'=>$search['time']['start'] ? $search['time']['start'] : date('Y-m-d H:i:s',strtotime('-7 day')),
                            'endtime'=>$search['time']['end'] ? $search['time']['end'] : date('Y-m-d H:i:s'),
                            'start'=>0,
                            'end'=>0
                        ], true)!!}
                    </div>
                    
                    <div class="form-group col-xs-12 col-sm-4">

                        <input type="submit" class="btn btn-success pull-right"  value="搜索">

                    </div>

                </div>
            </div>
        </form>
    </div>



    <div class="w1200 m0a">
        <div class="rightlist">

            <div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">总数：{{$total}} 已完成：{{$recharge}} 未完成：{{$noRecharge}} 总充值数量：{{$total}} 未充值数量：{{$noRecharge}}</div>
                    <div class="panel-body" style="margin-bottom:200px">
                        <table class="table table-hover" style="overflow:visible">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:12%;text-align: center;'>充值单号</th>
                                <th style='width:8%;text-align: center;'>会员</th>
                                <th style='width:10%;text-align: center;'>等级</br>分组</th>
                                <th style='width:15%;text-align: center;'>开始时间</th>
                                <th style='width:8%;text-align: center;'>已充值{{$loveName}}</th>
                                <th style='width:8%;text-align: center;'>待充值{{$loveName}}</th>
                                <th style='width:8%;text-align: center;'>已充值/总期数</th>
                                <th style='width:8%;text-align: center;'>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list['data'] as $item)
                                <tr style="text-align: center;">
                                    <td>
                                        {{$item['recharge_sn']}}
                                    </td>
                                    <td>
                                        <a href="{{ yzWebUrl('member.member.detail',['id' => $item['member_id']]) }}">
                                            <img src='{{ $item['has_one_member']['avatar']}}'
                                                 style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/>
                                            <br/>
                                            {{ $item['has_one_member']['realname'] ?: ($item['has_one_member']['nickname'] ? $item['has_one_member']['nickname'] : '未更新') }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $item['yz_member']['level_name'] ? $item['yz_member']['level_name'] : '普通等级'}}<br>
                                        {{ $item['yz_member']['group_name'] ? $item['yz_member']['group_name'] : '无分组'}}
                                    </td>
                                    <td>
                                        {{ $item['created_at'] }}
                                    </td>
                                    <td>{{ $item['recharge']['amount'] }}</td>
                                    <td>
                                        {{ $item['no_recharge']['amount'] }}
                                    </td>
                                    <td>
                                        {{ $item['recharge']['num'] }}/{{$item['total']}}
                                    </td>
                                    <td>
                                        <a href="{{yzUrl('plugin.love.Backend.Modules.Love.Controllers.timing-log.detail',['id'=>$item['id']])}}">查看详情</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!!$page!!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection