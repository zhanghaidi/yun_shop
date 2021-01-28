@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>养居益内容数据导入本程序<small>第二次及以后导入，建议勾选“覆盖更新”，该操作不会覆盖手工设置的关联数据</small></h3>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="form-group col-xs-12 col-sm-5 col-md-4 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">经络</div>
                        <input type="text" placeholder="养居益" value="{{$meridian['old']}} - 养居益" name="old[meridian]" class="form-control" disabled>
                        <div class="input-group-addon"> VS </div>
                        <input type="text" placeholder="本程序" value="{{$meridian['new']}} - 本程序" name="new[meridian]" class="form-control" disabled>
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon">穴位</div>
                        <input type="text" placeholder="养居益" value="{{$acupoint['old']}}" name="old[acupoint]" class="form-control" disabled>
                        <div class="input-group-addon"> VS </div>
                        <input type="text" placeholder="本程序" value="{{$acupoint['new']}}" name="new[acupoint]" class="form-control" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-1">
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
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <span class="help-block">1、经络关联的课程，需手工设置； 点击进入<a href="{{ yzWebUrl('plugin.minapp-content.admin.meridian.index') }}" target="_blank">经络列表</a></span>
                    <span class="help-block">2、穴位关联的文章、商品，需手工设置； 点击进入<a href="{{ yzWebUrl('plugin.minapp-content.admin.acupoint.index') }}" target="_blank">穴位列表</a></span>
                </div>
            </div>

            <hr />

            <div class="row">
                <div class="form-group col-xs-12 col-sm-5 col-md-4 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">文章分类</div>
                        <input type="text" placeholder="养居益" value="{{$article_category['old']}} - 养居益" name="old[article_category]" class="form-control" disabled>
                        <div class="input-group-addon"> VS </div>
                        <input type="text" placeholder="本程序" value="{{$article_category['new']}} - 本程序" name="new[article_category]" class="form-control" disabled>
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon">文章</div>
                        <input type="text" placeholder="养居益" value="{{$article['old']}}" name="old[article]" class="form-control" disabled>
                        <div class="input-group-addon"> VS </div>
                        <input type="text" placeholder="本程序" value="{{$article['new']}}" name="new[article]" class="form-control" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-1">
                    <div class="input-group">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="update[article]" value="1" /> 是否覆盖更新
                        </label>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success" id="article"><i class="fa fa-share-square-o"></i> 同步</button>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <span class="help-block">1、文章关联的商品，需手工设置； 点击进入<a href="{{ yzWebUrl('plugin.minapp-content.admin.article.index') }}" target="_blank">文章列表</a></span>
                    <span class="help-block">PS: 文章中的“文章来源/作者”属性，初次同步会同步导入；后续使用“覆盖更新”不覆盖</span>
                </div>
            </div>

            <hr />

            <div class="row">
                <div class="form-group col-xs-12 col-sm-5 col-md-4 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">症状</div>
                        <input type="text" placeholder="养居益" value="{{$label['old']}} - 养居益" name="old[label]" class="form-control" disabled>
                        <div class="input-group-addon"> VS </div>
                        <input type="text" placeholder="本程序" value="{{$label['new']}} - 本程序" name="new[label]" class="form-control" disabled>
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon">体质</div>
                        <input type="text" placeholder="养居益" value="{{$somato['old']}}" name="old[somato]" class="form-control" disabled>
                        <div class="input-group-addon"> VS </div>
                        <input type="text" placeholder="本程序" value="{{$somato['new']}}" name="new[somato]" class="form-control" disabled>
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon">题库</div>
                        <input type="text" placeholder="养居益" value="{{$question['old']}}" name="old[question]" class="form-control" disabled>
                        <div class="input-group-addon"> VS </div>
                        <input type="text" placeholder="本程序" value="{{$question['new']}}" name="new[question]" class="form-control" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-1">
                    <div class="input-group">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="update[question]" value="1" /> 是否覆盖更新
                        </label>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success" id="question"><i class="fa fa-share-square-o"></i> 同步</button>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <span class="help-block">1、体质关联的商品，需手工设置； 点击进入<a href="{{ yzWebUrl('plugin.minapp-content.admin.somato-type.index') }}" target="_blank">体质列表</a></span>
                </div>
            </div>

            <hr />

            <div class="row">
                <div class="form-group col-xs-12 col-sm-5 col-md-4 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">版块</div>
                        <input type="text" placeholder="养居益" value="{{$board['old']}} - 养居益" name="old[board]" class="form-control" disabled>
                        <div class="input-group-addon"> VS </div>
                        <input type="text" placeholder="本程序" value="{{$board['new']}} - 本程序" name="new[board]" class="form-control" disabled>
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon">话题</div>
                        <input type="text" placeholder="养居益" value="{{$post['old']}}" name="old[post]" class="form-control" disabled>
                        <div class="input-group-addon"> VS </div>
                        <input type="text" placeholder="本程序" value="{{$post['new']}}" name="new[post]" class="form-control" disabled>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-1">
                    <div class="input-group">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="update[post]" value="1" /> 是否覆盖更新
                        </label>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success" id="post"><i class="fa fa-share-square-o"></i> 同步</button>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <span class="help-block">1、版块关联的管理员，需手工设置； 点击进入<a href="{{ yzWebUrl('plugin.minapp-content.admin.sns-board.index') }}" target="_blank">版块列表</a></span>
                    <span class="help-block">2、话题关联的发帖用户，需手工设置； 点击进入<a href="{{ yzWebUrl('plugin.minapp-content.admin.post.index') }}" target="_blank">话题列表</a></span>
                </div>
            </div>

        </div>
    </div>
</div>

<script language="JavaScript">
$(function () {
    $('#acupoint').on('click', function(){
        $('#acupoint').attr('disabled', true);
        setTimeout(function(){
            $('#acupoint').attr('disabled', false);
        }, 1000);

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

    $('#article').on('click', function(){
        $('#article').attr('disabled', true);
        setTimeout(function(){
            $('#article').attr('disabled', false);
        }, 1000);

        _update = $('input[name="update[article]"]').is(":checked");
        if (_update == true && !confirm('选中覆盖更新，将会把艾居益应用中，关于文章的最新更改，同步入本应用，是否确认？')) {
            util.message('数据同步被中止', '', 'warning');
            return false;
        }

        _old = $('input[name="old[acupoint]"]').val();
        _new = $('input[name="new[acupoint]"]').val();
        if (_new < _old) {
            util.message('穴位数据尚未同步，请先同步经络穴位', '', 'warning');
            return false;
        }

        _url = "{{ yzWebUrl('plugin.minapp-content.admin.initialization.article') }}";
        _url = _url.replace(/&amp;/g, '&');
        if (_update == true) {
            _url += '&update=1';
        }
        $.get(_url, function(res) {
            if (res.result == 1) {
                util.message('文章分类、文章数据同步成功', '', 'success');
            } else {
                util.message(res.msg, '', 'warning');
            }
        });
    });

    $('#question').on('click', function(){
        $('#question').attr('disabled', true);
        setTimeout(function(){
            $('#question').attr('disabled', false);
        }, 1000);

        _update = $('input[name="update[question]"]').is(":checked");
        if (_update == true && !confirm('选中覆盖更新，将会把艾居益应用中，关于体质的最新更改，同步入本应用，是否确认？')) {
            util.message('数据同步被中止', '', 'warning');
            return false;
        }

        _old = $('input[name="old[acupoint]"]').val();
        _new = $('input[name="new[acupoint]"]').val();
        if (_new < _old) {
            util.message('穴位数据尚未同步，请先同步经络穴位', '', 'warning');
            return false;
        }

        _old = $('input[name="old[article]"]').val();
        _new = $('input[name="new[article]"]').val();
        if (_new < _old / 2) {
            util.message('文章数据尚未同步，请先同步文章及其分类', '', 'warning');
            return false;
        }

        _url = "{{ yzWebUrl('plugin.minapp-content.admin.initialization.question') }}";
        _url = _url.replace(/&amp;/g, '&');
        if (_update == true) {
            _url += '&update=1';
        }
        $.get(_url, function(res) {
            if (res.result == 1) {
                util.message('症状、体质、题库数据同步成功', '', 'success');
            } else {
                util.message(res.msg, '', 'warning');
            }
        });
    });

    $('#post').on('click', function(){
        $('#post').attr('disabled', true);
        setTimeout(function(){
            $('#post').attr('disabled', false);
        }, 1000);

        _update = $('input[name="update[post]"]').is(":checked");
        if (_update == true && !confirm('选中覆盖更新，将会把艾居益应用中，关于健康社区的最新更改，同步入本应用，是否确认？')) {
            util.message('数据同步被中止', '', 'warning');
            return false;
        }

        _url = "{{ yzWebUrl('plugin.minapp-content.admin.initialization.post') }}";
        _url = _url.replace(/&amp;/g, '&');
        if (_update == true) {
            _url += '&update=1';
        }
        $.get(_url, function(res) {
            if (res.result == 1) {
                util.message('话题版块、社区话题数据同步成功', '', 'success');
            } else {
                util.message(res.msg, '', 'warning');
            }
        });
    });
});
</script>
@endsection

