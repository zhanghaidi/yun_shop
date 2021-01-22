@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <!-- 新增加右侧顶部三级菜单 -->
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">快捷评语</a></li>
        </ul>
    </div>
    <!-- 新增加右侧顶部三级菜单结束 -->
    <div class="panel panel-default">
        <div class="panel-body">

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="{{$info['id']}}">

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">快评内容*</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="text" name="content" class="form-control" value="{{$info['content']}}"
                               placeholder="请输入快评内容">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">快评类型</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="radio" id="type-0" name="type" value="1" @if($info['type']==1) checked @endif >
                        <label class="radio-inline" for="type-0">穴位快评</label>
                        <input type="radio" id="type-1" name="type" value="3" @if($info['type']==3) checked @endif >
                        <label class="radio-inline" for="type-1">文章快评</label>
                        <input type="radio" id="type-2" name="type" value="4" @if($info['type']==4) checked @endif >
                        <label class="radio-inline" for="type-2">帖子快评</label>
                        <input type="radio" id="type-3" name="type" value="5" @if($info['type']==5) checked @endif >
                        <label class="radio-inline" for="type-3">课程快评</label>
                        <input type="radio" id="type-4" name="type" value="6" @if($info['type']==6) checked @endif >
                        <label class="radio-inline" for="type-4">灸师快评</label>
                        <input type="radio" id="type-5" name="type" value="7" @if($info['type']==7) checked @endif >
                        <label class="radio-inline" for="type-5">直播快捷消息</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启：</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="radio" id="is_display-1" name="status" value="1" @if($info['status']==1) checked @endif >
                        <label class="radio-inline" for="is_display-1">开启</label>
                        <input type="radio" name="status" id="is_display-0" value="0" @if($info['status']==0) checked @endif >
                        <label class="radio-inline" for="is_display-0"> 关闭</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-2">
                        <input type="submit" name="submit" value="提交" class="btn btn-success"
                               onclick="return formcheck()"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
