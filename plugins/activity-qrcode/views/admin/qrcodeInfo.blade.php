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
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">排序</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <input name="info[sort]" type="text" class="form-control" value="{{ $info['sort'] }}" placeholder="请输入排序不能重复" required/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">二维码名称</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <input name="info[name]" type="text" class="form-control" value="{{ $info['name'] }}" placeholder="请输二维码名称" required/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">二维码路径</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <input name="info[qr_path]" type="text" class="form-control" value="{{ $info['qr_path'] }}" placeholder="请输二维码路径地址" required/>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">切换频率</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <input name="info[switch_limit]" type="text" class="form-control" value="{{ $info['switch_limit'] }}" placeholder="切换频率" required/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">二维码图片</label>
                    <div class="col-sm-9 col-xs-12 col-md-6">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('info[qr_img]', $info['qr_img']) !!}
                        <span class="help-block">分享图片 5:4</span>
                    </div>
                </div>

               {{-- <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">二维码失效时间</label>
                    <div class="col-sm-9 col-xs-12">
                        {!! app\common\helpers\DateRange::tplFormFieldDateRange('info[end_time]', [
                       'starttime'=>$live['start_time'] ? $live['start_time'] : date('Y-m-d H:i:s') ,
                       'endtime'=>$live['end_time']  ? $live['end_time'] : date('Y-m-d H:i:s', time() + 86400),
                       'start'=> 0,
                       'end'=> 0
                       ], true) !!}
                    </div>
                </div>--}}
                <div class="form-group">
                    <label class="ol-xs-12 col-sm-3 col-md-1 control-label">二维码失效时间</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        {!! tpl_form_field_date('info[end_time]', $info['end_time'] ? $info['end_time'] : date("Y-m-d", strtotime("+7 day")), false) !!}

                        <span class="help-block">二维码失效时间</span>
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
