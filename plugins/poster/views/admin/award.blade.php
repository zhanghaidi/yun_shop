@extends('layouts.base')
@section('title', '海报奖励记录')

@section('content')
<section class="content">
    <div class="rightlist">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">奖励记录</a></li>
            </ul>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
                筛选
            </div>
            <div class="panel-body">
                <form action="" method="POST" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">推荐人信息</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="searchRecommender" id="" type="text" value="" placeholder="可搜索推荐人的昵称/姓名/手机号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">扫码人信息</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="searchSubscriber" id="" type="text" value="" placeholder="可搜索扫码人的昵称/姓名/手机号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">奖励时间</label>
                        <div class="col-sm-2" >
                            <label class='radio-inline'>
                                <input type='radio' value='0' name='searchTime' checked/>不搜索
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' value='1' name='searchTime' @if($timeStart) checked @endif/>搜索
                            </label>
                        </div>
                        <div class="col-sm-7 col-lg-7 col-xs-12" style='padding:0'>
                            {!!tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d H:i', ($timeStart ? $timeStart : strtotime('yesterday 0:00'))),'endtime'=>date('Y-m-d H:i', ($timeEnd ? $timeEnd : strtotime('today 0:00')))),true)!!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"> </label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class='panel panel-default'>
            <div class='panel-heading'>
                奖励记录 (注册人数: {{$posterAwardsSum}})<br>
                <span style="color:#666; font-size:90%">"奖励记录"包括两部分的记录:
                <br>1.之前未关注公众号的用户,通过海报扫码关注并注册; 2.是之前已经关注但是未注册商城的用户,通过海报扫码注册</span>
            </div>

            <div class='panel-body'>
                <table class="table">
                    <thead>
                    <tr>
                        <th>推荐人信息 <br/>(昵称/真实姓名/手机号)</th>
                        <th>推荐人奖励</th>
                        <th>扫码人信息 <br/>(昵称/真实姓名/手机号)</th>
                        <th>扫码人奖励</th>
                        <th>奖励时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($posterAwards as $row)
                    <tr>
                        <td>
                            <img src="{{$row['recommender']['avatar']}}" style='width:30px;height:30px;padding1px;border:1px solid #ccc' /> {{$row['recommender']['nickname']}}
                            ({{$row['recommender']['realname']}}/{{$row['recommender']['mobile']}})
                        </td>
                        <td>
                            @if(!empty($row['recommender_credit']))
                            {{$row['recommender_credit']}} 个积分;
                            @endif
                            @if(!empty($row['recommender_bonus']))
                            {{$row['recommender_bonus']}} 元现金;
                            @endif
                            @if(!empty($row['recommender_coupon_num']))
                            优惠券 (ID 为 {{$row['recommender_coupon_id']}}) 共 {{$row['recommender_coupon_num']}} 张.
                            @endif
                        </td>
                        <td>
                            <img src="{{$row['subscriber']['avatar']}}" style='width:30px;height:30px;padding1px;border:1px solid #ccc' /> {{$row['subscriber']['nickname']}}
                            ({{$row['subscriber']['realname']}}/{{$row['subscriber']['mobile']}})
                        </td>
                        <td>
                            @if(!empty($row['subscriber_credit']))
                            {{$row['subscriber_credit']}} 个积分;
                            @endif
                            @if(!empty($row['subscriber_bonus']))
                            {{$row['subscriber_bonus']}} 元现金;
                            @endif
                            @if(!empty($row['subscriber_coupon_num']))
                            优惠券 (ID 为 {{$row['subscriber_coupon_id']}}) 共 {{$row['subscriber_coupon_num']}} 张.
                            @endif
                        </td>
                        <td>
                            {{$row['created_at']}}
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                {!!$pager!!}
            </div>
        </div>
    </div>
</section><!-- /.content -->
@endsection

