@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.article.index')}}">文章列表</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.article-category.index')}}">文章分类</a></li>
                </ul>
            </div>

        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <div class="alert alert-info">主评内容：{{$info['content']}}</div>

            <table class="table">
                <thead>
                    <tr>
                        <th>回复ID</th>
                        <th>用户头像/昵称</th>
                        <th width="800">回复内容</th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;回复时间</th>
                        <th class="text-right">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>
                            <a href="{{ yzWebUrl('member.member.detail', ['id' => $value['user_id']]) }}" target="_blank">
                                <img src="{{tomedia($value['avatarurl'])}}" width="30" border="1"> <br/>
                                {{$value['nickname']}}
                            </a>
                        </td>
                        <td>{{$value['content']}}</td>
                        <td>{{$value['create_time']}}</td>
                        <td class="text-right">
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.article-replys.delete', ['id' => $value['id']]) }}" onclick="return confirm('确认删除该记录吗？');return false;"  class="btn btn-default" title="删除"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

