@extends('layouts.base')

@section('content')
@section('title', trans('代客下单'))

<div class="w1200 m0a">
    <div class="main">
        <form id="form1" method="post" action="{!! yzWebUrl('plugin.help-user-buying.admin.home.shopIndex') !!}" class="form-horizontal form" enctype="multipart/form-data">
            <div class="rightlist">
                <div class="right-titpos" style="border-bottom-color: white">
                    {{--<ul class="add-snav">--}}
                        {{--<li class="active"><a href="#">代客下单</a></li>--}}
                    {{--</ul>--}}
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading" style="background-color: rgb(245, 245, 245);border: 1px solid #ddd;margin: 0">代客下单</div>
                    <div class="panel-body" style="border: 1px solid #ddd">

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-4 control-label">订单类型</label>
                            <div class="col-sm-4 col-xs-6">
                                <label class="radio-inline">
                                    <input type="radio" name="order_type"  checked value="0">平台订单
                                </label>
                                &nbsp;&nbsp;&nbsp;
                                @if (!empty($store))
                                <label class="radio-inline">
                                    <input type="radio" name="order_type" value="1"> 门店订单
                                </label>
                                 @endif
                            </div>
                        </div>


                        <div class="form-group" id="store_list" style="display: none">
                            <label class="col-xs-12 col-sm-4 control-label">选择门店：</label>
                            <div class="col-sm-3 col-xs-12">
                                <select class="form-control" name="store_id">
                                    <option value="">选择门店</option>
                                    @foreach($store as $value)
                                    <option value="{{$value['id']}}">{{$value['store_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-4 control-label">选择下单人：</label>
                            <div class="col-sm-3 col-xs-12">
                                <input type='hidden' id='uid' name='uid' value=""/>
                                <div class='input-group'>
                                    <input type="text" name="saler" maxlength="30" value="" id="saler" class="form-control" readonly/>
                                    <div class='input-group-btn'>
                                        <button class="btn btn-default" type="button" onclick="$('#modal-module-menus-notice').modal();">
                                            选择用户
                                        </button>
                                        <button class="btn btn-danger" type="button" onclick="$('#uid').val('');$('#saler').val('');$('#saleravatar').hide()">
                                            清除选择
                                        </button>
                                    </div>
                                </div>
                                <span id="saleravatar" class='help-block' style="display:none">
                                    <img style="width: 100px" src=""/></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-4 control-label"></label>
                            <div class="col-sm-3 col-xs-12">
                                <input type="submit" name="submit" value="下一步" class="btn btn-primary" >
                                {{--<input type="button" name="submit" value="下一步" class="btn btn-primary" onclick="jump_url()">--}}
                                <input type="hidden" name="token" value="{$_W['token']}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="modal-module-menus-notice" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                    ×
                </button>
                <h3>选择用户</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value=""
                               id="search-kwd-notice"
                               placeholder="请输入粉丝昵称/姓名/手机号"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default"
                                    onclick="search_members();">搜索
                            </button>
                        </span>
                    </div>
                </div>
                <div id="module-menus-notice"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</button>
            </div>
        </div>
    </div>
</div>
<script>

    $(':radio[name="order_type"]').click(function () {

        if ($(this).val() == '1') {
            $('#form1').attr('action', '{!! yzWebUrl('plugin.help-user-buying.admin.home.storeIndex') !!}');
            $('#store_list').show();
        } else {
            $('#form1').attr('action', '{!! yzWebUrl('plugin.help-user-buying.admin.home.shopIndex') !!}');
            $('#store_list').hide();
        }
    });


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
        $("#uid").val(o.uid);
        $("#saleravatar").show();
        $("#saleravatar").find('img').attr('src', o.avatar);
        $("#saler").val(o.nickname + "/" + o.realname + "/" + o.mobile);
        $("#modal-module-menus-notice .close").click();
    }


    function jump_url() {
        var uid = $("#uid").val();
        var store_id = $(':input[name="data[store_id]"]').val();
        if (store_id == '') {
            alert('请选择基地')
            return false;
        }
        if (uid == '') {
            alert('请选择会员')
            return false;
        }
        window.location.href="{!! yzWebUrl('plugin.help-user-buying.admin.home.index') !!}" + '&uid='+uid+'&store_id='+store_id;
    }
</script>
@endsection
