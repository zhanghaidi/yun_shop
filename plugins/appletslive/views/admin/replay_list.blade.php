@extends('layouts.base')
@section('title', trans('回看列表'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            @if($room_type=='1')
                <li class="active"><a href="#">视频列表</a></li>
            @endif
            @if($room_type=='2')
                <li class="active"><a href="#">直播列表</a></li>
            @endif
        </ul>
    </div>

    @if($room_type=='1')
        <div class="panel panel-info">
            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form2">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="{{ $request['do'] }}"/>
                    <input type="hidden" name="route" value="plugin.appletslive.admin.controllers.room.replaylist"/>
                    <input type="hidden" name="rid" value="{{ $request['rid'] }}"/>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <input type="number" placeholder="视频ID" class="form-control" name="search[id]"
                                   value="{{$request['search']['id']}}"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <input type="text" class="form-control" name="search[title]"
                                   value="{{$request['search']['title']}}" placeholder="视频标题"/>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <select name="search[type]" class="form-control">
                                <option value="">请选择视频类型</option>
                                <option value="1" @if($request['search']['type']=='1') selected @endif>本地上传</option>
                                <option value='2' @if($request['search']['type']=='2') selected @endif>腾讯视频</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <select name="search[status]" class="form-control">
                                <option value="">请选择显示/隐藏</option>
                                <option value="1" @if($request['search']['status']=='1') selected @endif>显示</option>
                                <option value='0' @if($request['search']['status']=='0') selected @endif>隐藏</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class='panel panel-default'>
            <div class="clearfix panel-heading">
                <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:history.go(-1);">返回</a>
                <a id="btn-add-replay" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replayadd', ['rid' => $rid])}}">添加录播视频</a>
            </div>
            <div class='panel-body'>
                <table class="table table-hover" style="overflow:visible;">
                    <thead>
                    <tr>
                        <th style='width:5%;'>ID</th>
                        <th style='width:5%;'>排序</th>
                        <th style='width:8%;'>预览图</th>
                        <th style='width:15%;'>标题</th>
                        <th style='width:11%;'>创建时间</th>
                        <th style='width:11%;'>发布时间</th>
                        <th style='width:5%;'>类型</th>
                        <th style='width:25%;'>链接地址</th>
                        <th style='width:15%;'>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($replay_list as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td>{{ $row['sort'] }}</td>
                            <td>
                                <img src="{!! tomedia($row['cover_img']) !!}" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                            </td>
                            <td>{{ $row['title'] }}</td>
                            <td>{{ date('Y-m-d H:i:s', $row['create_time']) }}</td>
                            <td>{{ date('Y-m-d H:i:s', $row['publish_time']) }}</td>
                            <td>
                                @if($row['type']=='1') 本地上传 @endif
                                @if($row['type']=='2') 腾讯视频 @endif
                            </td>
                            <td>{{ $row['media_url'] }}</td>
                            <td style="overflow:visible;">
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replayedit', ['id' => $row['id']])}}"
                                   title='视频设置'><i class='fa fa-edit'></i>设置
                                </a>
                                @if ($row['delete_time'] > 0)
                                    <a class='btn btn-default btn-success'
                                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replayshowhide', ['id' => $row['id']])}}"
                                       title='显示'>显示
                                    </a>
                                @else
                                    <a class='btn btn-default btn-danger'
                                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replayshowhide', ['id' => $row['id']])}}"
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

    @if($room_type=='2')
        <div class="panel panel-info">
            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form2">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="{{ $request['do'] }}"/>
                    <input type="hidden" name="route" value="plugin.appletslive.admin.controllers.room.replaylist"/>
                    <input type="hidden" name="rid" value="{{ $request['rid'] }}"/>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <input type="number" placeholder="房间号" class="form-control" name="search[roomid]"
                                   value="{{$request['search']['roomid']}}"/>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <input type="text" class="form-control" name="search[name]"
                                   value="{{$request['search']['name']}}" placeholder="名称"/>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <select name="search[live_status]" class="form-control">
                                <option value="">直播状态</option>
                                <option value="101" @if($request['search']['live_status']=='101') selected @endif>直播中</option>
                                <option value='102' @if($request['search']['live_status']=='102') selected @endif>待开播</option>
                                <option value='103' @if($request['search']['live_status']=='103') selected @endif>已结束</option>
                                <option value='107' @if($request['search']['live_status']=='107') selected @endif>已过期</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <select name="search[status]" class="form-control">
                                <option value="">请选择显示/隐藏</option>
                                <option value="1" @if($request['search']['status']=='1') selected @endif>显示</option>
                                <option value='0' @if($request['search']['status']=='0') selected @endif>隐藏</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class='panel panel-default'>
            <div class="clearfix panel-heading">
                <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:history.go(-1);">返回</a>
                <a id="btn-add-replay" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replayadd', ['rid' => $rid])}}">添加直播</a>
            </div>
            <div class='panel-body'>
                <table class="table table-hover" style="overflow:visible;">
                    <thead>
                    <tr>
                        <th style='width:5%;'>ID</th>
                        <th style='width:5%;'>房间号</th>
                        <th style='width:10%;'>预览图</th>
                        <th style='width:15%;'>标题</th>
                        <th style='width:10%;'>主播</th>
                        <th style='width:5%;'>状态</th>
                        <th style='width:15%;'>开播时间</th>
                        <th style='width:15%;'>结束时间</th>
                        <th style='width:15%;'>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($replay_list as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td>{{ $row['roomid'] }}</td>
                            <td>
                                <img src="{!! tomedia($row['cover_img']) !!}" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                            </td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['anchor_name'] }}</td>
                            <td>
                                @if($row['live_status']==101)
                                    直播中
                                @elseif($row['live_status']==102)
                                    待开播
                                @elseif($row['live_status']==103)
                                    已结束
                                @elseif($row['live_status']==104)
                                    禁播
                                @elseif($row['live_status']==105)
                                    暂停
                                @elseif($row['live_status']==106)
                                    异常
                                @elseif($row['live_status']==107)
                                    已过期
                                @elseif($row['live_status']==108)
                                    已删除
                                @else
                                    未知
                                @endif
                            </td>
                            <td>{{ date('Y-m-d H:i:s', $row['start_time']) }}</td>
                            <td>{{ date('Y-m-d H:i:s', $row['end_time']) }}</td>
                            <td style="overflow:visible;">
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replayedit', ['id' => $row['id']])}}"
                                   title='设置'><i class='fa fa-edit'></i>设置
                                </a>
                                @if ($row['delete_time'] > 0)
                                    <a class='btn btn-default btn-success'
                                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replayshowhide', ['id' => $row['id']])}}"
                                       title='显示'>显示
                                    </a>
                                @else
                                    <a class='btn btn-default btn-danger'
                                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replayshowhide', ['id' => $row['id']])}}"
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
            $('#search').click(function () {
                $('#form1').attr('action', '{!! yzWebUrl('plugin.commission.admin.agent.index') !!}');
                $('#form1').submit();
            });
        });
    </script>
@endsection