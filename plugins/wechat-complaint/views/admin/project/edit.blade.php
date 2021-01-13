@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">投诉来源编辑</div>
        <div class="panel-body">
        
            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$data['id']}}">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">来源名称</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" class="form-control" name="data[name]" value="{{$data['name']}}" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-2">
                        <input type="submit" name="submit" value="提交" class="btn btn-success" />
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

