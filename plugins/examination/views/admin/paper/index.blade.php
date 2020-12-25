@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">试卷管理</div>
        <div class="panel-body">
            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">名称:</div>
                        <input type="text" placeholder="请输入试卷名称进行模糊搜索" value="{{$search['name']}}" name="search[name]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.examination.admin.paper.edit') }}" class="btn btn-info">添加试卷</a>
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
                        <th width="5%">题目数量</th>
                        <th width="5%">试卷总分</th>
                        <th width="5%">被引用次数</th>
                        <th width="10%">最后更新时间</th>
                        <th width="10%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['name']}}</td>
                        <td>{{$value['question']}}</td>
                        <td>{{$value['score']}}</td>
                        <td>{{$value['use_number']}}</td>
                        <td>{{$value['updated_at']}}</td>
                        <td>
                            <a class='btn btn-success' href="{{ yzWebUrl('plugin.examination.admin.paper.edit', ['id' => $value['id']]) }}"><i class="fa fa-edit"></i></a>

                            <a class='btn btn-danger' href="{{ yzWebUrl('plugin.examination.admin.paper.del', ['id' => $value['id']]) }}" onclick="return confirm('确认删除该记录吗？');return false;"><i class="fa fa-remove"></i></a>
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

