@extends('layouts.base')
@section('title', trans('课程管理'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">课程管理</a></li>
        </ul>
    </div>

    <div class="panel panel-info">
        <ul class="add-shopnav">
            <li @if($type=='0') class="active" @endif>
                <a href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.index')}}">直播</a>
            </li>
            <li @if($type=='1') class="active" @endif>
                <a href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.index', ['type' => 1])}}">录播</a>
            </li>
        </ul>
    </div>

    @if($type=='0')
        <div class="panel panel-info">
            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form1">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="{{ $request['do'] }}"/>
                    <input type="hidden" name="route" value="plugin.appletslive.admin.controllers.room.index"/>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <input type="number" placeholder="直播间ID" class="form-control" name="search[roomid]"
                                   value="{{$request['search']['roomid']}}"/>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <input type="text" class="form-control" name="search[name]"
                                   value="{{$request['search']['name']}}" placeholder="课程标题"/>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <select name="search[live_status]" class="form-control">
                                <option value="">请选择直播状态</option>
                                <option value="101" @if($request['search']['live_status']=='101') selected @endif>直播中</option>
                                <option value='102' @if($request['search']['live_status']=='102') selected @endif>未开始</option>
                                <option value='103' @if($request['search']['live_status']=='103') selected @endif>已结束</option>
                                <option value='104' @if($request['search']['live_status']=='104') selected @endif>禁播</option>
                                <option value='105' @if($request['search']['live_status']=='105') selected @endif>暂停</option>
                                <option value='106' @if($request['search']['live_status']=='106') selected @endif>异常</option>
                                <option value='107' @if($request['search']['live_status']=='107') selected @endif>已过期</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <select name="search[searchtime]" class="form-control">
                                <option value="" selected>请选择时间</option>
                                <option value="0" @if($request['search']['searchtime']=='0') selected @endif>直播开始时间</option>
                                <option value='1' @if($request['search']['searchtime']=='1') selected @endif>直播结束时间</option>
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

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <select name="search[status]" class="form-control">
                                <option value="">请选择显示/隐藏</option>
                                <option value="1" @if($request['search']['status']=='1') selected @endif>显示</option>
                                <option value='0' @if($request['search']['status']=='0') selected @endif>隐藏</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <button class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class='panel panel-default'>
            <div class='panel-body'>

                <div class="clearfix panel-heading">
                    <a id="btn-room-refresh" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.index', ['tag' => 'refresh'])}}">同步课程列表</a>
                </div>

                <table class="table table-hover" style="overflow:visible;">
                    <thead>
                    <tr>
                        <th style='width:5%;'>ID</th>
                        <th style='width:5%;'>课程号</th>
                        <th style='width:5%;'>封面</th>
                        <th style='width:15%;'>标题</th>
                        <th style='width:15%;'>开始时间</th>
                        <th style='width:15%;'>结束时间</th>
                        <th style='width:10%;'>订阅人数</th>
                        <th style='width:10%;'>直播状态</th>
                        <th style='width:20%;'>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($room_list as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td>{{ $row['roomid'] }}</td>
                            <td>
                                <img src="{!! tomedia($row['cover_img']) !!}" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                            </td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ date('Y-m-d H:i:s', $row['create_time']) }}</td>
                            <td>{{ date('Y-m-d H:i:s', $row['end_time']) }}</td>
                            <td>{{ $row['subscription_num'] }}</td>
                            <td>
                                @if ($row['live_status'] == 101)
                                    直播中
                                @elseif ($row['live_status'] == 102)
                                    未开始
                                @elseif ($row['live_status'] == 103)
                                    已结束
                                @elseif ($row['live_status'] == 104)
                                    禁播
                                @elseif ($row['live_status'] == 105)
                                    暂停
                                @elseif ($row['live_status'] == 106)
                                    异常
                                @elseif ($row['live_status'] == 107)
                                    已过期
                                @else
                                    未知
                                @endif
                            </td>
                            <td style="overflow:visible;">
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.edit', ['id' => $row['id']])}}"
                                   title='课程设置'><i class='fa fa-edit'></i>课程设置
                                </a>
                                @if ($row['delete_time'] > 0)
                                    <a class='btn btn-default btn-success'
                                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.showhide', ['id' => $row['id']])}}"
                                       title='显示'>显示
                                    </a>
                                @else
                                    <a class='btn btn-default btn-danger'
                                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.showhide', ['id' => $row['id']])}}"
                                       title='隐藏'>隐藏
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {!! $pager !!}
            </div>
        </div>
    @endif

    @if($type=='1')
        <div class="panel panel-info">
            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form2">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="{{ $request['do'] }}"/>
                    <input type="hidden" name="route" value="plugin.appletslive.admin.controllers.room.index"/>
                    <input type="hidden" name="type" value="1"/>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <input type="number" placeholder="课程ID" class="form-control" name="search[id]"
                                   value="{{$request['search']['id']}}"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <input type="text" class="form-control" name="search[name]"
                                   value="{{$request['search']['name']}}" placeholder="课程标题"/>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <select name="search[status]" class="form-control">
                                <option value="">请选择显示/隐藏</option>
                                <option value="1" @if($request['search']['status']=='1') selected @endif>显示</option>
                                <option value='0' @if($request['search']['status']=='0') selected @endif>隐藏</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class='panel panel-default'>
            <div class='panel-body'>
                <div class="clearfix panel-heading">
                    <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.add')}}">添加录播课程</a>
                </div>

                <table class="table table-hover" style="overflow:visible;">
                    <thead>
                    <tr>
                        <th style='width:15%;'>ID</th>
                        <th style='width:20%;'>封面</th>
                        <th style='width:30%;'>标题</th>
                        <th style='width:20%;'>订阅人数</th>
                        <th style='width:20%;'>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($room_list as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td>
                                <img src="{!! tomedia($row['cover_img']) !!}" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                            </td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['subscription_num'] }}</td>
                            <td style="overflow:visible;">
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.edit', ['id' => $row['id']])}}"
                                   title='课程设置'><i class='fa fa-edit'></i>课程设置
                                </a>
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $row['id']])}}"
                                   title='录播列表'><i class='fa fa-list'></i>录播列表
                                </a>
                                @if ($row['delete_time'] > 0)
                                    <a class='btn btn-default btn-success'
                                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.showhide', ['id' => $row['id']])}}"
                                       title='显示'>显示
                                    </a>
                                @else
                                    <a class='btn btn-default btn-danger'
                                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.showhide', ['id' => $row['id']])}}"
                                       title='隐藏'>隐藏
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {!! $pager !!}
            </div>
        </div>
    @endif

    <div style="width:100%;height:150px;"></div>

    <script type="text/javascript">
        $(function() {
        });
    </script>
@endsection