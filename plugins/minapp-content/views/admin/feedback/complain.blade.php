@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-defualt">
        <div class="top" style="margin-bottom:20px">
            <ul class="add-shopnav" id="myTab">
                <li ><a href="{{yzWebUrl('plugin.minapp-content.admin.feedback.index')}}">反馈列表</a></li>
                <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.feedback.complain')}}">投诉列表</a>
                <li ><a href="{{yzWebUrl('plugin.minapp-content.admin.feedback.complain-type')}}">投诉类型</a>
                </li>
            </ul>
        </div>
        <div class="panel-heading">用户投诉列表</div>
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>投诉者</th>
                        <th>投诉对象</th>
                        <th>投诉类型</th>
                        <th>图文内容</th>
                        <th>投诉时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pageList as $value)
                    <tr>
                        <td>{{$value->id}}</td>
                        <td>
                            <a href="{{ $value->user->avatarurl }}" target="_blank">
                                <img src="{{$value->user->avatarurl}}" width="50" height="50" class="img-circle">
                            </a>
                            {{$value->user->nickname}}
                        </td>

                        <td>
                            {{$value->info->name}}
                        </td>

                        <td>
                            {{$value->complain_type->name}}
                        </td>
                        <td>
                            <div>
                                @foreach($value->images as $img)
                                    <a href="{{tomedia($img)}}" target="_blank"><img src="{{tomedia($img)}}" width="50" height="50"></a>
                                @endforeach
                            </div>
                            <div>
                                {{$value->content}}
                            </div>
                        </td>

                        <td>
                            {{$value->create_time}}
                        </td>
                        <td><a href="{{ yzWebUrl('plugin.minapp-content.admin.feedback.complain-delete', ['id' => $value->id]) }}" onclick="return confirm('确定删除吗');return false;"  title="删除"><i class="fa fa-trash-o"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {!! $pager !!}
    </div>
</div>

@endsection

