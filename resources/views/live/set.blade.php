@extends('layouts.base')
@section('title', '云直播设置')
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">云直播设置</a></li>
        </ul>
    </div>

    <div class="main rightlist">

        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>
                <div class='panel-heading'>腾讯云直播</div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启云直播</label>
                        <div class="col-xs-12 col-sm-4 col-md-6">
                            <label class='radio-inline'><input type='radio' name='live[open]' value='1' @if($live['open'] == 1) checked @endif/>开启</label>
                            <label class='radio-inline'><input type='radio' name='live[open]' value='0' @if($live['open'] == 0) checked @endif/>关闭</label>
                            <span class='help-block'>是否允许主播进行直播</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>应用名称</label>
                        <div class="col-xs-12 col-sm-4 col-md-6">
                            <input type="text" class="form-control" name="live[app_name]" value="{{ $live['app_name'] }}" required>
                            <span class="help-block">腾讯云直播应用名称，必选配置</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>密钥ID</label>
                        <div class="col-xs-12 col-sm-4 col-md-6">
                            <input type="text" class="form-control" name="live[secret_id]" value="{{ $live['secret_id'] }}" required>
                            <span class="help-block">腾讯云API密钥ID，必选配置</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>密钥key</label>
                        <div class="col-xs-12 col-sm-4 col-md-6">
                            <input type="text" class="form-control" name="live[secret_key]" value="{{ $live['secret_key'] }}" required>
                            <span class="help-block">腾讯云API密钥key，必选配置</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>推流域名</label>
                        <div class="col-xs-12 col-sm-4 col-md-6">
                            <input type="text" class="form-control" name="live[push_domain]" value="{{ $live['push_domain'] }}" required>
                            <span class="help-block">用于推送直播流的域名，必选配置</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>推流鉴权key</label>
                        <div class="col-xs-12 col-sm-4 col-md-6">
                            <input type="text" class="form-control" name="live[push_key]" value="{{ $live['push_key'] }}" required>
                            <span class="help-block">用于推送直播流鉴权，必选配置</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>拉流域名</label>
                        <div class="col-xs-12 col-sm-4 col-md-6">
                            <input type="text" class="form-control" name="live[pull_domain]" value="{{ $live['pull_domain'] }}" required>
                            <span class="help-block">用于播放直播流的域名，必选配置</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>直播流名称前缀</label>
                        <div class="col-xs-12 col-sm-4 col-md-6">
                            <input type="text" class="form-control" name="live[stream_name_pre]" value="{{ $live['stream_name_pre'] }}" required>
                            <span class="help-block">直播流名称前缀，必选配置</span>
                        </div>
                    </div>

                </div>

                <div class='panel-heading'>腾讯云即时IM</div>
                <div class='panel-body'>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>IM应用ID</label>
                        <div class="col-xs-12 col-sm-4 col-md-6">
                            <input type="text" class="form-control" name="im[sdk_appid]" value="{{ $im['sdk_appid'] }}" required>
                            <span class="help-block">腾讯云即时IM应用ID，必选配置</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>密钥ID</label>
                        <div class="col-xs-12 col-sm-4 col-md-6">
                            <input type="text" class="form-control" name="im[app_key]" value="{{ $im['app_key'] }}" required>
                            <span class="help-block">腾讯云即时IM应用ID密钥，必选配置</span>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>管理员账号</label>
                        <div class="col-xs-12 col-sm-4 col-md-6">
                            <input type="text" class="form-control" name="im[identifier]" value="{{ $im['identifier'] }}" required>
                            <span class="help-block">管理员账号，必选配置</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>群组名称前缀</label>
                        <div class="col-xs-12 col-sm-4 col-md-6">
                            <input type="text" class="form-control" name="im[group_pre]" value="{{ $im['group_pre'] }}" required>
                            <span class="help-block">群组名称前缀，必选配置</span>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-xs-12 col-sm-4 col-md-6">
                    <input type="submit" name="submit" value="保存设置" class="btn btn-success"/>
                </div>
            </div>
            </div>
        </form>
    </div>
    <script language='javascript'>


    </script>

@endsection