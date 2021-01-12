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
            <div class="alert alert-info">文章ID：{{$info['id']}} &nbsp;&nbsp;&nbsp;&nbsp; 标题： {{$info['title']}}</div>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>用户头像/昵称</th>
                        <th width="800">主评内容</th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;审核状态</th>
                        <th>评论时间</th>
                        <th>回复</th>
                        <th class="text-right">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>
                            <a href="{{ yzWebUrl('member.member.detail', ['id' => $value['user_id']]) }}" target="_blank">
                                <img src="{{tomedia($value['avatarurl'])}}" width="40" border="1"> <br/>
                                {{$value['nickname']}}
                            </a>
                        </td>
                        <td>{{$value['content']}}</td>
                        <td>
                        @if($value['status'] == 1)
                            <span style="color:green;">正常</span>
                        @elseif($value['status'] == 0)
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.article-replys.status', ['id' => $value['id'], 'check' => '1']) }}"><span class="btn btn-success">通过审核</span></a>&nbsp;&nbsp;
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.article-replys.status', ['id' => $value['id'], 'check' => '-1']) }}"><span class="btn btn-danger">拒绝审核</span></a>
                        @else
                            <span>已拒绝</span>
                        @endif
                        </td>
                        <td>{{$value['create_time']}}</td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.article-replys.post', ['id' => $value['id']]) }}"><i class="fa fa-comment-o"></i> {{$value['counts']}}</a>
                        </td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.article-replys.status', ['id' => $value['id'], 'flag' => 1]) }}">
                            @if($value['display_order'] == 1) 
                                <span class="btn btn-success btn-circle icon-recommend"><i class="fa fa-thumbs-up"></i></span>
                            @else
                                <span class="btn btn-danger btn-circle icon-recommend"><i class="fa fa-thumbs-down"></i></span>
                            @endif
                            </a> &nbsp; 
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.article-replys.delete', ['id' => $value['id']]) }}" onclick="return confirm('确认删除该记录吗？');return false;"  class="btn btn-default" title="删除"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {!! $pager !!}
    </div>
</div>

@endsection

