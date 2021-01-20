@extends('layouts.base')

@section('content')
@section('title', trans('活动报名订单'))

<link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>
<div class="w1200 m0a">
    <script type="text/javascript" src="{{static_url('js/dist/jquery.gcjs.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/jquery.form.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>

    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="panel panel-info">
            <div class="panel-body">
                <div class="card">
                    <div class="card-header card-header-icon" data-background-color="rose">
                        <i class="fa fa-bars" style="font-size: 24px;" aria-hidden="true"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="card-title">订单管理</h4>
                        <form action="" method="post" class="form-horizontal" role="form" id="form1">
                            <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                                <input type="text" class="form-control"  name="search[member]" value="{{$search['member']?$search['member']:''}}" placeholder="购买者：会员ID/昵称/姓名/手机"/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                                <input type="text" class="form-control"  name="search[recommend_name]" value="{{$search['recommend_name']?$search['recommend_name']:''}}" placeholder="推荐者：会员ID/昵称/姓名/手机"/>
                            </div>
                            <div class='form-group col-xs-12 col-sm-2 col-md-2 col-lg-2'>
                                <select name="search[status]" class="form-control">
                                    <option value=""
                                            @if($search['status'] == '')  selected="selected"@endif>
                                        订单状态
                                    </option>
                                    <option value="1"
                                            @if($search['status'] == '1')  selected="selected"@endif>
                                        未完成
                                    </option>
                                    <option value="3"
                                            @if($search['status'] == '3')  selected="selected"@endif>
                                        已完成
                                    </option>
                                </select>
                            </div>
                            <div class='form-group col-xs-12 col-sm-6 col-md-6 col-lg-6'>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="search[is_time]" value="1"
                                               @if($search['is_time'] == '1')checked="checked"@endif>
                                    </span>
                                    {!!app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                                                                            'starttime'=>$search['time']['start'],
                                                                            'endtime'=>$search['time']['end'],
                                                                            'start'=>0,
                                                                            'end'=>0
                                                                            ], true)!!}
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                <button class="btn btn-success" id="search"><i class="fa fa-search"></i> 搜索</button>
                                <button type="submit" name="export" value="1" id="export" class="btn btn-default">导出 Excel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <table class='table' style='float:left;margin-bottom:0;table-layout: fixed;line-height: 40px;height: 40px'>
                <tr class='trhead'>
                    <td colspan='8' style="text-align: left;">
                        订单数: <span id="total">{{$total}}</span>
                    </td>
                </tr>
            </table>

            <div class=" order-info">
                <div class="table-responsive">
                <table class='table order-title table-hover table-striped'>
                    <thead>
                        <tr>
                            <th class="col-md-2">ID</th>
                            <th class="col-md-6">商城订单编号</th>
                            <th class="col-md-6">砍价订单编号</th>
                            <th class="col-md-4">下单时间</th>
                            <th class="col-md-3">购买者</th>
                            <th class="col-md-3">推荐者</th>

                            <th class="col-md-2">订单金额</th>
                            <th class="col-md-2">实付金额</th>
                            <th class="col-md-2">订单状态</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($list['data'] as $row)
                        <tr style="height: 40px">
                            <td>{{$row['id']}}</td>
                            <td>{{$row['has_one_order']['order_sn']}}</td>
                            <td>{{$row['order_sn']}}</td>
                            <td>{{$row['created_at']}}</td>
                            <td>{{$row['buyer_name']}}</td>
                            <td>{{$row['recommend_name']}}</td>
                            <td>{{$row['goods_price']}}</td>
                            <td>{{$row['price']}}</td>
                            <td>
                                @if($row['status'] == '3')
                                    已完成
                                @else
                                    未完成
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @include('order.modals')
            <div id="pager">{!! $pager !!}</div>
        </div>
    </div>
</div>
<script language='javascript'>

    require(['select2'], function () {
        $('.diy-notice').select2();
    })
</script>
@endsection
