@extends('Yunshop\Supplier::supplier.layouts.base')

@section('content')
    <div class="w1200 m0a">
        <style type='text/css'>
            .trhead td {  background:#efefef;text-align: center}
            .trbody td {  text-align: center; vertical-align:top;border-left:1px solid #ccc;overflow: hidden;}
            .goods_info{position:relative;width:60px;}
            .goods_info img {width:50px;background:#fff;border:1px solid #CCC;padding:1px;}
            .goods_info:hover {z-index:1;position:absolute;width:auto;}
            .goods_info:hover img{width:320px; height:320px;}
        </style>
        <div class="rightlist">

            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">提现</a></li>
                </ul>
            </div>

            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="" method="post" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">提现信息</label>
                            <div class="col-sm-8 col-lg-9 col-xs-12">
                                <input type="text" class="form-control"  name="search[apply]" value="{{array_get($params,'apply','')}}" placeholder='提现ID或提现单号' />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
                            <div class="col-sm-8 col-lg-9 col-xs-12">
                                <button class="btn btn-success" type="submit"><i class="fa fa-search"></i> 搜索</button>
                                <input type="hidden" name="token" value="{{$var['token']}}" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">总数：{{$list['total']}}</div>
                <div class="panel-body">
                    <table class="table table-hover table-responsive">
                        <thead class="navbar-inner" >
                        <tr>
                            <th style='width:6%;'>ID</th>
                            <th style='width:6%;'>账号</th>
                            <th style='width:22%;'>提现单号</th>
                            <th style='width:16%;'>提现金额<br/>打款方式</th>
                            <th style='width:10%;'>申请时间</th>
                            <th style='width:10%;'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['data'] as $row)
                        <tr>
                            <td>{{$row['id']}}</td>
                            <td>{{$row['has_one_supplier']['username']}}</td>
                            <td>{{$row['apply_sn']}}</td>
                            <td>
                                <label type="button" class="label label-danger">
                                    金额：{{$row['money']}}元
                                </label>
                                <br>
                                @if ($row['type'] == 1)
                                    <label type="button" class="label label-primary">
                                        手动提现
                                    </label>
                                @elseif($row['type'] == 2)
                                    <label type="button" class="label label-success">
                                        微信提现
                                    </label>
                                @elseif($row['type'] == 3)
                                    <label type="button" class="label label-warning">
                                        支付宝提现
                                    </label>
                                @endif
                            </td>
                            <td>{{$row['created_at']}}</td>
                            <td  style="overflow:visible;">
                                <a class='btn btn-default' href="{{yzWebUrl('plugin.supplier.supplier.controllers.withdraw.supplier-withdraw.detail', ['withdraw_id' => $row['id']])}}">详情</a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {!! $pager !!}
                </div>
            </div>
        </div>
    </div>
@endsection