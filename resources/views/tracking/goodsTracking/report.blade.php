@extends('layouts.base')

@section('content')
    <div id="member-blade" class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->

        <div class="right-titpos">
            @include('layouts.tabs')
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        {{--<div class="panel panel-info">
            <div class="panel-heading">统计报告</div>
            <div class="panel-body">
                <!-- 新增加统计时间筛选 -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">
                                <div class="m-t-md">
                                    <small class="pull-right">
                                        <a class="btn btn-default" href="#" role="button">日</a>
                                        <a class="btn btn-default" href="#" role="button">周</a>
                                        <a class="btn btn-default" href="#" role="button">月</a>
                                    </small>
                                    <small>
                                        <strong><i class="fa fa-clock-o"> </i> 统计时间：</strong> 2020-10-08
                                    </small>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- 新增加统计时间筛选结束 -->
            </div>

        </div>--}}
        {{--<div class="clearfix">

             <div class="panel panel-info">
               <div class="panel-heading">核心指标监控</div>
                 <div class="panel-body">
                     <!-- 新增加监控卡片 -->
                     <div class="row">
                         <div class="col-md-2">
                             <div class="panel panel-default">
                                 <div class="panel-heading">
                                     <span class="label label-success pull-right">月</span>
                                     <h5>浏览量</h5>
                                 </div>
                                 <div class="panel-body">
                                     <h1 class="no-margins">386,200</h1>
                                     <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i>
                                     </div>
                                     <small>总计浏览量</small>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-2">
                             <div class="panel panel-default">
                                 <div class="panel-heading">
                                     <span class="label label-info pull-right">年</span>
                                     <h5>订单</h5>
                                 </div>
                                 <div class="panel-body">
                                     <h1 class="no-margins">80,800</h1>
                                     <div class="stat-percent font-bold text-info">20% <i class="fa fa-level-up"></i>
                                     </div>
                                     <small>新订单</small>
                                 </div>
                             </div>
                         </div>

                         <div class="col-md-2">
                             <div class="panel panel-default">
                                 <div class="panel-heading">
                                     <span class="label label-primary pull-right">今天</span>
                                     <h5>访问人次</h5>
                                 </div>
                                 <div class="panel-body">

                                     <div class="row">
                                         <div class="col-md-6">
                                             <h1 class="no-margins">&yen; 406,420</h1>
                                             <div class="font-bold text-navy">44% <i class="fa fa-level-up"></i> <small>增长较快</small>
                                             </div>
                                         </div>
                                         <div class="col-md-6">
                                             <h1 class="no-margins">206,120</h1>
                                             <div class="font-bold text-navy">22% <i class="fa fa-level-up"></i> <small>增长较慢</small>
                                             </div>
                                         </div>
                                     </div>


                                 </div>
                             </div>
                         </div>
                         <div class="col-md-2">
                             <div class="panel panel-default">
                                 <div class="panel-heading">
                                     <span class="label label-success pull-right">月</span>
                                     <h5>浏览量</h5>
                                 </div>
                                 <div class="panel-body">
                                     <h1 class="no-margins">386,200</h1>
                                     <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i>
                                     </div>
                                     <small>总计浏览量</small>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-2">
                             <div class="panel panel-default">
                                 <div class="panel-heading">
                                     <span class="label label-success pull-right">月</span>
                                     <h5>浏览量</h5>
                                 </div>
                                 <div class="panel-body">
                                     <h1 class="no-margins">386,200</h1>
                                     <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i>
                                     </div>
                                     <small>总计浏览量</small>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-2">
                             <div class="panel panel-default">
                                 <div class="panel-heading">
                                     <span class="label label-success pull-right">月</span>
                                     <h5>浏览量</h5>
                                 </div>
                                 <div class="panel-body">
                                     <h1 class="no-margins">386,200</h1>
                                     <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i>
                                     </div>
                                     <small>总计浏览量</small>
                                 </div>
                             </div>
                         </div>

                     </div>
                     <!-- 新增加监控卡片结束 -->

                     <!-- 新增加统计图 -->
                     <div class="row">
                         <div class="col-sm-12">
                             <div class="panel panel-default">
                                 <div class="panel-heading">
                                     <h5>订单</h5>
                                     <div class="pull-right">
                                         <div class="btn-group">
                                             <button type="button" class="btn btn-xs btn-white active">天</button>
                                             <button type="button" class="btn btn-xs btn-white">月</button>
                                             <button type="button" class="btn btn-xs btn-white">年</button>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="panel-body">
                                     <div class="row">
                                         <div class="col-sm-9">
                                             <div class="flot-chart">
                                                 <div class="flot-chart-content" id="flot-dashboard-chart"></div>
                                             </div>
                                         </div>
                                         <div class="col-sm-3">
                                             <ul class="stat-list">
                                                 <li>
                                                     <h2 class="no-margins">2,346</h2>
                                                     <small>订单总数</small>
                                                     <div class="stat-percent">48% <i class="fa fa-level-up text-navy"></i>
                                                     </div>
                                                     <div class="progress progress-mini">
                                                         <div style="width: 48%;" class="progress-bar"></div>
                                                     </div>
                                                 </li>
                                                 <li>
                                                     <h2 class="no-margins ">4,422</h2>
                                                     <small>最近一个月订单</small>
                                                     <div class="stat-percent">60% <i class="fa fa-level-down text-navy"></i>
                                                     </div>
                                                     <div class="progress progress-mini">
                                                         <div style="width: 60%;" class="progress-bar"></div>
                                                     </div>
                                                 </li>
                                                 <li>
                                                     <h2 class="no-margins ">9,180</h2>
                                                     <small>最近一个月销售额</small>
                                                     <div class="stat-percent">22% <i class="fa fa-bolt text-navy"></i>
                                                     </div>
                                                     <div class="progress progress-mini">
                                                         <div style="width: 22%;" class="progress-bar"></div>
                                                     </div>
                                                 </li>
                                             </ul>
                                         </div>
                                     </div>
                                 </div>

                             </div>
                         </div>
                     </div>
                     <!-- 新增加统计图结束 -->
                 </div>
            </div>
        </div>--}}

        <div class="panel panel-info">
            <div class="panel-heading">全量商品排行</div>
            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form1">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="tracking" id="form_do"/>
                    <input type="hidden" name="route" value="tracking.goods-tracking.report" id="route"/>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">ID</label>-->
                        <div class="">
                            <input type="text" placeholder="商品ID/商品名" class="form-control" name="search[keywords]"
                                   value="{{$search['keywords']}}"/>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
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
                            {{-- <button type="button" name="export" value="1" id="export" class="btn btn-default">导出
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
                            <th style='width:12%; text-align: center;'>商品</th>
                            <th style='width:12%; text-align: center;'>商品访客数</th>
                            <th style='width:12%; text-align: center;'>商品收藏数</th>
                            <th style='width:12%; text-align: center;'>商品加购件数</th>
                            <th style='width:12%; text-align: center;'>下单件数</th>
                            <th style='width:12%; text-align: center;'>付款件数</th>
                           {{-- <th style='width:12%; text-align: center;'>付款金额</th>
                            <th style='width:12%; text-align: center;'>支付转化率</th>--}}
                            <th style='width:12%; text-align: center;'>统计时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr>
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
                                    {{ $list->view_num }}
                                </td>
                                <td style="text-align: center;">{{ $list->favorites_num }}</td>
                                <td style="text-align: center;">{{ $list->add_purchase_num }}</td>
                                <td style="text-align: center;">{{ $list->create_order_num }}</td>

                                <td style="text-align: center;">{{ $list->order_Payment_num }}</td>
                                {{--<td style="text-align: center;">{{ $list->order_Payment_amount }}</td>
                                <td style="text-align: center;">{{ $list->ext }}</td>--}}
                                <td style="text-align: center;">{{ $list->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $page !!}

                </div>
            </div>
        </div>


@endsection('content')
