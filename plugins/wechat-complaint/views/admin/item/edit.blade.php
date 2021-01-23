@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">投诉选项(内容)编辑</div>
        <div class="panel-body">
        
            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$info['id']}}">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">名称</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                    <input type="text" name="data[name]" class="form-control" value="{{$info['name']}}" placeholder="请输入分类名称">
                </div>
            </div>

            @if($info['id'] > 0)
            @else
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">父类</label>
                <div class="col-sm-9 col-xs-12">
                    <select name="data[pid]" class="form-control">
                    <option value="0">顶级分类</option>
                    @foreach($item as $v1)
                        <option value="{{$v1['id']}}">{{$v1['name']}}</option>
                        @if($v1['children'])
                        @foreach($v1['children'] as $v2)
                            <option value="{{$v2['id']}}">{{$v2['name']}}</option>
                        @endforeach
                        @endif
                    @endforeach
                    </select>
                </div>
            </div>
            @endif

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                    <input type="number" name="data[order]" class="form-control" value="{{$info['order']}}" placeholder="请输入分类排序的正整数值">
                    <span class="help-block">排序值数字越大，在选项列表中排序越靠上;限制不能超过65535</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-2">
                        <input type="submit" name="submit" value="提交" class="btn btn-success" onclick="return formcheck()"/>
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

