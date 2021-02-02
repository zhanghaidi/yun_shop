@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.somato-type.index')}}">体质管理</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.label.index')}}">症状标签</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.question.index')}}">测评题库</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.answer.index')}}">用户测评</a></li>
                </ul>
            </div>

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$info['id']}}">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">体质名称</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[name]" class="form-control" value="{{$info['name']}}">
                    <span class="help-block">请填写体质名称</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">体质描述</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <textarea class="form-control" rows="3" name="data[description]" placeholder="请输入体质描述">{{$info['description']}}</textarea>
                    <span class="help-block">请填写体质描述</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">体质症状：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[symptom][]" data-placeholder="请选择体质常见症状" class="form-control select2" multiple>
                    @foreach($label as $item)
                    <option value="{{$item['id']}}"@if(in_array($item['id'], $info['symptom'])) selected @endif>{{$item['name']}}</option>
                    @endforeach
                    </select>
                    <span class="help-block"><a href="{{ yzWebUrl('plugin.minapp-content.admin.label.edit') }}" target="_blank"><i class="fa fa-plus-circle"></i> 添加症状标签</a></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">体质调理方案</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! yz_tpl_ueditor('data[content]', $info['content']) !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">关联穴位：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[recommend_acupotion][]" data-placeholder="请选择关联穴位" class="form-control select2" multiple>
                    @foreach($acupoint as $item)
                    <option value="{{$item['id']}}"@if(in_array($item['id'], $info['recommend_acupotion'])) selected @endif>{{$item['name']}}</option>
                    @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">关联商品：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[recommend_goods][]" data-placeholder="请选择关联商品" class="form-control select2" multiple>
                        @foreach($goods as $item)
                        <option value="{{$item['id']}}"@if(in_array($item['id'], $info['recommend_goods'])) selected @endif>{{$item['title']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">关联文章：</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <select name="data[recommend_article][]" data-placeholder="请选择关联文章" class="form-control select2" multiple>
                        @foreach($article as $item)
                        <option value="{{$item['id']}}"@if(in_array($item['id'], $info['recommend_article'])) selected @endif>{{$item['title']}}</option>
                        @endforeach
                    </select>
                </div>
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

<script language="JavaScript">
$(function () {
    $('.select2').select2();
});
</script>
@endsection
