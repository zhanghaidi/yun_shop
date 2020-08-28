@extends('layouts.base')
@section('title', trans('视频设置'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">添加录播视频</a></li>
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
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

                @if($room['type']=='1')

                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">标题</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <input name="title" type="text" class="form-control" value="" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">类型</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <select name="type" class="form-control">
                                <option value="1" selected>本地上传</option>
                                <option value="2">腾讯视频</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">链接地址</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldVideo('media_url') !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">主讲人</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <input name="doctor" type="text" class="form-control" value="" placeholder="艾居益" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">视频时长</label>
                        <div class="col-md-10 col-sm-9 col-xs-12 form-inline">
                            <div class="input-group form-group col-sm-3" style="padding: 0">
                                <input type="number" name="minute" class="form-control" value="0" required />
                                <span class="input-group-addon">分钟</span>
                            </div>
                            <div class="input-group form-group col-sm-3" style="padding: 0">
                                <input type="number" name="second" class="form-control" value="0" required />
                                <span class="input-group-addon">秒</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">发布时间</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            {!! tpl_form_field_date('publish_time', date('Y-m-d H:i', time()), true) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">预览图片</label>
                        <div class="col-md-9 col-sm-9 col-xs-12 detail-logo">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img', '') !!}
                            <span class="help-block">图片比例 5:4，请按照规定尺寸上传</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">内容提示</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <textarea name="intro" rows="5" class="form-control"></textarea>
                        </div>
                    </div>

                @endif

                @if($room['type']=='2')

                    <div class="form-group">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">直播间</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            <select id="sltRoomId" name="room_id" class="form-control">
                                <option value="">请选择直播间</option>
                                @foreach($liverooms as $liveroom)
                                    <option value="{{ $liveroom->id }}" data-name="{{ $liveroom->name  }}" data-cover_img="{{ $liveroom->cover_img }}"
                                    data-live_status="{{ $liveroom->live_status  }}" data-anchor_name="{{ $liveroom->anchor_name }}"
                                    data-start_time="{{ date('Y-m-d H:i:s', $liveroom->start_time) }}"
                                    data-end_time="{{ date('Y-m-d H:i:s', $liveroom->end_time) }}" >{{ $liveroom->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <input name="title" type="hidden" value="" />
                    <input name="type" type="hidden" value="0" />
                    <input name="cover_img" type="hidden" value="" />
                    <input name="media_url" type="hidden" value="" />
                    <input name="doctor" type="hidden" value="" />
                    <input name="minute" type="hidden" value="0" />
                    <input name="second" type="hidden" value="0" />
                    <input name="publish_time" type="hidden" value="" />

                    <div class="form-group fg-showhide" style="display:none;">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">标题</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            @foreach($liverooms as $liveroom)
                                <span class="form-control fc-showhide fc-{{ $liveroom->id }}">{{ $liveroom->name }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group fg-showhide" style="display:none;">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">预览</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            @foreach($liverooms as $liveroom)
                                <div class="input-group fc-showhide fc-{{ $liveroom->id }}" style="margin-top:.5em;">
                                    <img src="{!! tomedia($liveroom->cover_img) !!}" onerror="this.src='/addons/yun_shop/static/resource/images/nopic.jpg'; this.title='图片未找到.'" class="img-responsive img-thumbnail" width="150">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group fg-showhide" style="display:none;">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">房间号</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            @foreach($liverooms as $liveroom)
                                <span class="form-control fc-showhide fc-{{ $liveroom->id }}">{{ $liveroom->roomid }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group fg-showhide" style="display:none;">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">主播</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            @foreach($liverooms as $liveroom)
                                <span class="form-control fc-showhide fc-{{ $liveroom->id }}">{{ $liveroom->anchor_name }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group fg-showhide" style="display:none;">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">状态</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            @foreach($liverooms as $liveroom)
                                @if($liveroom->live_status==101)
                                    <span class="form-control fc-showhide fc-{{ $liveroom->id }}">直播中</span>
                                @elseif($liveroom->live_status==102)
                                    <span class="form-control fc-showhide fc-{{ $liveroom->id }}">待开播</span>
                                @elseif($liveroom->live_status==103)
                                    <span class="form-control fc-showhide fc-{{ $liveroom->id }}">已结束</span>
                                @elseif($liveroom->live_status==104)
                                    <span class="form-control fc-showhide fc-{{ $liveroom->id }}">禁播</span>
                                @elseif($liveroom->live_status==105)
                                    <span class="form-control fc-showhide fc-{{ $liveroom->id }}">暂停</span>
                                @elseif($liveroom->live_status==106)
                                    <span class="form-control fc-showhide fc-{{ $liveroom->id }}">异常</span>
                                @elseif($liveroom->live_status==107)
                                    <span class="form-control fc-showhide fc-{{ $liveroom->id }}">已过期</span>
                                @else
                                    <span class="form-control fc-showhide fc-{{ $liveroom->id }}">未知</span>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group fg-showhide" style="display:none;">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">开播时间</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            @foreach($liverooms as $liveroom)
                                <span class="form-control fc-showhide fc-{{ $liveroom->id }}">
                                    {{ date('Y-m-d H:i:s', $liveroom->start_time) }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group fg-showhide" style="display:none;">
                        <label class="col-md-2 col-sm-3 col-xs-12 control-label">结束时间</label>
                        <div class="col-md-10 col-sm-9 col-xs-12">
                            @foreach($liverooms as $liveroom)
                                <span class="form-control fc-showhide fc-{{ $liveroom->id }}">
                                    {{ date('Y-m-d H:i:s', $liveroom->end_time) }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                @endif

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">排序</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input name="sort" type="number" class="form-control" value="0" placeholder="" required />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label"></label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input type="hidden" name="rid" value="{{ $rid }}" />
                        <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script type="text/javascript">
        var ueditoroption = {
            'toolbars' : [['source', 'preview', '|', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', '|',
                'justifyleft', 'justifycenter', 'justifyright', '|', 'insertorderedlist', 'insertunorderedlist', 'blockquote', 'emotion',
                'link', 'removeformat', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight','indent', 'paragraph', 'fontsize', '|',
                'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol',
                'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|', 'anchor', 'map', 'print', 'drafts']],
        };

        $(document).on('change', '#sltRoomId', function () {
            if ($(this).val() == '') {
                $('.fg-showhide').hide();
            } else {
                var room_id = $(this).val();
                $('.fg-showhide').hide();
                $('.fc-showhide').hide();
                $('.fc-' + room_id).show();
                $('.fg-showhide').show();
            }
        });
    </script>

@endsection
