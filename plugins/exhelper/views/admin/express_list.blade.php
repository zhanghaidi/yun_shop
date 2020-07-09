@extends('layouts.base')

@section('content')
@section('title', trans('快递单信息管理'))
<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading"><i class="fa fa-copy"></i> 快递单模版列表</div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th style="width:60px;">ID</th>
                    <th>快递单模版名称</th>
                    <th>快递类型</th>
                    <th>是否默认(只能设置一个)</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $row)
                <tr>
                    <td>{{$row->id}}</td>
                    <td>{{$row->expressname}}</td>
                    <td>@if(empty($row->expresscom)) 其他快递 @else {{$row->expresscom}} @endif</td>
                    <td>
                        @if($row->isdefault == 1)
                        <span class='label label-success'>已设为默认</span>
                        @else
                        <span class='label label-default'>未设为默认</span>
                        @endif
                    </td>
                    <td style="text-align:left;">
                        <a href="{{yzWebUrl(\Yunshop\Exhelper\common\models\Express::EXPRESS_EDIT_URL, ['express_id' => $row->id])}}" class="btn btn-default btn-sm" title="编辑"><i class="fa fa-edit"></i></a>

                        <a href="{{yzWebUrl(\Yunshop\Exhelper\common\models\Express::EXPRESS_DEL_URL, ['express_id' => $row->id])}}" class="btn btn-default btn-sm" onclick="return confirm('确认删除此快递单?')" title="删除"><i class="fa fa-times"></i></a>

                        @if($row->is_default == 0)
                        <a href="{{yzWebUrl(\Yunshop\Exhelper\common\models\Express::EXPRESS_DEFAULT_URL, ['express_id' => $row->id])}}" class="btn btn-default btn-sm" onclick="return confirm('确认设置默认?')" title="设置默认"><i class="fa fa-check"></i></a>
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
            <a class='btn btn-default' href="{{yzWebUrl(\Yunshop\Exhelper\common\models\Express::EXPRESS_ADD_URL)}}"><i class='fa fa-plus'></i>添加快递单</a>
        </div>
    </div>
</div>
@endsection