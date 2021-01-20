@extends('layouts.base')
@section('title', trans('编辑素材'))
@section('content')
        <div class="panel panel-default">
            <div class="panel-heading">编辑信息</div>
                <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="{{$data->id}}">
                <div class="info">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color: red;"> * </span>标题</label>
                        <div class="col-sm-6 col-xs-6">
                            <input type="text" name="data[title]" id="data[title]" class="form-control" value="{{$data['title']}}" placeholder="请输入标题" />
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color: red;">* </span>商品</label>
                        <div class="col-sm-6 col-xs-6">
                            <input type="hidden" id="goodsid" name="data[goods_id]"
                                       value="{{$data['goods_id']}}">

                                <div class="input-group">
                                    <input type="text" name="goods" maxlength="30"
                                           value="@if(isset($data)) [{{$data->goods_id}}]{{$data->goods->title}} @endif"
                                           id="goods" class="form-control" readonly="">
                                    <div class="input-group-btn">
                                        <button class="btn btn-default" type="button"
                                                onclick="popwin = $('#modal-module-menus-goods').modal();">
                                            选择商品
                                        </button>
                                        <button class="btn btn-danger" type="button"
                                                onclick="$('#goodsid').val('');$('#goods').val('');">
                                            清除选择
                                        </button>
                                    </div>
                                </div>

                            <span id="goodsthumb" class='help-block'
                                  @if(empty($data)) style="display:none" @endif ><img
                                        style="width:100px;height:100px;border:1px solid #ccc;padding:1px"
                                        src="@if(isset($data->goods->thumb)) {{tomedia($data->goods->thumb)}} @endif"/></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-9 col-xs-12">
                            <div id="modal-module-menus-goods" class="modal fade" tabindex="-1">
                                <div class="modal-dialog" style='width: 920px;'>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button aria-hidden="true" data-dismiss="modal"
                                                    class="close" type="button">
                                                ×
                                            </button>
                                            <h3>选择商品</h3></div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                           name="keyword" value=""
                                                           id="search-kwd-goods"
                                                           placeholder="请输入商品名称"/>
                                                    <span class='input-group-btn'>
                                                        <button type="button" class="btn btn-default"
                                                                onclick="search_goods();">搜索
                                                        </button></span>
                                                </div>
                                            </div>
                                            <div id="module-menus-goods"
                                                 style="padding-top:5px;"></div>
                                        </div>
                                        <div class="modal-footer"><a href="#"
                                                                     class="btn btn-default"
                                                                     data-dismiss="modal"
                                                                     aria-hidden="true">关闭</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color: red;">* </span>素材文案</label>
                        <div class="col-sm-6 col-xs-6">
                            <textarea name="data[content]" placeholder="请输入文案, 10-200字" rows="10" cols="50" maxlength="200">@if($data['content']){{$data['content']}}@endif</textarea>
                        </div>
                    </div>
                
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color: red;">* </span>商品图</label>
                    <div class="col-sm-9  col-md-6 col-xs-12">

                        {!! app\common\helpers\ImageHelper::tplFormFieldMultiImage('data[images]',$data['images']) !!}
                            <span class="help-block">建议尺寸: 640 * 640 ，或正方型图片, 最多添加9张图</span>
                            @if (!empty($data['images']))
                                 @foreach ($data['images'] as $p)
                                 <a href='{{tomedia($p)}}' target='_blank'>
                                   <img src="{{tomedia($p)}}" style='height:100px;border:1px solid #ccc;padding:1px;float:left;margin-right:5px;' />
                                 </a>
                                 @endforeach
                            @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                    <div class="col-sm-9 col-xs-12">
                        <label for="totalcnf1" class="radio-inline">
                            <input type="radio" name="data[is_show]" value="0" id="totalcnf1" @if (empty($data) || $data['is_show'] == 0) checked="true" @endif /> 不显示
                        </label>
                        &nbsp;&nbsp;&nbsp;
                        <label for="totalcnf1" class="radio-inline">
                            <input type="radio" name="data[is_show]" value="1" id="totalcnf1" @if ($data['is_show'] == 1) checked="true" @endif /> 显示
                        </label>
                    </div>
                </div>

                <div class="form-group col-sm-12 mrleft40 border-t">

                    <input type="submit" name="submit" value="提交" class="btn btn-success"
                           onclick="return formcheck()"/>
                    <input type="hidden" name="token" value="{{$var['token']}}"/>
                    <input type="button" name="back" value="返回列表" class="btn btn-default back" />
                </div>

            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
        
        $("input[name='back']").click(function () {

            location.href = "{!! yzWebUrl('plugin.material-center.admin.material.index', []) !!}";
        });

        function search_goods() {
            if ($.trim($('#search-kwd-goods').val()) == '') {
                Tip.focus('#search-kwd-goods', '请输入关键词');
                return;
            }
            $("#module-menus-goods").html("正在搜索....");
            $.get("{!! yzWebUrl('plugin.material-center.admin.material.getSearchGoods') !!}", {
                    keyword: $.trim($('#search-kwd-goods').val()),
                    // type: $.trim($('#goods_type').val())
                }, function (dat) {
                    $('#module-menus-goods').html(dat);
                }
            )
            ;
        }
        
        function select_good(o) {
            $("#goodsid").val(o.id);
            $("#goodsthumb").show();
            $("#goodsthumb").find('img').attr('src', o.thumb);
            $("#goods").val("[" + o.id + "]" + o.title);
            $("#modal-module-menus-goods .close").click();
        }

        function formcheck() {
            if ($(':input[name="data[title]"]').val() == '') {
                Tip.focus("#data[title]", "请输入商品标题!");
                return false;
            }
            if ($(':input[name="data[content]"]').val() == '') {
                Tip.focus("#data[content]", "请输入素材文案!");
                return false;
            }

            if ($(':input[name="data[images]"]').val() == '') {
                Tip.focus("#data[images]", "请上传商品图片!");
                return false;
            }
            console.log('goods_id', $(':input[name="data[goods_id]"]').val() );
            if ($(':input[name="data[goods_id]"]').val() == '') {
                Tip.focus("#goods_id", "请选择关联商品!");
                return false;
            }
            // if ($(':input[name="data[images]"]').length() > 9) {
            //     Tip.focus("#goods_id", "请选择少于9张图片!");
            //     return false;
            // }
        }
        
        $(".input-xs:text").each(function () {
            jQuery(this).change(function () {
            jQuery(this).val(jQuery.trim(jQuery(this).val()));
            })
        });
</script>
@endsection
