
@extends('layouts.base')
@section('title', trans('活码基础设置'))
@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class='panel-heading'>活码基础设置</div>
                    <div class='panel-body'>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">企业微信企业ID</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="setdata[corpid]" class="form-control" value="{{ $setdata['corpid'] }}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">企业微信企业Secret</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="password" name="setdata[corpsecret]" class="form-control encrypt" value="{{ $setdata['corpsecret'] }}"/>
                            </div>
                        </div>

                    </div>

                    </div>

                    <div class='panel-heading'>微伴助手设置</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">微伴助手企业ID</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="setdata[weiban_corpid]" class="form-control" value="{{ $setdata['weiban_corpid'] }}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">企业微信企业Secret</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="password" name="setdata[weiban_secret]" class="form-control encrypt" value="{{ $setdata['weiban_secret'] }}"/>
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

