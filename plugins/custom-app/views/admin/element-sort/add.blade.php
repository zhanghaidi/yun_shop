@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">添加页面元素</div>
        <div class="panel-body">
        
            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">元素名称</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" class="form-control" name="data[name]" value="" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">唯一标识</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" class="form-control" name="data[label]" value="" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">值类型</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                    <label class="radio-inline">
                        <input type="radio" name="data[type]" value="1" checked /> 文本
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[type]" value="2" /> 图片URL
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[type]" value="3" /> 文本 - 多值
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[type]" value="4" /> 图片URL - 多值
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[type]" value="5" /> 视频URL
                    </label>
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

@endsection

