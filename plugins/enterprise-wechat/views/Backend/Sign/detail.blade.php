@extends('layouts.base')

@section('content')
@section('title', trans('Yunshop\Sign::sign.record_detail'))
<div class="panel panel-default">
    <div class='panel-heading'>
        会员信息
    </div>
    <div class='panel-body'>
        <div style='height:auto;width:120px;float:left;'>
            <img src='{{tomedia($member_sign->member->avatar)}}' style='width:100px;height:100px;border:1px solid #ccc;padding:1px'/>
        </div>
        <div style='float:left;height:auto;overflow: hidden'>
            <p>
                <b>{{ trans('Yunshop\Sign::sign.member_id') }}: </b><span style='color:red'>{{ $member_sign->member->uid }}</span>
            </p>
            <p>
                <b>昵称:</b>
                {{ $member_sign->member->nickname }}
                <b>姓名:</b>
                {{ $member_sign->member->realname }}
                <b>手机号:</b>
                {{ $member_sign->member->mobile }}
            </p>
            <p>
                <b>连续{{trans('Yunshop\Sign::sign.plugin_name')}}状态: </b><span style='color:red'>{{ $member_sign->cumulative_name }}</span>
            <p>
            <p>
                <b>累计奖励: </b>积分：{{ $member_sign->cumulative_point }}，优惠券（{{ $member_sign->cumulative_coupon }}）张
            <p>

        </div>
    </div>


    <div class='panel-heading'>
        累计{{trans('Yunshop\Sign::sign.plugin_name')}}天数 共计 <span style="color:red; ">{{ $page_list->total() }}</span>天
    </div>

    <div class='panel-body'>
        <table class="table table-hover">
            <thead class="navbar-inner">
            <tr>
                <th style='width:5%;text-align: center;'>记录ID</th>
                <th style='width:8%;text-align: center;'>{{ trans('Yunshop\Sign::sign.plugin_name') }}时间</th>
                <th style='width:8%;text-align: center;'>奖励积分</th>
                <th style='width:8%;text-align: center;'>奖励优惠券</th>
                <th style='width:30%;text-align: center;'>备注</th>
            </tr>
            </thead>

            <tbody>
            @foreach($page_list as $key => $item)
                <tr  style="text-align: center; height: 40px;">
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->created_at }}</td>
                    <td>{{ $item->award_point }}</td>
                    <td>{{ $item->award_coupon }}张</td>
                    <td>{{ $item->remark }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {!! $page !!}

    <div class="form-group col-sm-12">
        <input type="button" class="btn btn-default" name="submit" onclick="goBack()" value="返回" style='margin-left:10px;'/>
    </div>

</div>

<script language='javascript'>
    function goBack() {
        window.location.href = "{!! yzWebUrl('plugin.sign.Backend.Modules.Sign.Controllers.sign.index') !!}";
    }
</script>

@endsection