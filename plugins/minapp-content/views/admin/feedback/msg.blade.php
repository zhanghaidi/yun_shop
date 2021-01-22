@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-defualt">
        <div class="panel-heading">用户反馈详情列表</div>
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>用户id</th>
                        <th>头像</th>
                        <th>昵称</th>
                        <th>账号</th>
                        <th>反馈内容</th>
                        <th>反馈图片</th>
                        <th>手机号</th>
                        <th>反馈时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['user_id']}}</td>
                        <td>
                            <a href="{{ tomedia($value['avatarurl']) }}" target="_blank">
                                <img src="{{$value['avatarurl']}}" width="50" height="50" class="img-circle">
                            </a>
                        </td>
                        <td>{{$value['nickname']}}</td>
                        <td>{{$value['account']}}</td>
                        <td>{{$value['content']}}</td>
                        <td>
                        @foreach($value['images'] as $img)
                            <a href="{{tomedia($img)}}" target="_blank"><img src="{{tomedia($img)}}" width="75" height="75"></a>
                        @endforeach
                        </td>
                        <td>{{$value['telephone']}}</td>
                        <td>{{$value['add_time']}}</td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.feedback.delete', ['id' => $value['id']]) }}"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

