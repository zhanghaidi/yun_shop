@extends('layouts.base')

@section('content')
@section('title', trans('分销等级管理'))
    <section class="content">
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="">分销等级管理</a></li>
            </ul>
        </div>
        <form action="" method="post" onsubmit="return formcheck(this)">
            <div class='panel panel-default'>

                <div class='panel-body'>
                  <div class="table-responsive ">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>等级权重</th>
                            <th>等级名称</th>
                            @if($set['level']>=1)
                                <th>一级比例</th> @endif
                            @if($set['level']>=2)
                                <th>二级比例</th> @endif
                            @if($set['level']>=3)
                                <th>三级比例</th> @endif
                            <th style="width: 210px;">升级条件</th>
                            <th>等级人数</th>
                            <th style="15%">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $row)
                            <tr>
                                <td>{{$row->level}}</td>
                                <td>{{$row->name}}</td>
                                @if($set['level']>=1)
                                    <td>{{$row->first_level}}%</td>
                                @endif
                                @if($set['level']>=2)
                                    <td>{{$row->second_level}}%</td>
                                @endif
                                @if($set['level']>=3)
                                    <td>{{$row->third_level}}%</td>
                                @endif
                                <td>
                                    @if($row['upgrades'])
                                        @foreach($row['upgrades'] as $type => $upgrade)
                                            @if($type == 'self_order_after')
                                                @unset($upgrade)
                                            @endif
                                            @if($type != 'goods' && $type != 'self_order_after' && $type != 'buy_and_sum' && $type != 'many_good')
                                                {{$upgrade['type']}}:{{$upgrade['value']}} {{$upgrade['unit']}}</br>
                                            @else
                                                {{$upgrade['type']}}</br><i></i>
                                            @endif
                                        @endforeach
                                    @else
                                        无升级条件
                                    @endif
                                </td>
                                <td>
                                    {{$row->agent_count}}
                                </td>
                                <td>
                                    <a class='btn btn-default' href="{{yzWebUrl("plugin.commission.admin.level.edit", ['id'=>$row['id']])}}">编辑</a>
                                    <a class='btn btn-default' href="{{yzWebUrl("plugin.commission.admin.level.deleted", ['id'=>$row['id']])}}"
                                       onclick="return confirm('确认删除此等级吗？');return false;">删除</a>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                  </div>
                </div>
                {!! $pager !!}
                <div class='panel-footer'>
                    <a class='btn btn-primary'
                       href="{{yzWebUrl("plugin.commission.admin.level.add")}}"><i
                                class="fa fa-plus"></i> 添加新等级</a>
                </div>
            </div>
        </form>

    </section><!-- /.content -->
@endsection