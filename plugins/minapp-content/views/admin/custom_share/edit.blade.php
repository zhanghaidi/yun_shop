@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <!-- 新增加右侧顶部三级菜单 -->
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">自定义分享</a></li>
        </ul>
    </div>
    <!-- 新增加右侧顶部三级菜单结束 -->
    <div class="panel panel-default">
        <div class="panel-body">

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="{{$info['id']}}">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享名称*</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <select class="form-control" data-placeholder="请选择分享名称" name="pageSelect">
                            <option value="">请选择分享名称</option>
                        </select>
                    </div>
                    <input type="hidden" name="name" value="{{ $info['name'] }}"/>
                    <input type="hidden" name="key" value="{{ $info['key'] }}"/>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题*</label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="text" name="title" class="form-control" value="{{$info['title']}}"
                               placeholder="请输入分享标题">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">享图片</label>
                    <div class="col-xs-12 col-sm-9 col-md-8">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('image', $info['image'])!!}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-2">
                        <input type="submit" name="submit" value="提交" class="btn btn-success"
                               onclick="return formcheck()"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script language="JavaScript">
    $(function () {
        $.get('/static/pages/share.json', function (data) {
            if (!('mainOptions' in data)) {
                return false;
            }
            let _pageOpt = '';
            _pageOpt += '<option value="">请选择分享名称</option>';
            for (var i = 0; i < data.mainOptions.length; i++) {
                let _list = data.mainOptions[i];
                if (!('value' in _list) || !('name' in _list)) {
                    continue;
                }
                if (_list.value == "{{ $info['key']}}") {
                    _pageOpt += '<option value="' + _list.value + '" selected>' + _list.name + '</option>'
                } else {
                    _pageOpt += '<option value="' + _list.value + '">' + _list.name + '</option>'
                }
            }
            $('select[name="pageSelect"]').html(_pageOpt);
        });
        $('select[name="pageSelect"]').on('change', function () {
            $('input[name="key"]').val($(this).val());
            $('input[name="name"]').val($(this).find('option:selected').text());
        });
        if ("{{$info['id']}}") {
            $('select[name="pageSelect"]').attr('disabled', 'disabled');
        }
    })
</script>
@endsection
