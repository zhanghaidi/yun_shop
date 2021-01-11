@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">轮播位置名*</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="text" name="name" class="form-control" value=""
                               placeholder="请输入轮播位置名">
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
@endsection
