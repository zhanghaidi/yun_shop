@extends('layouts.base')
@section('title', '店铺装修')
@section('content')
    <link href="{{ plugin_assets('designer', 'assets/css/designer.css') }}" rel="stylesheet">
    <div class="rightlist">
        <!-- 筛选区域 -->

        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="./index.php" method="get" class="form-horizontal" role="form">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="QDaf"/>
                    <input type="hidden" name="route" value="plugin.designer.admin.list.index"/>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
                        <div class="col-sm-8 col-lg-9">
                            <input class="form-control" name="keyword" id="" type="text" value="{{ $keyword or '' }}"
                                   placeholder="请输入页面名称进行搜索">
                        </div>
                        <div class=" col-xs-12 col-sm-2 col-lg-2">
                            <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- 页面列表 -->
        <div class='panel panel-default'>
            <div class='panel-heading'> 页面管理 (总数: {{ $designerList->count() }})</div>
            <div class='panel-body'>
                <table class="table">
                    <thead>
                    <tr>
                        <th style="width:5%; text-align: center;">ID</th>
                        <th style="width:12%;text-align: center;">页面名称</th>
                        <th style="width:12%;text-align: center;">页面类型</th>
                        {{--<th style=" text-align: center;">关键字</th>--}}
                        <th style="width:15%;text-align: center;">页面创建时间</th>
                        <th style="width:15%;text-align: center;">最后修改时间</th>
                        <th style="width:15%;text-align: center;">是否默认</th>
                        <th style="width:12%;text-align: center;width: 26%">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($designerList->count() >= 1)
                        @foreach($designerList as $list)
                            <tr pageid="{{ $list->id }}">
                                <td style="width:60px; text-align: center;">{{ $list->id }}</td>
                                <td style="text-align:  center;">{{ $list->page_name }}</td>
                                <td style="text-align: center;">
                                    @if($list->page_type == 1)
                                        <label class='label label-success'>店铺首页</label>
                                    @elseif($list->page_type == 2)
                                        <label class='label label-success'>商品列表</label>
                                    @elseif($list->page_type == 3)
                                        <label class='label label-warning'>商品详细</label>
                                    @elseif($list->page_type == 4)
                                        <label class='label label-danger'>其他自定义</label>
                                    @elseif($list->page_type == 9)
                                        <label class='label label-danger'>小程序</label>
                                    @endif
                                </td>
                               {{-- <td style=" text-align:  center;">{{ $list->keyword }}</td>--}}
                                <td style="text-align:  center;">{{ $list->created_at }}</td>
                                <td style=" text-align:  center;">{{ $list->updated_at }}</td>
                                <td style="text-align:  center;" data-id="{{ $list->id }}" data-type="{{$list->page_type}}">
                                    @if($list->page_type != 4)
                                        @if($list->is_default == 1)<label class='label label-success' style="cursor: pointer;" title="点击关闭"
                                                   data-do="off"
                                                   onclick="setdefault(this,{{ $list->id }},{{ $list->page_type }})">已启用</label>
                                        @else
                                            <label class='label label-default' style="cursor: pointer;" title="点击开启"
                                                   data-do="on"
                                                   onclick="setdefault(this,{{ $list->id }},{{ $list->page_type }})">未启用</label>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style=" text-align:  center;position:relative">
                                    <a href="javascript:;" onclick="preview({{ $list->id }})">预览</a> -
                                    {{--<a href="javascript:;" data-url="{{ yzAppUrl('plugin.designer.home.index.page', array('page_id' => $list->id)) }}" class="js-clip" title="复制链接">复制链接</a>
                                    <b style="cursor:pointer" title="请手动在设置中开启flash,或者右键->运行插件">?</b>---}}
                                    <a href="{{ yzWebUrl('plugin.designer.admin.list.update', array('id' => $list->id, 'page_type' => $list->page_type)) }}">编辑</a> -
                                    <a href="javascript:;" onclick="delpage({{ $list->id }})">删除</a> -
                                    <a href="javascript:;" data-url=" {{ yzAppFullUrl('diy', array('page_id' => $list->id)) }} "
                                       title="复制链接" class="js-clip">复制链接</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td style="text-align: center; line-height: 100px;" colspan="8">亲~您还没有添加自定义页面哦~您可以尝试 ↙ 左下角的
                                “<a href="{{ yzWebUrl('plugin.designer.admin.list.store') }}">添加一个新页面</a>”
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="8">

                            <a class='btn btn-success' href="{{ yzWebUrl('plugin.designer.admin.list.store') }}">
                                <i class = "fa fa-plus"></i> 添加公众号页面</a>
                            <a class='btn btn-success' href="{{ yzWebUrl('plugin.designer.admin.list.store', ['page_type' => '9']) }}">
                                <i class = "fa fa-plus"></i> 添加小程序页面</a>
                            <span>Tips:自定义页面启用默认后将代替系统默认页面(商城首页)，同一个类型的页面仅允许设置一个默认页面</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" style="padding:0px; margin: 0px;">{!! $pager !!}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 预览 start -->
        <div id="modal-module-menus2" class="modal fade" tabindex="-1">
            <div class="modal-dialog" style='width: 413px;'>
                <div class="fe-phone">
                    <div class="fe-phone-left"></div>
                    <div class="fe-phone-center">
                        <div class="fe-phone-top"></div>
                        <div class="fe-phone-main">
                            <iframe style="border:0px; width:342px; height:600px; padding:0px; margin: 0px;" src=""></iframe>
                        </div>
                        <div class="fe-phone-bottom" style="overflow:hidden;">
                            <div style="height:52px; width: 52px; border-radius: 52px; margin:20px 0px 0px 159px; cursor: pointer;"
                                 data-dismiss="modal" aria-hidden="true" title="点击关闭"></div>
                        </div>
                    </div>
                    <div class="fe-phone-right"></div>
                </div>
            </div>
        </div>

        <!-- 预览 end -->
        <script type="text/javascript">
            function preview(pageid) {
                var url = "{!! yzWebUrl('plugin.designer.admin.list.preview') !!}&preview=1&page_id=" + pageid;
                $('#modal-module-menus2').find("iframe").attr("src", url);
                popwin = $('#modal-module-menus2').modal();
            }
            function delpage(id) {
                if (confirm('此操作不可恢复，确认删除？')) {
                    $.ajax({
                        type: 'POST',
                        url: "{!! yzWebUrl('plugin.designer.admin.list.destory') !!}",
                        data: {page_id: id},
                        success: function (data) {
                            if (data == 'success') {
                                $("tr[pageid=" + id + "]").fadeOut();
                            }
                            else {
                                alert(data);
                            }
                        },
                        error: function () {
                            alert('操作失败~请刷新页面重试！');
                        }
                    });
                }
            }
            function setdefault(t, id, type) {
                thisdo = $(t).data("do");
                d = thisdo;
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{!! yzWebUrl('plugin.designer.admin.list.setToDefault') !!}",
                    data: {d: d, id: id, type: type},
                    success: function (data) {
                        if (data['result'] == 'on') {
                            $("td[data-id=" + data['id'] + "]").find("label").data("do", "off").removeClass("label-default").addClass("label-success").text("已启用").attr("title", "点击关闭");
                            $("td[data-id=" + data['closeid'] + "]").find("label").data("do", "on").removeClass("label-success").addClass("label-default").text("未启用").attr("title", "点击开启");
                        } else {
                            $("td[data-id=" + data['id'] + "]").find("label").data("do", "on").removeClass("label-success").addClass("label-default").text("未启用").attr("title", "点击开启");
                        }
                    },
                    error: function () {
                        alert('操作失败~请刷新页面重试！');
                    }
                });
            }
            $('.js-clip').each(function () {
                util.clip(this, $(this).attr('data-url'));
            });
        </script>


    </div>


@endsection