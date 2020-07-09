@extends('layouts.base')
@section('title', trans('定期充值详情'))
@section('content')
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">定期充值详情</a></li>
        </ul>
    </div>

    <div class='panel panel-default'>
        <div class="form-horizontal">
            <div class="panel panel-info">
                <div class="panel-body">

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员：</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline"> {{ $item['has_one_member']['realname'] ?: ($item['has_one_member']['nickname'] ? $item['has_one_member']['nickname'] : '未更新') }}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">充值冻结{{$loveName}}：</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">{{$item['amount']}}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">充值期数：</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">{{$item['total']}}</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">开始时间：</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">{{$item['created_at']}}</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">已充值</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class='panel-body'>
                                <div class="table-responsive ">
                                    @foreach($item['has_many_queue'] as $key=> $queue)
                                        @if($queue['status'])
                                            <div class="input-group">
                                                <label class="radio-inline">第{{$queue['period']}}期：</label>
                                                <label class="radio-inline">{{$queue['timing_days']}}天</label>
                                                <label class="radio-inline">数量：{{$queue['amount']}}</label>
                                            </div>
                                        @endif
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">待充值</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class='panel-body'>
                                <div class="table-responsive ">
                                    @foreach($item['has_many_queue'] as $key=> $queue)
                                        @if(!$queue['status'])
                                            <div class="input-group">
                                                <label class="radio-inline">第{{$queue['period']}}期：</label>
                                                <label class="radio-inline">{{$queue['timing_days']}}天</label>
                                                <label class="radio-inline">数量：{{$queue['amount']}}</label>
                                            </div>
                                        @endif
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-4">
                        <a href="{{yzUrl('plugin.love.Backend.Modules.Love.Controllers.timing-log.index')}}"
                           class="btn btn-success "> 返回列表</a>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection