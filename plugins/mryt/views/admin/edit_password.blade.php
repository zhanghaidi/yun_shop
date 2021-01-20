@extends('layouts.base')
@section('title', '修改门店密码')
@section('content')
<link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>
    <script type="text/javascript" src="./resource/js/lib/jquery-ui-1.10.3.min.js"></script>
<div class="rightlist">
    <form action="" method='post' class='form-horizontal'>
        <div class='panel panel-default'>
            <div class='panel-heading'>
                <span>账户信息</span>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员昵称</label>
                <div class="col-sm-9 col-xs-12">
                    <button type="button" class="btn btn-info">{{$nickname}}</button>
                </div>
            </div>
            @if($user_uid)
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">账号</label>
                <div class="col-sm-9 col-xs-12">
                    <button type="button" class="btn btn-info">{{$username}}</button>
                </div>
            </div>
            @else
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">账号</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="data[username]" id="username" class="form-control" value=""  />
                    </div>
                </div>
            @endif
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">密码</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="password" name="data[password]" id="password" class="form-control" value=""  />
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">验证密码</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="password" name="data[password_again]" id="password_again" class="form-control" value=""  />
                </div>
            </div>
            <div class="form-group"></div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    @if($user_uid)
                        <input type="hidden" name="is_add" value="0"  />
                        <input type="submit" name="submit" onclick="return sub()" value="提交" class="btn btn-primary col-lg-1"  />
                    @else
                        <input type="hidden" name="is_add" value="1"  />
                        <input type="submit" name="submit" onclick="return add()" value="添加账户" class="btn btn-primary col-lg-1"  />
                    @endif
                    <input type="hidden" name="token" value="{{$var['token']}}" />
                    <input type="button" name="back" onclick='history.back()'  value="返回列表" class="btn btn-default" />
                </div>
            </div>
        </div>
    </form>
</div>

    <script>
        function sub() {
            if($("#password").val() == '') {
                alert('请填写密码');
                Tip.focus('#password', '请填写密码','top');
                return false;
            }
            if($("#password").val() !== $("#password_again").val()) {
                alert('两次密码不一样');
                Tip.focus('#password_again', '两次密码不正确','top');
                return false;
            }
            return true;
        }

        function add() {
            if($("#username").val() != '' && $("#password").val() == '') {
                alert('请填写密码');
                Tip.focus('#password', '请填写密码','top');
                return false;
            }
            if($("#password").val() !== $("#password_again").val()) {
                alert('两次密码不一样');
                Tip.focus('#password_again', '两次密码不正确','top');
                return false;
            }
            var form_data = {
                'username': $("#username").val()
            };
            var status = "";
            $.post({
                url: '{!! yzWebUrl('plugin.mryt.admin.member.verify-account') !!}',
                async : false,
                dataType: "json",
                data: form_data,
                success: function (strValue) {
                    if (strValue.status == "-1") {
                        status = -1;
                        alert(strValue.result.msg);
                    }
                }
            });
            if (status == -1 ) {
                Tip.focus('#username', '账号已存在','top')
                return false;
            }
            return true;
        }
    </script>
@endsection