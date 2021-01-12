@extends('layouts.base')
@section('content')

    <div class='panel panel-default'>
        <div class="clearfix panel-heading">
            <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
               href="javascript:history.go(-1);">返回</a>
        </div>
    </div>

    <div class="w1200 m0a">
        <div class="rightlist">
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">活码名称</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <input name="info[activity_name]" type="text" class="form-control" value="{{ $info['activity_name'] }}" placeholder="请输入活码名称" required/>
                    </div>
                </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">活码标题</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="info[title]" type="text" class="form-control" value="{{ $info['title'] }}" placeholder="请输入活码标题" required/>
                        </div>
                    </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">顶部图标</label>
                    <div class="col-sm-9 col-xs-12 col-md-6">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('info[logo]', $info['logo']) !!}
                        <span class="help-block">活码图标 20X20</span>
                    </div>
                </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">顶部描述</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="info[description_top]" type="text" class="form-control" value="{{ $info['description_top'] }}" placeholder="顶部描述" required/>
                        </div>
                    </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">底部引导文字</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <input name="info[description_bottom]" type="text" class="form-control" value="{{ $info['description_bottom'] }}" placeholder="底部引导文字" required/>
                    </div>
                </div>

               {{-- <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">活码切换方式</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <label class="radio-inline">
                            <input type="radio" name="info[switch_type]" value="1" @if($info['switch_type'] == 1) checked="checked" @endif />平均切换
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="info[switch_type]" value="0" @if($info['switch_type'] == 0) checked="checked" @endif />满员切换
                        </label>
                    </div>
                </div>--}}


                {{--<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">启用/关闭</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <label class="radio-inline">
                            <input type="radio" name="info[status]" value="0" @if($info['status'] == 0) checked="checked" @endif />关闭
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="info[status]" value="1" @if($info['status'] == 1) checked="checked" @endif />开启
                        </label>
                    </div>
                </div>--}}



                    {{--<div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">客服微信</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="helper_nickname" type="text" class="form-control" value="{{ $info['helper_nickname'] }}" required/>
                            <span class="help-block">助手设置，助手名称</span>
                        </div>
                    </div>--}}
                    {{--<div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">客服二维码</label>
                        <div class="col-sm-9 col-xs-12 col-md-6">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('helper_avatar', $info['helper_avatar']) !!}
                            <span class="help-block">助手设置，助手二维码</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">助手微信</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="helper_wechat" type="text" class="form-control" value="{{ $info['helper_wechat'] }}" required/>
                            <span class="help-block">助手设置，助手微信</span>
                        </div>
                    </div>--}}
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">分享域名</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <input name="info[share_domain]" type="text" class="form-control" value="{{ $info['share_domain'] }}" required/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">分享标题</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <input name="info[share_title]" type="text" class="form-control" value="{{ $info['share_title'] }}" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">分享描述</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <input name="info[share_description]" type="text" class="form-control" value="{{ $info['share_description'] }}" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">分享图片</label>
                    <div class="col-sm-9 col-xs-12 col-md-6">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('info[share_img]', $info['share_img']) !!}
                        <span class="help-block">分享图片 5:4</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{--搜索课程的弹窗--}}
    <div id="modal-module-menus-goods" class="modal fade" tabindex="-1">
        <div class="modal-dialog" style='width: 920px;'>
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>选择课程</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="input-group">
                            <input type="text" class="form-control" name="keyword" value="" id="search-kwd-goods"
                                   placeholder="请输入课程名称"/>
                            <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_goods();">搜索</button>
                        </span>
                        </div>
                    </div>
                    <div id="module-menus-goods" style="padding-top:5px;"></div>
                </div>
                <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal"
                                             aria-hidden="true">关闭</a>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var ueditoroption = {
            'toolbars': [['source', 'preview', '|', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', '|',
                'justifyleft', 'justifycenter', 'justifyright', '|', 'insertorderedlist', 'insertunorderedlist', 'blockquote', 'emotion',
                'link', 'removeformat', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight', 'indent', 'paragraph', 'fontsize', '|',
                'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol',
                'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|', 'anchor', 'map', 'print', 'drafts']],
        };

        {{--搜索商品--}}
        function search_goods() {
            if ($.trim($('#search-kwd-goods').val()) == '') {
                Tip.focus('#search-kwd-goods', '请输入关键词');
                return;
            }
            $("#module-menus-goods").html("正在搜索....");
            $.get('{!! yzWebUrl('plugin.xiaoe-clock.admin.clock.get_search_course') !!}', {
                    keyword: $.trim($('#search-kwd-goods').val())
                }, function (dat) {
                    $('#module-menus-goods').html(dat);
                }
            );
        }

        function select_good(o) {
            $("input[name=course_id]").val(o.id);
            $("#course_name").val(o.title);
            $("#modal-module-menus-goods .close").click();
        }

        function clearGoods() {
            $("input[name=course_id]").val('');
            $("#course_name").val('');
        }

        $('input[name=join_type]').change(function () {
            // console.log($(this).val())
            if ($(this).val() == 1) {
                $('.goods-div').show();
            } else {
                $('.goods-div').hide();
            }
        })

    </script>

@endsection
