@extends('layouts.base')
@section('title', trans('基础设置'))
@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class='panel-heading'>基础设置</div>
                    <div class='panel-body'>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">域名</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="setting[host]" class="form-control" value="{{ $setting['host'] }}"/>
                                <span class="help-text">
                                    域名例如：https://www.aijuyi.net
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">路由地址</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="setting[domain]" class="form-control" value="{{ $setting['domain'] }}"/>
                                <span class="help-text">
                                    域名后地址例如：/static/ajy-h5/index.html?#/pages/punch-the-lock/index/index?tid=
                                </span>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                        </div>
                        <div class="form-group mt-1">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">客服助手二维码</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="setting[helper_avatar]" class="form-control" value="{{ $setting['helper_avatar'] }}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">客服助手微信</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="setting[helper_wechat]" class="form-control" value="{{ $setting['helper_wechat'] }}"/>
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
