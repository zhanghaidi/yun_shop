@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-defualt">
        <div class="panel-heading">用户反馈列表</div>
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>反馈用户</th>
                        <th>用户ID</th>
                        <th>用户账号</th>
                        <th>反馈内容</th>
                        <th>反馈图片</th>
                        <th>手机号</th>
                        <th>反馈条数</th>
                        <th>反馈时间</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>
                            <a href="{{ tomedia($value['avatarurl']) }}" target="_blank">
                                <img src="{{$value['avatarurl']}}" width="50" height="50" class="img-circle">
                            </a>
                            {{$value['nickname']}}
                        </td>
                        <td>{{$value['user_id']}}</td>
                        <td>{{$value['account']}}</td>
                        <td>{{$value['content']}}</td>
                        <td>
                        @foreach($value['images'] as $img)
                            <a href="{{tomedia($img)}}" target="_blank"><img src="{{tomedia($img)}}" width="75" height="75"></a>
                        @endforeach
                        </td>
                        <td>{{$value['telephone']}}</td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.feedback.msg', ['id' => $value['user_id']]) }}"><i class="fa fa-comment-o"></i> {{$value['counts']}}</a>
                        </td>
                        <td>{{$value['add_time']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {!! $pager !!}
    </div>
</div>

@endsection

