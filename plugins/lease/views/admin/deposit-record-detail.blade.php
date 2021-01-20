@extends('layouts.base')
@section('title', '记录列表')
@section('content')

    
    <div class="w1200 m0a">
        <div class="rightlist" style="padding-bottom:100px">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{!! yzWebUrl('plugin.lease-toy.admin.deposit-record.index') !!}">记录列表</a></li>
                    <li><a  style="padding-left:0" href="javascript:void"><i class="fa fa-angle-double-right"></i>押金明细</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info"><!--
                <div class="panel-heading">筛选</div>-->
                <div class="panel-body">
                    <form action="" method="get" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="c" value="site"/>
                        <input type="hidden" name="a" value="entry"/>
                        <input type="hidden" name="m" value="yun_shop"/>
                        <input type="hidden" name="do" value="plugin" id="form_do"/>
                        <input type="hidden" name="route" value="plugin.lease-toy.admin.deposit-record.abcd" id="route"/>
                        <input type="hidden" name="lease_id" value="{{$lease['lease_id']}}"/>
                        {{--<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control" name="search[realname]"
                                       value="{{$search['realname']}}" placeholder="可搜索昵称/姓名/手机号"/>
                            </div>
                        </div>
                         <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <select name="search[level]" class='form-control'>
                                    <option value=''>会员等级不限</option>
                                    @foreach($levels as $level)
                                        <option value="{{$level['id']}}"
                                                @if($search['level']==$level['id'])
                                                selected
                                                @endif
                                        >{{$level['level_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        --}}
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <select name="search[status]" class='form-control'>
                                    <option value=''>押金状态</option>
                                        <option value="0"
                                            @if($search['status']=='0') selected @endif >
                                            {{$lease['frozen']}}
                                        </option>
                                        <option value="3"
                                            @if($search['status']=='3') selected @endif >
                                            {{$lease['return']}}
                                        </option>
                                </select>
                            </div>
                        </div>
                         <div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg">

                            <div style="width: 30%;float: left;">

                                <select name='search[searchtime]' class='form-control'>
                                    <option value='0'
                                            @if($search['searchtime']=='0')
                                            selected
                                            @endif>不搜索
                                    </option>
                                    <option value='1'
                                            @if($search['searchtime']=='1')
                                            selected
                                            @endif>搜索创建时间
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

                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <button class="btn btn-success" id="search"><i class="fa fa-search"></i> 搜索</button>
                                
                                <button type="button" name="export" value="1" id="export" class="btn btn-default">导出
                                </button>

                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading" style="padding: 10px 10px">
                        押金总数：<span style="color: red">{{$lease['total']['and'] ?:0}}</span>元；
                        冻结押金：<span style="color: red">{{$lease['total']['frozens'] ?:0}}</span>元；
                        已退还押金：<span style="color: red">{{$lease['total']['returns']?:0}}</span>元
                    </div>
                    <div class="panel-body" style="margin-bottom:200px">
                        <table class="table table-hover" style="overflow:visible">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:7%;text-align: center;'>会员ID</th>
                                <th style='width:12%;text-align: center;'>粉丝</th>
                                <th style='width:10%;'>会员等级</th>
                                <th style='width:15%;'>订单号</th>
                                <th style='width:12%;'>押金(元)</th>
                                <th style='width:12%;'>退还金额</th>
                                <th style='width:10%;'>状态</th>
                                <th style='width:15%;'>时间</th>
                                <th style='width:13%'>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list['data'] as $row)
                                <tr>
                                    <td style="text-align: center;">{{$row['member_id']}}</td>
                                    <td style="text-align: center;">
                                        @if(!empty($row['belongs_to_member']['avatar']))
                                            <img src="{{$row['belongs_to_member']['avatar']}}"
                                                 style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/><br/>
                                        @endif
                                        @if(empty($row['belongs_to_member']['nickname']))
                                            未更新
                                        @else
                                            {{$row['belongs_to_member']['nickname']}}
                                        @endif
                                    </td>
                                    <td>
                                        {{$row['levelname']}}
                                    </td>
                                    <td>{{$row['order_sn']}}</td>
                                    <td>{{$row['deposit_total']}}</td>
                                    <td>{{$row['return_deposit']}}</td>
                                    <td>
                                        @if($row['return_status'] == 3)
                                            {{$lease['return']}}

                                        @else
                                            {{$lease['frozen']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($row['return_status'] == 3) 
                                            {{$row['return_time']}}
                                        @else
                                            {{$row['start_time']}}
                                        @endif
                                    </td>
                                    <td  style="overflow:visible;">
                                        <a class="btn btn-info" href="{{yzWebUrl('plugin.lease-toy.admin.order.detail', ['id' => $row['order_id']])}}">订单详情</span></a>

                                    </td>
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
        $(function () {
            $('#export').click(function () {
                //$('#form1').attr('action', '{!! yzWebUrl('plugin.lease-toy.admin.deposit-record.detail-export') !!}');
                $('#route').val("plugin.lease-toy.admin.deposit-record.detail-export");
                $('#form1').submit();
            });
            $('#search').click(function () {
                //$('#form1').attr('action', '{!! yzWebUrl('plugin.lease-toy.admin.deposit-record.detail') !!}');
                $('#route').val("plugin.lease-toy.admin.deposit-record.detail");
                $('#form1').submit();
            });
        });
    </script>
@endsection