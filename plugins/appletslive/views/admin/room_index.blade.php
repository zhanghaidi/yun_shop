@extends('layouts.base')
@section('title', trans('房间管理'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">房间管理</a></li>
        </ul>
    </div>

    <div class="panel panel-info">
        <ul class="add-shopnav">
            <li class="active">
                <a href="">直播</a>
            </li>
            <li class="">
                <a href="">录播</a>
            </li>
        </ul>
    </div>

    <div class='panel panel-default'>
        <div class='panel-body'>

            <table class="table table-hover" style="overflow:visible;">
                <thead>
                <tr>
                    <th style='width:8%;'>ID</th>
                    <th style='width:12%;'>封面</th>
                    <th style='width:20%;'>标题</th>
                    <th style='width:15%;'>开始时间</th>
                    <th style='width:15%;'>结束时间</th>
                    <th style='width:8%;'>报名人数</th>
                    <th style='width:7%;'>直播状态</th>
                    <th style='width:15%;'>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($room_list as $row)
                    <tr>
                        <td>{{$row['roomid']}}</td>
                        <td>
                            <img src="{{$row['cover_img']}}" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        </td>
                        <td>{{$row['name']}}</td>
                        <td>{{$row['start_time']}}</td>
                        <td>{{$row['end_time']}}</td>
                        <td>99999999</td>
                        <td>{{$row['live_status']}}</td>
                        <td style="overflow:visible;">
                            <a class='btn btn-default'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.set', ['roomid' => $row['roomid']])}}"
                               title='房间设置'><i class='fa fa-edit'></i>房间设置
                            </a>
                            <a class='btn btn-default'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replaylist', ['roomid' => $row['roomid']])}}"
                               title='回看列表'><i class='fa fa-list'></i>回看列表
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
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