@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.article.index')}}">文章列表</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.article-category.index')}}">文章分类</a></li>
                </ul>
            </div>

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$info['id']}}">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[list_order]" class="form-control" value="{{$info['list_order']}}" placeholder="文章显示顺序">
                    <span class="help-block">文章的显示顺序，越大则越靠前</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">文章标题</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[title]" class="form-control" value="{{$info['title']}}">
                    <span class="help-block">请填写文章标题</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">文章分类</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[cateid]" class="form-control">
                        <option value="">==请选择文章分类==</option>
                        @foreach($category as $cate)
                        <option value="{{$cate['id']}}"@if($cate['id']== $info['cateid']) selected @endif>{{$cate['name']}}</option>
                        @endforeach
                    </select>
                    <span class="help-block"><a href="{{ yzWebUrl('plugin.minapp-content.admin.article-category.edit') }}"><i class="fa fa-plus-circle"></i> 添加分类</a></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">封面图片</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[thumb]', $info['thumb'])!!}
                    <span class="help-block">文章封面图</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享封面图片</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[share_img]', $info['share_img'])!!}
                    <span class="help-block">分享封面图片（5:4）</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">文章简介</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <textarea class="form-control" rows="3" name="data[description]" placeholder="请输入文章简介描述">{{$info['description']}}</textarea>
                    <span class="help-block">请填写文章简介/描述</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">文章内容</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! yz_tpl_ueditor('data[content]', $info['content']) !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">文章视频上传</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldVideo('data[video]', $info['video'])!!}
                    <span class="help-block">上传文章视频</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">文章关联商品：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[recommend_goods][]" data-placeholder="请选择文章关联推荐商品" class="form-control select2" multiple>
                    @foreach($goods as $item)
                    <option value="{{$item['id']}}"@if(in_array($item['id'], $info['recommend_goods'])) selected @endif>{{$item['title']}}</option>
                    @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">文章关联穴位：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[recommend_acupotion][]" data-placeholder="请选择文章关联推荐穴位" class="form-control select2" multiple>
                    @foreach($acupoint as $item)
                    <option value="{{$item['id']}}"@if(in_array($item['id'], $info['recommend_acupotion'])) selected @endif>{{$item['name']}}</option>
                    @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">文章作者/来源</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[author]" class="form-control" value="{{$info['author']}}">
                    <span class="help-block">请填写文章作者/来源</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[status]" value="1" @if($info['status'] === 1) checked="checked" @endif /> 显示
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[status]" value="0" @if($info['status'] === 0) checked="checked" @endif /> 不显示
                    </label>
                    <span class="help-block">显示状态：不显示不会在小程序端显示</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否推荐</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[is_hot]" value="1" @if($info['is_hot'] === 1) checked="checked" @endif /> 是
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[is_hot]" value="0" @if($info['is_hot'] === 0) checked="checked" @endif /> 否
                    </label>
                    <span class="help-block">小程序首页推荐（注意：首页页只显示4条推荐）</span>
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
