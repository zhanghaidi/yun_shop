@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-defualt">
        <div class="top" style="margin-bottom:20px">
            <ul class="add-shopnav" id="myTab">
                <li><a href="{{yzWebUrl('plugin.minapp-content.admin.feedback.index')}}">反馈列表</a></li>
                <li><a href="{{yzWebUrl('plugin.minapp-content.admin.feedback.complain')}}">投诉列表</a>
                <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.feedback.complain-type')}}">投诉类型</a>
                </li>
            </ul>
        </div>
        <div class="panel-heading">投诉类型管理</div>
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-1 control-label">排序</label>
                <div class="col-sm-9 col-xs-12 col-md-11">
                    <input name="info[list_order]" type="text" class="form-control" value="{{ $info['list_order'] }}" placeholder="按倒叙排序" required/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-1 control-label">投诉类型名称</label>
                <div class="col-sm-9 col-xs-12 col-md-11">
                    <input name="info[name]" type="text" class="form-control" value="{{ $info['name'] }}" placeholder="请输入投诉类型名称" required/>
                </div>
            </div>


            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-1 control-label">是否显示</label>
                <div class="col-sm-9 col-xs-12 col-md-11">
                    <label class="radio-inline">
                        <input type="radio" name="info[status]" value="1" @if($info['status'] == 1) checked="checked" @endif />显示
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="info[status]" value="0" @if($info['status'] == 0) checked="checked" @endif />不显示
                    </label>
                    <span class="help-block">小程序端显示隐藏状态</span>
                </div>

            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection

