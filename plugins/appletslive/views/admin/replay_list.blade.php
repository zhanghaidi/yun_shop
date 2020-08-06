@extends('layouts.base')
@section('title', trans('回看列表'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            @if($type=='0')
            <li class="active"><a href="#">回看列表</a></li>
            @endif
            @if($type=='1')
            <li class="active"><a href="#">视频列表</a></li>
            @endif
        </ul>
    </div>

    @if($type=='0')
    <div class='panel panel-default'>
        <div class="clearfix panel-heading">
            <a id="btn-room-refresh" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
               href="javascript:history.go(-1);">返回</a>
            <a id="btn-room-refresh" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $rid, 'tag' => 'refresh'])}}">同步回看列表</a>
        </div>
        <div class='panel-body'>

            <table class="table table-hover" style="overflow:visible;">
                <thead>
                <tr>
                    <th style='width:5%;'>ID</th>
                    <th style='width:10%;'>预览图</th>
                    <th style='width:20%;'>标题</th>
                    <th style='width:15%;'>创建时间</th>
                    <th style='width:15%;'>过期时间</th>
                    <th style='width:20%;'>链接地址</th>
                    <th style='width:15%;'>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($replay_list as $row)
                    <tr>
                        <td>{{$row['id']}}</td>
                        <td>
                            <img src="{!! tomedia($row['cover_img']) !!}" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        </td>
                        <td>{{$row['title']}}</td>
                        <td>{{$row['create_time']}}</td>
                        <td>{{$row['expire_time']}}</td>
                        <td>{{$row['media_url']}}</td>
                        <td style="overflow:visible;">
                            <a class='btn btn-default'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replayedit', ['id' => $row['id']])}}"
                               title='视频设置'><i class='fa fa-edit'></i>设置
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
                    <th style='width:8%;'>预览图</th>
                    <th style='width:20%;'>标题</th>
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
                        <td>{{$row['id']}}</td>
                        <td>
                            <img src="{!! tomedia($row['cover_img']) !!}" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        </td>
                        <td>{{$row['title']}}</td>
                        <td>{{$row['create_time']}}</td>
                        <td>{{$row['publish_time']}}</td>
                        <td>
                            @if($row['type']=='1') 本地上传 @endif
                            @if($row['type']=='2') 腾讯视频 @endif
                        </td>
                        <td>{{$row['media_url']}}</td>
                        <td style="overflow:visible;">
                            <a class='btn btn-default'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replayedit', ['id' => $row['id']])}}"
                               title='视频设置'><i class='fa fa-edit'></i>设置
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
            $('#search').click(function () {
                $('#form1').attr('action', '{!! yzWebUrl('plugin.commission.admin.agent.index') !!}');
                $('#form1').submit();
            });
        });
    </script>
@endsection