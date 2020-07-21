@extends('layouts.base')
@section('title', trans('回看列表'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">回看列表</a></li>
        </ul>
    </div>

    <div class='panel panel-default'>
        <div class='panel-body'>

            <table class="table table-hover" style="overflow:visible;">
                <thead>
                <tr>
                    <th style='width:8%;'>ID</th>
                    <th style='width:20%;'>预览图</th>
                    <th style='width:22%;'>概述</th>
                    <th style='width:10%;'>创建时间</th>
                    <th style='width:10%;'>过期时间</th>
                    <th style='width:20%;'>链接地址</th>
                    <th style='width:10%;'>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($room_list as $row)
                    <tr>
                        <td>0</td>
                        <td>
                            <img src="" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        </td>
                        <td>这里是概述</td>
                        <td>{{date('Y-m-d H:i:s',$row['start_time'])}}</td>
                        <td>{{date('Y-m-d H:i:s',$row['expire_time'])}}</td>
                        <td>$row['media_url']</td>
                        <td style="overflow:visible;">
                            <a class='btn btn-default'
                               href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replayedit', ['replayid' => $row['id']])}}"
                               title='房间设置'><i class='fa fa-edit'></i>设置
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