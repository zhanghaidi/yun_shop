@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.post.index')}}">话题管理</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-board.index')}}">话题版块</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-filter.post')}}">敏感词库</a></li>
                </ul>
            </div>


            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">类目名称*</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[title]" class="form-control" value="">
                    <span class="help-block">请输入类目名称</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <input type="text" name="data[list_order]" class="form-control" value="0">
                    <span class="help-block">显示顺序，越大则越靠前</span>
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

