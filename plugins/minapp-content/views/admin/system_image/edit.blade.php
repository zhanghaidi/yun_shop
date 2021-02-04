@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.banner.index')}}">轮播图列表</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.banner-position.index')}}">轮播位</a>
                    </li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.system-category.index')}}">首页功能区分类</a>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.system-image.index')}}">系统图片</a>
                </ul>
            </div>

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$info['id']}}">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">名称</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[name]" class="form-control" value="{{$info['name']}}">
                    <span class="help-block">请填写名称</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">位置</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[cid]" class="form-control">
                    <option value="">选择图片位置</option>
                    @foreach($position as $item)
                    <option value="{{$item['id']}}"@if($item['id'] == $info['cid']) selected @endif>{{$item['name']}}</option>
                    @endforeach
                    </select>
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
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">图片</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[image]', $info['image'])!!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">描述</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[description]" class="form-control" value="{{$info['description']}}">
                    <span class="help-block">请填写描述</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示状态</label>
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
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">小程序跳转页面</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[jumpurl]" class="form-control" value="{{$info['jumpurl']}}">
                    <span class="help-block">小程序跳转页面(pages/)开头</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">跳转小程序</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[appid]" class="form-control">
                    @foreach($app as $item)
                    <option value="{{$item['key']}}"@if($item['key'] == $info['appid']) selected @endif>{{$item['name']}}</option>
                    @endforeach
                    </select>
                    <span class="help-block">请选择需要跳转的小程序，此小程序必须有此跳转路径</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">跳转类型</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[jumptype]" value="1" @if($info['jumptype'] === 1) checked="checked" @endif /> 普通页跳转
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[jumptype]" value="2" @if($info['jumptype'] === 2) checked="checked" @endif /> 导航跳转
                    </label>
                    <span class="help-block">跳转类型</span>
                </div>
            </div>

            <!-- <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">所属平台</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[aid]" value="3" @if($info['aid'] === 3) checked="checked" @endif /> 仙草公众号
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[aid]" value="45" @if($info['aid'] === 45) checked="checked" @endif /> 艾居益小程序
                    </label>
                    <span class="help-block">所属平台</span>
                </div>
            </div> -->

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
