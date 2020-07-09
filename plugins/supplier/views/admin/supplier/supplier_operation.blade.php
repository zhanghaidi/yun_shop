@extends('layouts.base')

@section('content')
<div class="w1200 m0a">
    <div class="rightlist">
        <form id="setform" action="" method="post" class="form-horizontal form">
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    供应商设置
                </div>
                <div class="form-group"></div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单完成n天后可申请提现</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="setdata[apply_day]" class="form-control" value="{{$set['apply_day']}}" />
                            <div class="help-block">订单完成后 ，用户在x天后可以发起提现申请,如果不填写则订单完成就可以提现</div>
                        </div>
                    </div>
                </div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">n天内只能提现一次</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="setdata[limit_day]" class="form-control" value="{{$set['limit_day']}}" />
                            <div class="help-block">用户n天内只能提现一次</div>

                        </div>
                    </div>
                </div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启微信提现</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[wx_withdraw]" value="0" @if($set['wx_withdraw'] == 0) checked="checked"@endif /> 关闭</label>
                            <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[wx_withdraw]" value="1" @if($set['wx_withdraw'] == 1) checked="checked"@endif /> 开启</label>
                        </div>
                    </div>
                </div>

                {{--<div class='panel-heading'>
                    通知设置
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">下单通知</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="setdata[create_order_title]" class="form-control" value="{{$set['create_order_title']}}" />
                        <div class="help-block">标题，默认"下单通知"</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <textarea  name="setdata[create_order_become]" class="form-control" >{{$set['create_order_become']}}</textarea>
                        模板变量: [昵称] [时间]
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付通知</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="setdata[pay_order_title]" class="form-control" value="{{$set['pay_order_title']}}" />
                        <div class="help-block">标题，默认"支付通知"</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <textarea  name="setdata[pay_order_become]" class="form-control" >{{$set['pay_order_become']}}</textarea>
                        模板变量: [昵称] [时间]
                    </div>
                </div><div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">收货通知</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="setdata[complete_order_title]" class="form-control" value="{{$set['complete_order_title']}}" />
                        <div class="help-block">标题，默认"收货通知"</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <textarea  name="setdata[complete_order_become]" class="form-control" >{{$set['complete_order_become']}}</textarea>
                        模板变量: [昵称] [时间]
                    </div>
                </div>--}}

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-1"
                               onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection
