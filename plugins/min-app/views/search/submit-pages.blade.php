@extends('layouts.base')
@section('title', '提交收录小程序页面')
@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('plugin.min-app.Backend.Controllers.search.site-search')}}">收录查询</a></li>
                    <li><a href="#">&nbsp;<i class="fa fa-angle-double-right"></i> &nbsp;提交收录小程序页面</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="" method='post' class='form-horizontal'>
                <div class='panel panel-default'>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">小程序选择</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'><input type='radio' name='page[minid]' value='1' />主体小程序</label>
                                <label class='radio-inline'><input type='radio' name='page[minid]' value='2' />商城小程序</label>
                            </div>
                        </div>

                        <div class="form-group mainappSelect">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">主体小程序页面</label>
                            <div class="col-sm-9 col-xs-12">
                                <select class="form-control" name="page[mainapp][path]">
                                    <option value="">请选择页面</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group mainappSelect">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">页面参数</label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="page[mainapp][query]" class="form-control" value="" placeholder="请输入所选择页面的页面参数" /></div>
                        </div>

                        <div class="form-group shopappSelect">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城小程序页面路径</label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="page[shopapp][path]" class="form-control" value="" placeholder="请输入页面的路径" /></div>
                        </div>

                        <div class="form-group shopappSelect">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">页面参数</label>
                            <div class="col-sm-9 col-xs-12"><input type="text" name="page[shopapp][query]" class="form-control" value="" placeholder="请输入所选择页面的页面参数" /></div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-9 col-xs-12 col-md-10 col-sm-offset-3 col-md-offset-2">
                                小程序页面 和 页面参数，请参考 <a class="mp-links" href="javascript:void(0);">小程序页面路径</a>
                            </div>
                        </div>

                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit"  name="submit" value="提交" class="btn btn-success"/>
                                <input type="hidden" name="popup[id]" value="{{$popup['id']}}"/>
                                <input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回" style='margin-left:10px;'/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script>
$(function(){
    $('.mainappSelect').hide();
    $('.shopappSelect').hide();

    $.get('/static/pages/share.json', function(data) {
        if (!('options' in data)) {
            return false;
        }
        _pageOpt = '<option value="">请选择页面</option>';
        for (var i = 0; i < data.options.length; i++) {
            _list = data.options[i];
            if (!('value' in _list) || !('name' in _list)) {
                continue;
            }
            if (_list.value == '') {
                _pageOpt += '<option value="' + _list.value + '" selected>' + _list.name + '</option>'
            } else {
                _pageOpt += '<option value="' + _list.value + '">' + _list.name + '</option>'
            }
        }
        $('select[name="page[mainapp][path]"]').html(_pageOpt);
    });

    $('select[name="page[mainapp][path]"]').on('change', function(){
        _desc = '页面路径: ';
        _desc += $(this).val();
        $(this).next('span').html(_desc);
    });
    
    $('input[name="page[minid]"]').on('change', function(){
        if ($(this).val() == 1) {
            $('.mainappSelect').show();
            $('.shopappSelect').hide();
        } else {
            $('.mainappSelect').hide();
            $('.shopappSelect').show();
        }
    });

    var linkUrl = '//www.aijuyi.net/static/pages/links.html';

    $('.mp-links').click(function() {
        linkUrl += '?v=' + new Date().getTime();
        $('#mp-links-iframe').attr('src', linkUrl);
        $('#mp-modal').modal()
    });
});

</script>
@endsection