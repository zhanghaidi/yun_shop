@extends('layouts.base')
@section('title', '云直播设置')
@section('content')

    <div class="main rightlist">

        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-4 control-label">开启云直播</label>
                <div class="col-xs-12 col-sm-3 col-md-4">
                    <label class='radio-inline'><input type='radio' name='live[open]' value='1' @if($live['open'] == 1) checked @endif/>开启</label>
                    <label class='radio-inline'><input type='radio' name='live[open]' value='0' @if($live['open'] == 0) checked @endif/>关闭</label>
                    <span class='help-block'>是否允许主播进行直播</span>
                </div>
            </div>


            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-4 control-label"><span style='color:red'>*</span>推流域名</label>
                <div class="col-xs-12 col-sm-3 col-md-4">
                    <input type="text" class="form-control" name="live[push_domain]" value="{{ $live['push_domain'] }}" required>
                    <span class="help-block">用于推送直播流的域名，必选配置</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-4 control-label"><span style='color:red'>*</span>推流鉴权key</label>
                <div class="col-xs-12 col-sm-3 col-md-4">
                    <input type="text" class="form-control" name="live[push_key]" value="{{ $live['push_key'] }}" required>
                    <span class="help-block">用于推送直播流鉴权，必选配置</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-4 control-label"><span style='color:red'>*</span>拉流域名</label>
                <div class="col-xs-12 col-sm-3 col-md-4">
                    <input type="text" class="form-control" name="live[pull_domain]" value="{{ $live['pull_domain'] }}" required>
                    <span class="help-block">用于播放直播流的域名，必选配置</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-4 control-label"></label>
                <div class="col-xs-12 col-sm-3 col-md-4">
                    <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                </div>
            </div>
        </form>
    </div>
    <script language='javascript'>


    </script>

@endsection