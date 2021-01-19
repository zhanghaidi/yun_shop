@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">养居益内容数据导入本程序</div>
        <div class="panel-body">

            <div class="row">
                <div class="form-group col-xs-12 col-sm-4 col-md-3 col-lg-3">
                    <div class="input-group">
                        <div class="input-group-addon">穴位</div>
                        <input type="text" placeholder="养居益" value="{{$acupoint['old']}}" name="old[acupoint]" class="form-control" disabled>
                        <div class="input-group-addon"> VS </div>
                        <input type="text" placeholder="本程序" value="{{$acupoint['new']}}" name="new[acupoint]" class="form-control" disabled>
                    </div>
                    <span class="help-block">1、经络关联的课程，需重新设置； 点击进入<a href="{{ yzWebUrl('plugin.minapp-content.admin.meridian.index') }}" target="_blank">经络列表</a></span>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success" id="acupoint"><i class="fa fa-share-square-o"></i> 同步</button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <div class="input-group">
                        <div class="input-group-addon">体质测试-题库</div>
                        <input type="text" placeholder="养居益" value="" name="old[question]" class="form-control" disabled>
                        <div class="input-group-addon"> VS </div>
                        <input type="text" placeholder="本程序" value="" name="new[question]" class="form-control" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success" id="question"><i class="fa fa-share-square-o"></i> 同步</button>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="form-group col-xs-12 col-sm-5 col-md-5 col-lg-5">
                    <div class="input-group">
                        <div class="input-group-addon">轮播图位置</div>
                        <input type="text" placeholder="养居益" value="" name="old[banner]" class="form-control" disabled>
                        <div class="input-group-addon"> VS </div>
                        <input type="text" placeholder="本程序" value="" name="new[banner]" class="form-control" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success" id="banner"><i class="fa fa-share-square-o"></i> 同步</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script language="JavaScript">
$(function () {
    $('#acupoint').on('click', function(){
        _old = $('input[name="old[acupoint]"]').val();
        _new = $('input[name="new[acupoint]"]').val();
        if (_old == _new) {
            util.message('新老数据量一致，不能进行同步操作', '', 'info');
            return false;
        }

        _url = "{{ yzWebUrl('plugin.minapp-content.admin.initialization.acupoint') }}";
        _url = _url.replace(/&amp;/g, '&');
        $.get(_url, function(res) {
            if (res.result == 1) {
                util.message('经络、穴位数据导入成功', '', 'success');
            } else {
                util.message(res.msg, '', 'warning');
            }
        });
    });
});
</script>
@endsection

