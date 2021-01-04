@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">元素管理</div>
        <div class="panel-body">
            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">名称:</div>
                        <input type="text" placeholder="请输入元素名称进行模糊搜索" value="{{$search['name']}}" name="search[name]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.custom-app.admin.element-sort.add') }}" class="btn btn-info">添加页面元素</a>
            </div>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="20%">元素名称</th>
                        <th width="20%">唯一标识</th>
                        <th width="20%">元素值</th>
                        <th width="10%">创建时间</th>
                        <th width="10%">最后修改时间</th>
                        <th width="10%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['name']}}</td>
                        <td>{{$value['label']}}</td>
                        <td>@if($value['type'] == 1) 文本
                        @elseif($value['type'] == 2) 图片URL
                        @elseif($value['type'] == 3) 文本数组
                        @elseif($value['type'] == 4) 图片URL数组
                        @else
                        @endif
                        </td>
                        <td>{{$value['created_at']}}</td>
                        <td>{{$value['updated_at']}}</td>
                        <td>
                            <a class='btn btn-success' href="{{ yzWebUrl('plugin.custom-app.admin.element.edit', ['id' => $value['id']]) }}" title="编辑"><i class="fa fa-edit"></i></a>
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
