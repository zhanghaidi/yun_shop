@extends('layouts.base')

@section('content')
@section('title', '充值码列表')
<link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>

<div class="rightlist">
    <div class="panel panel-info">
        <div class="panel-body">
            <div class="card">
                <div class="card-content">
                    <h4 class="card-title">充值后自动跳转指定页面</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<form action="" method="post">
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">公众号链接</label>
    <div class="col-sm-9 col-xs-9">
        <div class="input-group">
            <input class="form-control" type="text" data-id="PAL-00010" placeholder="请填写指向的链接 (请以http://开头, 不填则不显示)" value="{{ $setting['jump_link'] }}" name="setting[jump_link]">
            <span class="input-group-btn">
                                        <button class="btn btn-default nav-link" type="button" data-id="PAL-00010" >选择链接</button>
                                    </span>
        </div>
        <span class="help-block">用户使用充值码充值后跳转的指定页面，默认跳转到商城首页</span>
    </div>
</div>
@include('public.admin.mylink')

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">小程序链接</label>
        <div class="col-sm-9 col-xs-9">
            <div class="input-group">
                <input type="text" name="setting[small_jump_link]" data-id="PAL-00012" class="form-control" placeholder="请填写指向的链接 (请以http://开头, 不填则不显示)" value="{{$setting['small_jump_link']}}" />
                <span class="input-group-btn">
                                    <button class="btn btn-default nav-app-link" type="button" data-id="PAL-00012" >选择链接</button>
                                </span>
            </div>
        </div>
    </div>
@include('public.admin.small')
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
        <div class="col-sm-9">
            <input type="submit" name="submit" value="提交" class="btn btn-success"  />
        </div>
    </div>
</form>
@endsection