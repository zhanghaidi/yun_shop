@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.banner.index')}}">轮播图列表</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.banner-position.index')}}">轮播位</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.system-category.index')}}">首页功能区分类</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.system-image.index')}}">系统图片</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.hot-spot.index')}}">首页热区</a>
                </ul>
            </div>

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$info['id']}}">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[list_order]" class="form-control" value="{{$info['list_order']}}" required>
                    <span class="help-block">显示顺序，越大则越靠前</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">标题 <span style="color: red">*</span></label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[title]" class="form-control" value="{{$info['title']}}" required>
                    <span class="help-block">请填写热区标题</span>
                </div>
            </div>




            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
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
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示类型</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio-inline">
                            <input type="radio" name="data[type]" value="1"
                                   @if($info['type'] == 1) checked="checked" @endif /> 横版</label>
                        <label class="radio-inline">
                            <input type="radio" name="data[type]" value="2"
                                   @if($info['type'] == 2) checked="checked" @endif /> 竖版</label>
                        <span class="help-block">首页显示类型</span>
                    </div>
                    {{--<div class="col-xs-12 col-sm-9 col-md-10">
                        <label class="radio-inline">
                            <input type="radio" name="data[type]"  id="type-0" value="1" @if($info['type'] === 1) checked="checked" @endif /> 横版
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="data[type]" id="type-1" value="2" @if($info['type'] === 2) checked="checked" @endif /> 竖版
                        </label>
                        <span class="help-block">首页显示类型</span>
                    </div>--}}
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
