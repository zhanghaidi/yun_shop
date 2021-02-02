@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.banner.index')}}">轮播图列表</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.banner-position.index')}}">轮播位</a>
                    </li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.system-category.index')}}">首页功能区分类</a>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.system-image.index')}}">系统图片</a>
                </ul>
            </div>

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="{{ $info['id'] }}">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">轮播位置名*</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="text" name="name" class="form-control" value="{{ $info['name'] }}"
                               placeholder="请输入轮播位置名">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">轮播位置标识*</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="text" name="label" class="form-control" value="{{ $info['label'] }}"
                               placeholder="请输入轮播位置唯一标识">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-2">
                        <input type="submit" name="submit" value="提交" class="btn btn-success"
                               onclick="return formcheck()"/>
                        <button class="btn btn-error" type="reset" onclick="history.back(-1)">返回上一页</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
