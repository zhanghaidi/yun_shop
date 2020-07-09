@extends('layouts.base')
@section('title', '推送消息')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="rightlist">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>

            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form1">
                    <input type="hidden" name="c" value="site" />
                    <input type="hidden" name="a" value="entry" />
                    <input type="hidden" name="m" value="yun_shop" />
                    <input type="hidden" name="do" value="1245" />
                    <input type="hidden" name="route" value="plugin.jd-supply.admin.push-message.index" id="route" />
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <select name='search[type]' class='form-control'>
                                <option value=''>消息类型</option>
                                @foreach($data['msg'] as $k=>$v)
                                    <option value="{{$k}}" @if($search['type'] == $k) selected @endif>{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-6">
                        <div class="time">

                            <select name='search[search_time]' class='form-control'>
                                <option value='0' @if($search['search_time']=='0') selected @endif>不搜索时间</option>
                                <option value='1' @if($search['search_time']=='1') selected @endif>搜索时间</option>
                            </select>
                        </div>
                        <div class="search-select">
                            {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                            'starttime'=>date('Y-m-d H:i', strtotime($search['time']['start']) ?: strtotime('-1 month')),
                            'endtime'=>date('Y-m-d H:i',strtotime($search['time']['end']) ?: time()),
                            'start'=>0,
                            'end'=>0
                            ], true) !!}
                        </div>
                    </div>
                    <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                        <div class="">
                            <input type="hidden" name="token" value="{{$var['token']}}" />
                            <button class="btn btn-success" style="float:left"><i class="fa fa-search"></i>搜索</button>

                        </div>
                    </div>

                </form>
            </div>


        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:10%;text-align: center;'>ID</th>
                            <th style='width:20%;text-align: center;'>消息类型</th>
                            <th style='width:60%;text-align: center;'>内容</th>
                            <th style='width:20%;text-align: center;'>时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $v)
                            <tr style="text-align: center">
                                <td style="text-align: center;">{{ $v['id'] }}</td>
                                <td style="text-align: center;">{{ $data['msg'][$v['type']]}}</td>
                                @if($data['type'][$v['type']] == 1)
                                    <td style="text-align: center;"><a href="{{yzWebUrl('plugin.jd-supply.admin.shop-goods.edit', array('id' => $v['goods_id']))}}">商品id:{{ $v['goods_id'] }}</a> </td>
                                @else
                                    <td style="text-align: center;"><a href="{!! yzWebUrl('plugin.jd-supply.admin.order-list.detail',['id'=>$v['order_id']])!!}">订单id:{{ $v['order_id'] }} </a></td>
                                @endif

                                <td style="text-align: center;">{{ $v['created_at'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $page !!}

                </div>
            </div>
        </div>
    </div>


@endsection