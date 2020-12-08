@extends('layouts.base')

@section('content')
    <link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div id="member-blade" class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->

        <div class="right-titpos">
            @include('layouts.tabs')
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="" method="post" class="form-horizontal" role="form" id="form1">
                    <div class="form-group col-sm-11 col-lg-11 col-xs-12">
                        <div class="">
                            <div class='input-group'>
                                <input class="form-control" name="search[jiushi_wechat]" type="text"
                                       value="{{ $request['search']['jiushi_wechat'] or ''}}" placeholder="灸师微信号">
                                <input class="form-control" name="search[jiushi_id]" type="text"
                                       value="{{ $request['search']['jiushi_id'] or ''}}" placeholder="灸师ID">
                                {{--                                <input class="form-control" name="search[jiushi_name]" type="text" value="{{ $request['search']['jiushi_name'] or ''}}" placeholder="灸师真实姓名">--}}
                                <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                                    <select name="search[searchtime]" class="form-control">
                                        <option value="" selected>请选择时间</option>
                                        <option value="0" @if($request['search']['searchtime']=='0') selected @endif>
                                            发送短信时间
                                        </option>
                                        {{--                                        <option value='1' @if($request['search']['searchtime']=='1') selected @endif>直播结束时间</option>--}}
                                    </select>
                                </div>

                                <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                                    <div class="search-select">
                                        {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[date]', [
                                        'starttime'=>$request['search']?$request['search']['date']['start']:date('Y-m-01',time()),
                                        'endtime'=>$request['search']?$request['search']['date']['end']:date('Y-m-t',time()),
                                        'start'=>0,'end'=>0
                                        ], false) !!}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-1 col-lg-1 col-xs-12">
                        <div class="">
                            <input type="submit" class="btn btn-block btn-success" value="搜索">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">总数：{{ $count }}&nbsp;&nbsp;成功百分比：{{ $success_percentage }} %</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:15%; text-align: center;'>序号ID</th>
                            <th style='width:15%; text-align: center;'>灸师ID</th>
                            <th style='width:10%; text-align: center;'>客户手机号</th>
                            <th style='width:10%; text-align: center;'>灸师微信号</th>
                            <th style='width:12%; text-align: center;'>发送时间</th>
                            <th style='width:10%; text-align: center;'>发送状态/原因</th>
                            <th style='width:10%; text-align: center;'>加友状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr style="text-align: center;">
                                <td>{{ $list['id'] }}</td>
                                <td>{{ $list['jiushi_id'] }}</td>
                                <td>
                                    {{ $list['mobile'] }}
                                </td>
                                <td>
                                    {{ $list['jiushi_wechat'] }}
                                </td>
                                <td>{{ date('Y-m-d H:i:s',$list['createtime']) }}</td>
                                <td>
                                    @if($list['result'] == '0')
                                        <span class='label label-success'>成功</span><br>
                                        {{ $list['result_error_msg'] }}
                                    @else
                                        <span class='label label-warning'>失败</span><br>
                                        {{ $list['result_error_msg'] }}
                                    @endif
                                </td>
                                <td style="overflow:visible;">
                                    @if( $list['friends_status'] == 1 )
                                        <a class='btn btn-success'
                                           href="{{ yzWebUrl('jiushisms.jiushisms.jiushifriendsstatus', array('id' => $list['id'], 'friends_status' => 0)) }}"
                                           style="margin-bottom: 2px">加友成功</a>
                                    @else
                                        <a class='btn btn-danger'
                                           href="{{ yzWebUrl('jiushisms.jiushisms.jiushifriendsstatus',  array('id' => $list['id'], 'friends_status' => 1)) }}"
                                           style="margin-bottom: 2px">加友失败</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $pager !!}

                </div>
            </div>
        </div>

@endsection