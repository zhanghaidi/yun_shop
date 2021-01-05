@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">添加页面、协议的文章</div>
        <div class="panel-body">
        
            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">名称</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                    <input type="text" name="data[name]" class="form-control" value="" placeholder="请输入名称">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">标识</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                    <input type="text" name="data[label]" class="form-control" value="" placeholder="请输入唯一标识">
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

