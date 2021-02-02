@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-defualt">
        <div class="top" style="margin-bottom:20px">
            <ul class="add-shopnav" id="myTab">
                <li ><a href="{{yzWebUrl('plugin.minapp-content.admin.feedback.index')}}">反馈列表</a></li>
                <li><a href="{{yzWebUrl('plugin.minapp-content.admin.feedback.complain')}}">投诉列表</a>
                <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.feedback.complain-type')}}">投诉类型</a>
                </li>
            </ul>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.minapp-content.admin.feedback.complain-type-add') }}" class="btn btn-info">添加投诉类型</a>
            </div>
        </div>
        <div class="panel-heading">投诉类型列表</div>
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>投诉类型名称</th>
                        <th>排序</th>
                        <th>添加时间</th>
                        <th>显示状态</th>
                        <th>操作</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($pageList as $value)
                    <tr>
                        <td>{{$value->id}}</td>
                        <td>{{$value->name}}</td>
                        <td>{{$value->list_order}}</td>
                        <td>{{$value->create_time}}</td>
                        <td>
                            @if($value->status == 1)
                                <span class="label label-success">显示</span>
                                @else
                                <span class="label label-default">隐藏</span>
                                @endif
                        </td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.feedback.complain-type-edit', ['id' => $value->id]) }}" title="编辑"><i class="fa fa-edit"></i></a> &nbsp;
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.feedback.complain-type-delete', ['id' => $value->id]) }}" onclick="return confirm('确定删除吗');return false;"  title="删除"><i class="fa fa-trash-o"></i></a>
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

