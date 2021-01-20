@extends('layouts.base')
@section('title', trans('Yunshop\Mryt::pack.cashier_statistics_detail_title'))
@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li><a href="#">{!! trans('Yunshop\Mryt::pack.cashier_statistics_detail_title') !!}</a></li>
                </ul>
            </div>
            <form action="" method='post' class='form-horizontal'>
                <div class='panel panel-default'>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店</label>
                            <div class="col-sm-9 col-xs-12">
                                <img src='{{$cashier_model->thumb}}' style='width:100px;height:100px;padding:1px;border:1px solid #ccc' />
                                {{$cashier_model->store_name}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信</label>
                            <div class="col-sm-9 col-xs-12">
                                <img src='{{$cashier_model->hasOneMember->avatar}}' style='width:100px;height:100px;padding:1px;border:1px solid #ccc' />
                                {{$cashier_model->hasOneMember->nickname}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店ID</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{$cashier_model->id}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{!! trans('Yunshop\Mryt::pack.cashier_statistics_store_category') !!}</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{$cashier_model->hasOneCateGory->name}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{!! trans('Yunshop\Mryt::pack.cashier_statistics_order_price') !!}</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    [
                                        收银台:{{number_format($cashier_model->order_price, 2)}}元
                                        门店:{{number_format($cashier_model->store_order_price, 2)}}元
                                    ]
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{!! trans('Yunshop\Mryt::pack.cashier_statistics_receivable_price') !!}</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    [
                                        收银台:{{number_format($cashier_model->receivable_price, 2)}}元
                                        门店:{{number_format($cashier_model->store_receivable_price, 2)}}元
                                    ]
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{!! trans('Yunshop\Mryt::pack.cashier_statistics_finish_withdraw') !!}</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    [
                                        收银台:{{number_format($cashier_model->finish_withdraw, 2)}}元
                                        门店:{{number_format($cashier_model->store_finish_withdraw, 2)}}元
                                    ]
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{!! trans('Yunshop\Mryt::pack.cashier_statistics_not_withdraw') !!}</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    [
                                        收银台:{{number_format($cashier_model->not_withdraw, 2)}}元
                                        门店:{{number_format($cashier_model->store_not_withdraw, 2)}}元
                                    ]
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分抵扣金额</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    [
                                        收银台:{{number_format($cashier_model->deduct_point, 2)}}元
                                        门店:{{number_format($cashier_model->store_deduct_point, 2)}}元
                                    ]
                                </div>
                            </div>
                        </div>
                        @if($exist_love)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$love_name}}抵扣金额</label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class='form-control-static'>
                                        [
                                            收银台:{{number_format($cashier_model->deduct_love, 2)}}元
                                            门店:{{number_format($cashier_model->store_deduct_love, 2)}}元
                                        ]
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券抵扣金额</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    [
                                        收银台:{{number_format($cashier_model->deduct_coupon, 2)}}元
                                        门店:{{number_format($cashier_model->store_deduct_coupon, 2)}}元
                                    ]
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员奖励积分数量</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    [
                                        收银台:{{$cashier_model->remard_buyer_point}}
                                        门店:{{$cashier_model->store_remard_buyer_point}}
                                    ]
                                </div>
                            </div>
                        </div>
                        @if($exist_love)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员{{$love_name}}奖励数量</label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class='form-control-static'>
                                        [
                                            收银台:{{$cashier_model->remard_buyer_love}}
                                            门店:{{$cashier_model->store_remard_buyer_love}}
                                        ]
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员优惠券奖励数量</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    [
                                        收银台:{{$cashier_model->remard_buyer_coupon}}
                                    ]
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">商家积分奖励数量</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    [
                                        收银台:{{$cashier_model->remard_store_point}}
                                        门店:{{$cashier_model->store_remard_store_point}}
                                    ]
                                </div>
                            </div>
                        </div>
                        @if($exist_love)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">商家{{$love_name}}奖励数量</label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class='form-control-static'>
                                        [
                                            收银台:{{$cashier_model->remard_store_love}}
                                            门店:@if($cashier_model->store_remard_store_love){{$cashier_model->store_remard_store_love}}@else{{0}}@endif
                                        ]
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回" style='margin-left:10px;'/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection