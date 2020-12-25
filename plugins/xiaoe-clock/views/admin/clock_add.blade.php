@extends('layouts.base')
@section('title', trans('创建打卡'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">创建打卡</a></li>
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

                @if($type=='1')
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">打卡名称</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="name" type="text" class="form-control" value="" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">封面图</label>
                        <div class="col-sm-9 col-xs-12 col-md-6">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img', '') !!}
                            <span class="help-block">图片比例 5:4，请按照规定尺寸上传</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">打卡图文介绍</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            {!! yz_tpl_ueditor('text_desc', $info['text_desc']) !!}
                        </div>
                    </div>
{{--                    <div class="form-group">--}}
{{--                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">打卡音频介绍</label>--}}
{{--                        <div class="col-sm-9 col-xs-12 col-md-6">--}}
{{--                            {!! yz_tpl_form_field_audio('audio_desc') !!}--}}

{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">打卡视频介绍</label>
                        <div class="col-sm-9 col-xs-12 col-md-6">
                            {!! app\common\helpers\ImageHelper::tplFormFieldVideo('video_desc') !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">参与方式</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <label class="radio-inline">
                                <input type="radio" name="join_type" value="1"/>购买课程
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="join_type" value="0" checked="checked"/>免费课程
                            </label>
                        </div>
                    </div>
                    <div class="form-group goods-div" style="display: none;">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">关联课程</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="course_id" type="hidden" class="form-control" value=""/>
                            <input class="form-control" type="text" placeholder="请选择课程" value="" id="course_name"
                                   style="width:400px;display:inline-block;" readonly="true">
                            <span class="input-group-btn" style="display:inline-block;width: 100px;">
                            <button class="btn btn-default nav-link-goods" style="display:inline-block" type="button"
                                    onclick="$('#modal-module-menus-goods').modal();">选择课程</button>
                        </span>
                            <a href="javascript:;" onclick="clearGoods()"
                               style="margin-top:10px;display:inline-block;width: 20px;" title="清除课程"><i
                                        class='fa fa-times'></i></a>
                            <span class='help-block' style="color: red">设置之后，严禁修改！请谨慎操作</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="ol-xs-12 col-sm-3 col-md-1 control-label">开始日期</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            {!! tpl_form_field_date('start_time', date('Y-m-d'), false) !!}
                            <span class="help-block">打卡有效日期，开始日期</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="ol-xs-12 col-sm-3 col-md-1 control-label">结束日期</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            {!! tpl_form_field_date('end_time', date('Y-m-d'), false) !!}
                            <span class="help-block">打卡有效日期，结束日期</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">打卡有效时段</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            开始时间：<input name="valid_time_start" type="text" value="" required/>&nbsp;&nbsp;&nbsp;&nbsp;结束时间：<input name="valid_time_end" type="text" value="" required/>
                            <span class="help-block">打卡有效时段，开始时间，结束时间，如果每天早上九点才能打开就想写数字9,如果下午3点打卡结束就填写数字15，不要用空格等特殊字符</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">文字字数限制</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="text_length" type="text" class="form-control" value="" required/>
                            <span class="help-block">用户打卡要求，文字字数限制</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">图片张数限制</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="image_length" type="text" class="form-control" value="" required/>
                            <span class="help-block">用户打卡要求，图片张数限制</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">音频时间限制</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="video_length" type="text" class="form-control" value="" required/>
                            <span class="help-block">用户打卡要求，音频时间限制</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">显示设置</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <label class="radio-inline">
                                <input type="radio" name="display_status" value="1" checked="checked"/> 显示
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="display_status" value="2" />隐藏
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">助手名称</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="helper_nickname" type="text" class="form-control" value="" required/>
                            <span class="help-block">助手设置，助手名称</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">助手头像</label>
                        <div class="col-sm-9 col-xs-12 col-md-6">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('helper_avatar', '') !!}
                            <span class="help-block">助手设置，助手头像</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">助手微信</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="helper_wechat" type="text" class="form-control" value="" required/>
                            <span class="help-block">助手设置，助手微信</span>
                        </div>
                    </div>
                @endif

                @if($type=='2')
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">打卡名称</label>
                            <div class="col-sm-9 col-xs-12 col-md-11">
                                <input name="name" type="text" class="form-control" value="" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">封面图</label>
                            <div class="col-sm-9 col-xs-12 col-md-6">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img', '') !!}
                                <span class="help-block">图片比例 5:4，请按照规定尺寸上传</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">打卡图文介绍</label>
                            <div class="col-sm-9 col-xs-12 col-md-11">
                                {!! yz_tpl_ueditor('text_desc', $info['text_desc']) !!}
                            </div>
                        </div>
                        {{--                    <div class="form-group">--}}
                        {{--                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">打卡音频介绍</label>--}}
                        {{--                        <div class="col-sm-9 col-xs-12 col-md-6">--}}
                        {{--                            {!! yz_tpl_form_field_audio('audio_desc') !!}--}}

                        {{--                        </div>--}}
                        {{--                    </div>--}}
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">打卡视频介绍</label>
                            <div class="col-sm-9 col-xs-12 col-md-6">
                                {!! app\common\helpers\ImageHelper::tplFormFieldVideo('video_desc') !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">参与方式</label>
                            <div class="col-sm-9 col-xs-12 col-md-11">
                                <label class="radio-inline">
                                    <input type="radio" name="join_type" value="1"/>购买课程
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="join_type" value="0" checked="checked"/>免费课程
                                </label>
                            </div>
                        </div>
                        <div class="form-group goods-div" style="display: none;">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">关联课程</label>
                            <div class="col-sm-9 col-xs-12 col-md-11">
                                <input name="course_id" type="hidden" class="form-control" value=""/>
                                <input class="form-control" type="text" placeholder="请选择课程" value="" id="course_name"
                                       style="width:400px;display:inline-block;" readonly="true">
                                <span class="input-group-btn" style="display:inline-block;width: 100px;">
                            <button class="btn btn-default nav-link-goods" style="display:inline-block" type="button"
                                    onclick="$('#modal-module-menus-goods').modal();">选择课程</button>
                        </span>
                                <a href="javascript:;" onclick="clearGoods()"
                                   style="margin-top:10px;display:inline-block;width: 20px;" title="清除课程"><i
                                            class='fa fa-times'></i></a>
                                <span class='help-block' style="color: red">设置之后，严禁修改！请谨慎操作</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">防作弊模式</label>
                            <div class="col-sm-9 col-xs-12 col-md-11">
                                <label class="radio-inline">
                                    <input type="radio" name="is_cheat_mode" value="0" checked="checked"/> 关闭
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_cheat_mode" value="1" /> 开启
                                </label>
                                <span class="help-block">开启，则打卡后才可查看其它学员内容</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">重新打卡</label>
                            <div class="col-sm-9 col-xs-12 col-md-11">
                                <label class="radio-inline">
                                    <input type="radio" name="is_resubmit" value="0" checked="checked"/> 关闭
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_resubmit" value="1" /> 开启
                                </label>
                                <span class="help-block">开启后，用户不允许删除打卡，重新提交</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">文字字数限制</label>
                            <div class="col-sm-9 col-xs-12 col-md-11">
                                <input name="text_length" type="text" class="form-control" value="" required/>
                                <span class="help-block">用户打卡要求，文字字数限制</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">图片张数限制</label>
                            <div class="col-sm-9 col-xs-12 col-md-11">
                                <input name="image_length" type="text" class="form-control" value="" required/>
                                <span class="help-block">用户打卡要求，图片张数限制</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">音频时间限制</label>
                            <div class="col-sm-9 col-xs-12 col-md-11">
                                <input name="video_length" type="text" class="form-control" value="" required/>
                                <span class="help-block">用户打卡要求，音频时间限制</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">显示设置</label>
                            <div class="col-sm-9 col-xs-12 col-md-11">
                                <label class="radio-inline">
                                    <input type="radio" name="display_status" value="1" checked="checked"/> 显示
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="display_status" value="2" />隐藏
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">助手名称</label>
                            <div class="col-sm-9 col-xs-12 col-md-11">
                                <input name="helper_nickname" type="text" class="form-control" value="" required/>
                                <span class="help-block">助手设置，助手名称</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">助手头像</label>
                            <div class="col-sm-9 col-xs-12 col-md-6">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('helper_avatar', '') !!}
                                <span class="help-block">助手设置，助手头像</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">助手微信</label>
                            <div class="col-sm-9 col-xs-12 col-md-11">
                                <input name="helper_wechat" type="text" class="form-control" value="" required/>
                                <span class="help-block">助手设置，助手微信</span>
                            </div>
                        </div>
                @endif

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">排序</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <input name="sort" type="number" class="form-control" value="0" required/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="hidden" name="type" value="{{ $type }}"/>
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
