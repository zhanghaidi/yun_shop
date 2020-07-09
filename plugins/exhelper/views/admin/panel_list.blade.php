@extends('layouts.base')

@section('content')
@section('title', trans('电子面单管理'))
<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading"><i class="fa fa-copy"></i> 电子面单列表</div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th style="width:60px;">ID</th>
                    <th>电子面单模板名称</th>
                    <th>快递类型</th>
                    <th>是否默认(只能设置一个)</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $row)
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{$row->panel_name}}</td>
                        <td>{{$company["$row->exhelper_style"]}}</td>
                        <td>
                            @if($row->isdefault == 1)
                                <span class='label label-success'>已设为默认</span>
                            @else
                                <span class='label label-default'>未设为默认</span>
                            @endif
                        </td>
                        <td style="text-align:left;">
                            <a href="{{yzWebUrl('plugin.exhelper.admin.panel.edit', ['id' => $row->id])}}" class="btn btn-default btn-sm" title="编辑"><i class="fa fa-edit"></i></a>

                            <a href="{{yzWebUrl('plugin.exhelper.admin.panel.destory', ['id' => $row->id])}}" class="btn btn-default btn-sm" onclick="return confirm('确认删除此快递单?')" title="删除"><i class="fa fa-times"></i></a>

                            @if($row->is_default == 0)
                                <a href="{{yzWebUrl('plugin.exhelper.admin.panel.isDefault', ['id' => $row->id])}}" class="btn btn-default btn-sm" onclick="return confirm('确认设置默认?')" title="设置默认"><i class="fa fa-check"></i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $pager !!}
            <script>
                require(['bootstrap'], function($) {
                    $('.btn').hover(function() {
                        $(this).tooltip('show');
                    }, function() {
                        $(this).tooltip('hide');
                    });
                });
            </script>
        </div>
        <div class="panel-footer">
            <a class='btn btn-default' href="{{yzWebUrl('plugin.exhelper.admin.panel.add', [])}}"><i class='fa fa-plus'></i>添加电子面单</a>
        </div>
    </div>
</div>
@endsection