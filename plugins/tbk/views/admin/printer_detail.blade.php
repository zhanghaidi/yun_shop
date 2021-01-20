@extends('layouts.base')

@section('content')
@section('title', '打印机信息')
    <div class="rightlist">
        <form action="" method='post' class='form-horizontal'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <span>打印机信息</span>
                </div>
                <div class='panel-body'>
                    <div class="alert alert-info">
                        USER与UKEY, <a href="http://admin.feieyun.com/login.php" target="_blank">请前往飞鹅注册查看</a>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">打印机名称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="printer[title]" class="form-control" value="{{$printer['title']}}" placeholder="请输入打印机名称"  />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">USER</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="printer[user]" class="form-control" value="{{$printer['user']}}" placeholder="请输入USER"  />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">UKEY</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="printer[ukey]" class="form-control" value="{{$printer['ukey']}}" placeholder="请输入UKEY"  />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">打印机编号</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="printer[printer_sn]" class="form-control" value="{{$printer['printer_sn']}}" placeholder="请输入打印机编号"  />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">打印联数</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="printer[times]" class="form-control" value="@if($printer['times'] > 0){{$printer['times']}}@else{{1}}@endif" placeholder="请输入打印联数"  />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline"><input type="radio" class="sendmoth" name="printer[status]" value="0" @if($printer['status'] == 0) checked="checked"@endif /> 关闭</label>
                            <label class="radio-inline"><input type="radio" class="sendmoth" name="printer[status]" value="1" @if($printer['status'] == 1) checked="checked"@endif /> 开启</label>
                        </div>
                    </div>
                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"  />
                            <input type="button" name="back" onclick='history.back()'  value="返回列表" class="btn btn-default" />
                        </div>
                    </div>
                </div>
        </form>
    </div>
@endsection