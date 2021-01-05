@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[sort_id]" value="{{$id}}">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label mt-5 pt-5 fs-6">内容(*)</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                {!! yz_tpl_ueditor('data[content]', $data['content']) !!}
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

