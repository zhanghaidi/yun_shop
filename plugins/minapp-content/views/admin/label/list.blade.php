@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.somato-type.index')}}">体质管理</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.label.index')}}">症状标签</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.question.post')}}">测评题库</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.answer.index')}}">用户测评</a></li>
                </ul>
            </div>

            <form id="form1" role="form" class="form-horizontal form" method="post" action="">

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <input type="text" placeholder="请输入标签进行搜索" value="{{$search['keywords']}}" name="search[keywords]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.minapp-content.admin.label.edit') }}" class="btn btn-info">添加标签</a>
            </div>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>标签名称</th>
                        <th>排序</th>
                        <th>是否显示</th>
                        <th>标签类型</th>
                        <th>添加时间</th>
                        <th class="text-right">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>
                            <a href="#">
                                <h4><span class="label @if($value['type'] == 1) label-default @elseif($value['type'] == 2) label-success @else label-info @endif">{{$value['name']}}</span></h4>
                            </a>
                        </td>
                        <td>{{$value['list_order']}}</td>
                        <td>
                            @if($value['status'] == 1) 是
                            @else 否
                            @endif
                        </td>
                        <td>
                            @if($value['type'] == 1) 文章标签
                            @elseif($value['type'] == 2) 症状标签
                            @elseif($value['type'] == 3) 话题标签
                            @endif
                        </td>
                        <td>{{$value['create_time']}}</td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.label.edit', ['id' => $value['id']]) }}" title="编辑"><i class="fa fa-edit"></i></a> &nbsp; 
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.label.delete', ['id' => $value['id']]) }}" onclick="return confirm('确定删除吗');return false;"  title="删除"><i class="fa fa-trash-o"></i></a>
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

