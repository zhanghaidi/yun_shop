@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">投诉来源(项目)管理</div>
        <div class="panel-body">
            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">名称:</div>
                        <input type="text" placeholder="请输入来源名称进行模糊搜索" value="{{$search['name']}}" name="search[name]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.wechat-complaint.admin.project.edit') }}" class="btn btn-info">添加投诉来源</a>
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
                        <th width="5%">投诉单数</th>
                        <th width="5%">投诉人数</th>
                        <th width="10%">创建时间</th>
                        <th width="10%">最新投诉时间</th>
                        <th width="10%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['name']}}</td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.wechat-complaint.admin.log.index', ['id' => $value['id']]) }}">
                                <i class="fa fa-comment"></i> {{$value['total_num']}}
                            </a>
                        </td>
                        <td>{{$value['total_people']}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>{{$value['last_at']}}</td>
                        <td>
                            <a class='btn btn-success' href="{{ yzWebUrl('plugin.wechat-complaint.admin.project.edit', ['id' => $value['id']]) }}" title="编辑"><i class="fa fa-edit"></i></a>

                            <a class='btn btn-danger' href="{{ yzWebUrl('plugin.wechat-complaint.admin.project.delete', ['id' => $value['id']]) }}" onclick="return confirm('确认删除该记录吗？');return false;" title="删除"><i class="fa fa-remove"></i></a>
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
