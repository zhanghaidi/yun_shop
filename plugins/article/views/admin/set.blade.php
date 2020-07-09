@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        {{--<div class="ulleft-nav">--}}
            {{--<ul class="nav nav-tabs">--}}
                {{--<li><a href="{{ yzWebUrl('plugin.article.article.index') }}" style="cursor: pointer;">文章管理</a></li>--}}
                {{--<li><a href="" style="cursor: pointer;">添加文章</a></li>--}}
                {{--<li><a href="{{ yzWebUrl('plugin.article.category.index') }}" style="cursor: pointer;">分类管理</a></li>--}}
                {{--<li><a href="" style="cursor: pointer;">其他设置</a></li>--}}
                {{--<li><a href="" style="cursor: pointer;">举报记录</a></li>--}}

            {{--</ul>--}}
        {{--</div>--}}
        <div class="rightlist">
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

                <div class='panel panel-default form-horizontal form'>
                    <div class='panel-heading'>基础设置</div>
                    <div class='panel-body'>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">文章入口链接</label>
                            <div class="col-sm-9 col-xs-12">
                                <a href="javascript:;"
                                   data-url="{!! \app\common\helpers\Url::absoluteApp('notice/') !!}"
                                   title="复制连接" class="btn btn-default btn-sm" id="btn">文章入口链接
                                </a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启文章系统</label>
                            <div class="col-sm-4 col-xs-6">
                                <label class="radio-inline"><input type="radio" name="article[enabled]" value="0"
                                                                   @if ($set['enabled'] == 0) checked="checked" @endif />
                                    关闭</label>
                                <label class="radio-inline"><input type="radio" name="article[enabled]" value="1"
                                                                   @if ($set['enabled'] == 1) checked="checked" @endif />
                                    开启</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示分享者二维码</label>
                            <div class="col-sm-4 col-xs-6">
                                <label class="radio-inline"><input type="radio" name="article[qr]" value="1"
                                                                   @if ($set['qr'] == 1) checked="checked" @endif />
                                    关闭</label>
                                <label class="radio-inline"><input type="radio" name="article[qr]" value="0"
                                                                   @if ($set['qr'] == 0) checked="checked" @endif />
                                    开启</label>
                            </div>
                        </div>

                        {{--<div class="form-group district_html" style="padding-top:0px;">--}}
                            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">可阅读的地区</label>--}}

                        {{--</div>--}}
                        {{--@if (!empty($set['area']))--}}
                            {{--@foreach ($set['area'] as  $key => $area)--}}
                        {{--<div class="form-group district_html" style="padding-top:0px;">--}}
                            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
                            {{--<div class="col-sm-9 col-xs-12 ">--}}

                                        {{--<div class="row row-fix tpl-district-container">--}}
                                            {{--<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">--}}
                                                {{--<select name="article[area][{{ $key }}][province]"--}}
                                                        {{--data-value="{{ $area['province'] }}"--}}
                                                        {{--class="form-control tpl-province">--}}
                                                {{--</select>--}}
                                            {{--</div>--}}
                                            {{--<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">--}}
                                                {{--<select name="article[area][{{ $key }}][city]" data-value="{{ $area['city'] }}"--}}
                                                        {{--class="form-control tpl-city">--}}
                                                {{--</select>--}}
                                            {{--</div>--}}
                                            {{--<div class="col-sm-1">--}}
                                                {{--<input type="button" class="btn btn-default" value="删除"--}}
                                                       {{--onclick="$(this).parent().parent().parent().parent().remove();"/>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                            {{--@endforeach--}}
                        {{--@endif--}}
                        {{--<div class="form-group" style="padding-top:20px;">--}}
                            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
                            {{--<div class="col-sm-9 col-xs-12">--}}
                                {{--<input type="button" name="" class="btn btn-default" value="添加新区域"--}}
                                       {{--onclick="addDistrict();"/>--}}
                                {{--<input value="{{ count($set['area']) }}" type="hidden" id="distrist_index">--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <script type="text/javascript">
                            function addDistrict() {
                                require(["jquery", "district"], function ($, dis) {
                                    var objIndex = $('.district_html').length-1;
                                    //alert(objIndex);
                                    if (objIndex >= 0) {
                                        //复制html：
                                        html = '<div class="form-group district_html" style="padding-top:0px;">' +
                                                '<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>' +
                                                '<div class="col-sm-9 col-xs-12 " >' +
                                                '<div class="row row-fix tpl-district-container">' +
                                                '<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">' +
                                                '<select name="article[area]['+ objIndex +'][province]" data-value="" class="form-control tpl-province"></select>' +
                                                '</div>' +
                                                '<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">' +
                                                '<select name="article[area]['+ objIndex +'][city]" data-value="" class="form-control tpl-city"></select>' +
                                                '</div>' +
                                                '<div class="col-sm-1">' +
                                                '<input type="button" class="btn btn-default" value="删除" onclick="$(this).parent().parent().parent().parent().remove();" /></div></div> </div></div>';
                                        //alert(html);
                                        //prevIndex = objIndex;
                                        $('.district_html').eq(objIndex).after(html);
                                    }
                                    var contain = $('.tpl-district-container')[objIndex];
                                    console.log(contain);
                                    var elms = {};
                                    elms.province = $(contain).find(".tpl-province")[0];
                                    elms.city = $(contain).find(".tpl-city")[0];

                                    var vals = {};
                                    vals.province = $(elms.province).attr("data-value");
                                    vals.city = $(elms.city).attr("data-value");

                                    dis.render(elms, vals, {withTitle: true});

                                    objIndex++;
                                    $('#distrist_index').val(objIndex);
                                });

                            }


                            require(["jquery", "district"], function ($, dis) {

                                $('.tpl-district-container').each(function (objIndex) {
                                    var elms = {};
                                    elms.province = $(this).find(".tpl-province")[0];
                                    elms.city = $(this).find(".tpl-city")[0];

                                    var vals = {};
                                    vals.province = $(elms.province).attr("data-value");
                                    vals.city = $(elms.city).attr("data-value");

                                    dis.render(elms, vals, {withTitle: true});

                                });

                            });
                            
                            $('.js-clip').each(function () {
                                util.clip(this, $(this).attr('data-url'));
                            });
                        //addDistrict();
                        </script>


                        <div class="form-group" style="padding-top:20px;">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">文章列表图片</label>
                            <div class="col-sm-9 col-xs-12">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('article[banner]', $set['banner']) !!}
                            </div>
                        </div>
                        <div class="form-group" style="padding-top:20px;">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">文章列表默认数量</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="article[num_per_page]" class="form-control"
                                       value="{{ $set['num_per_page'] }}" placeholder="空则默认显示10条记录"/>
                                <span class="help-block">提示：默认模板(列表)时限制文章显示数量，历史消息样式模板时限制显示天数</span>
                            </div>
                        </div>
                        {{--<div class="form-group" style="padding-top:20px;">--}}
                            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">文章列表默认模板</label>--}}
                            {{--<div class="col-sm-9 col-xs-12">--}}
                                {{--<label for="article_temp_1" class="radio-inline"><input type="radio"--}}
                                                                                        {{--name="article[template_type]" value="1"--}}
                                                                                        {{--id="temp_1"--}}
                                                                                        {{--@if ($set['template_type'] == 1) checked="cheaked" @endif>--}}
                                    {{--默认模板(列表)</label>--}}
                                {{--<label for="article_temp_2" class="radio-inline"><input type="radio"--}}
                                                                                        {{--name="article[template_type]" value="2"--}}
                                                                                        {{--id="temp_2"--}}
                                                                                        {{--@if ($set['template_type'] == 2) checked="cheaked" @endif>--}}
                                    {{--历史消息样式</label>--}}
                                {{--<label for="article_temp_3" class="radio-inline"><input type="radio"--}}
                                                                                        {{--name="article[template_type]" value="3"--}}
                                                                                        {{--id="temp_3"--}}
                                                                                        {{--@if ($set['template_type'] == 3) checked="cheaked"--}}
                                                                                        {{--@endif}> 分类列表样式</label>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                    </div>


                    <div class='panel-heading'>文字设置</div>
                    <div class='panel-body'>
                        <div class="form-group" style="padding-top:20px;">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">文章列表标题</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="article[title]" class="form-control"
                                       value="{{ $set['title'] }}" placeholder="文章列表页面标题与封面标题同为此标题"/>
                            </div>
                        </div>
                        <div class="form-group" style="padding-top:20px;">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员中心</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="article[center]" class="form-control"
                                       value="{{ $set['center'] }}"/>
                            </div>
                        </div>
                        <div class="form-group" style="padding-top:20px;">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">文章列表进入关键字</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="article[keyword]" class="form-control"
                                       value="{{ $set['keyword'] }}"/>
                            </div>
                        </div>

                        <div class="form-group"></div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="保存" class="btn btn-primary col-lg-1"/>
                            </div>
                        </div>

                    </div>

                </div>
            </form>
        </div>
        <script type="text/javascript">
            $(document).ready(function(){
                const btn = document.querySelector('#btn');
                btn.addEventListener('click',() => {
                    const input = document.createElement('input');
                    document.body.appendChild(input);
                    input.setAttribute('value', $("#btn").attr("data-url"));
                    input.select();
                    if (document.execCommand('copy')) {
                        document.execCommand('copy');
                        alert('复制成功');
                    }
                    document.body.removeChild(input);
                })
            });
        </script>
@endsection

