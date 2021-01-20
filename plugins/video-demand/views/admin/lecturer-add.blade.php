@extends('layouts.base')

@section('content')

@section('title', trans('添加讲师'))

<style>
	select{width: 25%; height: 34px;}
	#saleravatar img{width: 200px; height: 200px;}
</style>

<div class="w1200 ">
    <div class=" rightlist ">
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">添加讲师</a></li>
            </ul>
        </div>

	    <div class="right-addbox"><!-- 此处是右侧内容新包一层div -->
                <div class="panel panel-default">
                    <div class="panel-body">
				        <form id="" action="" method="post" class="form-horizontal form">
				            <div class="info">
				                <div class="panel-body">
				
				                    <div class="form-group notice">
				                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信角色</label>
				                        <div class="col-xs-6">
				                            <input type='hidden' id='uid' name='lecturer[member_id]' value=""/>
				                            <div class='input-group'>
				                                <input type="text" name="saler" maxlength="30"
				                                       value="" id="saler" class="form-control" readonly/>
				                                <div class='input-group-btn'>
				                                    <button class="btn btn-default" type="button"
				                                            onclick="popwin = $('#modal-module-menus-notice').modal();">选择角色
				                                    </button>
				                                    <button class="btn btn-danger" type="button"
				                                            onclick="$('#uid').val('');$('#saler').val('');$('#saleravatar').hide()">
				                                        清除选择
				                                    </button>
				                                </div>
				                            </div>
				                            <span id="saleravatar" class='help-block' style="display:none">
				                            <img style="" src=""/></span>
				
				                            <div id="modal-module-menus-notice" class="modal fade" tabindex="-1">
				                                <div class="modal-dialog">
				                                    <div class="modal-content">
				                                        <div class="modal-header">
				                                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
				                                                ×
				                                            </button>
				                                            <h3>选择角色</h3>
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
				                                            <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
				                                        </div>
				                                    </div>
				                                </div>
				                            </div>
				                        </div>
				                    </div>
				
				                    <div class="form-group">
				                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">真实姓名</label>
				                        <div class="col-xs-6">
				                            <input type="text" name="lecturer[real_name]" class="form-control"
				                                   value=""/>
				                        </div>
				                    </div>
				
				                    <div class="form-group">
				                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">联系方式</label>
				                        <div class="col-xs-6">
				                            <input type="text" name="lecturer[mobile]" class="form-control"
				                                   value=""/>
				                        </div>
				                    </div>

				
				
				                    <div class="form-group">
				                    	<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
				                    	<div class="col-xs-6">
				                    		<input type="submit" name="submit" value="提交" class="btn btn-success"
				                               onclick="return formcheck()"/>
				                    	</div> 
				                    </div>
				                </div>
				            </div>
				
				        </form>
	    			</div>
	    		</div>
	    	</div>
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
            $("#uid").val(o.uid);
            $("#saleravatar").show();
            $("#saleravatar").find('img').attr('src', o.avatar);
            $("#saler").val(o.nickname + "/" + o.realname + "/" + o.mobile);
            $("#modal-module-menus-notice .close").click();
        }

    </script>
@endsection

