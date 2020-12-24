@extends('layouts.base')
@section('title', trans('创建日历打卡'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">创建日历打卡</a></li>
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
                        <div class="col-sm-9 col-xs-12 col-md-10">
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
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">打卡音频介绍</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            {!! yz_tpl_ueditor('audio_desc', $info['audio_desc']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">打卡视频介绍</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            {!! yz_tpl_ueditor('video_desc', $info['video_desc']) !!}
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
                            <input name="goods_id" type="hidden" class="form-control" value=""/>
                            <input class="form-control" type="text" placeholder="请选择课程" value="" id="goods_name"
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
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">开始时间</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            {!! tpl_form_field_date_hi('valid_time_start', date('H:i'), true) !!}
                            <span class="help-block">打卡有效时段，开始时间</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">结束时间</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <span class="help-block">打卡有效时段，结束时间</span>
                        </div>
                    </div>

                @endif

                @if($type=='2')
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">专辑名称</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="name" type="text" class="form-control" value="" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">专辑封面</label>
                        <div class="col-sm-9 col-xs-12 col-md-10">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img', '') !!}
                            <span class="help-block">图片比例 5:4，请按照规定尺寸上传</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">专辑介绍</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            {!! yz_tpl_ueditor('desc', $info['desc']) !!}
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

    {{--搜索商品的弹窗--}}
    <div id="modal-module-menus-goods" class="modal fade" tabindex="-1">
        <div class="modal-dialog" style='width: 920px;'>
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>选择商品</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="input-group">
                            <input type="text" class="form-control" name="keyword" value="" id="search-kwd-goods"
                                   placeholder="请输入商品名称"/>
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
            $.get('{!! yzWebUrl('goods.goods.get-search-goods') !!}', {
                    keyword: $.trim($('#search-kwd-goods').val())
                }, function (dat) {
                    $('#module-menus-goods').html(dat);
                }
            );
        }

        function select_good(o) {
            $("input[name=goods_id]").val(o.id);
            $("#goods_name").val(o.title);
            $("#modal-module-menus-goods .close").click();
        }

        function clearGoods() {
            $("input[name=goods_id]").val('');
            $("#goods_name").val('');
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
