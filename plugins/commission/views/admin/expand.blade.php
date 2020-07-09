@extends('layouts.base')

@section('content')
@section('title', trans('分销基础设置'))
<section class="content">

    <form id="setform" action="" method="post" class="form-horizontal form">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">分销设置</a></li>
            </ul>
        </div>
        @include('Yunshop\Commission::admin.tabs')

        <div class='panel panel-default'>
            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">定制版设置</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio-inline">
                            <input type="radio" name="setdata[is_expand]" value="0"
                                   @if($set['is_expand'] == 0)
                                   checked="checked" @endif />
                            关闭</label>
                        <label class="radio-inline">
                            <input type="radio" name="setdata[is_expand]" value="1"
                                   @if($set['is_expand'] == 1)
                                   checked="checked" @endif />
                            开启</label>
                        <span class='help-block' style="color: red">开启定制版设置后，所有分红走定制版,不会再走之前分销设置，请谨慎启用</span>
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
@endsection
<script type="text/javascript" src="./resource/js/lib/jquery-ui-1.10.3.min.js"></script>
{{--<script language='javascript'>--}}



    {{--window.onload=function(){--}}
        {{--var option=$("#commission_level option:selected").val();--}}

        {{--if (option < 3) {--}}
            {{--$("#goods_detail_level3").hide();--}}
        {{--}--}}
        {{--if (option < 2) {--}}
            {{--$("#goods_detail_level2").hide();--}}
        {{--}--}}
        {{--if (option < 1) {--}}
            {{--$("#goods_detail_level1").hide();--}}
        {{--}--}}
    {{--}--}}

    {{--require(['select2'], function () {--}}
        {{--$('.diy-notice').select2();--}}
    {{--})--}}

    {{--function dataIdentical(){--}}
        {{--$.get("{!! yzWebUrl('plugin.commission.admin.data-identical.index') !!}", function(data) {--}}
            {{--alert(data);--}}
        {{--});--}}
    {{--}--}}

    {{--function commissionLevel(){--}}
        {{--var selectdIndex = $("#commission_level").get(0).selectedIndex;--}}

        {{--$("#goods_detail_level option").attr("selected", false);--}}
        {{--$("#goods_detail_level option").eq(selectdIndex).prop("selected", true);--}}


        {{--if (selectdIndex == 0) {--}}
            {{--$("#goods_detail_level1").show();--}}
            {{--$("#goods_detail_level2").hide();--}}
            {{--$("#goods_detail_level3").hide();--}}
        {{--}--}}
        {{--if (selectdIndex == 1) {--}}
            {{--$("#goods_detail_level1").show();--}}
            {{--$("#goods_detail_level2").show();--}}
            {{--$("#goods_detail_level3").hide();--}}
        {{--}--}}
        {{--if (selectdIndex == 2) {--}}
            {{--$("#goods_detail_level1").show();--}}
            {{--$("#goods_detail_level2").show();--}}
            {{--$("#goods_detail_level3").show();--}}
        {{--}--}}

        {{--//var goods_detail = $("#goods_detail_level option:selected").val();--}}

        {{--// alert(goods_detail);--}}
    {{--}--}}
    {{--function formcheck() {--}}
        {{--var numerictype = /^(0|[1-9]\d*)$/; //整数验证--}}
        {{--var reg = /^(([1-9]+)|([0-9]+\.[0-9]{1,2}))$/; //小数验证--}}
        {{--var nr = /^(0|[1-9][0-9]*)+(\.\d{1,2})?$/; // Yy 整数或小数--}}
        {{--var level = "{{$set['level']}}";--}}

        {{--if (level >= '1') {--}}
            {{--if (!nr.test($(':input[name="setdata[first_level]"]').val())) {--}}
                {{--Tip.focus(':input[name="setdata[first_level]"]', '只能是整数.');--}}
                {{--return false;--}}
            {{--}--}}
        {{--}--}}
        {{--if (level >= '2') {--}}
            {{--if (!nr.test($(':input[name="setdata[second_level]"]').val())) {--}}
                {{--Tip.focus(':input[name="setdata[second_level]"]', '只能是整数.');--}}
                {{--return false;--}}
            {{--}--}}
        {{--}--}}

        {{--if (level >= '3') {--}}
            {{--if (!nr.test($(':input[name="setdata[third_level]"]').val())) {--}}
                {{--Tip.focus(':input[name="setdata[third_level]"]', '只能是整数.');--}}
                {{--return false;--}}
            {{--}--}}
        {{--}--}}

        {{--if (!numerictype.test($(':input[name="setdata[settle_days]"]').val())) {--}}
            {{--Tip.focus(':input[name="setdata[settle_days]"]', '只能是整数.');--}}
            {{--return false;--}}
        {{--}--}}
    {{--}--}}
{{--</script>--}}