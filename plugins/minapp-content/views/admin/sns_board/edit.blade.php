@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.post.index')}}">话题管理</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-board.index')}}">话题版块</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-filter.post')}}">敏感词库</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-upload-filter.index')}}">上传敏感图用户</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.cos-images.index')}}">敏感图片</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.cos-video.index')}}">敏感视频管理</a></li>
                </ul>
            </div>

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$info['id']}}">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">版块名称</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[name]" class="form-control" value="{{$info['name']}}">
                    <span class="help-block">请填写版块名称</span>
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
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">缩略图</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[thumb]', $info['thumb'])!!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">发帖需要审核</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[need_check]" value="1" @if($info['need_check'] === 1) checked="checked" @endif /> 需要
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[need_check]" value="0" @if($info['need_check'] === 0) checked="checked" @endif /> 不需要
                    </label>
                    <span class="help-block">发帖审核状态</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">回帖需要审核</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[need_check_replys]" value="1" @if($info['need_check_replys'] === 1) checked="checked" @endif /> 需要
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[need_check_replys]" value="0" @if($info['need_check_replys'] === 0) checked="checked" @endif /> 不需要
                    </label>
                    <span class="help-block">回帖审核状态</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否允许用户发帖</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[is_user_publish]" value="1" @if($info['is_user_publish'] === 1) checked="checked" @endif /> 允许
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[is_user_publish]" value="0" @if($info['is_user_publish'] === 0) checked="checked" @endif /> 不允许
                    </label>
                    <span class="help-block">是否允许用户发帖状态</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">选择版主</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[manager]" data-placeholder="请选择用户" class="form-control select2">
                    <option value="">==请选择版主==</option>
                    @foreach($user as $item)
                    <option value="{{$item['ajy_uid']}}"@if($item['ajy_uid'] == $info['manager']) selected @endif>{{$item['nickname']}}</option>
                    @endforeach
                    </select>
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
                <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-2">
                        <input type="submit" name="submit" value="提交" class="btn btn-success" onclick="return formcheck()"/>
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>

<script language="JavaScript">
$(function () {
    $('.select2').select2();
});
</script>
@endsection
