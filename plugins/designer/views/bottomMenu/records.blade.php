@extends('layouts.base')
@section('title', '店铺菜单')
@section('content')
    <div class="w1200 m0a">
        <!-- 导入CSS样式 -->
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
                        @if($ingress == 'weChatApplet')
                            <input type="hidden" name="route" value="plugin.designer.Backend.Modules.WeChatAppletBottomMenu.Controllers.records.index"/>
                        @else
                            <input type="hidden" name="route" value="plugin.designer.Backend.Modules.BottomMenu.Controllers.records.index"/>
                        @endif
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
                            <div class="col-sm-8 col-lg-9">
                                <input class="form-control" name="search[menu_name]" id="" type="text" value="{{ $search['menu_name'] or '' }}" placeholder="请输入菜单名称进行搜索">
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
                <div class='panel-heading'> 自定义菜单管理 (总数: {{ $pageList->count() }})</div>
                <div class='panel-body'>
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="width:10%; text-align: center;">ID</th>
                            <th style="width:48%">菜单名称</th>
                            <th style="width:14%; text-align: center;">创建时间</th>
                            <th style="width:14%; text-align: center;">
                                @if($ingress != 'weChatApplet')
                                是否默认
                                @endif
                            </th>
                            <th style="width:14%; text-align: center;">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($pageList)
                            @foreach($pageList as $list)
                                <tr menuid="{{ $list->id }}">
                                    <td style="width:10%; text-align: center;">
                                        {{ $list->id }}
                                    </td>
                                    <td>
                                        {{ $list->menu_name }}
                                    </td>
                                    <td style="width:14%; text-align:  center;">
                                        {{ $list->created_at }}
                                    </td>
                                    @if($ingress != 'weChatApplet')
                                    <td style="width:14%; text-align:  center;" data-id="{{ $list->id }}">
                                        @if($list->is_default == 1)
                                            <label class='label label-success' style="cursor: pointer;" title="点击关闭" data-do="off" onclick="setDefault(this,{{ $list->id }})">已启用</label>
                                        @else
                                            <label class='label label-default' style="cursor: pointer;" title="点击开启" data-do="on" onclick="setDefault(this,{{ $list->id }})">未启用</label>
                                        @endif
                                    </td>
                                    @else
                                        <td style="width:14%; text-align:  center;" data-id="{{ $list->id }}"></td>
                                    @endif

                                    <td style="width:14%; text-align:  center;">
                                        <a class='btn btn-default' href="{{ $storeUrl }}&menu_id={{ $list->id }}">
                                            <span>编辑</span>
                                        </a>
                                        <a class='btn btn-default' href="javascript:;" onclick="deleteMenu({{ $list->id }})">
                                            <span>删除</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td style="text-align: center; line-height: 100px;" colspan="8">
                                    <span>亲~您还没有添加自定义菜单哦~您可以尝试 ↙ 左下角的 “</span>
                                    <a href="{{ $storeUrl }}">
                                        <span>添加一个新菜单</span>
                                    </a>
                                    <span>”</span>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="5">
                                <a class='btn btn-success' href="{{ $storeUrl }}">
                                    <i class="fa fa-plus"></i>
                                    <span>添加一个新菜单</span>
                                </a>
                                <span>Tips:自定义菜单启用默认后将代替系统默认菜单</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" style="padding:0px; margin: 0px;">{!! $pager !!}</td>
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
                                <div style="height:52px; width: 52px; border-radius: 52px; margin:20px 0px 0px 159px; cursor: pointer;" data-dismiss="modal" aria-hidden="true" title="点击关闭"></div>
                            </div>
                        </div>
                        <div class="fe-phone-right"></div>
                    </div>
                </div>
            </div>
            <!-- 预览 end -->

            <script type="text/javascript">

                var defaultUrl = "{!! $defaultUrl !!}";
                var destroyUrl = "{!! $destroyUrl !!}";

                function preview(menuid) {
                    var url = "{php echo $this->createPluginMobileUrl('designer')}&preview=1&menuid=" + menuid;
                    $('#modal-module-menus2').find("iframe").attr("src", url);
                    popwin = $('#modal-module-menus2').modal();
                }


                //修改默认
                function setDefault(t, id) {
                    op = $(t).data("do");
                    $.ajax({
                        url: defaultUrl,
                        type: 'POST',
                        data: {op: op, menu_id: id},
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

                //删除菜单
                function deleteMenu(id) {
                    if (confirm('此操作不可恢复，确认删除？')) {
                        $.ajax({
                            url: destroyUrl,
                            type: 'POST',
                            data: {menu_id: id},
                            success: function (data) {
                                if (data.result == 1) {
                                    $("tr[menuid=" + id + "]").fadeOut();
                                } else {
                                    alert('操作失败~请刷新页面重试！');
                                }
                            },
                            error: function () {
                                alert('操作失败~请刷新页面重试！');
                            }
                        });
                    }
                }
            </script>
        </div>
    </div>

@endsection
