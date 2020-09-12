@extends('layouts.base')
@section('title', trans('添加直播间'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">添加直播间</a></li>
        </ul>
    </div>

    <div class='panel panel-default'>
        <div class="clearfix panel-heading">
            <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
               href="javascript:history.go(-1);">返回</a>
        </div>
    </div>

    <div class="w1200 m0a">
        <div class="rightlist">
            <form action="" method="post" class="form-horizontal form" onsubmit="return false;">

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">直播间名称</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input id="formName" name="name" type="text" class="form-control" value="" required />
                        <span class='help-block'>最短3个汉字，最长17个汉字</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">背景图</label>
                    <div class="col-md-9 col-sm-8 col-xs-12">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img') !!}
                        <span class="help-block">图片规则：建议像素1080 * 1920，大小不超过2M</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">开播时间</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        {!! tpl_form_field_date('start_time', date('Y-m-d H:i:s'), true) !!}
                        <span class="help-block">开播时间需要在当前时间的10分钟后 并且 开始时间不能在 6 个月后</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">结束时间</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        {!! tpl_form_field_date('end_time', date('Y-m-d H:i:s'), true) !!}
                        <span class="help-block">结束时间和开播时间间隔不得短于30分钟，不得超过24小时</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">主播昵称</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input id="formAnchorName" name="anchor_name" type="text" class="form-control" value="" required />
                        <span class='help-block'>最短2个汉字，最长15个汉字</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">主播微信号</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input id="formAnchorWechat" name="anchor_wechat" type="text" class="form-control" value="" required />
                        <span class='help-block'>请填写已实名认证的主播微信号</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">主播微信副号</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input name="sub_anchor_wechat" type="text" class="form-control" value="" />
                        <span class='help-block'>请填写已实名认证的主播微信号</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">分享图</label>
                    <div class="col-md-9 col-sm-8 col-xs-12">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('share_img') !!}
                        <span class="help-block">图片规则：建议像素800 * 640，大小不超过1M</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">购物直播频道封面图</label>
                    <div class="col-md-9 col-sm-8 col-xs-12">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('feeds_img') !!}
                        <span class="help-block">图片规则：建议像素800*800，大小不超过100KB</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">是否开启官方收录</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                            <input type="radio" name="is_feeds_public" value="1" /> 开启
                        </label>
                        <label class="radio radio-inline">
                            <input type="radio" name="is_feeds_public" value="0" checked /> 关闭
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">直播间类型</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                            <input type="radio" name="type" value="1" checked /> 推流
                        </label>
                        <label class="radio radio-inline">
                            <input type="radio" name="type" value="0" /> 手机直播
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">屏幕方向</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                            <input type="radio" name="screen_type" value="1" checked /> 横屏
                        </label>
                        <label class="radio radio-inline">
                            <input type="radio" name="screen_type" value="0" /> 竖屏
                        </label>
                        <span class="help-block">横屏：视频宽高比为16:9、4:3、1.85:1 ；竖屏：视频宽高比为9:16、2:3</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">是否关闭点赞</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                            <input type="radio" name="close_like" value="0" checked /> 开启
                        </label>
                        <label class="radio radio-inline">
                            <input type="radio" name="close_like" value="1" /> 关闭
                        </label>
                        <span class="help-block">若关闭，直播开始后不允许开启</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">是否关闭货架</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                            <input type="radio" name="close_goods" value="0" checked /> 开启
                        </label>
                        <label class="radio radio-inline">
                            <input type="radio" name="close_goods" value="1" /> 关闭
                        </label>
                        <span class="help-block">若关闭，直播开始后不允许开启</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">是否关闭评论</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                            <input type="radio" name="close_comment" value="0" checked /> 开启
                        </label>
                        <label class="radio radio-inline">
                            <input type="radio" name="close_comment" value="1" /> 关闭
                        </label>
                        <span class="help-block">若关闭，直播开始后不允许开启</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">是否关闭回放</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                            <input type="radio" name="close_repaly" value="0" /> 开启
                        </label>
                        <label class="radio radio-inline">
                            <input type="radio" name="close_repaly" value="1" checked /> 关闭
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">是否关闭分享</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                            <input type="radio" name="close_share" value="0" checked /> 开启
                        </label>
                        <label class="radio radio-inline">
                            <input type="radio" name="close_share" value="1" /> 关闭
                        </label>
                        <span class="help-block">直播开始后不允许修改</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">是否关闭客服</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                            <input type="radio" name="close_kf" value="0" /> 开启
                        </label>
                        <label class="radio radio-inline">
                            <input type="radio" name="close_kf" value="1" checked /> 关闭
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="submit" name="submit" value="提交" class="btn btn-success" id="submitLiveForm" />
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>

        var Page = {
            data: {
                name: "",
                coverImg: "",
                startTime: "",
                endTime: "",
                anchorName: "",
                anchorWechat: "",
                subAnchorWechat: "",
                shareImg: "",
                feedsImg: "",
                isFeedsPublic: 0,
                type: 1,
                screenType: 1,
                closeLike: 0,
                closeGoods: 0,
                closeComment: 0,
                closeReplay: 1,
                closeShare: 0,
                closeKf: 1,
            },
            init: function () {
                var that = this;

                // 点击提交表单
                $('#submitLiveForm').on('click', function () {

                    $('input[name="cover_img"]').attr('required', true);
                    $('input[name="cover_img"]').attr('id', 'formCoverImg');
                    $('input[name="start_time"]').attr('required', true);
                    $('input[name="end_time"]').attr('required', true);
                    $('input[name="share_img"]').attr('required', true);
                    $('input[name="share_img"]').attr('id', 'formShareImg');

                    var check = that.checkForm();
                    if (check) {

                        var submitBtn = $(this);
                        submitBtn.button('loading');

                        $.ajax({
                            url: "",
                            type: 'POST',
                            data: that.data,
                            success: function (res) {
                                submitBtn.button('reset');
                                var jump = "{!! yzWebUrl('plugin.appletslive.admin.controllers.live.index') !!}";
                                util.message(res.msg, res.result == 1 ? jump : '', res.result == 1 ? 'success' : 'info');
                            }
                        });
                    }
                });
            },
            checkForm: function () {
                var that = this;

                // 表单验证 - 直播间名称长度
                var name = $('input[name="name"]').val().trim();
                if (name.length < 3 || name.length > 17) {
                    Tip.focus('#formName');
                    return false;
                }
                that.data.name = name;

                // 表单验证 - 直播间背景图
                var coverImg = $('input[name="cover_img"]').val().trim();
                if (coverImg.length == 0) {
                    Tip.focus('#formCoverImg');
                    return false;
                }
                that.data.coverImg = coverImg;

                // 表单验证 - 直播开播时间
                var startTime = $('input[name="start_time"]').val().trim();
                if (startTime.length == 0) {
                    return false;
                }
                that.data.startTime = startTime;

                // 表单验证 - 直播结束时间
                var endTime = $('input[name="end_time"]').val().trim();
                if (endTime.length == 0) {
                    return false;
                }
                that.data.endTime = endTime;

                // 表单验证 - 主播昵称长度
                var anchorName = $('input[name="anchor_name"]').val().trim();
                if (anchorName.length < 2 || anchorName.length > 15) {
                    Tip.focus('#formAnchorName');
                    return false;
                }
                that.data.anchorName = anchorName;

                // 表单验证 - 主播微信号
                var anchorWechat = $('input[name="anchor_wechat"]').val().trim();
                if (anchorWechat.length == 0) {
                    Tip.focus('#formAnchorWechat');
                    return false;
                }
                that.data.anchorWechat = anchorWechat;

                // 主播微信副号
                that.data.subAnchorWechat = $('input[name="sub_anchor_wechat"]').val().trim();;

                // 表单验证 - 直播间分享图
                var shareImg = $('input[name="share_img"]').val().trim();
                if (shareImg.length == 0) {
                    Tip.focus('#formShareImg');
                    return false;
                }
                that.data.shareImg = shareImg;

                // 购物直播频道封面图
                that.data.feedsImg = $('input[name="feeds_img"]').val().trim();

                // 是否开启官方收录
                that.data.isFeedsPublic = $('input[name="is_feeds_public"]:checked').val();

                // 直播间类型
                that.data.type = $('input[name="type"]:checked').val();

                // 横屏|竖屏
                that.data.screenType = $('input[name="screen_type"]:checked').val();

                // 是否关闭点赞
                that.data.closeLike = $('input[name="close_like"]:checked').val();

                // 是否关闭货架
                that.data.closeGoods = $('input[name="close_goods"]:checked').val();

                // 是否关闭评论
                that.data.closeComment = $('input[name="close_comment"]:checked').val();

                // 是否关闭回放
                that.data.closeReplay = $('input[name="close_replay"]:checked').val();

                // 是否关闭分享
                that.data.closeShare = $('input[name="close_share"]:checked').val();

                // 是否关闭客服
                that.data.closeKf = $('input[name="close_kf"]:checked').val();

                return true;
            }
        };

        Page.init();

    </script>

@endsection
