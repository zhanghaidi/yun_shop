@extends('layouts.base')
@section('title', trans('主题|作业添加'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            @if($room['type']=='1')
                <li class="active"><a href="#">创建主题</a></li>
            @else
                <li class="active"><a href="#">添加作业</a></li>
            @endif
        </ul>
    </div>

    @if($room['type']=='1')

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
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">主题名称</label>
                        <div class="col-md-10 col-sm-9 col-xs-8">
                            <input name="name" type="text" class="form-control" value="" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">主题日期</label>
                        <div class="col-md-10 col-sm-9 col-xs-6">
                            {!! tpl_form_field_date('theme_time', date('Y-m-d', time()), false) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">主题封面图</label>
                        <div class="col-md-9 col-sm-9 col-xs-8 detail-logo">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img', '') !!}
                            <span class="help-block">图片比例 5:4，请按照规定尺寸上传</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">主题正文：图文</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            {!! yz_tpl_ueditor('text_desc', $info['text_desc']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">主题正文：视频</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldVideo('video_desc') !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">排序</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <input name="sort" type="number" class="form-control" value="0" placeholder="" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label"></label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <input type="hidden" name="rid" value="{{ $clock_id }}" />
                            <input type="hidden" name="type" value="{{ $room['type'] }}" />
                            <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    @endif

    @if($room['type']=='2')

        <div class='panel panel-default'>
            <div class='panel-body'>
                <div class="clearfix panel-heading" id="goodsTable">
                    <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                       href="javascript:history.go(-1);">返回</a>
                </div>
                <div class="w1200 m0a">
                    <div class="rightlist">
                        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

                            <div class="form-group">
                                <label class="col-md-2 col-sm-3 col-xs-12 control-label">作业名称</label>
                                <div class="col-md-10 col-sm-9 col-xs-12">
                                    <input name="name" type="text" class="form-control" value="" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 col-sm-3 col-xs-12 control-label">有效期：开始日期</label>
                                <div class="col-md-10 col-sm-9 col-xs-12">
                                    {!! tpl_form_field_date('start_time', date('Y-m-d'), false) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 col-sm-3 col-xs-12 control-label">有效期：结束日期</label>
                                <div class="col-md-10 col-sm-9 col-xs-12">
                                    {!! tpl_form_field_date('end_time', date('Y-m-d'), false) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 col-sm-3 col-xs-12 control-label">主题正文：图文</label>
                                <div class="col-md-10 col-sm-9 col-xs-12">
                                    {!! yz_tpl_ueditor('text_desc', $info['text_desc']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 col-sm-3 col-xs-12 control-label">主题正文：视频</label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    {!! app\common\helpers\ImageHelper::tplFormFieldVideo('video_desc') !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 col-sm-3 col-xs-12 control-label">排序</label>
                                <div class="col-md-10 col-sm-9 col-xs-12">
                                    <input name="sort" type="number" class="form-control" value="0" placeholder="" required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 col-sm-3 col-xs-12 control-label"></label>
                                <div class="col-md-10 col-sm-9 col-xs-12">
                                    <input type="hidden" name="rid" value="{{ $clock_id }}" />
                                    <input type="hidden" name="type" value="{{ $room['type'] }}" />
                                    <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>


            </div>
        </div>

        @include('Yunshop\Appletslive::admin.modals')

    @endif

    <script type="text/javascript">

        var Page = {
            data: {},
            init: function () {
                var that = this;

                // 查看直播间封面大图
                $('.show-cover-img-big').on('mouseover', function () {
                    $(this).find('.img-big').show();
                });
                $('.show-cover-img-big').on('mouseout', function () {
                    $(this).find('.img-big').hide();
                });

                // 监听表格中使用直播间按钮事件
                $('.btn-use-liveroom').on('click', function () {
                    var btnSureUseThisLiveroom = document.getElementById('sureUseThisLiveroom');
                    btnSureUseThisLiveroom.dataset.rid = $(this).attr('data-rid');
                    btnSureUseThisLiveroom.dataset.room_id = $(this).attr('data-room_id');
                    console.log('room id:', btnSureUseThisLiveroom.dataset.rid);
                    console.log('use room room_id:', btnSureUseThisLiveroom.dataset.room_id);
                });

                // 监听模态框确定使用直播间按钮事件
                $('#sureUseThisLiveroom').on('click', function () {
                    var btnSureUseThisLiveroom = document.getElementById('sureUseThisLiveroom');
                    that.useLiveroom(btnSureUseThisLiveroom.dataset.rid, btnSureUseThisLiveroom.dataset.room_id);
                });
            },
            useLiveroom: function (rid, room_id) {
                var data = {
                    rid: rid,
                    room_id: room_id,
                    type: 0,
                    cover_img: '',
                    media_url: '',
                    doctor: '',
                    minute: 0,
                    second: 0,
                    publish_time: '',
                    sort: 0,
                };

                $('#sureUseThisLiveroom').button('loading');

                $.ajax({
                    url: "",
                    type: 'POST',
                    data: data,
                    success: function (res) {
                        $('#sureUseThisLiveroom').button('reset');
                        if (res.result == 1) {
                            $('#modal-use-liveroom').find('a').trigger('click');
                        }
                        var jump = "{!! yzWebUrl('plugin.appletslive.admin.controllers.room.replaylist',['rid'=>$room['id']]) !!}";
                        util.message(res.msg, res.result == 1 ? jump : '', res.result == 1 ? 'success' : 'info');
                    }
                });
            }
        };

        Page.init();

    </script>

@endsection
