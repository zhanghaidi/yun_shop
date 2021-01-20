@extends('layouts.base')
@section('title', trans('添加会员'))
@section('content')
    <div class="w1200 ">
        <div class=" rightlist ">
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">添加会员</a></li>
                </ul>
            </div>

            <div class="right-addbox"><!-- 此处是右侧内容新包一层div -->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form action="" method="post" name="member_form" class="form-horizontal" role="form" id="form1">
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信角色</label>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="input-group">
                                        <input type="hidden" id="agencyid" name="team[uid]" value="">
                                        <input type="text" name="agency" maxlength="30" value="" id="agency" class="form-control" readonly="">
                                        <div class="input-group-btn">
                                            <button class="btn btn-default" type="button" onclick="popwin = $('#modal-module-menus-agency').modal();">
                                                选择角色
                                            </button>
                                            <button class="btn btn-danger" type="button" onclick="$('#agencyid').val('');$('#agency').val('');">
                                                清除选择
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">真实姓名</label>
                                <div class="col-sm-6 col-xs-6">
                                    <input type='text' name='team[realname]' id="realname" class="form-control" value=""/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">联系方式</label>
                                <div class="col-sm-6 col-xs-6">
                                    <input type='text' name='team[contact]' id="mobile" class="form-control" value=""/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级</label>
                                <div class="col-sm-6 col-xs-6">
                                    <select class="form-control tpl-category-parent" name="team[level]">
                                            <option value="0">{{$default_level}}</option>
                                        @foreach ($level as $item)
                                            <option value="{{$item->id}}">{{$item->level_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">账号</label>
                                <div class="col-sm-6 col-xs-6">
                                    <input type='text' name='team[username]' id="username" class="form-control" value=""/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">登录密码</label>
                                <div class="col-sm-6 col-xs-6">
                                    <input type='password' name='team[password]' id="password" class="form-control" value=""/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">确认密码</label>
                                <div class="col-sm-6 col-xs-6">
                                    <input type='password' name='team[password_again]' id="password_again" class="form-control" value=""/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-xs-6">
                                    <input type="submit" name="submit" value="提交" class="btn btn-success"
                                           onclick="return sub()"/>
                                    <input type="button" name="back" onclick='history.back()' style=''
                                           value="返回列表"
                                           class="btn btn-default back"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-9">
            <div id="modal-module-menus-agency" class="modal fade" tabindex="-1">
                <div class="modal-dialog" style='width: 920px;'>
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-hidden="true" data-dismiss="modal"
                                    class="close" type="button">
                                ×
                            </button>
                            <h3>选择会员</h3></div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                           name="keyword" value=""
                                           id="search-kwd-agencys"
                                           placeholder="请输入微信角色"/>
                                    <span class='input-group-btn'>
                                                            <button type="button" class="btn btn-default"
                                                                    onclick="search_agencys();">搜索
                                                            </button></span>
                                </div>
                            </div>
                            <div id="module-menus-agencys"
                                 style="padding-top:5px;"></div>
                        </div>
                        <div class="modal-footer"><a href="#"
                                                     class="btn btn-default"
                                                     data-dismiss="modal"
                                                     aria-hidden="true">关闭</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    function search_agencys() {
        if ($.trim($('#search-kwd-agencys').val()) == '') {
            Tip.focus('#search-kwd-agencys', '请输入关键词');
            return;
        }
        $("#module-menus-agencys").html("正在搜索....");
        $.get('{!! yzWebUrl('member.member.get-search-member') !!}', {
                keyword: $.trim($('#search-kwd-agencys').val())
            }, function (dat) {
                $('#module-menus-agencys').html(dat);
            }
        );
    }

    function select_member(o) {
        console.log(o);
        $("#agencyid").val(o.uid);
        $("#agency").val("[" + o.uid + "]" + o.nickname);
        $("#realname").val(o.realname);
        $("#mobile").val(o.mobile);
        $("#parentid").val(o.yz_member.parent_id);
        $("#relation").val(o.yz_member.relation);
        $("#modal-module-menus-agency .close").click();
    }

    function sub() {
        if ($("#agencyid").val() == '') {
            alert('未选微信角色');
            Tip.focus('#agency', '未选微信角色','top');
            return false;
        }
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

