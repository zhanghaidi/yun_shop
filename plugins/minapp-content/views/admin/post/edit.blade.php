@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.post.index')}}">话题管理</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-board.index')}}">话题版块</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-filter.post')}}">敏感词库</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-upload-filter.index')}}">上传敏感图用户</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.cos-images.index')}}">敏感图片</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.cos-video.index')}}">敏感视频管理</a></li>
                </ul>
            </div>

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$info['id']}}">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">话题标题</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[title]" class="form-control" value="{{$info['title']}}">
                    <span class="help-block">请填写话题标题</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">选择话题发布用户：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[user_id]" data-placeholder="请选择用户" class="form-control select2">
                    @foreach($user as $item)
                    <option value="{{$item['ajy_uid']}}"@if(in_array($item['ajy_uid'], $info['user_id'])) selected @endif>{{$item['nickname']}}</option>
                    @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">话题图片</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldMultiImage('data[images]', $info['images'])!!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">话题版块</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[board_id]" class="form-control">
                        <option value="">==请选择话题版块==</option>
                        @foreach($board as $item)
                        <option value="{{$item['id']}}"@if($item['id']== $info['board_id']) selected @endif>{{$item['name']}}</option>
                        @endforeach
                    </select>
                    <span class="help-block"><a href="{{ yzWebUrl('plugin.minapp-content.admin.article-category.edit') }}"><i class="fa fa-plus-circle"></i> 添加版块</a></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">话题视频</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldVideo('data[video]', $info['video'])!!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">话题内容</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <textarea class="form-control" rows="5" name="data[content]" placeholder="请输话题内容">{{$info['content']}}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">话题关联商品：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[goods_id][]" data-placeholder="请选择文章关联推荐商品" class="form-control select2" multiple>
                        <option value="">==请选择话题商品==</option>
                        @foreach($goods as $item)
                        <option value="{{$item['id']}}"@if(in_array($item['id'], $info['goods_id'])) selected @endif>{{$item['title']}}</option>
                        @endforeach
                    </select>
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
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否推荐</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[is_recommend]" value="1" @if($info['is_recommend'] === 1) checked="checked" @endif /> 是
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[is_recommend]" value="0" @if($info['is_recommend'] === 0) checked="checked" @endif /> 否
                    </label>
                    <span class="help-block">小程序推荐</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否首页精选</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[is_hot]" value="1" @if($info['is_hot'] === 1) checked="checked" @endif /> 是
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[is_hot]" value="0" @if($info['is_hot'] === 0) checked="checked" @endif /> 否
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
