@extends('layouts.base')
@section('title', '编辑海报')
@section('content')
    <style type='text/css'>
        #poster {width:320px;height:504px;border:1px solid #ccc;position:relative}
        #poster .bg { position:absolute;width:100%;z-index:0}
        #poster .drag[type=img] img,#poster .drag[type=thumb] img { width:100%;height:100%; }
        #poster .drag { position: absolute; width:80px;height:80px; border:1px solid #000; }
        #poster .drag[type=nickname],#poster .drag[type=time] { width:80px;height:40px; font-size:16px; font-family: 黑体;}
        #poster .drag img {position:absolute;z-index:0;width:100%; }
        #poster .rRightDown,.rLeftDown,.rLeftUp,.rRightUp,.rRight,.rLeft,.rUp,.rDown{position:absolute; width:7px; height:7px; z-index:1; font-size:0;}
        #poster .rRightDown,.rLeftDown,.rLeftUp,.rRightUp,.rRight,.rLeft,.rUp,.rDown{background:#C00;}
        .rLeftDown,.rRightUp{cursor:ne-resize;}
        .rRightDown,.rLeftUp{cursor:nw-resize;}
        .rRight,.rLeft{cursor:e-resize;}
        .rUp,.rDown{cursor:n-resize;}
        .rLeftDown{left:-4px;bottom:-4px;}
        .rRightUp{right:-4px;top:-4px;}
        .rRightDown{right:-4px;bottom:-4px;}
        .rRightDown{background-color:#00F;}
        .rLeftUp{left:-4px;top:-4px;}
        .rRight{right:-4px;top:50%;margin-top:-4px;}
        .rLeft{left:-4px;top:50%;margin-top:-4px;}
        .rUp{top:-4px;left:50%;margin-left:-4px;}
        .rDown{bottom:-4px;left:50%;margin-left:-4px;}
        .context-menu-layer { z-index:9999;}
        .context-menu-list { z-index:9999;}
        .context-menu-root { z-index:9999;}
        .poster-edit-body{
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>

    <div class="w1200 m0a">
        <div class="rightlist">
            <form action="" method='post' class='form-horizontal'>

                <div class='panel panel-default'>

                    <ul class="add-shopnav" id="myTab">
                        <li class="active" ><a href="#tab_basicset">基本设置</a></li>
                        <li><a href="#tab_authset">权限设置</a></li>
                        <li><a href="#tab_responseset">推送设置</a></li>
                        <li><a href="#tab_awardset">奖励设置</a></li>
                        <li><a href="#tab_noticeset">通知设置</a></li>
                        <li><a href="#tab_commissionset">分销设置</a></li>
                    </ul>

                    <div class='panel-body'>

                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_basicset">@include('Yunshop\Poster::admin.basicset')</div>
                            <div class="tab-pane" id="tab_authset">@include('Yunshop\Poster::admin.authset')</div>
                            <div class="tab-pane" id="tab_responseset">@include('Yunshop\Poster::admin.responseset')</div>
                            <div class="tab-pane" id="tab_awardset">@include('Yunshop\Poster::admin.awardset')</div>
                            <div class="tab-pane" id="tab_noticeset">@include('Yunshop\Poster::admin.noticeset')</div>
                            <div class="tab-pane" id="tab_commissionset">@include('Yunshop\Poster::admin.commissionset')</div>
                        </div>

                        <div class="form-group"></div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"  />
                                <a href="{{yzWebUrl('plugin.poster.admin.poster.index')}}"><input type="button" name="back" style='margin-left:10px;' value="返回列表" class="btn btn-default" /></a>
                            </div>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection('content')

@section('js')
    <script type="text/javascript" src="{{ plugin_assets('poster', 'assets/js/edit.js') }}"></script>

    <script language='javascript'>
        function search_coupons() {
            $("#module-menus-coupon").html("正在搜索....");
            $.get('{!! yzWebUrl('coupon.coupon.get-search-coupons') !!}', {
                keyword: $.trim($('#search-kwd-coupons').val())
            }, function (dat) {
                $('#module-menus-coupon').html(dat);
            });
        }
    </script>
@endsection('js')