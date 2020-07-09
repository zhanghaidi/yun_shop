@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        <div class="ulleft-nav">
            <ul class="nav nav-tabs">
                <li><a href="{{ yzWebUrl('plugin.article.article.index') }}" style="cursor: pointer;">文章管理</a></li>
                <li><a href="" style="cursor: pointer;">添加文章</a></li>
                <li><a href="{{ yzWebUrl('plugin.article.category.index') }}" style="cursor: pointer;">分类管理</a></li>
                <li><a href="" style="cursor: pointer;">其他设置</a></li>
                <li><a href="" style="cursor: pointer;">举报记录</a></li>

            </ul>
        </div>
        <div class="rightlist">
            <form id="dataform" action="" method="post" class="form-horizontal form">

                <div class="right-titpos">
                    <ul class="add-snav">
                        <li class="active"><a href="#">文章采集</a></li>
                    </ul>
                </div>

                <div class="panel panel-default">

                    <div class="panel-body">

                        <div class='alert alert-danger' style='display:block!important'>尽量在服务器空闲时间来操作，会占用大量内存与带宽，在获取过程中，请不要进行任何操作!</div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 链接</label>
                            <div class="col-sm-9">
                                <textarea style="width:600px;height:200px" id="url" name="article[url]" class="form-control"></textarea>
                                <span class="help-block">文章连接, 例如: http://mp.weixin.qq.com/s/v8NltS6EG3MlFFjsX5H5hA</span>
                                <span class="help-block">每一行一个链接</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 设置分类</label>

                            <div class="col-sm-9">
                                <div class="row row-fix tpl-category-container">
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        <select class="form-control tpl-category-parent" name="article[category_id]">
                                            <option value="0">请选择文章分类</option>
                                            @foreach ($categorys as $category)
                                            <option value="{{ $category['id'] }}" >{{ $category['name'] }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"> </label>
                            <div class="col-sm-9">
                                <span class="help-block">此分类读取的是文章营销分类, 设置默认采集文章的分类</span>
                            </div>
                        </div>

                    </div>

                    <div class='panel-footer'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"> </label>
                            <div class="col-sm-9">
                                <input id="btn_submit" type="button"  value="立即采集" class="btn btn-primary"  onclick="formcheck()"/>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <script>
        var len = 0;
        var urls = [];
        var total = 0;
        function formcheck() {

            if ($(":input[name='article[url]']").val() == '') {
                alert('请输入文章链接');
                return;
            }
            if($(":input[name='article[category_id]']").val()=='0'){
                alert('请选择文章分类');
                return;
            }
            $("#dataform").attr("disabled", "true");
            $("#btn_submit").val("正在获取中...").removeClass("btn-primary").attr("disabled", "true");

            urls = $("#url").val().split('\n');
            total = urls.length;
            $("#btn_submit").val("检测到需要采集 " + total + " 篇文章, 请等待开始....");
            fetch_next();
            return;
        }
        function fetch_next() {
            var postdata =  {
                url: urls[len],
                category_id: $("select[name='article[category_id]']").val(),
            };
            $.post("{!! yzWebUrl('plugin.article.admin.article.collect') !!}",
                    postdata,
                    function (data) {
                        len++;
                        if (data == 0) {
                            if (confirm('第' + len + '个链接未采集到内容,请确认采集地址的正确!')) {
                                location.reload();
                                return false;
                            }
                        }
                        $("#btn_submit").val("已经采集  " + len + " / " + total + " 篇文章, 请等待....");

                        if (len >= total) {
                            $("#btn_submit").val("立即采集").addClass("btn-primary").removeAttr("disabled");
                            if (confirm('文章已经采集成功, 是否跳转到文章管理?')) {
                                location.href = "{!! yzWebUrl('plugin.article.admin.article.index') !!}";
                            }
                            else {
                                location.reload();
                            }
                        } else {
                            fetch_next();
                        }
                    }, "json");
        }
    </script>
@endsection

