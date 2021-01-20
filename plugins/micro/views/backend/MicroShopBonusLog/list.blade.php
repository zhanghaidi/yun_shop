@extends('layouts.base')
@section('title', '分红记录')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active">分红统计</li>
                </ul>
            </div>
            <div class="panel-body">
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    未结算分红：{!! number_format($no_bonus_money, 2) !!}元<br>已结算分红：{!! number_format($ok_bonus_money, 2) !!}元<br>分红总金额：{!! number_format($total_bonus_money, 2) !!}元
                </div>
            </div>
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active">分红记录</li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info"><!--
                <div class="panel-heading">筛选</div>-->
                <div class="panel-body">
                    <form action="" method="post" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="route" value="plugin.micro.backend.controllers.MicroShopBonusLog.list" id="route" />
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>-->
                            <div class="">
                                <input type="text" class="form-control"  name="search[order_sn]" value="{{$request['order_sn']}}" placeholder="可搜索订单编号"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>-->
                            <div class="">
                                <input type="text" class="form-control"  name="search[member]" value="{{$request['member']}}" placeholder="可搜索昵称/姓名/手机号"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            {{--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微店名称</label>--}}
                            <div class="">
                                <input type="text" class="form-control"  name="search[shop_name]" value="{{$request['shop_name']}}" placeholder="可搜索微店名称"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            {{--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微店等级</label>--}}
                            <div class="">
                                <select name='search[level_id]' class='form-control'>
                                    <option value=''>等级不限</option>
                                    @foreach($levels as $level)
                                        <option value='{{$level->id}}'
                                                @if($request['level_id'] == $level->id)
                                                selected
                                                @endif
                                        >{{$level->level_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            {{--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微店等级</label>--}}
                            <div class="">
                                <select name='search[is_lower]' class='form-control'>
                                    <option value='1'@if($request['is_lower'] == 1)
                                    selected
                                            @endif>下级微店分红</option>
                                    <option value='0'@if($request['is_lower'] == 0)
                                    selected
                                            @endif>微店分红</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            {{--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微店等级</label>--}}
                            <div class="">
                                <select name='search[apply_status]' class='form-control'>
                                    <option value='' @if(array_get($request,'apply_status',''))
                                    selected
                                            @endif>分红状态</option>
                                    <option value='1' @if(array_get($request,'apply_status','') == 1)
                                    selected
                                            @endif>已结算</option>
                                    <option value='0' @if(array_get($request,'apply_status','') === '0')
                                    selected
                                            @endif>未结算</option>
                                    <option value='-1' @if(array_get($request,'apply_status','') == -1)
                                    selected
                                            @endif>已失效</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                            <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
                            <div class="">
                                <button type="button" name="export" value="1" id="export" class="btn btn-default excel back ">导出 Excel</button>
                                <input type="hidden" name="token" value="{{$var['token']}}" />
                                <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>

                            </div>
                        </div>

                    </form>
                </div>
            </div><div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">总数：{{$list->total()}}   </div>
                    <div class="panel-body" style="margin-bottom:200px">
                        <table class="table table-hover" style="overflow:visible">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:16%;text-align: center;'>下单时间<br>结算/失效时间</th>
                                <th style='width:12%;text-align: center;'>订单编号</th>
                                <th style='width:10%;text-align: center;'>店主</th>
                                <th style='width:8%;text-align: center;'>店主等级</th>
                                <th style='width:8%;text-align: center;'>业务类型</th>
                                <th style='width:12%;text-align: center;'>商品金额</th>
                                <th style='width:12%;text-align: center;'>分红结算金额</th>
                                <th style='width:16%;text-align: center;'>下级店主分红金额</th>
                                <th style='width:8%;text-align: center;'>分红比例</th>
                                <th style='width:8%;text-align: center;'>分红状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $row)
                                <tr>
                                    <td style="text-align: center;">
                                        {{$row->created_at}}<br>
                                        @if($row->apply_status == 1)
                                            {!! date('Y-m-d H:i:s', $row->apply_time) !!}
                                        @elseif($row->apply_status == -1)
                                            {!! date('Y-m-d H:i:s', $row->refund_time) !!}
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{!! yzWebUrl('plugin.micro.backend.controllers.MicroShopBonusLog.detail.index', ['id' => $row->order_id]) !!}">{{$row->order_sn}}</a>
                                    </td>
                                    <td style="text-align: center;">
                                        <img src='{{$row->hasOneMember->avatar}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                                        <br/>
                                        <a href="{!! yzWebUrl($member_detail_url,['id' => $row->hasOneMember->uid])!!}">@if ($row->hasOneMember->nickname) {{$row->hasOneMember->nickname}} @else {{$row->hasOneMember->mobile}} @endif</a>
                                    </td>
                                    <td style="text-align: center;">{{$row->hasOneMicroShopLevel->level_name}}</td>
                                    <td style="text-align: center;">
                                        {{$row->mode_type}}
                                    </td>
                                    <td style="text-align: center;">
                                        <label class="label label-info">{{$row->goods_price}}元</label>
                                    </td>
                                    <td style="text-align: center;">
                                        <label class="label label-info">{{$row->bonus_money}}元</label>
                                    <td style="text-align: center;">
                                        <label class="label label-info">{{$row->lower_level_bonus_money}}元</label>
                                    </td>
                                    <td style="position: relative;overflow: visible;">
                                        <label class="label label-info">@if($row->is_lower == 0){{$row->bonus_ratio}}@else {{$row->agent_bonus_ratio}} @endif%</label>
                                    </td>
                                    <td style="position: relative;overflow: visible;">
                                        @if ($row->apply_status == 0)
                                            <label class="label label-default">
                                        @elseif($row->apply_status == 1)
                                            <label class="label label-success">
                                        @elseif($row->apply_status == -1)
                                            <label class="label label-danger">
                                        @endif
                                            {{$row->status_name}}
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!!$pager!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script language='javascript'>
        $('.umphp').hover(function () {
                    var url = $(this).attr('data-url');
                    $(this).addClass("selected");
                },
                function () {
                    $(this).removeClass("selected");
                })
        $('.js-clip').each(function () {
            util.clip(this, $(this).attr('data-url'));
        });
        $(function () {
            $('#export').click(function(){
                $('#route').val("plugin.micro.backend.controllers.MicroShopBonusLog.list.export");
                $('#form1').submit();
                $('#route').val("plugin.micro.backend.controllers.MicroShopBonusLog.list");
            });
        });
    </script>
@endsection