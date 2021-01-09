@extends('layouts.base')
@section('title','活码编辑')
@section('content')

    <link rel="stylesheet" href="{{ plugin_assets('activity', 'assets/css/activity.css') }}" />
    <script type="text/javascript" src="{{ plugin_assets('activity', 'assets/js/jquery.json.js') }}"></script>
    <script type="text/javascript" src="{{ plugin_assets('activity', 'assets/js/ueditor/ueditor.config.js') }}"></script>
    <script type="text/javascript" src="{{ plugin_assets('activity', 'assets/js/ueditor/ueditor.all.min.js') }}"></script>
    <script type="text/javascript" src="{{ plugin_assets('activity', 'assets/js/ueditor/ueditor.parse.js') }}"></script>
    <script type="text/javascript" src="{{ plugin_assets('activity', 'assets/js/ueditor/lang/zh-cn/zh-cn.js') }}"></script>
    <div class="rightlist" style="margin: 0;">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">添加文章</a></li>
            </ul>
        </div>

        <!-- 新增加右侧顶部三级菜单结束 -->
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <script type="text/javascript">
                var pathname = window.location.pathname;

                if (pathname == '/admin/shop') {
                    webroot = '/';
                } else {
                    webroot = '../addons/yun_shop/';
                }

                $(function(){
                    var pagestate = 0;
                    //初始化百度编辑器
                    var opts = {type: 'image',direct: false,multi: true,tabs: {'upload': 'active','browser': '','crawler': ''},path: '',dest_dir: '',global: false,thumb: false,width: 0};
                    var ue = UE.getEditor("editor", {
                        topOffset: 0,
                        autoFloatEnabled: false,
                        autoHeightEnabled: false,
                        autotypeset: {
                            removeEmptyline: true
                        },
                        maximumWords : 9999999999999,
                        initialFrameHeight: 607,
                        focus : true,
                        toolbars : [['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', '|','justifyleft', 'justifycenter', 'justifyright', '|', 'insertorderedlist', 'insertunorderedlist', 'blockquote', 'emotion', 'insertvideo', 'removeformat', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight','indent', 'paragraph', 'fontsize', '|','inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol','mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|', 'anchor', 'map', 'print', 'drafts', '|','autotypeset']],
                    });
                    ue.ready(function() {
                        ue.addListener('contentChange',function(){
                            $("#preview-content").html(ue.getContent());
                            pagestate = 1;
                        });
                        $(".itembox").click(function(a) {
                            ue.execCommand("insertHtml", "<div>" + $(this).html()+"<p></p>"+ "</div>");
                        });
                        $(".trash").click(function(){
                            if(confirm("确定要清空编辑器？此操作不可恢复。")){
                                ue.setContent("");
                            }
                        });
                        $(document).on("click",".mylink-nav",function(){
                            var href = $(this).data("href");
                            var id = $("#modal-mylink").attr("data-id");
                            if(id){
                                $("input[data-id="+id+"]").val(href);
                                $("#modal-mylink").attr("data-id","");
                            }else{
                                ue.execCommand('link', {href:href, 'class':'acolor'});
                            }
                            $("#modal-mylink .close").click();
                        });
                        $(".mylink-nav2").click(function(){
                            var href = $("textarea[name=mylink_href]").val();
                            if(href){
                                var id = $("#modal-mylink").attr("data-id");
                                if(id){
                                    $("input[data-id="+id+"]").val(href);
                                    $("#modal-mylink").attr("data-id","");
                                }else{
                                    ue.execCommand('link', {href:href, 'class':'acolor'});
                                }
                                $("#modal-mylink .close").click();
                                $("textarea[name=mylink_href]").val("");
                            }else{
                                $("textarea[name=mylink_href]").focus();
                                alert("链接不能为空!");
                            }
                        });
                    });

                    let uploadUrl = @php echo json_encode(uploadUrl()); @endphp

                    // 初始化调用微擎上传图片
                    UE.registerUI('myinsertimage',function(editor, uiName) {
                                editor.registerCommand(uiName, {
                                    execCommand: function() {
                                        require(['fileUploader'],
                                                function(uploader) {
                                                    uploader.upload_url(uploadUrl.upload_url);
                                                    uploader.image_url(uploadUrl.image_url);
                                                    uploader.fetch_url(uploadUrl.fetch_url);
                                                    uploader.delet_url(uploadUrl.delete_url);
                                                    uploader.show(function(imgs) {
                                                                if (imgs.length == 0) {
                                                                    return;
                                                                } else if (imgs.length == 1) {
                                                                    editor.execCommand('insertimage', {
                                                                        'src': imgs[0]['url'],
                                                                        '_src': imgs[0]['url'],
                                                                        'width': '100%',
                                                                        'alt': imgs[0].filename
                                                                    });
                                                                } else {
                                                                    var imglist = [];
                                                                    for (i in imgs) {
                                                                        imglist.push({
                                                                            'src': imgs[i]['url'],
                                                                            '_src': imgs[i]['url'],
                                                                            'width': '100%',
                                                                            'alt': imgs[i].filename
                                                                        });
                                                                    }
                                                                    editor.execCommand('insertimage', imglist);
                                                                }
                                                            },
                                                            opts);
                                                });
                                    }
                                });
                                var btn = new UE.ui.Button({
                                    name: '插入图片',
                                    title: '插入图片',
                                    cssRules: 'background-position: -726px -77px',
                                    onclick: function() {
                                        editor.execCommand(uiName);
                                    }
                                });
                                editor.addListener('selectionchange',
                                        function() {
                                            var state = editor.queryCommandState(uiName);
                                            if (state == -1) {
                                                btn.setDisabled(true);
                                                btn.setChecked(false);
                                            } else {
                                                btn.setDisabled(false);
                                                btn.setChecked(state);
                                            }
                                        });
                                return btn;
                            },
                            19);
                        // 初始化 系统链接选择
                    UE.registerUI('mylink', function(editor, uiName) {
                        editor.registerCommand(uiName, {
                            execCommand: function() {
                                $("#modal-mylink").modal();
                            }
                        });
                        var btn = new UE.ui.Button({
                            name: '超链接',
                            title: '超链接',
                            cssRules: 'background-position: -500px 0;',
                            onclick: function() {
                                editor.execCommand(uiName);
                            }
                        });
                        editor.addListener('selectionchange', function() {
                            var state = editor.queryCommandState(uiName);
                            if (state == -1) {
                                btn.setDisabled(true);
                                btn.setChecked(false);
                            } else {
                                btn.setDisabled(false);
                                btn.setChecked(state);
                            }
                        });
                        return btn;
                    });
                                            //初始化百度编辑器结束
                    // 大选项卡切换
                    $(".fart-editor-menu nav").click(function(){
                        var step = $(this).attr("step");
                        if(!step){
                            return;
                        }
                        $(this).addClass("navon").siblings().removeClass("navon");
                        $(".fart-editor-content[step="+step+"]").fadeIn().siblings().hide();
                    });
                    // 素材选项卡切换
                    $(".con2 .tab .nav").click(function(){
                        var n = $(this).attr("n");
                        $(this).addClass("navon").siblings().removeClass("navon");
                        $("#tabcon .con[n="+n+"]").fadeIn().siblings().hide();
                    });
                    $(".color").change(function(){
                        var color = $(this).val();
                        $(".itembox .tc").css("color",color);
                        $(".itembox .bc").css("background-color",color);
                        $(".itembox .bdc").css("border-color",color);
                        $(".itembox .blc").css("border-left-color",color);
                        $(".itembox .btc").css("border-top-color",color);
                        $(".itembox .bbc").css("border-bottom-color",color);
                        $(".itembox .brc").css("border-right-color",color);
                    });
                    // 监听 输入框 change
                    $("input").bind('input propertychange',function(){
                        pagestate = 1;
                        var bindint = $(this).attr("bind-in");
                        var bindinfo = !$(this).val()?$(this).attr("bind-de"):$(this).val();
                        if(parseInt(bindinfo) > 100000){
                            var bindinfo = '100000+';
                        }
                        $("*[bind-to="+bindint+"]").text(bindinfo);
                    });
                    $("select").change(function(){
                        pagestate = 1;
                    });
                    // 监听按钮是否显示商品
                    $(".product_advs_type").change(function(){
                        check = $(".product_advs_type:checked").val();
                        if(check!=0){
                            $(".product").show();
                        }else{
                            $(".product").hide();
                        }
                    });
                    // ajax 选择商品
                  /*  $("#select-good-btn").click(function(){
                        var kw = $("#select-good-kw").val();
                        $.ajax({
                            type: 'POST',
                            url: "{php echo $this->createPluginWebUrl('activity',array('method'=>'api','apido'=>'selectgoods'))}",
                            data: {kw:kw},
                            dataType:'json',
                            success: function(data){
                                //console.log(data);
                                $("#select-goods").html("");
                                if(data){
                                    $.each(data,function(n,value){
                                        var html = '<div class="good">';
                                        html+='<div class="img"><img src="'+value.thumb+'"/></div>'
                                        html+='<div class="choosebtn">';
                                        html+='<a href="javascript:;" class="mylink-nav" data-href="'+"{php echo $this->createMobileUrl('shop/detail')}&id="+value.id+'">详情链接</a><br>';
                                        if(value.hasoption==0){
                                            html+='<a href="javascript:;" class="mylink-nav" data-href="'+"{php echo $this->createMobileUrl('order/confirm')}&id="+value.id+'">下单链接</a>';
                                        }
                                        html+='</div>';
                                        html+='<div class="info">';
                                        html+='<div class="info-title">'+value.title+'</div>';
                                        html+='<div class="info-price">原价:￥'+value.productprice+' 现价￥'+value.marketprice+'</div>';
                                        html+='</div>'
                                        html+='</div>';
                                        $("#select-goods").append(html);
                                    });
                                }
                            }
                        });
                    });*/
                    // ajax 选择文章
                    $("#select-activity-btn").click(function(){
                        var category = $("#select-activity-ca option:selected").val();
                        var keyword = $("#select-activity-kw").val();
                        $.ajax({
                            type: 'POST',
                            url: "{php echo $this->createPluginWebUrl('activity',array('method'=>'api','apido'=>'selectactivitys'))}",
                            data: {category:category,keyword:keyword},
                            dataType:'json',
                            success: function(data){
                                //console.log(data);
                                $("#select-activitys").html("");
                                if(data){
                                    $.each(data,function(n,value){
                                        var html = '<div class="mylink-line">['+value.category_name+'] '+value.activity_title;
                                        html+='<div class="mylink-sub">';
                                        html+='<a href="javascript:;" class="mylink-nav" data-href="'+"{php echo $this->createPluginMobileUrl('activity')}&aid="+value.id+'">选择</a>';
                                        html+='</div></div>';
                                        $("#select-activitys").append(html);
                                    });
                                }
                            }
                        });
                    });
                    $("#nav_save").click(function(){
                        var content = ue.getContent();
                        $("#getContent").val(content);
                    });
                    // 离开页面未保存提示
                    /*$(window).bind('beforeunload',function(){
                     if(pagestate==1){
                     return '您输入的内容尚未保存，确定离开此页面吗？';
                     }
                     });*/

                    $(".nav-imgp").click(function(){
                        var id = $(this).data("id");
                        var imgurl = $("input[data-id="+id+"]").val();
                        if(imgurl){
                            $("#imgp").attr("src",imgurl);
                            $("#modal-imgp").modal();
                        }else{
                            alert("您还没选择图片哦！");
                        }
                    });
                    $(document).on("click",".nav-imgc",function(){
                        var id = $(this).data("id");
                        let util_name = "<?php if (config('app.framework') == 'platform') echo 'utils'; else echo 'util';?>";
                        require(['jquery', util_name], function($, util){
                            util.image('',function(data){
                                $("input[data-id="+id+"]").val(data.url);
                                $("img[data-id="+id+"]").attr("src",data.url);
                            });
                        });
                    });
                    $(document).on("click",".nav-link",function(){
                        var id = $(this).data("id");
                        if(id){
                            $("#modal-mylink").attr({"data-id":id});
                            $("#modal-mylink").modal();
                        }
                    });
                    $(document).on("click",".del",function(){
                        $(this).parent().remove();
                    });
                    $(".addbtn").click(function(){
                        var id = new Date().getTime();
                        var num = 0;
                        $("#advs .adv").each(function(){
                            num++;
                        });
                        if(num<5){
                            var html = '<div class="adv">';
                            html+='<div class="del">×</div>';
                            html+='<div class="img"><img src="' + webroot + 'plugin/activity/template/imgsrc/nochooseimg.jpg" data-id="PAI-'+id+'" /></div>';
                            html+='<div class="info">';
                            html+='<div class="input-group form-group" style="margin-top:5px; margin-bottom:0px; margin-right:5px;">';
                            html+='<span class="input-group-addon">广告图片</span>';
                            html+='<input type="text" name="activity[advs][' + num + '][img]" class="form-control post-adv-img" placeholder="推广广告图，可直接输入或者选择系统图片 (请以http://开头)" data-id="PAI-'+id+'">';
                            html+='<span class="input-group-addon btn btn-default nav-imgc" style="background: #fff;" data-id="PAI-'+id+'">选择图片</span>';
                            html+='</div>';
                            html+='<div class="input-group form-group" style="margin-top:10px; margin-bottom:0px; margin-right:5px;">';
                            html+='<span class="input-group-addon">广告链接</span>';
                            html+='<input type="text" name="activity[advs][' + num + '][url]" class="form-control post-adv-link" placeholder="推广广告链接，可直接输入或者选择系统连接 (请以http://开头，单规格商品可直接下单)" data-id="PAL-'+id+'" value="1">';
                            html+='<span class="input-group-addon btn btn-default nav-link" style="background: #fff;" data-id="PAL-'+id+'">选择链接</span>';
                            html+='</div></div></div>';
                            $("#advs").append(html);
                        }else{
                            alert("组多添加5张广告图! ");
                        }
                    });
                    $('.chkall').click(function () {
                        var checked = $(this).get(0).checked;
                        if (checked) {
                            $(this).closest('div').find(':checkbox[class!="chkall"]').removeAttr('checked');
                        }
                    });
                    $('.chksingle').click(function () {
                        $(this).closest('div').find(':checkbox[class="chkall"]').removeAttr('checked');
                    })
                });
            </script>

            <!-- 文章头部 -->
            <div class="page-heading">
                <span class="pull-right">
                    <input type="submit" value="保存文章" class="btn btn-primary btn-sm" id="nav_save">
                    <input type="hidden" value="" name="activity[content]" id="getContent">
                    <a class="btn btn-default  btn-sm" onclick="confirm('您输入的内容尚未保存，确定离开此页面吗？');history.back()">返回列表</a>
                </span>
                {{--<h2>添加文章 <small></small></h2>--}}
            </div>
            <div id="modal-imgp" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                            <h4>图片预览</h4>
                        </div>
                        <div class="modal-body">
                            <img src="" id="imgp" style="width:100%;" />
                        </div>
                    </div>
                </div>
            </div>
            <!-- mylink start -->
            @include('Yunshop\activity::admin.mylink')
            <!-- mylink end -->

            <div class="fart-main">
                <!--左侧预览页面-->
                <div class="fart-preview">
                    <div class="top"><p bind-to="art_title"> @if ($activity['id']){{ $activity['title'] }} @else 这里是文章标题 @endif</p></div>
                    <div class="fart-rich-primary">
                        <div class="fart-rich-title" bind-to="art_title"> @if($activity['id']){{ $activity['title'] }}@else 这里是文章标题 @endif</div>
                        <div class="fart-rich-mate">
                            <div class="fart-rich-mate-text" bind-to="art_date_v"> @if ($activity['id']){{ date('Y-m-d',$activity['virtual_create_at']) }} @else {{ date('Y-m-d') }} @endif</div>
                            <div class="fart-rich-mate-text" bind-to="art_author"> @if ($activity['id']){{ $activity['author'] }} @else 编辑小芸 @endif</div>
                            <div class="fart-rich-mate-text href" bind-to="art_mp"> @if ($activity['id']){{ $activity['uniacid'] }} @else  测试公众号 @endif</div>
                        </div>
                        <div class="fart-rich-content" id="preview-content">
                            {!! htmlspecialchars_decode($activity['content']) !!}
                        </div>
                        <div class="fart-rich-tool">
                            <div class="fart-rich-tool-text link">阅读原文</div>
                            <div class="fart-rich-tool-text" bind-to="art_read">阅读 @if ($activity['id']) @if ($activity['virtual_read_num']>100000) 100000+ @else {{ $activity['virtual_read_num'] }} @endif @else 100000+ @endif</div>
                            <div class="fart-rich-tool-text">
                                <div class="fart-rich-tool-like"></div>
                                <span bind-to="art_like">@if ($activity['id']) @if ($activity['virtual_like_num']>100000) 100000+ @else {{ $activity['virtual_like_num'] }} @endif @else 54321 @endif</span>
                            </div>
                            <div class="fart-rich-tool-text right">举报</div>
                        </div>
                    </div>
                    <div class="fart-rich-sift product" @if($activity['advs_type']!=0)style="display:block;"@endif>
                        <div class="fart-rich-sift-line">
                            <div class="fart-rich-sift-border"></div>
                            {{--下面代码需要重新审核 todo: aid未知--}}
                            <div class="fart-rich-sift-text"><a bind-to="product_adv_title">@if($aid){{$activity['advs_title']}}@else精品推荐@endif</a></div>
                        </div>
                        <div class="fart-rich-sift-img"><img src="{{resource_get('plugin/activity/template/imgsrc/img01.jpg', 1)}}"></div>
                        <div class="fart-rich-sift-more" bind-to="product_adv_more">@if($aid){{$activity['advs_more']}}@else更多精品@endif</div>
                    </div>
                </div>
                <!--end 左侧预览页面-->

                <!--右侧编辑页面-->
                <div class="fart-editor" style="height: auto;">
                    <div class="fart-editor-menu">
                        <nav step="1" class="navon">① 编辑文章内容</nav>
                        <nav step="2" id="nav-step-2">② 设置文章及页面信息</nav>
                        <nav step="3">③ 设置营销内容</nav>
                    </div>
                    <div id="fart-editor-content">
                        <div class="fart-editor-content" step="2" style="height: auto"><!--设置文章及页面信息-->
                            <div class="fart-form">
                                <form>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">文章标题</span>
                                            <input type="text" name="activity[title]" class="form-control judge" value="{{ $activity['title'] }}" placeholder="请填写文章标题 (30个汉字以内)" bind-in="art_title" bind-de="这里是文章标题">
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">文章类型</span>
                                            <div class="form-control">&nbsp;
                                                <label for="" class="radio-inline"><input type="radio" name="activity[type]" value="0" id="" @if ($activity['type'] == 0) checked="true" @endif> 普通文章</label>&nbsp;&nbsp;&nbsp;
                                                <label for="" class="radio-inline"><input type="radio" name="activity[type]" value="1" id="" @if ($activity['type'] == 1) checked="true" @endif> 音频文章</label>
                                            </div>
                                        </div>
                                    </div>
                                    <span>注:如是音频类型文章则从装修页面可以选择链接的地方选择音频文章入口链接</span>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">音频链接</span>
                                            <input type="text" name="activity[audio_link]'" class="form-control judge" value="{{ $activity['audio_link'] }}">
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">文章排序</span>
                                            <input type="text" name="activity[display_order]'" class="form-control judge" value="{{ $activity['display_order']?:0 }}">
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">文章分类</span>
                                            <select class="form-control tpl-category-parent" name="activity[category_id]" id="select1">
                                                <option value="0">请选择文章分类</option>
                                                @foreach ($categorys as $category)
                                                <option value="{{ $category['id'] }}" @if ($activity['category_id'] == $category['id'])  selected="selected" @endif>{{ $category['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">发布作者</span>
                                            <input type="text" name="activity[author]" class="form-control" value="{{ $activity['author'] }} " placeholder="请填写发布作者 (不填则不显示)" bind-in="art_author" bind-de="编辑小芸">
                                        </div>
                                    </div>

                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">页面设置</span>
                                            <div class="form-control" style="height: auto">
                                                <label for="page_set_option2" class="checkbox-inline"><input type="checkbox" name="activity[no_copy_url]" value="1" id="page_set_option2" @if ($activity['no_copy_url'] == '1') checked="checked" @endif> 禁止复制链接</label>
                                                <label for="page_set_option3" class="checkbox-inline"><input type="checkbox" name="activity[no_share]" value="1" id="page_set_option3" @if ($activity['no_share'] == '1') checked="checked" @endif> 禁止分享至朋友圈</label>
                                                <label for="page_set_option1" class="checkbox-inline"><input type="checkbox" name="activity[no_share_to_friend]" value="1" id="page_set_option1" @if ($activity['no_share_to_friend'] == '1') checked="checked" @endif> 禁止分享给好友</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">举报按钮</span>
                                            <div class="form-control">
                                                <label for="activity_report1" class="radio-inline"><input type="radio" name="activity[report_enabled]" value="1" id="activity_report1" @if ($activity['report_enabled'] == 1) checked="true" @endif> 模拟举报(使用有风险)</label>&nbsp;&nbsp;&nbsp;
                                                <label for="activity_report0" class="radio-inline"><input type="radio" name="activity[report_enabled]" value="0" id="activity_report0" @if ($activity['report_enabled'] == 0) checked="true" @endif> 不显示</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">是否开启</span>
                                            <div class="form-control">
                                                <label for="activity_state_1" class="radio-inline"><input type="radio" name="activity[state]" value="1" id="activity_state_1" @if ($activity['id'] && $activity['state'] == 1) checked="true" @endif> 开启</label>
                                                <label for="activity_state_0" class="radio-inline"><input type="radio" name="activity[state]" value="0" id="activity_state_0" @if (!$activity['id'] || $activity['state'] == 0) checked="true" @endif> 关闭</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">页面关键字</span>
                                            <input type="text" name="activity[keyword]" class="form-control judge" value="{{ $activity['keyword'] }}" placeholder="页面关键字">
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">虚拟阅读数量</span>
                                            <input type="number" name="activity[virtual_read_num]" class="form-control judge" value="{{  $activity['virtual_read_num'] }}" placeholder="页面阅读量 = 真实阅读量 + 虚拟阅读量" bind-in="art_read" bind-de="100000+">
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">虚拟点赞数量</span>
                                            <input type="number" name="activity[virtual_like_num]" class="form-control judge" value="{{  $activity['virtual_like_num'] }}" placeholder="页面点赞数 = 真实点赞数 + 虚拟点赞数" bind-in="art_like" bind-de="54321">
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">虚拟发布时间</span>
                                            <input type="text" name="activity[virtual_created_at]" class="form-control judge" style="padding-left: 12px; " value="@if ($activity['id'] && !empty($activity['virtual_created_at'])) {{ date('Y-m-d', $activity['virtual_created_at']) }} @else {{ date('Y-m-d') }} @endif" placeholder="虚拟发布时间 (格式: {{ date('Y-m-d') }})" bind-in="art_date_v" bind-de="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">阅读原文链接</span>
                                            <input type="text" name="activity[link]"  class="form-control" value="{{  $activity['link'] }}" placeholder="请填写阅读原文指向的链接 (请以http://开头, 不填则不显示)" data-id="PAL-00010">
                                            <span class="input-group-addon btn btn-default nav-link" style="background: #fff;" data-id="PAL-00010">选择链接</span>
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">文章介绍(封面)</span>
                                            <input type="text" name="activity[desc]" class="form-control" value="{{ $activity['desc'] }}" placeholder="文章介绍(封面 50字以内)">
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">文章图片(封面)</span>
                                            <input type="text" name="activity[thumb]" class="form-control" value="{{ $activity['thumb'] }}" data-id="resp_img">
                                            <span class="input-group-addon btn nav-imgp" style="border-left: 0px; cursor: pointer;" data-id="resp_img">预览图片</span>
                                            <span class="input-group-addon btn btn-default nav-imgc" style="background: #fff;" data-id="resp_img">选择图片</span>
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">会员等级浏览权限</span>
                                            <div class="form-control" style="height: auto">
                                                    <label class="checkbox-inline" style="margin-left: 10px">
                                                        <input type="checkbox" class='chkall' name="activity[show_levels]" value="" @if ( $activity['show_levels']=='') checked="true" @endif  /> 全部会员等级
                                                    </label>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class='chksingle' name="activity[show_levels][]" value="0" @if ( $activity['show_levels'] != '' && is_array($activity['show_levels']) && in_array('0', $activity['show_levels'])) checked="true" @endif  />  普通等级
                                                    </label>
                                                @foreach ( $levels as $level)
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" class='chksingle' name="activity[show_levels][]" value="{{ $level['id'] }}" @if ( $activity['show_levels'] != '' && is_array($activity['show_levels'])  && in_array($level['id'], $activity['show_levels'])) checked="true" @endif  /> {{ $level['level_name'] }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                        <div class="fart-editor-content" step="1" style="display: block;"><!--编辑文章内容-->
                            <div class="menu">
                                <div class="nav1" style="width:100% !important;">富文本编辑器
                                    <div class="trash" title="清空编辑器内容"><i class="fa fa-trash-o"></i></div>
                                </div>
                            </div>
                            <div class="content">
                                <div class="con1" style="width:100% !important;">
                                    <textarea id="editor" style="width:100%;">{!! htmlspecialchars_decode($activity['content']) !!}</textarea>
                                    {{--<script id="editor" style="width:100%;">{!! htmlspecialchars_decode($activity['content']) !!}</script>--}}
                                </div>
                            </div>
                        </div>
                        <div class="fart-editor-content" step="3" style="overflow-y: auto;"><!--设置营销内容-->
                            <div class="fart-form">
                                <div class="line">
                                    <div class="line2" style="margin-right: 10px;">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">奖励规则&nbsp;&nbsp;&nbsp;&nbsp;每人每天奖励</span>
                                            <input type="text" name="activity[per_person_per_day]" style="width: 60px" class="form-control judge" value="{{ $activity['per_person_per_day'] }}">
                                            <span class="input-group-addon" style="border-left: 0px; border-right: 0px;">次&nbsp;&nbsp;每人总共奖励</span>
                                            <input type="text" name="activity[total_per_person]" style="width: 60px" class="form-control judge" value="{{ $activity['total_per_person'] }}" >
                                            <span class="input-group-addon" style="border-left: 0px;">次</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="line">
                                    <div class="line2" >
                                        <div class="input-group form-group">
                                            <span class="input-group-addon" style="border-right: 0px;">每分享1次可获得</span>
                                            <input type="text" name="activity[point]" style="width: 90px" class="form-control judge" value="{{ $activity['point'] }}" >
                                            <span class="input-group-addon" style="border-left: 0px; border-right: 0px;">个积分</span>
                                            <input type="text" name="activity[credit]" style="width: 115px" class="form-control judge" value="{{ $activity['credit'] }}">
                                            <span class="input-group-addon">元余额</span>
                                        </div>
                                        <div class="input-group form-group" >
                                            <span class="input-group-addon">奖励方式</span>
                                            <div class="form-control" style="width: 390px">
                                                <label class="radio-inline" style="padding: 0 60px;">
                                                    <input type="radio" name="activity[reward_mode]" value="0" @if ($activity['reward_mode'] == 0) checked="true" @endif >按次
                                                </label>
                                                <label class="radio-inline" style="padding-top: 0;">
                                                    <input type="radio" name="activity[reward_mode]" value="1" @if ($activity['reward_mode'] == 1) checked="true" @endif>按天
                                                </label>
                                            </div>
                                        </div>
                                        <div class="input-group form-group">
                                            <span class="input-group-addon" style="border-right: 0px;">最高累计奖金</span>
                                            <input type="text" name="activity[bonus_total]" style="width: 180px"  class="form-control" value="{{ $activity['bonus_total'] }}" placeholder="">
                                            <span class="input-group-addon" style="border-left: 0px; ">元&nbsp;&nbsp;(截至目前已奖励 @if($bonus_sum){{$bonus_sum}}@else 0 @endif元)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="line">
                                    <span class='help-block'>奖励规则提示：分享后，好友点击进入后，才算成功分享一次。
                                        <br>
                                        奖励方式：1，按次，一篇文章同一个浏览者只奖励分享者一次积分/余额（只奖励首次浏览）。
                                        <br>
                                        2，按天，一篇文章同一个浏览者每天点击浏览，每天奖励分享者一次积分/余额（只奖励每天首次浏览）。
                                    </span>
                                </div>

                                <div class="line">
                                    <div class="input-group form-group">
                                        <span class="input-group-addon">推广产品显示设置</span>
                                        <div class="form-control">&nbsp;
                                            <label for="product_advs_type1" class="radio-inline"><input type="radio" class="product_advs_type" name="activity[advs_type]" value="0" id="product_advs_type1" @if ($activity['advs_type'] == 0) checked="true" @endif> 关闭此功能</label>&nbsp;&nbsp;&nbsp;
                                            <label for="product_advs_type2" class="radio-inline"><input type="radio" class="product_advs_type" name="activity[advs_type]" value="1" id="product_advs_type2" @if ($activity['advs_type'] == 1) checked="true" @endif> 启用此功能</label>&nbsp;&nbsp;&nbsp;
                                            {{--<label for="product_advs_type3" class="radio-inline"><input type="radio" class="product_advs_type" name="activity[advs_type]" value="2" id="product_advs_type3" @if ($activity['advs_type'] == 2) checked="true" @endif> 随机显示</label>&nbsp;&nbsp;&nbsp;--}}
                                            {{--<label for="product_advs_type4" class="radio-inline"><input type="radio" class="product_advs_type" name="activity[advs_type]" value="3" id="product_advs_type4" @if ($activity['advs_type'] == 3) checked="true" @endif> 轮播显示</label>--}}
                                        </div>
                                    </div>
                                </div>
                                <div class="product" style="display:block;" >
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">推广产品标题</span>
                                            <input type="text" name="activity[advs_title]"  class="form-control" value="{{ $activity['advs_title'] }}" placeholder="推广产品标题，不填则不显示标题" bind-in="product_adv_title" bind-de="精品推荐">
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">推广产品底部文字</span>
                                            <input type="text" name="activity[advs_title_footer]"  class="form-control" value="{{ $activity['advs_title_footer'] }}" placeholder="推广产品底部文字，不填则不显示" bind-in="product_adv_more" bind-de="更多精彩">
                                        </div>
                                    </div>
                                    <div class="line">
                                        <div class="input-group form-group">
                                            <span class="input-group-addon">推广产品底部链接</span>
                                            <input type="text" name="activity[advs_link]"  class="form-control" value="{{ $activity['advs_link'] }}" placeholder="推广产品底部文字链接，可直接输入或者选择系统连接 (请以http://开头)" data-id="PAL-00000">
                                            <span class="input-group-addon btn btn-default nav-link" style="background: #fff;" data-id="PAL-00000">选择链接</span>
                                        </div>
                                    </div>
                                    <div class="input-group form-group">
                                        <span class="input-group-addon">推广产品图片</span>
                                        {!!tpl_form_field_image('activity[advs_img]',$activity['advs_img'])!!}
                                        <span class='help-block'>推广产品图片，建议尺寸：380*130</span>
                                    </div>
                                </div>
                                {{--
                                <div class="advs">
                                    <div id="advs">
                                        @if (!empty($activity['advs']))
                                        @foreach ($activity['advs'] as $i => $adv)
                                        <div class="adv">
                                            <div class="del">×</div>
                                            <div class="img"><img src="@if (empty($adv['img']))../addons/sz_yi/plugin/activity/template/imgsrc/nochooseimg.jpg @else {{  $adv['img'] }} @endif" data-id="PAI-{{ time()+$i+1 }}" /></div>
                                            <div class="info">
                                                <div class="input-group form-group" style="margin-top:5px; margin-bottom:0px; margin-right:5px;">
                                                    <span class="input-group-addon">广告图片</span>
                                                    <input type="text" name="activity[advs][{{ $i }}][img]" class="form-control post-adv-img" placeholder="推广广告图，可直接输入或者选择系统图片 (请以http://开头)" value="{{  $adv['img'] }}" data-id="PAI-{{ time()+$i+1 }}">
                                                    <span class="input-group-addon btn btn-default nav-imgc" style="background: #fff;" data-id="PAI-{{ time()+$i+1 }}">选择图片</span>
                                                </div>
                                                <div class="input-group form-group" style="margin-top:10px; margin-bottom:0px; margin-right:5px;">
                                                    <span class="input-group-addon">广告链接</span>
                                                    <input type="text" name="activity[advs][{{ $i }}][url]" class="form-control post-adv-link" placeholder="推广广告链接，可直接输入或者选择系统连接 (请以http://开头，单规格商品可直接下单)" value="{{ $adv['url'] }}" data-id="PAL-{{ time()+$i+1 }}" >
                                                    <span class="input-group-addon btn btn-default nav-link" style="background: #fff;" data-id="PAL-{{ time()+$i+1 }}">选择链接</span>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        @endif
                                    </div>
                                    <div class="addbtn"><i class="fa fa-plus"></i> 添加一个</div>
                                </div>
                                --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!--end 右侧编辑页面-->
            </div>

        </form>
    </div>

@endsection
