@extends('layouts.base')

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
                    <form action="" method="get" class="form-horizontal" role="form" id="form">
                        <input type="hidden" name="c" value="site"/>
                        <input type="hidden" name="a" value="entry"/>
                        <input type="hidden" name="m" value="yun_shop"/>
                        <input type="hidden" name="do" value="supplier" id="form_do"/>
                        <input type="hidden" name="route" value="plugin.supplier.admin.controllers.withdraw.supplier-withdraw.index" id="route" />
                        <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                            <div class="">
                                <input class="form-control" placeholder="供货商ID或供货商账号" name="search[supplier]" id=""
                                       type="text" value="{{array_get($params,'supplier','')}}" ／>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                            <div class="">
                                <input class="form-control" placeholder="会员ID、昵称、手机号" name="search[member]" id=""
                                       type="text" value="{{array_get($params,'member','')}}" ／>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                            <div class="">
                                <input class="form-control" placeholder="提现ID或提现单号" name="search[apply]" id=""
                                       type="text" value="{{array_get($params,'apply','')}}" ／>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                            <div class="">
                                <select name="search[status]" class='form-control'>
                                    <option value="">提现状态不限</option>
                                    <option value="1"
                                            @if($params['status'] == '1') selected @endif>申请中</option>
                                    <option value="2"
                                            @if($params['status'] == '2') selected @endif>待打款</option>
                                    <option value="2"
                                            @if($params['status'] == '4') selected @endif>打款中</option>
                                    <option value="3"
                                            @if($params['status'] == '3') selected @endif>已打款</option>
                                    <option value="-1"
                                            @if($params['status'] == '-1') selected @endif>驳回</option>
                                </select>
                            </div>
                        </div>

                        <div class='form-group col-xs-12 col-sm-8 col-lg-2'>
                            <select name="search[time_range][field]" class="form-control form-time">
                                <option value=""
                                        @if( array_get($params,'time_range.field',''))selected="selected"@endif >
                                    操作时间
                                </option>
                                <option value="created_at"
                                        @if( array_get($params,'time_range.field','')=='created_at')  selected="selected"@endif >
                                    申请时间
                                </option>
                                <option value="pay_time"
                                        @if( array_get($params,'time_range.field','')=='pay_time')  selected="selected"@endif>
                                    打款时间
                                </option>
                            </select>
                            {!!
                                app\common\helpers\DateRange::tplFormFieldDateRange('search[time_range]', [
                        'starttime'=>array_get($search,'time_range.start',0),
                        'endtime'=>array_get($search,'time_range.end',0),
                        'start'=>0,
                        'end'=>0
                        ], true)!!}

                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
                            <div class="col-sm-8 col-lg-9 col-xs-12">
                                <button type="button" name="export" value="1" id="export" class="btn btn-default excel back ">导出 Excel</button>
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
                            <th style='width:12%;'>提现单号</th>
                            <th style='width:16%;'>提现金额<br/>打款方式</th>
                            <th style='width:10%;'>状态</th>
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
                                @elseif($row['type'] == 5)
                                    <label type="button" class="label label-warning">
                                        汇聚提现
                                    </label>
                                @endif
                            </td>
                            <td><label type="button" class="{{$row['status_obj']['style']}}">
                                    {{$row['status_obj']['name']}}
                                </label></td>
                            <td>{{$row['created_at']}}</td>
                            <td  style="overflow:visible;">
                                <a class='btn btn-default' href="{{yzWebUrl('plugin.supplier.admin.controllers.withdraw.supplier-withdraw.detail', ['withdraw_id' => $row['id']])}}">详情</a>
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
    <script language='javascript'>
        $(function () {
            $('#export').click(function(){
                $('#route').val("plugin.supplier.admin.controllers.withdraw.supplier-withdraw.export");
                $('#form').submit();
                $('#route').val("plugin.supplier.admin.controllers.withdraw.supplier-withdraw.index");
            });
        });
    </script>
@endsection