@extends('layouts.base')
@section('title', trans('基础设置'))
@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class='panel-heading'>客户端设置</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">客户端</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'>
                                    <input type='radio' name='min[switch]' value='1' @if ($set['switch'] == 1) checked @endif/>
                                    开启
                                </label>
                                <label class='radio-inline'>
                                    <input type='radio' name='min[switch]' value='0' @if ($set['switch'] == 0) checked @endif />
                                    关闭
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">App ID</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="min[key]" class="form-control" value="{{ $set['key'] }}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">App Secret</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="min[secret]" class="form-control" value="{{ $set['secret'] }}"/>
                            </div>
                        </div>
                        <div class="form-group mt-4">
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城App ID</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="min[shop_key]" class="form-control" value="{{ $set['shop_key'] }}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城App Secret</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="min[shop_secret]" class="form-control" value="{{ $set['shop_secret'] }}"/>
                            </div>
                        </div>

                    </div>
                    <div class='panel-heading'>腾讯位置服务</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">Key</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="min[qq_map_web_key]" class="form-control" value="{{ $set['qq_map_web_key'] }}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">Sign</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="min[qq_map_web_sign]" class="form-control" value="{{ $set['qq_map_web_sign'] }}"/>
                                <span class="help-text">
                                    腾讯位置服务：此处为小程序定位功能,不填则为默认key和Sign一天限制请求一万次,申请开发者密钥(Key),
                                </span>
                                <a href="https://lbs.qq.com/dev/console/key/add" target="_blank">申请密钥</a>
                            </div>
                        </div>
                    </div>
                    <div class='panel-heading'>好物圈设置</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'>
                                    <input type='radio' name='min[hwq]' value='1' @if ($set['hwq'] == 1) checked @endif/>
                                    开启
                                </label>
                                <label class='radio-inline'>
                                    <input type='radio' name='min[hwq]' value='0' @if ($set['hwq'] == 0) checked @endif />
                                    关闭
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class='panel-heading'>微信商户设置</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付商户号</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="min[mchid]" class="form-control" value="{{ $set['mchid'] }}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付密钥</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="min[api_secret]" class="form-control" value="{{ $set['api_secret'] }}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">CERT证书文件</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="hidden" name="min[apiclient_cert]" value="{{ $set['apiclient_cert'] }}"/>
                                <input type="file" name="apiclient_cert" class="form-control"/>
                                <span class="help-block">
                                    @if (!empty($set['apiclient_cert']))
                                        <span class='label label-success'>已上传</span>
                                    @else
                                        <span class='label label-danger'>未上传</span>
                                    @endif
                                    下载证书 cert.zip 中的 apiclient_cert.pem 文件
                                </span>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">KEY密钥文件</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="hidden" name="min[apiclient_key]" value="{{ $set['apiclient_key'] }}"/>

                                <input type="file" name="apiclient_key" class="form-control"/>
                                <span class="help-block">
                                    @if (!empty($set['apiclient_key']))
                                        <span class='label label-success'>已上传</span>
                                    @else
                                        <span class='label label-danger'>未上传</span>
                                    @endif
                                    下载证书 cert.zip 中的 apiclient_key.pem 文件
                                </span>

                            </div>
                        </div>
{{--                        <div class="form-group">--}}
{{--                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">ROOT文件</label>--}}
{{--                            <div class="col-sm-9 col-xs-12">--}}
{{--                                <input type="hidden" name="min[weixin_root]" value="{{ $set['weixin_root'] }}"/>--}}
{{--                                <input type="file" name="weixin_root" class="form-control"/>--}}
{{--                                <span class="help-block">--}}
{{--                                    @if (!empty($set['weixin_root']))--}}
{{--                                        <span class='label label-success'>已上传</span>--}}
{{--                                    @else--}}
{{--                                        <span class='label label-danger'>未上传</span>--}}
{{--                                    @endif--}}
{{--                                    下载证书 cert.zip 中的 rootca.pem 文件--}}
{{--                                </span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="form-group"></div>
                    </div>

                    <div class='panel-heading'>关联公众号</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">公众号关注链接</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="min[follow_link]" class="form-control" value="{{ $set['follow_link'] }}"/>
                            </div>
                        </div>
                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
