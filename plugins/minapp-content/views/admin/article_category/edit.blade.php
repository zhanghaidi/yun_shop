@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.article.index')}}">文章列表</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.article-category.index')}}">文章分类</a></li>
                </ul>
            </div>

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$info['id']}}">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">栏目名称</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[name]" class="form-control" value="{{$info['name']}}" placeholder="请填写文章栏目名称">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[list_order]" class="form-control" value="{{$info['list_order']}}" placeholder="文章的显示顺序，越大则越靠前">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">栏目图标</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[image]', $info['image'])!!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示状态</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[status]" value="1" @if($info['status'] == 1) checked="checked" @endif /> 显示
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[status]" value="0" @if($info['status'] == 0) checked="checked" @endif /> 隐藏
                    </label>
                    <span class="help-block">是否显示</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">栏目类型</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[type]" value="1" @if($info['type'] == 1) checked="checked" @endif /> 辟谣类
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[type]" value="2" @if($info['type'] == 2) checked="checked" @endif /> 视频类
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[type]" value="3" @if($info['type'] == 3) checked="checked" @endif /> 文章类
                    </label>
                    <span class="help-block">选择栏目类型</span>
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
