@extends('layouts.base')
@section('title', trans('等级管理'))
@section('content')
    <form id="goods-list" action="{!! yzWebUrl($sort_url) !!}" method="post">
        <div class="panel panel-default">
            <div class="panel-heading">等级管理</div>
            <div class="panel-body table-responsive">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                        <tr>
                            <th width="6%">等级权重</th>
                            <th width="6%">等级名称</th>
                            <th width="10%">{{$set['teammanage_name']}}比例</th>
                            <th width="6%">{{$set['team_name']}}</th>
                            <th width="6%">{{$set['thanksgiving_name']}}</th>
                            <th width="10%">{{$set['parenting_name']}}比例</th>
                            <th width="6%">{{$set['referral_name']}}</th>
                            <th width="10%">{{$set['tier_name']}}层级</th>
                            <th width="10%">{{$set['tier_name']}}金额</th>
                            <th width="25%">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list['data'] as $item)
                            <tr>
                                <td>
                                    <input type="text" class="form-control"
                                           name="display_order[{{$item['id']}}]"
                                           value="{{$item['level_weight']}}">
                                </td>
                                <td>{{$item['level_name']}}</td>
                                <td>{{$item['team_manage_ratio']}}%</td>
                                <td>{{$item['team']}}</td>
                                <td>{{$item['thankful']}}</td>
                                <td>{{$item['train_ratio']}}%</td>
                                <td>{{$item['direct']}}</td>
                                <td>{{$item['tier']}}</td>
                                <td>{{$item['tier_amount']}}</td>
                                <td>
                                    <a class="btn btn-default" href="{!! yzWebUrl('plugin.mryt.admin.level.edit', ['id'=>$item['id']]) !!}">编辑</a>
                                    <a class="btn btn-default" href="{!! yzWebUrl('plugin.mryt.admin.level.deleted', ['id'=>$item['id']]) !!}" onclick="return confirm('{{$delete_msg}}');return false;">删除</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            {!!$pager!!}
            <!--分页-->

            </div>
            <div class='panel-footer'>
                @section('sub_sort')
                    <input name="submit" type="submit" class="btn btn-default back" value="权重排序">
                @show

                @section('add_levele')
                    <a class='btn btn-success ' href="{{yzWebUrl('plugin.mryt.admin.level.add')}}"><i
                                class='fa fa-plus'></i> 添加等级</a>
                @show
            </div>

        </div>
    </form>


@endsection
