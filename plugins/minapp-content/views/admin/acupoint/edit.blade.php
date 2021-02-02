@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li @if(strpos(\YunShop::request()->route, '.acupoint.') !== false) class="active" @endif><a href="{{yzWebUrl('plugin.minapp-content.admin.acupoint.index')}}">穴位列表</a></li>
                    <li @if(strpos(\YunShop::request()->route, '.meridian.') !== false) class="active" @endif><a href="{{yzWebUrl('plugin.minapp-content.admin.meridian.index')}}">经络列表</a></li>
                </ul>
            </div>

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$info['id']}}">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">穴位首字母：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[chart]" class="form-control" value="{{$info['chart']}}" placeholder="输入大写穴位首字母（A-Z）">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">穴位名称：*</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[name]" class="form-control" value="{{$info['name']}}" placeholder="请输穴位名称">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">穴位拼音：*</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[zh]" class="form-control" value="{{$info['zh']}}" placeholder="请输穴位拼音">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">穴位所属经络：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[meridian_id][]" class="form-control select2" data-placeholder="请选择穴位所属经络" multiple>
                        <optgroup label="十二经络">
                        @foreach($meridian as $value)
                            @if($value['type_id'] == 1)
                            <option value="{{$value['id']}}" @if(in_array($value['id'], $info['meridian_id'])) selected @endif>{{$value['discription']}}</option>
                            @endif
                        @endforeach
                        </optgroup>
                        <optgroup label="奇经八脉">
                        @foreach($meridian as $value)
                            @if($value['type_id'] == 2)
                            <option value="{{$value['id']}}" @if(in_array($value['id'], $info['meridian_id'])) selected @endif>{{$value['discription']}}</option>
                            @endif
                        @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">穴位展示图：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[image]', $info['image'])!!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">穴位类别：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[type]" class="form-control" value="{{$info['type']}}" placeholder="请输穴位类别">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">经验取穴：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[get_position]" class="form-control" value="{{$info['get_position']}}" placeholder="请输经验取穴">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">穴位主调：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[effect]" class="form-control" value="{{$info['effect']}}" placeholder="请输穴位主调">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">穴位讲解音频：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldAudio('data[audio]', $info['audio'])!!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">穴位讲解视频：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldVideo('data[video]', $info['video'])!!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">视频封面图(方形)：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[video_image_f]', $info['video_image_f'])!!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">视频封面图(竖版)：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[video_image_s]', $info['video_image_s'])!!}
                </div>
            </div>


            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">热门显示推荐：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[is_hot]" value="0" @if($info['is_hot'] == 0) checked="checked" @endif /> 否
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[is_hot]" value="1" @if($info['is_hot'] == 1) checked="checked" @endif /> 是
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">穴位关联商品：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[recommend_goods][]" class="form-control select2" data-placeholder="请选择穴位推荐关联商品" multiple>
                        @foreach($goods as $value)
                        <option value="{{$value['id']}}" @if(in_array($value['id'], $info['recommend_goods'])) selected @endif>{{$value['title']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">穴位关联文章：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[recommend_article][]" class="form-control select2" data-placeholder="请选择穴位推荐关联文章" multiple>
                        @foreach($article as $value)
                        <option value="{{$value['id']}}" @if(in_array($value['id'], $info['recommend_article'])) selected @endif>{{$value['title']}}</option>
                        @endforeach
                    </select>
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
