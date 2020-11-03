@extends('layouts.base')
@section('title', '会员优惠券列表')
@section('content')

    <div class="w1200 m0a">
        @include('layouts.tabs')
        <form action="" method="post" class="form-horizontal" role="form" id="form1">
            <input type="hidden" name="c" value="site"/>
            <input type="hidden" name="a" value="entry"/>
            <input type="hidden" name="m" value="yun_shop"/>
            <input type="hidden" name="do" value="plugin"/>
            <input type="hidden" name="p" value="coupon"/>
            <input type="hidden" name="method" value="coupon"/>
            <input type="hidden" name="op" value="display"/>

            <div class="panel panel-info">
                <div class="panel-heading">会员优惠券</div>
                <div class="panel-body">

                </div>
            </div>
        </form>

        <div class="panel panel-default">
            <div class="panel-heading">总数: {{$total}} &nbsp;&nbsp;&nbsp;&nbsp;<input type="button"
                                                                                     class="btn btn-default"
                                                                                     name="submit"
                                                                                     onclick="history.go(-1)"
                                                                                     value="返回"
                                                                                     style='margin-left:10px;'/></div>
            <div class="panel-body">
                <table class="table table-hover table-responsive">
                    <thead class="navbar-inner">
                    <tr>
                        <th width="4%">ID</th>
                        <th width="16%">优惠券名称</th>
                        <th width="10%">优惠券类型</th>
                        <th width="10%">使用条件/优惠</th>
                        <th width="10%">使用时间</th>
                        <th width="10%">有效时间</th>
                        <th width="6%">状态</th>
                        <th width="15%">领取时间</th>
                        <th width="22%">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $row)
                        <tr>
                            <td>{{$row['id']}}</td>
                            <td>
                                {{$row['belongs_to_coupon']['name']}}
                            </td>
                            <td>
                                @if($row['belongs_to_coupon']['coupon_method']==0)
                                    <label class="label label-danger">满减</label>
                                @else
                                    <label class="label label-warning">打折</label>
                                @endif
                            </td>
                            <td>
                                @if($row['belongs_to_coupon']['enough']>0)
                                    <label class="label label-danger">满{{$row['belongs_to_coupon']['enough']}}可用</label>
                                @else
                                    <label class="label label-warning">不限</label>
                                @endif
                                <br/>@if($row['belongs_to_coupon']['coupon_method']==1)
                                    立减 {{$row['belongs_to_coupon']['deduct'] ? $row['belongs_to_coupon']['deduct'] : 0}} 元
                                @elseif( $row['coupon_method']==2)
                                    打 {{$row['belongs_to_coupon']['discount'] ? $row['belongs_to_coupon']['discount'] : 1}} 折
                                @endif
                            </td>
                            <td>
                                @if($row['used']>0)
                                    <label class="label label-danger">{{$row['use_time']}}</label>
                                @else
                                    <label class="label label-success">未使用</label>
                                @endif
                            </td>
                            <td>
                                {{$row['time_start']}} -- {{$row['time_end']}}
                            </td>
                            <td>
                                @if($row['api_status'] == 1)
                                    <label class="label label-success">未使用</label>
                                @elseif($row['api_status'] == 2)
                                    <label class="label label-default">已过期</label>
                                @elseif($row['api_status'] == 3)
                                    <label class="label label-danger">已使用</label>
                                @endif
                            </td>

                            <td>{{$row['get_time']}}</td>
                            <td style="position:relative">
                                <a class='btn btn-default btn-sm'
                                   href="{{yzWebUrl('coupon.member-coupon.delete', ['id' => $row["id"]])}}" title="删除"
                                   onclick="return confirm('确定要删除该优惠券吗？');"><i class='fa fa-remove'></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {!! $pager !!}
            </div>
            <div class='panel-footer'>

            </div>
        </div>
    </div>

@endsection('content')