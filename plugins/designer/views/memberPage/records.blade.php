@extends('layouts.base')
@section('title', '店铺装修')
@section('content')
    <link href="{{ plugin_assets('designer', 'assets/css/designer.css') }}" rel="stylesheet">
    <div class="rightlist">
        <!-- 筛选区域 -->
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="QDaf"/>
                    <input type="hidden" name="route"
                           value="plugin.designer.Backend.Modules.MemberPage.Controllers.records.index"/>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
                        <div class="col-sm-8 col-lg-9">
                            <input class="form-control" name="search[name]" id="" type="text"
                                   value="{{ $search['name'] or '' }}" placeholder="请输入页面名称进行搜索">
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
            <div class='panel-heading'> 页面管理 (总数: {{ $pageList->total() }})</div>
            <div class='panel-body'>
                <table class="table">
                    <thead>
                    <tr>
                        <th style="width:5%; text-align: center;">ID</th>
                        <th style="width:35%;text-align: center;">上次修改时间</th>
                        <th style="width:25%;text-align: left;">页面名称</th>
                        <th style="width:35%;text-align: left;">客户端类型</th>
                        <th style="width:35%;text-align: left;">页面类型</th>
                        <th style="width:15%;text-align: left;">是否默认</th>
                        <th style="width:30%;text-align: center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($pageList->total() >= 1)
                        @foreach($pageList as $key => $list)
                            <tr pageid="{{ $list->id }}" style="height: 59px">

                                <td style="width:60px; text-align: center;">{{ $list->id }}</td>
                                <td style="text-align:  center;">{{ $list->updated_at }}</td>

                                <td style="text-align: left;">{{ $list->page_name }}</td>
                                <td style="text-align: left;">
                                    @if(in_array(1, $list->page_type_cast))
                                        <label class='label label-success'>公众号</label>
                                    @endif
                                    @if(in_array(2, $list->page_type_cast))
                                        <label class='label label-danger'>小程序</label>
                                    @endif
                                    @if(in_array(7, $list->page_type_cast))
                                        <label class='label label-primary'>APP</label>
                                    @endif
                                    @if(in_array(8, $list->page_type_cast))
                                        <label class='label label-info'>支付宝</label>
                                    @endif
                                    @if(in_array(5, $list->page_type_cast))
                                        <label class='label label-warning'>WAP</label>
                                    @endif
                                    @if(in_array(9, $list->page_type_cast))
                                        <label class='label label-danger'>小程序（原生版）</label>
                                    @endif
                                </td>
                                <td style="text-align: left;">会员中心首页</td>
                                <td style="text-align: left">
                                    @if($list->is_default == 1)
                                        <label class='label label-success' style="cursor: pointer;" title="点击关闭"
                                               data-do="off"
                                               onclick="setMemberDefault(this,{{ $list->id }})">已启用</label>
                                    @else
                                        <label class='label label-default' style="cursor: pointer;" title="点击开启"
                                               data-do="on" onclick="setMemberDefault(this,{{ $list->id }})">未启用</label>
                                    @endif
                                </td>
                                <td style=" text-align:  center;position:relative">
                                    <a href="javascript:;" onclick="preview({{ $list->id }})">预览</a> -
                                    {{--<a href="javascript:;" data-url="{{ yzAppUrl('plugin.designer.home.index.page', array('page_id' => $list->id)) }}" class="js-clip" title="复制链接">复制链接</a>
                                    <b style="cursor:pointer" title="请手动在设置中开启flash,或者右键->运行插件">?</b>---}}
                                    @if(in_array(9, $list->page_type_cast) || in_array(10, $list->page_type_cast))
                                        <a href="{{ yzWebUrl('plugin.designer.admin.member-list.update', array('id' => $list->id, 'page_type' => 9)) }}">编辑</a>
                                        -
                                    @else
                                        <a href="{{ yzWebUrl('plugin.designer.admin.member-list.update', array('id' => $list->id)) }}">编辑</a>
                                        -
                                    @endif
                                    <a href="javascript:;" onclick="delpage({{ $list->id }})">删除</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td style="text-align: center; line-height: 100px;" colspan="8">亲~您还没有添加自定义页面哦~您可以尝试 ↙ 左下角的
                                “<a href="{{ yzWebUrl('plugin.designer.admin.member-list.store') }}">添加一个新页面</a>”
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="8">
                            <a class='btn btn-success' href="{{ yzWebUrl('plugin.designer.admin.member-list.store') }}">
                                <i class="fa fa-plus"></i>
                                添加会员中心装修页面
                            </a>
                            <a class='btn btn-success'
                               href="{{ yzWebUrl('plugin.designer.admin.member-list.store', ['page_type' => 9]) }}">
                                <i class="fa fa-plus"></i>
                                添加小程序（原生版）
                            </a>
                            <span>Tips:同一个类型仅允许设置一个默认开启页面，其他页面为默认关闭页面</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" style="padding:0px; margin: 0px;">{!! $page !!}</td>
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
                            <iframe style="border:0px; width:342px; height:600px; padding:0px; margin: 0px;"
                                    src=""></iframe>
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
                var url = "{!! yzWebUrl('plugin.designer.admin.member-list.preview') !!}&preview=1&page_id=" + pageid;
                $('#modal-module-menus2').find("iframe").attr("src", url);
                popwin = $('#modal-module-menus2').modal();
            }

            function delpage(id) {
                if (confirm('此操作不可恢复，确认删除？')) {
                    $.ajax({
                        type: 'POST',
                        url: "{!! yzWebUrl('plugin.designer.admin.member-list.destory') !!}",
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
                    url: "{!! yzWebUrl('plugin.designer.admin.member-list.setToDefault') !!}",
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

            //修改默认
            function setMemberDefault(t, id) {
                op = $(t).data("do");
                $.ajax({
                    url: "{!! yzWebUrl('plugin.designer.admin.member-list.setMemberDefault') !!}",
                    type: 'POST',
                    data: {op: op, id: id},
                    success: function (data) {
                        if (data.result == 1) {
                            location.reload();
                            return;
                        }
                        alert("操作失败~请刷新页面重试");
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