@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li @if($type=='step') class="active" @endif><a
                                href="{{yzWebUrl('plugin.minapp-content.admin.sport-clock.step')}}">运动打卡设置</a></li>
                    <li @if($type=='step_exchange_list') class="active" @endif><a
                                href="{{yzWebUrl('plugin.minapp-content.admin.sport-clock.step-exchange-list')}}">步数兑换记录</a></li>
                </ul>
            </div>

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="{{$info['id']}}">

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">健康金兑换最低步数：</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="text" name="least_step" class="form-control" value="{{$info['least_step']}}"
                               placeholder="健康金兑换最低步数">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">步数兑换健康金比率‰：</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="text" name="ratio" class="form-control" value="{{$info['ratio']}}"
                               placeholder="输入兑换比例‰(千分比)">
                        <span class='help-block' style="color: red">健康金=步数*千分之比例。</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">参与辟谣正确奖励健康金：</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="text" name="discuss_point" class="form-control" value="{{$info['discuss_point']}}"
                               placeholder="输入奖励健康金">
                        <span class='help-block' style="color: red">输入正确奖励健康金(输入正整数)。</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">健康金规则：</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        {!! yz_tpl_ueditor('health_gold_rules', $info['health_gold_rules']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-2">
                        <input type="submit" name="submit" value="提交" class="btn btn-success"
                               onclick="return formcheck()"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script language="JavaScript">
    $(function () {
        $('.select2').select2();
    });
</script>
@endsection
