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

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$info['id']}}">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">标签名称*</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[name]" class="form-control" value="{{$info['name']}}">
                    <span class="help-block">请输入标签名称</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[list_order]" class="form-control" value="{{$info['list_order']}}">
                    <span class="help-block">显示顺序，越大则越靠前</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">状态</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[status]" value="1" @if($info['status'] === 1) checked="checked" @endif /> 显示
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[status]" value="0" @if($info['status'] === 0) checked="checked" @endif /> 隐藏
                    </label>
                    <span class="help-block">是否显示</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-2">
                    <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
