@extends('layouts.base')
@section('title','用户购物车列表')

@section('content')
    <div id="member-blade" class="rightlist">
        <div class="right-titpos">
            @include('layouts.tabs')
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-heading">用户购物车记录筛选</div>

            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form1">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="tracking" id="form_do"/>
                    <input type="hidden" name="route" value="tracking.goods-tracking.cart" id="route"/>

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

                    {{--<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">ID</label>-->
                        <div class="">
                            <input type="text" placeholder="搜索动作/类型" class="form-control" name="search[type]"
                                   value="{{$search['type']}}"/>
                        </div>
                    </div>--}}
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
                   {{-- <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
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
                                --}}{{--<option value='5'
                                        @if($search['type_id']=='5')
                                        selected
                                        @endif>体质
                                </option>--}}{{--
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
                                <option value='12'
                                        @if($search['type_id']=='12')
                                        selected
                                        @endif>搜索
                                </option>
                                <option value='13'
                                        @if($search['type_id']=='13')
                                        selected
                                        @endif>购物车
                                </option>
                                <option value='14'
                                        @if($search['type_id']=='14')
                                        selected
                                        @endif>我的订单
                                </option>
                                <option value='15'
                                        @if($search['type_id']=='15')
                                        selected
                                        @endif>优惠券
                                </option>
                                <option value='13'
                                        @if($search['type_id']=='404')
                                        selected
                                        @endif>未知
                                </option>
                            </select>
                        </div>
                    </div>--}}


                    {{--<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
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
                    </div>--}}
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">

                        <div class="time">
                            <select name='search[search_time]' class='form-control'>
                                <option value='0' @if($search['search_time']=='0') selected @endif>不搜索时间</option>
                                <option value='1' @if($search['search_time']=='1') selected @endif>搜索时间</option>
                            </select>
                            <div class="search-select">
                                {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                                'starttime'=>date('Y-m-d H:i', strtotime($search['time']['start']) ?: strtotime('-1 month')),
                                'endtime'=>date('Y-m-d H:i',strtotime($search['time']['end']) ?: time()),
                                'start'=>0,
                                'end'=>0
                                ], true) !!}
                            </div>
                        </div>

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
                            <th style='width:6%; text-align: center;'>ID</th>
                            <th style='width:6%; text-align: center;'>加购用户</th>
                            <th style='width:6%; text-align: center;'>加购商品</th>
                            <th style='width:12%; text-align: center;'>商品规格</th>
                            <th style='width:12%; text-align: center;'>加购时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr>
                                <td style="text-align: center;">{{ $list->id }}</td>

                                <td style="text-align: center;">
                                    <a href="{{yzWebUrl('tracking.goods-tracking.cart',array('search[realname]' => $list->user_id ))}}">
                                        <img src='{{$list->user->avatarurl}}'
                                             style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/><br/>
                                        {{ $list->user->nickname }}
                                    </a>
                                </td>

                                <td style="text-align: center;">
                                    <a href="{{yzWebUrl('tracking.goods-tracking.cart', array('search[keywords]' => $list->goods_id ))}}" title="{{ $list->goods->title }}">
                                        <img src="{{yz_tomedia($list->goods->thumb)}}" style='width:45px;height:45px;padding:1px;border:1px solid #ccc' />
                                        <br/>
                                        {{ $list->goods_id }}
                                        <br/>
                                        {{ $list->goods->title }}
                                    </a>
                                </td>

                                <td style="text-align: center;">
                                    @if($list->$option)

                                            <img src="{{yz_tomedia($list->option->thumb)}}" style='width:45px;height:45px;padding:1px;border:1px solid #ccc' />
                                            <br/>
                                            {{ $list->option->goods_sn }}
                                            <br/>
                                            {{ $list->option->title }}
                                            <br/>
                                            {{ $list->option->product_price }}

                                    @endif

                                </td>

                                <td style="text-align: center;">{{date('Y-m-d H:i:s', $list->created_at)}}</td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $page !!}

                </div>
            </div>
        </div>


@endsection('content')
