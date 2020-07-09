@extends('layouts.base')
@section('title', '自定义菜单')
@section('content')
    <div class="w1200 m0a">
        <!-- 导入CSS样式 -->
        <div class="rightlist">

            <!-- 筛选区域 -->
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="" method="get" class="form-horizontal" role="form">
                        <input type="hidden" name="c" value="site" />
                        <input type="hidden" name="a" value="entry" />
                        <input type="hidden" name="m" value="yun_shop" />
                        <input type="hidden" name="do" value="QDaf" />
                        <input type="hidden" name="route" value="plugin.designer.Backend.Modules.TopMenu.Controllers.records" />
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
                            <div class="col-sm-8 col-lg-9">
                                <input class="form-control" name="search[menu_name]" id="" type="text" value="{{ $search['menu_name'] or '' }}" placeholder="请输入菜单名称进行搜索">
                            </div>
                            <div class=" col-xs-12 col-sm-2 col-lg-2">
                                <button class="btn btn-success"  ><i class="fa fa-search"></i> 搜索</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- 页面列表 -->
            <div class='panel panel-default'>
                <div class='panel-heading'>顶部菜单管理 (总数: {{ $pageList->count() }})</div>
                <div class='panel-body'>
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width:12%; text-align: center;">ID</th>
                                <th style="width:20%; text-align: center;">创建时间</th>
                                <th style="width:48%">菜单名称</th>
                                <th style="width:20%; text-align: center;">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if($pageList)
                            @foreach($pageList as $key => $list)
                                <tr>
                                    <td style="width:10%; text-align: center;">
                                        {{ $list->id }}
                                    </td>
                                    <td style="width:14%; text-align:  center;">
                                        {{ $list->created_at }}
                                    </td>
                                    <td>
                                        {{ $list->menu_name }}
                                    </td>
                                    <td style="width:14%; text-align:  center;">
                                        <a class='btn btn-default' href="{{ yzWebUrl('plugin.designer.Backend.Modules.TopMenu.Controllers.store.index', array('menu_id' => $list->id)) }}">
                                            编辑
                                        </a>
                                        <a class='btn btn-default' href="{{ yzWebUrl('plugin.designer.Backend.Modules.TopMenu.Controllers.delete.index', array('menu_id' => $list->id)) }}:;" onclick="return confirm('确认删除此顶部菜单吗？'); return false;">
                                            删除
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td style="text-align: center; line-height: 100px;" colspan="8">
                                    亲~您还没有添加顶部菜单哦~您可以尝试 ↙ 左下角的 “
                                    <a href="{{ yzWebUrl('plugin.designer.Backend.Modules.TopMenu.Controllers.store.index') }}">
                                        添加一个新菜单
                                    </a>
                                    ”
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="5">
                                <a class='btn btn-success' href="{{ yzWebUrl('plugin.designer.Backend.Modules.TopMenu.Controllers.store.index') }}">
                                    <i class="fa fa-plus"></i>
                                    添加一个新菜单
                                </a>
                                <span>
                                    Tips:顶部菜单
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" style="padding:0px; margin: 0px;">{!! $page !!}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection