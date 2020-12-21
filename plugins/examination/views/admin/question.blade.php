@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">题库管理</div>
        <div class="panel-body">
            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">分类:</div>
                        <select class="form-control" name="search[sort_id]">
                            <option value="">全部</option>
                            @foreach($sort as $v1)
                                <option value="{{$v1['id']}}" @if($v1['id'] == $search['sort_id']) selected="selected" @endif>{{$v1['name']}}</option>
                                @if($v1['children'])
                                @foreach($v1['children'] as $v2)
                                    <option value="{{$v2['id']}}" @if($v2['id'] == $search['sort_id']) selected="selected" @endif>{{$v2['name']}}</option>
                                    @if($v2['children'])
                                    @foreach($v2['children'] as $v3)
                                        <option value="{{$v3['id']}}" @if($v3['id'] == $search['sort_id']) selected="selected" @endif>{{$v3['name']}}</option>
                                    @endforeach
                                    @endif
                                @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">题型:</div>
                        <select class="form-control" name="search[type]">
                            <option value="">全部</option>
                            <option value="1" @if($search['type'] == 1) selected="selected" @endif>单选题</option>
                            <option value="2" @if($search['type'] == 2) selected="selected" @endif>多选题</option>
                            <option value="3" @if($search['type'] == 3) selected="selected" @endif>判断题</option>
                            <option value="4" @if($search['type'] == 4) selected="selected" @endif>填空题</option>
                            <option value="5" @if($search['type'] == 5) selected="selected" @endif>问答题</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">题目:</div>
                        <input type="text" placeholder="请输入题目进行模糊搜索" value="{{$search['problem']}}" name="search[problem]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.examination.admin.question.add') }}" class="btn btn-info">添加题目</a>
            </div>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th width="20%">题目</th>
                        <th width="10%">题型</th>
                        <th width="10%">分类</th>
                        <th width="10%">最后更新时间</th>
                        <th width="10%">状态</th>
                        <th width="10%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['problem']}}</td>
                        <td>
                            @if($value['type'] == 1) 单选 
                            @elseif($value['type'] == 2) 多选 
                            @elseif($value['type'] == 3) 判断
                            @elseif($value['type'] == 4) 填空
                            @elseif($value['type'] == 5) 问答
                            @else 未知
                            @endif
                        </td>
                        <td>{{$value['sort_name']}}</td>
                        <td>{{$value['updated_at']}}</td>
                        <td>@if($value['deleted_at']) 删除 @else 有效 @endif</td>
                        <td>
                            <a class='btn btn-default' href="{{ yzWebUrl('plugin.examination.admin.question.edit', ['id' => $value['id']]) }}"><i class="fa fa-edit"></i></a>

                            <a class='btn btn-default' href="{{ yzWebUrl('plugin.face-analysis.admin.face-analysis-log-manage.del', ['id' => $value['id']]) }}" onclick="return confirm('确认删除该记录吗？');return false;"><i class="fa fa-remove"></i></a>
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

