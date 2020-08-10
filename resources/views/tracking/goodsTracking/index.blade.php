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


            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form1">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="member" id="form_do"/>
                    <input type="hidden" name="route" value="member.member.index" id="route"/>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">ID</label>-->
                        <div class="">
                            <input type="text" placeholder="会员ID" class="form-control" name="search[mid]"
                                   value="{{$request['search']['mid']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>-->
                        <div class="">
                            <input type="text" class="form-control" name="search[realname]"
                                   value="{{$request['search']['realname']}}" placeholder="可搜索昵称/姓名/手机号"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <input type="text" class="form-control" name="search[first_count]"
                                   value="{{$request['search']['first_count']}}" placeholder="一级人数"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <input type="text" class="form-control" name="search[second_count]"
                                   value="{{$request['search']['second_count']}}" placeholder="二级人数"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <input type="text" class="form-control" name="search[third_count]"
                                   value="{{$request['search']['third_count']}}" placeholder="三级人数"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <input type="text" class="form-control" name="search[team_count]"
                                   value="{{$request['search']['team_count']}}" placeholder="团队人数"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <input type="text" class="form-control" name="search[custom_value]"
                                   value="{{$request['search']['custom_value']}}" placeholder="自定义字段"/>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
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
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <!--  <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员分组</label>-->
                        <div class="">
                            <select name='search[groupid]' class='form-control'>
                                <option value=''>会员分组不限</option>
                                @foreach($groups as $group)
                                    <option value='{{$group['id']}}'
                                            @if($request['search']['groupid']==$group['id'])
                                            selected
                                            @endif
                                    >{{$group['group_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <select name='search[isagent]' class='form-control'>
                                <option value=''>推广员不限</option>
                                <option value='0'
                                        @if($request['search']['isagent']=='0')
                                        selected
                                        @endif>否
                                </option>
                                <option value='1'
                                        @if($request['search']['isagent']=='1')
                                        selected
                                        @endif>是
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <!--      <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否关注</label>-->
                        <div class="">
                            <select name='search[followed]' class='form-control'>
                                <option value=''>不限关注</option>
                                </option>
                                <option value='1'
                                        @if($request['search']['followed']=='1')
                                        selected
                                        @endif
                                >已关注
                                </option>
                                <option value='0'
                                        @if($request['search']['followed']=='0')
                                        selected
                                        @endif
                                >未关注
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <!--        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">黑名单</label>-->
                        <div class="">
                            <select name='search[isblack]' class='form-control'>
                                <option value=''>不限黑名单</option>
                                <option value='0'
                                        @if($request['search']['isblack']=='0')
                                        selected
                                        @endif>否
                                </option>
                                <option value='1'
                                        @if($request['search']['isblack']=='1')
                                        selected
                                        @endif>是
                                </option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg">

                        <div class="time">

                            <select name='search[searchtime]' class='form-control'>
                                <option value='0'
                                        @if($request['search']['searchtime']=='0')
                                        selected
                                        @endif>注册时间不限
                                </option>
                                <option value='1'
                                        @if($request['search']['searchtime']=='1')
                                        selected
                                        @endif>搜索注册时间
                                </option>
                            </select>
                        </div>
                        <div class="search-select">
                            {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[times]', [
                            'starttime'=>date('Y-m-d H:i', $starttime),
                            'endtime'=>date('Y-m-d H:i',$endtime),
                            'start'=>0,
                            'end'=>0
                            ], true) !!}
                        </div>
                    </div>

                    <div class="form-group  col-xs-12 col-md-12 col-lg-6">
                        <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
                        <div class="">
                            <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>
                            <button type="button" name="export" value="1" id="export" class="btn btn-default">导出
                                Excel
                            </button>


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
                                <td style="text-align: center;"><a href="{{yzWebUrl('order.list.index',array('search[ambiguous][field]'=>'order','search[ambiguous][string]'=> $list->order->order_sn))}}">{{ $list->order->order_sn }}</a></td>
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
