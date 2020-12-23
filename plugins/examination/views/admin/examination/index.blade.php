@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">考试管理</div>
        <div class="panel-body">
            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">状态:</div>
                        <select class="form-control" name="search[status]">
                            <option value="">全部</option>
                            <option value="1" @if($search['status'] == 1) selected="selected" @endif>开启</option>
                            <option value="2" @if($search['status'] == 2) selected="selected" @endif>关闭</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">名称:</div>
                        <input type="text" placeholder="请输入名称进行模糊搜索" value="{{$search['name']}}" name="search[name]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.examination.admin.examination.edit') }}" class="btn btn-info">添加考试</a>
            </div>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="10%">名称</th>
                        <th width="15%">开始结束时间</th>
                        <th width="5%">考试时长</th>
                        <th width="5%">参与次数</th>
                        <th width="5%">重考间隔</th>
                        <th width="10%">状态</th>
                        <th width="10%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['name']}}</td>
                        <td>
                            @if($value['start_at']) {{$value['start_at']}}
                            @else 不限
                            @endif
                            - 
                            @if($value['start_at']) {{$value['start_at']}}
                            @else 不限
                            @endif
                        </td>
                        <td>
                            @if($value['duration']) {{$value['duration']}} 分钟
                            @else 不限
                            @endif
                        </td>
                        <td>
                            @if($value['frequency']) {{$value['frequency']}} 次
                            @else 不限
                            @endif
                        </td>
                        <td>
                            @if($value['interval']) {{$value['interval']}} 分钟
                            @else 不限
                            @endif
                        </td>
                        <td>@if($value['open_status'] == 1) 有效 @else 无效 @endif</td>
                        <td>
                            <a class='btn btn-default' href="{{ yzWebUrl('plugin.examination.admin.examination.edit', ['id' => $value['id']]) }}"><i class="fa fa-edit"></i></a>

                            <a class='btn btn-default' href="{{ yzWebUrl('plugin.examination.admin.examination.del', ['id' => $value['id']]) }}" onclick="return confirm('确认删除该记录吗？');return false;"><i class="fa fa-remove"></i></a>
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

