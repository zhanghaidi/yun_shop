@extends('layouts.base')
@section('title', '分红统计记录')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="">分红统计记录</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info">
                <div class="panel-body">
                    <form action="" method="get" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="c" value="site" />
                        <input type="hidden" name="a" value="entry" />
                        <input type="hidden" name="m" value="yun_shop" />
                        <input type="hidden" name="do" value="5201" />
                        <input type="hidden" name="route" value="plugin.love.Backend.Modules.Love.Controllers.dividend-log.index" id="route" />
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                            <div class="">
                                <input type="text" placeholder="分红ID" class="form-control"  name="search[log_id]" value="{{$search['log_id']}}"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                            <div class="">
                                <input type="text" placeholder="会员ID" class="form-control"  name="search[member_id]" value="{{$search['member_id']}}"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control"  name="search[member]" value="{{$search['member']}}" placeholder="昵称/姓名/手机号"/>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2 col-md-12 col-lg-6">

                            <div class="time">

                                <select name='search[search_time]' class='form-control'>
                                    <option value='0' @if($search['search_time']=='0') selected @endif>不搜索时间</option>
                                    <option value='1' @if($search['search_time']=='1') selected @endif>搜索时间</option>
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

                        <div class="form-group  col-xs-12 col-sm-2 col-md-12 col-lg-6">
                            <div class="">
                                <button type="button" name="export" value="1" id="export" class="btn btn-default excel back ">导出excel</button>
                                <input type="hidden" name="token" value="{{$var['token']}}" />
                                <button class="btn btn-success "><i class="fa fa-search"></i>搜索</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div><div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('Yunshop\Love::change_records.total') }}：{{$pageList->total()}}   </div>
                    <div class="panel-body" style="margin-bottom:200px">
                        <table class="table table-hover" style="overflow:visible">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:10%;text-align: center;'>id</th>
                                <th style='width:10%;text-align: center;'>时间</th>
                                <th style='width:10%;text-align: center;'>会员信息</th>
                                <th style='width:8%;text-align: center;'>商城营业额</th>
                                <th style='width:8%;text-align: center;'>个人{{ $love_name }}</th>
                                <th style='width:8%;text-align: center;'>总{{ $love_name }}</th>
                                <th style='width:8%;text-align: center;'>分红比例(%)</th>
                                <th style='width:8%;text-align: center;'>分红金额</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pageList as $key => $list)
                                <tr style="text-align: center;">
                                    <td>{{ $list->id }}</td>
                                    <td>{{ $list->created_at }}</td>
                                    <td>
                                        <a href="{{ yzWebUrl('member.member.detail',['id' => $list->member_id]) }}">
                                            @if($list->member->avatar || $shopSet['headimg'])
                                                <img src='{{ $list->member->avatar ? tomedia($list->member->avatar) : tomedia($shopSet['headimg'])}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/>
                                                <br/>
                                            @endif
                                                {{ $list->member->realname ?: ($list->member->nickname ? $list->member->nickname : '未更新') }}
                                        </a>
                                    </td>
                                    <td>{{ $list->shop_amount }}</td>
                                    <td>{{ $list->love }}</td>
                                    <td>{{ $list->love_all }}</td>
                                    <td>{{ $list->dividend_rate }}</td>
                                    <td  style="overflow:visible;">{{ $list->dividend }}</td>
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
    <script language='javascript'>
        $(function () {
            $('#export').click(function(){
                $('#route').val("plugin.love.Backend.Modules.Love.Controllers.dividend-log.export");
                $('#form1').submit();
                $('#route').val("plugin.love.Backend.Modules.Love.Controllers.dividend-log.index");
            });
        });
    </script>
@endsection