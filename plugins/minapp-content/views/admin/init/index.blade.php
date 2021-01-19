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
                    <span class="help-block">1、经络关联的课程，需手工设置； 点击进入<a href="{{ yzWebUrl('plugin.minapp-content.admin.meridian.index') }}" target="_blank">经络列表</a></span>
                    <span class="help-block">2、穴位关联的文章、商品，需手工设置； 点击进入<a href="{{ yzWebUrl('plugin.minapp-content.admin.acupoint.index') }}" target="_blank">穴位列表</a></span>
                </div>
                <div class="form-group col-xs-12 col-sm-1 col-md-1 col-lg-1">
                    <div class="input-group">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="update[acupoint]" value="1" /> 是否覆盖更新
                        </label>
                    </div>
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
        _update = $('input[name="update[acupoint]"]').is(":checked");
        if (_update == true && !confirm('选中覆盖更新，将会把艾居益应用中，关于经络穴位的最新更改，同步入本应用，是否确认？')) {
            util.message('数据同步被中止', '', 'warning');
            return false;
        }

        _old = $('input[name="old[acupoint]"]').val();
        _new = $('input[name="new[acupoint]"]').val();

        _url = "{{ yzWebUrl('plugin.minapp-content.admin.initialization.acupoint') }}";
        _url = _url.replace(/&amp;/g, '&');
        if (_update == true) {
            _url += '&update=1';
        }
        $.get(_url, function(res) {
            if (res.result == 1) {
                util.message('经络、穴位数据同步成功', '', 'success');
            } else {
                util.message(res.msg, '', 'warning');
            }
        });
    });
});
</script>
@endsection

