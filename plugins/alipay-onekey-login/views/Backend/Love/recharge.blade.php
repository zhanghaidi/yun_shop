@extends('layouts.base')
@section('title', '会员充值')
@section('content')
    <div class="rightlist">
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">{{ $rechargeMenu['title'] }}</a></li>
                </ul>
            </div>
            <div class="panel panel-default">


                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ $rechargeMenu['name'] }}</label>
                        <div class="col-sm-9 col-xs-12">
                            <img src='{{ $memberInfo['avatar'] }}' style='width:100px;height:100px;padding:1px;border:1px solid #ccc' />
                            {{ $memberInfo['nickname'] }}
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ $rechargeMenu['profile'] }}</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static">姓名: {{ $memberInfo['realname'] or $memberInfo['nickname'] }} / 手机号: {{ $memberInfo['mobile'] }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ $rechargeMenu['usable_value'] }}</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static">{{ $memberInfo->love->usable or '0.00'}}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ $rechargeMenu['froze_value'] }}</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static">{{ $memberInfo->love->froze or '0.00'}}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">充值类型</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="value_type" value="1" checked="checked"/>充值可用</label>
                            <label class="radio-inline">
                                <input type="radio" name="value_type" value="2"/>充值冻结</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ $rechargeMenu['charge_value'] }}</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="love" class="form-control" value=""/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">备注信息</label>
                        <div class="col-sm-9 col-xs-12">
                            <textarea name="remark" rows="5" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="hidden" name="uid" value="{{ $memberInfo['uid'] }}"/>
                            <input name="submit" type="submit" value="充 值" class="btn btn-success span2" onclick="return confirm('确认充值？');return false;">
                        </div>
                    </div>

                </div>
            </div>

        </form>
    </div>
@endsection
