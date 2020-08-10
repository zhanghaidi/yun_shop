@extends('layouts.base')
@section('title','商品追踪列表')

@section('content')
    <div id="member-blade" class="rightlist">
        <div class="panel panel-info">
            <div class="panel-heading">
                <span>当前位置：</span>
                <a href="{{yzWebUrl('tracking.goods-tracking.index')}}">
                    <span>商品追踪</span>
                </a>
                <span>>></span>
                <a href="#">
                    <span>追踪列表</span>
                </a>
            </div>
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">记录总数：{{ $pageList->total() }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:6%; text-align: center;'>主键ID</th>
                            <th style='width:12%; text-align: center;'>来源类型</th>
                            <th style='width:12%; text-align: center;'>所属资源</th>
                            <th style='width:12%; text-align: center;'>商品信息</th>
                            <th style='width:12%; text-align: center;'>操作用户</th>
                            <th style='width:12%; text-align: center;'>操作动作</th>
                            <th style='width:12%; text-align: center;'>动作变量</th>
                            <th style='width:12%; text-align: center;'>订单号</th>
                            <th style='width:12%; text-align: center;'>报点时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr>
                                <td style="text-align: center;">{{ $list->id }}</td>
                                <td style="text-align: center;">
                                    @if($list->type_id == 1) 穴位
                                    @elseif ($list->type_id == 2) 病例
                                    @elseif ($list->type_id == 3) 文章
                                    @elseif ($list->type_id == 4) 话题
                                    @elseif ($list->type_id == 5) 体质
                                    @elseif ($list->type_id == 6) 灸师
                                    @endif
                                </td>

                                <td style="text-align: center;">
                                    @if($list->type_id == 1) {{ $list->resource->name }}
                                    @elseif ($list->type_id == 2) 病例
                                    @elseif ($list->type_id == 3) {{ $list->resource->title }}
                                    @elseif ($list->type_id == 4) {{ $list->resource->title }}
                                    @elseif ($list->type_id == 5) {{ $list->resource->name }}
                                    @elseif ($list->type_id == 6) {{ $list->resource->username }}
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{yzWebUrl('goods.goods.index')}}" title="{{ $list->goods->title }}">
                                        <img src="{{yz_tomedia($list->goods->thumb)}}" style='width:45px;height:45px;padding:1px;border:1px solid #ccc' />
                                        <br/>
                                        {{ $list->goods->title }}
                                    </a>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{yzWebUrl('member.member.index',array('search[mid]' => $list->user_id ))}}">
                                        <img src='{{$list->user->avatarurl}}'
                                             style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/><br/>
                                        {{ $list->user->nickname }}
                                    </a>
                                </td>
                                <td style="text-align: center;">{!! $list->action_name !!}</td>
                                <td style="text-align: center;">{{ $list->val }}</td>
                                <td style="text-align: center;">{{ $list->order->order_sn }}</td>
                                <td style="text-align: center;">{{date('Y-m-d H:i:s', $list->create_time)}}</td>
                                <td style="overflow:visible; text-align: center;">
                                    <a class='btn btn-default' href="{{ yzWebUrl('tracking.goods-tracking.index', array('id' => $list->id)) }}" style="margin-bottom: 2px">详细记录</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $page !!}

                </div>
            </div>
        </div>


@endsection('content')
