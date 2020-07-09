@extends('layouts.base')

@section('content')
<div class="rightlist">
    <form action="" method='post' class='form-horizontal'>
        <div class='panel panel-default'>
            <div class='panel-heading'>
                <span>修改供应商密码</span>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">账号</label>
                <div class="col-sm-9 col-xs-12">
                    {{$supplier->hasOneWqUser->username}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">密码</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="password" name="data[new_pwd]" class="form-control" value=""  />
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">验证密码</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="password" name="data[new_pwd_too]" class="form-control" value=""  />
                </div>
            </div>
            <div class="form-group"></div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"  />
                    <input type="hidden" name="token" value="{{$var['token']}}" />
                    <input type="button" name="back" onclick='history.back()'  value="返回列表" class="btn btn-default" />
                </div>
            </div>
        </div>
    </form>
</div>
@endsection