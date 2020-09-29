@extends('layouts.base')
@section('title','商品埋点列表')

@section('content')
    <div id="member-blade" class="rightlist">
        <div class="panel panel-info">
            <div class="panel-heading">
                <span>当前位置：</span>
                <a href="{{yzWebUrl('tracking.goods-tracking.index')}}">
                    <span>商品埋点</span>
                </a>
                <span>>></span>
                <a href="#">
                    <span>埋点列表</span>
                </a>
            </div>


            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form1">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="tracking" id="form_do"/>
                    <input type="hidden" name="route" value="tracking.goods-tracking.index" id="route"/>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>-->
                        <div class="">
                            <input type="text" class="form-control" name="search[realname]"
                                   value="{{$search['realname']}}" placeholder="可搜索会员ID/昵称/姓名/手机号"/>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">ID</label>-->
                        <div class="">
                            <input type="text" placeholder="商品ID/商品名" class="form-control" name="search[keywords]"
                                   value="{{$search['keywords']}}"/>
                        </div>
                    </div>

                    {{--<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员等级</label>-->
                        <div class="">
                            <select name='search[level]' class='form-control'>
                                <option value=''>会员等级不限</option>
                                @foreach($levels as $level)
                                    <option value='{{$level['id']}}'
                                            @if($request['search']['level']==$level['id'])
                                            selected
                                            @endif
                                    >{{$level['level_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>--}}
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <!--  <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员分组</label>-->
                        <div class="">
                            <select name='search[type_id]' class='form-control'>
                                <option value=''>来源类型不限</option>
                                <option value='1'
                                        @if($search['type_id']=='1')
                                        selected
                                        @endif>穴位
                                </option>
                                <option value='3'
                                        @if($search['type_id']=='3')
                                        selected
                                        @endif>文章
                                </option>
                                <option value='4'
                                        @if($search['type_id']=='4')
                                        selected
                                        @endif>帖子
                                </option>
                                <option value='5'
                                        @if($search['type_id']=='5')
                                        selected
                                        @endif>体质
                                </option>
                                <option value='6'
                                        @if($search['type_id']=='6')
                                        selected
                                        @endif>灸师
                                </option>
                                <option value='7'
                                        @if($search['type_id']=='7')
                                        selected
                                        @endif>课时
                                </option>
                                <option value='8'
                                        @if($search['type_id']=='8')
                                        selected
                                        @endif>直播
                                </option>
                                <option value='9'
                                        @if($search['type_id']=='9')
                                        selected
                                        @endif>商城
                                </option>
                                <option value='10'
                                        @if($search['type_id']=='10')
                                        selected
                                        @endif>活动
                                </option>
                                <option value='11'
                                        @if($search['type_id']=='11')
                                        selected
                                        @endif>分享
                                </option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <!--      <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否关注</label>-->
                        <div class="">
                            <select name='search[action_id]' class='form-control'>
                                <option value=''>操作动作不限</option>
                                </option>
                                <option value='1'
                                        @if($search['action_id']=='1')
                                        selected
                                        @endif
                                >查看
                                </option>
                                <option value='2'
                                        @if($search['action_id']=='2')
                                        selected
                                        @endif
                                >收藏
                                </option>
                                <option value='3'
                                        @if($search['action_id']=='3')
                                        selected
                                        @endif
                                >加购
                                </option>

                                <option value='4'
                                        @if($search['action_id']=='4')
                                        selected
                                        @endif
                                >下单
                                </option>

                                <option value='5'
                                        @if($search['action_id']=='5')
                                        selected
                                        @endif
                                >支付
                                </option>

                            </select>
                        </div>
                    </div>
                    <div class="search-select">
                        {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[times]', [
                        'starttime'=>date('Y-m-d H:i', $search['times']['start']),
                        'endtime'=>date('Y-m-d H:i',$search['times']['start']),
                        'start'=>0,
                        'end'=>0
                        ], true) !!}
                    </div>

                    <div class="form-group  col-xs-12 col-md-12 col-lg-6">
                        <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
                        <div class="">
                            <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>
                            {{--<button type="button" name="export" value="1" id="export" class="btn btn-default">导出
                                Excel
                            </button>--}}

                        </div>
                    </div>

                </form>

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
                                    @elseif ($list->type_id == 4) 帖子
                                    @elseif ($list->type_id == 5) 体质
                                    @elseif ($list->type_id == 6) 灸师
                                    @elseif ($list->type_id == 7) 课时
                                    @elseif ($list->type_id == 8) 直播
                                    @elseif ($list->type_id == 9) 商城
                                    @elseif ($list->type_id == 10) 活动
                                    @elseif ($list->type_id == 11) 用户分享
                                    @elseif ($list->type_id == 12) 搜索
                                    @endif
                                </td>

                                <td style="text-align: center;">
                                    {{ $list->resource_id }}<br>
                                    @if($list->type_id == 1) {{ $list->resource->name }}
                                    @elseif ($list->type_id == 2) 病例
                                    @elseif ($list->type_id == 3) {{ $list->resource->title }}
                                    @elseif ($list->type_id == 4) {{ $list->resource->title }}
                                    @elseif ($list->type_id == 5) {{ $list->resource->name }}
                                    @elseif ($list->type_id == 7) {{ $list->resource->title }}
                                    @elseif ($list->type_id == 8) {{ $list->resource->name }}
                                    @elseif ($list->type_id == 9) @if ($list->resource_id ==1) 底部菜单商城 @elseif ($list->resource_id ==2)功能导航商城  @endif
                                    @elseif ($list->type_id == 10) {{ $list->resource->title }}
                                    @elseif ($list->type_id == 11) {{ $list->user->nickname }}
                                    @elseif ($list->type_id == 12) @if ($list->resource_id ==1) 全局搜索 @elseif ($list->resource_id ==2)商城搜索  @endif
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{yzWebUrl('goods.goods.index')}}" title="{{ $list->goods->title }}">
                                        <img src="{{yz_tomedia($list->goods->thumb)}}" style='width:45px;height:45px;padding:1px;border:1px solid #ccc' />
                                        <br/>
                                        {{ $list->goods_id }}
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
                                <td style="text-align: center;">
                                    @if($list->action_id == 4)
                                    <a href="{{yzWebUrl('order.list.index',array('search[ambiguous][field]'=>'order','search[ambiguous][string]'=> $list->order->order_sn))}}">{{ $list->order->order_sn }}</a>
                                    @elseif($list->action_id == 5)
                                    <a href="{{yzWebUrl('order.list.index',array('search[ambiguous][field]'=>'order','search[ambiguous][string]'=> $list->order->pay_sn))}}">{{ $list->order->pay_sn }}</a>
                                    @endif
                                </td>
                                <td style="text-align: center;">{{date('Y-m-d H:i:s', $list->create_time)}}</td>
                                {{--<td style="overflow:visible; text-align: center;">
                                    <a class='btn btn-default' href="{{ yzWebUrl('tracking.goods-tracking.index', array('id' => $list->id)) }}" style="margin-bottom: 2px">详细记录</a>
                                </td>--}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $page !!}

                </div>
            </div>
        </div>


@endsection('content')
