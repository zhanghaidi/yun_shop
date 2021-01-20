<div class="panel panel-default">
    <div class="panel-body">
        {{--统计：订单数：{{ $statistics['count'] }}  订单金额：{{ $statistics['price'] }}元   累计收入：{{ $statistics['income'] }}   总客户数量： {{ $statistics['people'] }}--}}
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-body" style="margin-bottom:200px">
        <table class="table table-hover" style="overflow:visible">
            <thead class="navbar-inner">
            <tr>
                <th style='width:4%;text-align: center;'>
                    门店ID
                </th>
                <th style='width:14%;text-align: center;'>
                    门店名称
                </th>
                <th style='width:10%;text-align: center;'>
                    门店店长
                </th>
                <th style='width:10%;text-align: center;'>
                    分类
                </th>
                <th style='width:12%;text-align: center;'>
                    累计订单金额
                </th>
                <th style='width:10%;text-align: center;'>
                    累计订单数量
                </th>
                <th style='width:14%;text-align: center;'>
                    累计收入
                </th>
                <th style='width:14%;text-align: center;'>
                    客户数量
                </th>
                <th style='width:16%;'>
                    操作
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
                <tr>
                    <td style="text-align: center;">{{$row->id}}</td>
                    <td style="text-align: center;">
                        <img src='{{tomedia($row->thumb)}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                        <br/>
                        {{$row->store_name}}
                    </td>
                    <td style="text-align: center;">
                        <img src='{{tomedia($row->hasOneMember->avatar)}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                        <br/>
                        {{$row->hasOneMember->nickname}}
                    </td>
                    <td style="text-align: center;">{{$row->hasOneCategory->name}}</td>
                    <td style="text-align: center;">
                        收银台:{{number_format($row->order_price, 2)}}<br>
                        门店:{{number_format($row->store_order_price, 2)}}
                    </td>
                    <td style="text-align: center;">
                        收银台:{{ $row->order_sum }}<br>
                        门店:{{ $row->store_order_sum }}
                    </td>
                    <td style="text-align: center;">
                        收银台:{{number_format($row->finish_withdraw, 2)}}<br>
                        门店:{{number_format($row->store_finish_withdraw, 2)}}
                    </td>
                    <td style="text-align: center;">
                        {{ $row->client_sum ?: 0 }}
                    </td>
                    <td style="overflow:visible;">
                        <div class="btn-group btn-group-sm" >
                            <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="javascript:;">{!! trans('Yunshop\Mryt::pack.cashier_statistics_operation') !!} <span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu" style='z-index: 9999'>
                                <li><a  href="{{yzWebUrl('plugin.mryt.store.admin.order.index', ['search[store][cashier_id]' => $row->cashier_id])}}" title='收银台订单'><i class='fa fa-list'></i> 收银台订单</a></li>
                                <li><a  href="{{yzWebUrl('plugin.mryt.store.admin.store-order.index', ['store_order_search[store][store_id]' => $row->id])}}" title='门店订单'><i class='fa fa-list'></i> 门店订单</a></li>
                                <li><a  href="{{yzWebUrl('plugin.mryt.store.admin.cashier.client', ['id' => $row->uid])}}" title='客户'><i class='fa fa-list'></i> 客户</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>