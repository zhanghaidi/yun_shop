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
                        <td>{{$row['id']}}</td>
                        <td>{{$row['roomid']}}</td>
                        <td>
                            <img src="{!! tomedia($row['cover_img']) !!}" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        </td>
                        <td>{{$row['name']}}</td>
                        <td>{{$row['start_time']}}</td>
                        <td>{{$row['end_time']}}</td>
                        <td>{{$row['subscription_num']}}</td>
                        <td>{{$row['live_status_text']}}</td>
                        <td style="overflow:visible;">
                            <a class='btn btn-default'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.edit', ['rid' => $row['id']])}}"
                               title='课程设置'><i class='fa fa-edit'></i>课程设置
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($type=='1')
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
                        <td>{{$row['id']}}</td>
                        <td>
                            <img src="{!! tomedia($row['cover_img']) !!}" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        </td>
                        <td>{{$row['name']}}</td>
                        <td>{{$row['subscription_num']}}</td>
                        <td style="overflow:visible;">
                            <a class='btn btn-default'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.edit', ['rid' => $row['id']])}}"
                               title='课程设置'><i class='fa fa-edit'></i>课程设置
                            </a>
                            <a class='btn btn-default'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $row['id']])}}"
                               title='录播列表'><i class='fa fa-list'></i>录播列表
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div style="width:100%;height:150px;"></div>

    <script type="text/javascript">
        $(function() {
        });
    </script>
@endsection