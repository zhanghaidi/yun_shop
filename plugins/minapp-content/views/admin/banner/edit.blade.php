@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.banner.index')}}">轮播图列表</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.banner-position.index')}}">轮播位</a>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.system-category.index')}}">首页功能区分类</a>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.system-image.index')}}">系统图片</a>
                    </li>
                </ul>
            </div>

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="{{$info['id']}}">

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">轮播图排序</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="text" name="list_order" class="form-control" value="{{$info['list_order']}}"
                               placeholder="排序按数字由大到小排序">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">轮播图位置</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <select name="position_id" class="form-control">
                            <option value="">请选择轮播图位置</option>
                            @foreach($bannerPosition as $value)
                                <option value="{{$value['id']}}"
                                        @if($info['position_id']==$value['id']) selected @endif>{{$value['name']}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">轮播图名称*</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="text" name="title" class="form-control" value="{{$info['title']}}"
                               placeholder="请输入轮播图名称">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">轮播图：</label>
                    <div class="col-xs-12 col-sm-9 col-md-8">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('image', $info['image']) !!}
                        <span class="help-block">请按照规定尺寸上传</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否跳转：</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <label class="radio-inline">
                            <input type="radio" name="is_href" value="1" @if($info['is_href']==1) checked @endif/>是
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="is_href" value="0" @if($info['is_href']==0) checked @endif/>否
                        </label>
                    </div>
                </div>
                <div class="form-group ios_open-div" @if($info['is_href'] == 0) style="display: none; @endif">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">跳转地址：</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="text" name="jumpurl" class="form-control" value="{{$info['jumpurl']}}"
                               placeholder="请输入跳转地址">
                    </div>
                </div>
                <div class="form-group expire-div" @if($info['is_href'] == 0) style="display: none; @endif">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">跳转小程序：</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <select name="appid" class="form-control">
                            @foreach($minappList as $k => $v)
                                <option value="{{$v['key']}}" @if($info['appid']==$v['key']) selected @endif>{{$v['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group jump_type-div" @if($info['is_href'] == 0) style="display: none; @endif">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">跳转类型：</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="radio" id="is_display-1" name="jumptype" value="1"
                               @if($info['jumptype']==1) checked @endif >
                        <label class="radio-inline" for="is_display-1">普通页面</label>
                        <input type="radio" name="jumptype" id="is_display-0" value="2"
                               @if($info['jumptype']==2) checked @endif >
                        <label class="radio-inline" for="is_display-0"> 底部导航</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">状态：</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="radio" id="is_display-1" name="status" value="1"
                               @if($info['status']==1) checked @endif >
                        <label class="radio-inline" for="is_display-1">显示</label>
                        <input type="radio" name="status" id="is_display-0" value="0"
                               @if($info['status']==0) checked @endif >
                        <label class="radio-inline" for="is_display-0">隐藏</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">类型：</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="radio" id="is_display-1" name="type" value="1"
                               @if($info['type']==1) checked @endif >
                        <label class="radio-inline" for="is_display-1">banner图</label>
                        <input type="radio" name="type" id="is_display-2" value="2"
                               @if($info['type']==2) checked @endif >
                        <label class="radio-inline" for="is_display-2">功能导航</label>
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
<script type="text/javascript">
    $('input[name=is_href]').change(function () {
        if ($(this).val() == 1) {
            $('.expire-div').show();
            $('.ios_open-div').show();
            $('.jump_type-div').show();
        } else {
            $('.expire-div').hide();
            $('.ios_open-div').hide();
            $('.jump_type-div').hide();
        }
    })
</script>

@endsection
