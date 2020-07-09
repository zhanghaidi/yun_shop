@extends('layouts.base')
@section('title', trans('客服设置'))
@section('content')
    <div class="w1200 m0a">
        <div class="main">
            <form id="baseform" method="post" class="form-horizontal form">
                <div class="rightlist">
                    <div class="right-titpos">
                        <ul class="add-snav">
                            <li class="active"><a href="#">客服设置</a></li>
                        </ul>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                    插件开启
                                </label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class="radio-inline">
                                        <input type="radio" name="form_data[is_open]" value="0"
                                               @if($set['is_open'] == 0) checked="checked" @endif /> 关闭</label>
                                    <label class="radio-inline">
                                        <input type="radio" name="form_data[is_open]" value="1"
                                               @if($set['is_open'] == 1) checked="checked" @endif /> 开启</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="right-titpos">
                        <ul class="add-snav">
                            <li class="active"><a href="#">H5端设置</a></li>
                        </ul>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">客服链接</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="form_data[link]" class="form-control"
                                   value="{{$set['link']}}" placeholder="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">二维码图片</label>
                        <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('form_data[QRcode]', $set['QRcode']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">联系电话</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="form_data[mobile]" class="form-control"
                                   value="{{$set['mobile']}}" placeholder="">
                        </div>
                    </div>
                    <div class="right-titpos">
                        <ul class="add-snav">
                            <li class="active"><a href="#">小程序端设置</a></li>
                        </ul>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                            小程序在线客服
                        </label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="form_data[mini_open]" value="0"
                                       @if($set['mini_open'] == 0) checked="checked" @endif /> 隐藏</label>
                            <label class="radio-inline">
                                <input type="radio" name="form_data[mini_open]" value="1"
                                       @if($set['mini_open'] == 1) checked="checked" @endif /> 显示</label>
                        </div>
                    </div>
                </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">二维码图片</label>
                        <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('form_data[mini_QRcode]', $set['mini_QRcode']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">联系电话</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="form_data[mini_mobile]" class="form-control"
                                   value="{{$set['mini_mobile']}}" placeholder="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="保存设置" class="btn btn-primary"
                                   data-original-title="" title="">
                            <input type="hidden" name="token" value="{$_W['token']}">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection