@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">
            小程序独立文章或协议管理
            <a href="{{ yzWebUrl('plugin.custom-app.admin.article-sort.add') }}" class="pull-right btn btn-sm btn-info">添加页面</a>
        </div>
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="20%">页面名称</th>
                        <th width="10%">页面标识</th>
                        <th width="20%">创建时间</th>
                        <th width="20%">最后修改时间</th>
                        <th width="10%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['name']}}</td>
                        <td>{{$value['label']}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>{{$value['updated_at']}}</td>
                        <td>
                            <a class='btn btn-success' href="{{ yzWebUrl('plugin.custom-app.admin.article.edit', ['id' => $value['id']]) }}" title="编辑"><i class="fa fa-edit"></i> 编辑内容</a>
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

