@extends('layouts.base')
@section('title', trans('课程设置'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            @if($info['type']=='1')
                <li class="active"><a href="#">课程设置</a></li>
            @endif

            @if($info['type']=='2')
                <li class="active"><a href="#">专辑设置</a></li>
            @endif
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

                @if($info['type']=='1')
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">课程名称</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="name" type="text" class="form-control" value="{{ $info['name'] }}" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">课程标签</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="tag" type="text" class="form-control" value="{{ $info['tag'] }}" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">是否收费</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <label class="radio-inline">
                                <input type="radio" name="buy_type" value="1" @if($info['buy_type'] == 1) checked="checked" @endif />是
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="buy_type" value="0"  @if($info['buy_type'] == 0) checked="checked" @endif />否
                            </label>
                        </div>
                    </div>
                    <div class="form-group ios_open-div" @if($info['buy_type'] == 0) style="display: none; @endif">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">IOS显示开关</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <label class="radio-inline">
                                <input type="radio" name="ios_open" value="1" @if($info['ios_open'] == 1) checked="checked" @endif />开启
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="ios_open" value="0"  @if($info['ios_open'] == 0) checked="checked" @endif />关闭
                            </label>
                            <span class='help-block'>关闭状态下，ios设备不显示该收费课程。打开之后很有可能被人举报，请谨慎操作！</span>
                        </div>
                    </div>
                    <div class="form-group expire-div" @if($info['buy_type'] == 0) style="display: none; @endif">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">有效期</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <span class="col-sm-3 col-xs-2 col-md-2 input-group">
                                <input name="expire_time" type="text" class="form-control" value="{{ $info['expire_time'] }}" />
                                <span class='input-group-addon'>天</span>
                            </span>
                            <span class='help-block' style="color: red">设置之后，严禁修改！请谨慎操作</span>
                            <span class='help-block'>过期时间单位为天，-1为永不过期</span>
                        </div>
                    </div>
                    <div class="form-group goods-div" @if($info['buy_type'] == 0) style="display: none; @endif">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">关联商品</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="goods_id" type="hidden" class="form-control" value="{{ $info['goods_id'] }}" />
                            <input class="form-control" type="text" placeholder="请选择商品" value="{{ $info['goods_name'] }}" id="goods_name" style="width:400px;display:inline-block;" readonly="true">
                            <span class="input-group-btn" style="display:inline-block;width: 100px;">
                            <button class="btn btn-default nav-link-goods" style="display:inline-block" type="button" onclick="$('#modal-module-menus-goods').modal();">选择商品</button>
                        </span>
                            <a href="javascript:;" onclick="clearGoods()" style="margin-top:10px;display:inline-block;width: 20px;"  title="清除商品"><i class='fa fa-times'></i></a>
                            <span class='help-block' style="color: red">设置之后，严禁修改！请谨慎操作</span>
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">课程封面</label>
                        <div class="col-sm-9 col-xs-12 col-md-10">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img', $info['cover_img']) !!}
                            <span class="help-block">图片比例 5:4，请按照规定尺寸上传</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">在列表、搜索显示</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <label class="radio-inline">
                                <input type="radio" name="is_display" value="1" @if($info['is_display'] == 1) checked="checked" @endif /> 显示
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="is_display" value="2" @if($info['is_display'] == 2) checked="checked" @endif  />隐藏
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">分享控制</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <label class="radio-inline">
                                <input type="radio" name="is_share" value="1" @if($info['is_share'] == 1) checked="checked" @endif /> 可以分享
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="is_share" value="2" @if($info['is_share'] == 2) checked="checked" @endif  /> 不能分享
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">课程介绍</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            {!! yz_tpl_ueditor('desc', $info['desc']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">课程状态</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <select name="live_status" class="form-control">
                                <option value="">请选择课程状态</option>
                                <option value="0" @if($info['live_status']=='0') selected @endif>筹备中</option>
                                <option value='1' @if($info['live_status']=='1') selected @endif>更新中</option>
                                <option value='2' @if($info['live_status']=='2') selected @endif>已完结</option>
                            </select>
                        </div>
                    </div>
                {{--fixby-wk-课程设置精选 20201019--}}
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">是否精选</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <label class="radio-inline">
                                <input type="radio" name="is_selected" value="1" @if ($info['is_selected'] == 1) checked="checked" @endif />
                               是
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="is_selected" value="0" @if ($info['is_selected'] == 0) checked="checked" @endif />
                                否
                            </label>
                        </div>
                    </div>
                @endif

                @if($info['type']=='2')
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">专辑名称</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="name" type="text" class="form-control" value="{{ $info['name'] }}" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">专辑封面</label>
                        <div class="col-sm-9 col-xs-12 col-md-10">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img', $info['cover_img']) !!}
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
                        <input name="sort" type="number" class="form-control" value="{{ $info['sort'] }}" required />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="hidden" name="id" value="{{$id}}" />
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
                            <input type="text" class="form-control" name="keyword" value="" id="search-kwd-goods" placeholder="请输入商品名称"/>
                            <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_goods();">搜索</button>
                        </span>
                        </div>
                    </div>
                    <div id="module-menus-goods" style="padding-top:5px;"></div>
                </div>
                <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>
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

        $('input[name=buy_type]').change(function () {
            // console.log($(this).val())
            if($(this).val() == 1){
                $('.expire-div').show();
                $('.goods-div').show();
                $('.ios_open-div').show();
            }else{
                $('.expire-div').hide();
                $('.goods-div').hide();
                $('.ios_open-div').hide();
            }
        })
    </script>

@endsection
