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
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">经络排序：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[list_order]" class="form-control" value="{{$info['list_order']}}" placeholder="排序按数字由小到大排序">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">经络简称：*</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[name]" class="form-control" value="{{$info['name']}}" placeholder="请输经络名称">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">经络全称：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[discription]" class="form-control" value="{{$info['discription']}}" placeholder="请输经络全称">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">经络内容介绍：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[content]" class="form-control" value="{{$info['content']}}" placeholder="请输经络内容介绍">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">经络图：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[image]', $info['image'])!!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">经络类型：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[type_id]" value="1" @if($info['type_id'] == 1) checked="checked" @endif /> 十二经络
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[type_id]" value="2" @if($info['type_id'] == 2) checked="checked" @endif /> 奇经八脉
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">养生开始时间：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[start_time]" class="form-control" value="{{substr($info['start_time'],0,5)}}" placeholder="请选择经脉养生开始时间" id="start_time">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">养生结束时间：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[end_time]" class="form-control" value="{{substr($info['end_time'],0,5)}}" placeholder="请选择经脉养生结束时间" id="end_time">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">经络养生通知提醒：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[notice]" class="form-control" value="{{$info['notice']}}" placeholder="经络养生通知提醒">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">经络讲解音频：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldAudio('data[audio]', $info['audio'])!!}
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
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示状态：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[status]" value="1" @if($info['status'] == 1) checked="checked" @endif /> 显示
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[status]" value="0" @if($info['status'] == 0) checked="checked" @endif /> 隐藏
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">经络关联课程：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <div class="input-group">
                    <div class="input-group">
                        <select class="form-control" data-placeholder="请选择经络推荐关联课程" id="first">
                            <option value="0">------ 请选择经络推荐关联课程 ------</option>
                            @foreach($course as $value)
                            <option value="{{$value['id']}}" @if($value['id'] == $info['recommend_course']) selected @endif>{{$value['name']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="recommend_course_hour" value="{{$info['recommend_course_hour']}}">
                        <div class="input-group-addon">&nbsp;&nbsp;经络关联课时：&nbsp;&nbsp;</div>
                        <select class="form-control" id="second" name="data[recommend_course]">
                        </select>
                    </div>
                    </div>
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
require(["{{yz_tomedia('/images/ajy/js/layer/laydate/laydate.js')}}"], function(laydate) {
    laydate.render({
        elem: '#start_time'
        ,type: 'time'
        ,format: 'HH:mm'
    });
    laydate.render({
        elem: '#end_time'
        ,type: 'time'
        ,format: 'HH:mm'
    });
});

$(function () {
    _first_id = $('#first').val();
    if (_first_id > 0) {
        getSecond();
    }

    $('#first').on('change', function(){
        getSecond();
    });
});

function getSecond() {
    _rid = $('#first').val();
    _id = $('#recommend_course_hour').val();
    _course_url = "{{ yzWebUrl('plugin.minapp-content.admin.meridian.course-hour') }}";
    _course_url = _course_url.replace(/&amp;/g, '&');
    _course_url += '&id=' + _rid;
    $.get(_course_url, function(res) {
        _data = res.data;
        if (_data[0] == undefined) {
            _course_opt_str = '<option value="0">暂无数据</option>';
        } else {
            _course_opt_str = '<option value="0">------ 请选择课时 ------</option>';
            for (i in _data) {
                if (_data[i].id == _id) {
                    _course_opt_str += '<option value="' + _data[i].id + '" selected>' + _data[i].title + '</option>';
                } else {
                    _course_opt_str += '<option value="' + _data[i].id + '">' + _data[i].title + '</option>';
                }
            }
        }
        $('#second').html(_course_opt_str);
    });
}
</script>
@endsection
