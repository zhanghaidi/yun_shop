@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<style>
.title-key > i,.describe-key > i  {
    margin-right: 20px;
    font-size: 20px;
    color: orange;
    font-style: normal;
}
</style>
<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">考试编辑</div>
        <div class="panel-body">

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$info['id']}}">

            <div><b>基本信息:</b></div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">名称</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[name]" class="form-control" value="{{$info['name']}}" placeholder="请输入考试名称">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">封面图片</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[url]', $info['url'])!!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">详情</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                {!! yz_tpl_ueditor('data[content]', $info['content']['content']) !!}
                </div>
            </div>

            <hr>
            <div><b>考试设置:</b></div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">试卷选择</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[paper_id]" class="form-control">
                        <option value="0">------ 选择试卷 ------</option>
                        @foreach($paper as $value)
                        <option value="{{$value['id']}}" data-question="{{$value['question']}}" data-score="{{$value['score']}}"@if($value['id'] == $info['paper_id']) selected="selected" @endif>{{$value['name']}}</option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">参与考试时间</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[time_status]" value="0" @if($info['time_status'] == 0) checked="checked" @endif /> 不限制
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[time_status]" value="1" @if($info['time_status'] == 1) checked="checked" @endif /> 限时
                    </label>
                    {!! app\common\helpers\DateRange::tplFormFieldDateRange('data[time_range]', [
                        'starttime'=>array_get($info,'start',0),
                        'endtime'=>array_get($info,'end',0),
                        'start'=>0,
                        'end'=>0
                        ], true) !!}
                    <span class="help-block">限制考试时间的情况下，学员考试中离开，倒计时不会停止，建议提醒学员不要长时间离开考试</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">参与考试时长</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <div class="input-group">
                        <div class="input-group">
                            <input type="text" name="data[duration]" class="form-control" value="{{$info['duration']}}" placeholder="请输入大于等于0的整数">
                            <div class="input-group-addon">分钟</div>
                        </div>
                    </div>
                    <span class="help-block">限制考试时长的情况下，学员考试中离开，倒计时不会停止，建议提醒学员不要长时间离开考试</span>
                    <span class="help-block">0表示无限制</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">参与考试次数</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[frequency_status]" value="0" @if($info['frequency'] == 0) checked="checked" @endif /> 不限次数
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[frequency_status]" value="1" @if($info['frequency'] >= 1) checked="checked" @endif /> 限制
                    </label>
                    <div class="input-group">
                        <div class="input-group">
                            <div class="input-group-addon">限制可参与考试</div>
                            <input type="text" name="data[frequency_number]" class="form-control" value="{{$info['frequency']}}" placeholder="请输入大于等于0的整数">
                            <div class="input-group-addon">次</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">重考间隔</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <div class="input-group">
                        <div class="input-group">
                            <input type="text" name="data[interval]" class="form-control" value="{{$info['interval']}}" placeholder="请输入大于等于0的整数">
                            <div class="input-group-addon">小时</div>
                        </div>
                    </div>
                    <span class="help-block">0表示无限制</span>
                </div>
            </div>

            <hr>
            <div><b>题目分值:</b></div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">题目分值</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[is_question_score]" value="1" @if($info['is_question_score'] == 1) checked="checked" @endif /> 显示
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[is_question_score]" value="2" @if($info['is_question_score'] == 2) checked="checked" @endif /> 隐藏
                    </label>
                    <span class="help-block">隐藏后，考试中题目的分值将不会显示，考试成绩分值仍然会显示</span>
                </div>
            </div>

            <hr>
            <div><b>结果设置:</b></div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">成绩展示</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[is_score]" value="1" @if($info['is_score'] == 1) checked="checked" @endif /> 批改完成后立即展示
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[is_score]" value="2" @if($info['is_score'] == 2) checked="checked" @endif /> 隐藏
                    </label>
                    <span class="help-block">可配置考试结束后，成绩是否展示</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">题目展示</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[is_question]" value="1" @if($info['is_question'] == 1) checked="checked" @endif /> 提交考试后立即展示
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[is_question]" value="2" @if($info['is_question'] == 2) checked="checked" @endif /> 提交且批改完成后立即展示
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[is_question]" value="3" @if($info['is_question'] == 3) checked="checked" @endif /> 隐藏
                    </label>
                    <span class="help-block">可配置考试结束后，题目是否展示</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">答案展示</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <label class="radio-inline">
                        <input type="radio" name="data[is_answer]" value="1" @if($info['is_answer'] == 1) checked="checked" @endif /> 提交考试后立即展示
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[is_answer]" value="2" @if($info['is_answer'] == 2) checked="checked" @endif /> 提交且批改完成后立即展示
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[is_answer]" value="3" @if($info['is_answer'] == 3) checked="checked" @endif /> 隐藏
                    </label>
                    <span class="help-block">可配置考试结束后，答案是否展示</span>
                </div>
            </div>

            <hr>
            <div><b>答题前分享设置:</b></div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <textarea name="data[share_title]" class="form-control" placeholder="请输入考试名称" rows="4">{{$info['content']['share_title']}}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享描述</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <textarea name="data[share_describe]" class="form-control" placeholder="请输入考试名称" rows="4">{{$info['content']['share_describe']}}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图片</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[share_image]', $info['content']['share_image'])!!}
                </div>
            </div>

            <hr>
            <div><b>答题后分享设置:</b></div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <textarea name="data[share_title_after]" class="form-control" placeholder="请输入考试名称" rows="4">{{$info['content']['share_title_after']}}</textarea>
                    <span class='help-block'>分享标题中支持变量，请双击下面变量，增加到内容尾部</span>
                    <span class="help-block title-key"><i>{考试名称}</i><i>{成绩得分}</i></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享描述</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <textarea name="data[share_describe_after]" class="form-control" placeholder="请输入考试名称" rows="4">{{$info['content']['share_describe_after']}}</textarea>
                    <span class='help-block'>分享描述中支持变量，请双击下面变量，增加到内容尾部</span>
                    <span class="help-block describe-key"><i>{考试名称}</i><i>{成绩得分}</i></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图片</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[share_image_after]', $info['content']['share_image_after'])!!}
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
    $('input[name="data[is_question]"]').parents('.form-group').hide('slow');
    $('input[name="data[is_answer]"]').parents('.form-group').hide('slow');

    $('.title-key > i').on('dblclick', function(){
        _title = $('textarea[name="data[share_title_after]').val();
        _title += $(this).html();
        $('textarea[name="data[share_title_after]').val(_title);
    });
    $('.describe-key > i').on('dblclick', function(){
        _title = $('textarea[name="data[share_describe_after]').val();
        _title += $(this).html();
        $('textarea[name="data[share_describe_after]').val(_title);
    });
});
</script>
@endsection
