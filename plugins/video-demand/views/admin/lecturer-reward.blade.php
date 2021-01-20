@extends('layouts.base')

@section('content')
@section('title', trans('讲师分红'))
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">讲师分红</a></li>
    </ul>
</div>

<div class='panel panel-default'>
    <form action="" method="post" class="form-horizontal" id="form1">
        <div class="panel panel-info">
            <div class="panel-body">

                {{--<div class="form-group col-xs-12 col-sm-3">--}}
                    {{--<input class="form-control" name="search[queue_id]" type="text"--}}
                           {{--value="{{$search['queue_id']}}" placeholder="ID">--}}
                {{--</div>--}}

                <div class="form-group col-xs-12 col-sm-3">
                    <input class="form-control" name="search[lecturer]" type="text"
                           value="{{$search['lecturer']}}" placeholder="可搜索讲师姓名/手机号码">
                </div>

                <div class="form-group col-xs-12 col-sm-3">
                    <input class="form-control" name="search[course_goods]" type="text"
                           value="{{$search['course_goods']}}" placeholder="可搜索课程商品关键字">
                </div>

                <div class="form-group col-xs-12 col-sm-3">
                    <input class="form-control" name="search[order_sn]" id="" type="text"
                           value="{{$search['order_sn']}}" placeholder="请输入订单编号">
                </div>


                <div class="form-group col-xs-12 col-sm-3">
                    <select name='search[reward_type]' class='form-control'>
                        <option value=''>业务类型</option>
                        <option value='0' @if($search['reward_type'] == '0') selected @endif>讲师分红</option>
                        <option value='1' @if($search['reward_type'] == '1') selected @endif>打赏佣金</option>
                    </select>
                </div>

                <div class="form-group col-xs-12 col-sm-3">
                    <select name='search[status]' class='form-control'>
                        <option value=''>分红状态</option>
                        <option value='0' @if($search['status'] == '0') selected @endif>未结算</option>
                        <option value='1' @if($search['status'] == '1') selected @endif>已结算</option>
                    </select>
                </div>

                <div class="form-group col-xs-12 col-sm-8">

                    <div class="col-sm-3">
                        <label class='radio-inline'>
                            <input type='radio' value='0' name='search[is_time]'
                                   @if($search['is_time'] == '0') checked @endif>不搜索
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' value='1' name='search[is_time]'
                                   @if($search['is_time'] == '1') checked @endif>搜索
                        </label>
                    </div>
                    {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', ['starttime'=>$search['time']['start'],
                         'endtime'=>$search['time']['end'],
                         'start'=>$search['time']['start'],
                         'end'=>$search['time']['end']
                         ], true) !!}

                </div>

                <div class="form-group col-xs-12 col-sm-4">

                    <input type="button" class="btn btn-success" id="export" value="导出">

                    <input type="button" class="btn btn-success pull-right" id="search" value="搜索">

                </div>

            </div>
        </div>
    </form>
</div>

<div class='panel panel-default'>
    <div class='panel-heading'>
        总数 ：{{$total}}
    </div>
    <div class='panel-body'>
        <table class="table table-hover" style="overflow:visible;">
            <thead>
            <tr>
                <th style='width:5%;'>ID</th>
                <th style='width:8%;'>讲师</th>
                <th style='width:8%;'>课程商品</th>
                <th style='width:10%;'>购买/打赏人</th>
                <th style='width:10%;'>订单号</th>
                <th style='width:8%;'>订单金额</th>
                <th style='width:8%;'>业务类型</th>
                <th style='width:8%;'>分红金额</th>
                <th style='width:8%;'>分红状态</th>
                <th style='width:10%;'>分红时间</th>

            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
                <tr>
                    <td>{{$row->id}}</td>
                    <td>
                        <img src="{{tomedia($row->hasOneLecturer->hasOneMember->avatar)}}"
                             style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        </br>
                        {{$row->hasOneLecturer->real_name}}
                    </td>
                    <td>
                        <a target="_blank"
                           href="{{yzWebUrl('goods.goods.edit',['id'=>$row->hasOneCourse->hasOneGoods->id])}}">
                            <img src="{{tomedia($row->hasOneCourse->hasOneGoods->thumb)}}"
                                 style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                            </br>
                            {{$row->hasOneCourse->hasOneGoods->title}}
                        </a>
                    </td>
                    <td>{!! $row->hasOneRewardMember->nickname ?: '未记录' !!}</td>
                    <td>{{$row->order_sn}}</td>
                    <td>{{$row->order_price}}</td>
                    <td>{{$row->reward_type_name}}</td>
                    <td>{{$row->amount}}</td>
                    <td>{{$row->status_name}}</td>
                    <td>{{$row->created_at}}</td>
                </tr>

            @endforeach
            </tbody>
        </table>

        {!! $pager !!}
    </div>
</div>
<div style="width:100%;height:150px;"></div>
<script language='javascript'>
    $(function () {
        $('#export').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('plugin.video-demand.admin.lecturer-reward.export') !!}');
            $('#form1').submit();
        });
        $('#search').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('plugin.video-demand.admin.lecturer-reward.index') !!}');
            $('#form1').submit();
        });
    });
</script>
@endsection