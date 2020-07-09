@extends('layouts.base')

@section('content')
<div class="rightlist">
    <form action="" method='post' class='form-horizontal'>
        <div class='panel panel-default'>
            <div class='panel-heading'>
                <span>详细信息</span>
            </div>
            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝</label>
                    <div class="col-sm-9 col-xs-12">
                        <img src='{{$apply->hasOneMember->avatar}}'
                             style='width:100px;height:100px;padding:1px;border:1px solid #ccc'/>
                        {{$apply->hasOneMember->nickname}}
                    </div>
                </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">账号</label>
                <div class="col-sm-9 col-xs-12">
                    {{$apply->username}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">真实姓名</label>
                <div class="col-sm-9 col-xs-12">
                    {{$apply->realname}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">手机号码</label>
                <div class="col-sm-9 col-xs-12">
                    {{$apply->mobile}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">产品</label>
                <div class="col-sm-9 col-xs-12">
                    {{$apply->product}}
                </div>
            </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">地址</label>
                    <div class="col-sm-9 col-xs-12">
                        {{$apply->province_name}}/{{$apply->city_name}}/{{$apply->district_name}}
                    </div>
                </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">备注</label>
                <div class="col-sm-9 col-xs-12">
                    {{$apply->remark}}
                </div>
            </div>
            <div class="form-group"></div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <input type="hidden" name="token" value="{{$var['token']}}" />
                    <input type="button" name="back" onclick='history.back()'  value="返回列表" class="btn btn-default" />
                </div>
            </div>
        </div>
    </form>
</div>
<script language='javascript'>

            $('form').submit(function(){
                if($('#saler').val() == ''){
                    Tip.focus($('#saler'),'请选择微信角色!');
                    return false;
                }
                if($('#username').val() == ''){
                    Tip.focus($('#username'),'请输入账号!');
                    return false;
                }
                if($('#password').val() == ''){
                    Tip.focus($('#password'),'请输入密码!');
                    return false;
                }
                if($('#realname').val() == ''){
                    Tip.focus($('#realname'),'请输入姓名!');
                    return false;
                }
                if($('#mobile').val() == ''){
                    Tip.focus($('#mobile'),'请输入手机号!');
                    return false;
                }
                return true;
            })

    function search_members() {
        if( $.trim($('#search-kwd-notice').val())==''){
            $('#search-kwd-notice').attr('placeholder','请输入关键词');
            return;
        }
        $("#module-menus-notice").html("正在搜索....")
        $.get('{!! yzWebUrl('member.query') !!}', {
            keyword: $.trim($('#search-kwd-notice').val())
        }, function(dat){
            $('#module-menus-notice').html(dat);
        });
    }
    function select_member(o) {
        $("#noticeopenid").val(o.uid);
        $("#saleravatar").show();
        $("#saleravatar").find('img').attr('src',o.avatar);
        $("#saler").val( o.nickname+ "/" + o.realname + "/" + o.mobile );
        $("#modal-module-menus-notice .close").click();
    }
</script>
@endsection