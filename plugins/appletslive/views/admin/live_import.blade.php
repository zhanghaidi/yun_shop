@extends('layouts.base')
@section('title', trans('直播间管理'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">直播间管理</a></li>
        </ul>
    </div>

    <div class="panel panel-info">
        <div class="panel-body">
            <form action="" method="get" class="form-horizontal" role="form" id="form1">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="{{ $request['do'] }}"/>
                <input type="hidden" name="route" value="plugin.appletslive.admin.controllers.live.index"/>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <input type="number" placeholder="直播间ID" class="form-control" name="search[roomid]"
                               value="{{$request['search']['roomid']}}"/>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <input type="text" class="form-control" name="search[name]"
                               value="{{$request['search']['name']}}" placeholder="房间名称"/>
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
                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.live.index', ['tag' => 'refresh'])}}">同步直播间列表</a>
                <a id="btn-room-refresh" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.live.index', ['tag' => 'clean'])}}">清除已失效直播间</a>
            </div>

            <table class="table table-hover" style="overflow:visible;">
                <thead>
                <tr>
                    <th style='width:5%;'>ID</th>
                    <th style='width:5%;'>房间号</th>
                    <th style='width:5%;'>封面</th>
                    <th style='width:15%;'>名称</th>
                    <th style='width:15%;'>开始时间</th>
                    <th style='width:15%;'>结束时间</th>
                    <th style='width:10%;'>直播状态</th>
                    <th style='width:20%;'>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $row)
                    <tr>
                        <td>{{ $row['id'] }}</td>
                        <td>{{ $row['roomid'] }}</td>
                        <td>
                            <img src="{!! tomedia($row['cover_img']) !!}" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        </td>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ date('Y-m-d H:i:s', $row['start_time']) }}</td>
                        <td>{{ date('Y-m-d H:i:s', $row['end_time']) }}</td>
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
                            @elseif ($row['live_status'] == 108)
                                已删除
                            @else
                                未知
                            @endif
                        </td>
                        <td>
                            <a class='btn btn-default'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.live.edit', ['id' => $row['id']])}}"
                               title='编辑'><i class='fa fa-edit'></i>编辑
                            </a>
                            <a class='btn btn-default'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.live.importgoods', ['id' => $row['id']])}}"
                               title='导入商品'><i class='fa fa-list'></i>导入商品
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

    <script type="text/javascript">
        $(function() {
        });
    </script>
@endsection