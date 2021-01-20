@extends('layouts.base')

@section('content')
@section('title', trans('微店等级列表'))
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">微店等级</a></li>

            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <form action="" method="post">
            <div class='panel panel-default'>
                <div class="panel-heading">总数：{{$list->total()}}   </div>
                <div class='panel-body'>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>等级权重</th>
                            <th>等级名称</th>
                            <th>分红比例</th>
                            <th style="width:26%">商品</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $row)
                            <tr>
                                <td><span class="label label-danger">{{ $row->level_weight }}</span></td>
                                <td>{{ $row->level_name }}</td>
                                <td>{{ $row->bonus_ratio }}</td>
                                <td>
                                    <img src='{{tomedia($row->hasOneGoods->thumb)}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                                    <br/>
                                    {{$row->hasOneGoods->title}}
                                </td>
                                <td>
                                    <a class='btn btn-default' href="{{ yzWebUrl($operation_url['edit_level_url'], ['id' => $row->id]) }}" title="编辑／查看"><i class='fa fa-edit'></i></a>
                                    <a class='btn btn-default' href="{{ yzWebUrl($operation_url['delete_level_url'], ['id' => $row->id]) }}" onclick="return confirm('确认删除此等级吗？');return false;"><i class='fa fa-remove'></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{--{!! $pager !!}--}}
                <div class='panel-footer'>
                    <a class='btn btn-info' href="{{ yzWebUrl($operation_url['add_level_url']) }}"><i class="fa fa-plus"></i> 添加新等级</a>
                </div>
            </div>
        </form>
    </div>
@endsection