@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.post.index')}}">话题管理</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-board.index')}}">话题版块</a></li>
                </ul>
            </div>

            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <select name="search[status]" class="form-control">
                            <option value="">状态</option>
                            <option value="1"@if($search['status'] === 1) selected="selected" @endif>显示</option>
                            <option value="0"@if($search['status'] === 0) selected="selected" @endif>隐藏</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <input type="text" placeholder="请输入标题进行搜索" value="{{$search['keywords']}}" name="search[keywords]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.minapp-content.admin.sns-board.edit') }}" class="btn btn-info">添加版块</a>
            </div>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>排序</th>
                        <th>版块名称</th>
                        <th>版块logo</th>
                        <th>发帖审核</th>
                        <th>回帖审核</th>
                        <th>用户发帖</th>
                        <th>版块管理员</th>
                        <th>话题数</th>
                        <th>状态</th>
                        <th>添加时间</th>
                        <th class="text-right">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['list_order']}}</td>
                        <td>{{$value['name']}}</td>
                        <td>
                        @if(isset($value['thumb'][0]))
                            <a href="{{ tomedia($value['thumb']) }}" target="_blank">
                                <img src="{{ tomedia($value['thumb']) }}" width="50" />
                            </a>
                        @endif
                        </td>
                        <td>
                            @if($value['need_check'] == 1) 需要
                            @else 不需要
                            @endif
                        </td>
                        <td>
                            @if($value['need_check_replys'] == 1) 需要
                            @else 不需要
                            @endif
                        </td>
                        <td>
                            @if($value['is_user_publish'] == 1) <span style="color: green">允许</span>
                            @else <span style="color: red">不允许</span>
                            @endif
                        </td>
                        <td>{{$value['nickname']}}</td>
                        <td>{{$value['posts_nums']}}</td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.sns-board.status', ['id' => $value['id'], 'check' => 1]) }}">
                            @if($value['status'] == 1)
                                <span class="label label-primary">显示</span>
                            @else
                                <span class="label label-default">隐藏</span>
                            @endif
                            </a>
                        </td>
                        <td>{{$value['create_time']}}</td>
                        <td class="text-right">
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.sns-board.edit', ['id' => $value['id']]) }}" title="编辑"><i class="fa fa-edit"></i></a> &nbsp; 
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.sns-board.delete', ['id' => $value['id']]) }}" onclick="return confirm('确定删除吗');return false;"  title="删除"><i class="fa fa-trash-o"></i></a>
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

