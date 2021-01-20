@extends('layouts.base')

@section('content')
@section('title', trans('微店等级'))
<div class="w1200 m0a">
    <section class="content">
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">微店等级设置</a></li>
            </ul>
        </div>
        <form action="" method="post" class="form-horizontal form">
            <div class='panel panel-default'>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style='color:red'>*</span>等级权重</label>
                        <div class="col-sm-9 col-xs-12">
                            <input class="form-control" type="text" value="{{$level->level_weight}}"
                                   name="level[level_weight]">
                            <span class="help-block">等级权重一定要设置且不能重复</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style='color:red'>*</span>
                            等级名称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="level[level_name]" class="form-control"
                                   value="{{$level->level_name}}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style='color:red'>*</span>分红比例%</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="level[bonus_ratio]" class="form-control"
                                   value="{{$level->bonus_ratio}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style='color:red'>*</span>商品</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type='hidden' class='form-control' id='goodsid' name='level[goods_id]' value="{{$level->goods_id}}" />
                            <div class='input-group' style='border:none;'>
                                <input type='text' class='form-control' id='goods' value="@if (!empty($level->hasOneGoods)){{'['.$level->hasOneGoods->id.']'.$level->hasOneGoods->title}} @endif" readonly />
                                <div class="input-group-btn">
                                    <button type="button" onclick="$('#modal-goods').modal()" class="btn btn-default" >选择商品</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="modal-goods"  class="modal fade" tabindex="-1">
                        <div class="modal-dialog" style='width: 920px;'>
                            <div class="modal-content">
                                <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择商品</h3></div>
                                <div class="modal-body" >
                                    <div class="row">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="keyword" value="" id="search-kwd-goods" placeholder="请输入商品名称" />
                                            <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_goods();">搜索</button></span>
                                        </div>
                                    </div>
                                    <div id="module-menus-goods" style="padding-top:5px;"></div>
                                </div>
                                <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"
                                   onclick='return formcheck()'/>
                        </div>
                    </div>
                </div>
        </form>
    </section><!-- /.content -->
</div>
@endsection
<script>
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
        )
        ;
    }


    function select_good(o) {
        $("#goodsid").val(o.id);
        $("#goods").val( "[" + o.id + "]" + o.title);
        $("#modal-goods .close").click();
    }
</script>