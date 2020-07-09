@extends('layouts.base')

@section('content')
@section('title', '生成充值码')
<script type="text/javascript">
    function formcheck() {
        if ($(':input[name="code[total]"]').val() == '' || $(':input[name="code[total]"]').val() == 0) {
            Tip.focus(':input[name="code[total]"]', "请输入生成数量!");
            return false;
        }
        if ($(':input[name="code[price]"]').val() == '' || $(':input[name="code[price]"]').val() == 0) {
            Tip.focus(':input[name="code[price]"]', "请输入充值数量!");
            return false;
        }
        if ($(':input[name="code[uid]"]').val() == '' || $(':input[name="code[uid]"]').val() == 0) {
            alert('请选择微信角色!');
            return false;
        }
        return true;
    }
</script>
<div class="page-heading"><h2>生成充值码</h2></div>
<div class="w1200 m0a">
    <div class="rightlist">
        <form action="" method="post" class="form-horizontal form">
            <div class='panel panel-default'>
                <div class="alert alert-info">绑定微信角色用于推广下线</div>
                <div class="form-group notice">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信角色</label>
                    <div class="col-sm-4">
                        <input type='hidden' id='noticeopenid' name='code[uid]' value="" />
                        <div class='input-group'>
                            <input type="text" name="memeber" maxlength="30" value="" id="saler" class="form-control" readonly />
                            <div class='input-group-btn'>
                                <button class="btn btn-default" type="button" onclick="popwin = $('#modal-module-menus-notice').modal();">选择角色</button>
                                <button class="btn btn-danger" type="button" onclick="$('#noticeopenid').val('');$('#saler').val('');$('#saleravatar').hide()">清除选择</button>
                            </div>
                        </div>
                        <span id="saleravatar" class='help-block' style="display:none"><img  style="width:100px;height:100px;border:1px solid #ccc;padding:1px" src=""/></span>

                        <div id="modal-module-menus-notice"  class="modal fade" tabindex="-1">
                            <div class="modal-dialog" style='width: 920px;'>
                                <div class="modal-content">
                                    <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择角色</h3></div>
                                    <div class="modal-body" >
                                        <div class="row">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="keyword" value="" id="search-kwd-notice" placeholder="请输入昵称/姓名/手机号" />
                                                <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_members();">搜索</button></span>
                                            </div>
                                        </div>
                                        <div id="module-menus-notice" style="padding-top:5px;"></div>
                                    </div>
                                    <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">充值类型</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio-inline"><input type="radio" class="sendmoth" name="code[type]" value="1" checked="checked"/> 积分</label>
                        <label class="radio-inline"><input type="radio" class="sendmoth" name="code[type]" value="2" /> 余额</label>
                        @if (app('plugins')->isEnabled('love'))
                        <label class="radio-inline"><input type="radio" class="sendmoth" name="code[type]" value="3" />可用{{ LOVE_NAME }}类型</label>
                        <label class="radio-inline"><input type="radio" class="sendmoth" name="code[type]" value="4" />冻结{{ LOVE_NAME }}类型</label>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">充值码设置</label>
                    <div class="col-sm-9 col-xs-12 form-inline">
                        <div class="input-group form-group col-sm-3">
                            <span class="input-group-addon">生成数量</span>
                            <input type="text" name="code[total]" class="form-control" value="1" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" />
                            <span class="input-group-addon">个</span>
                        </div>
                        <div class="input-group form-group col-sm-3">
                            <span class="input-group-addon">充值数量</span>
                            <input type="text" name="code[price]" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" class="form-control" value="1" />
                            <span class="input-group-addon">个or元</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">有效期</label>
                    <div class="col-sm-9 col-xs-12">
                    {!! tpl_form_field_date('code[end_time]', '') !!}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="submit" name="submit" value="生成充值码" class="btn btn-success"
                               onclick="return formcheck()"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script language='javascript'>
    function search_members() {
        if ($('#search-kwd-notice').val() == '') {
            Tip.focus('#search-kwd-notice', '请输入关键词');
            return;
        }
        $("#module-menus-notice").html("正在搜索....");
        $.get("{!! yzWebUrl('member.member.get-search-member') !!}", {
            keyword: $.trim($('#search-kwd-notice').val())
        }, function (dat) {
            $('#module-menus-notice').html(dat);
        });
    }

    function select_member(o) {
        $("#noticeopenid").val(o.uid);
        $("#saleravatar").show();
        $("#saleravatar").find('img').attr('src', o.avatar);
        $("#saler").val(o.nickname + "/" + o.realname + "/" + o.mobile);
        $("#modal-module-menus-notice .close").click();
    }
</script>
@endsection