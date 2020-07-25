@extends('layouts.base')
@section('title', trans('视频设置'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">视频设置</a></li>
        </ul>
    </div>

    <div class='panel panel-default'>
        <div class="clearfix panel-heading">
            <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
               href="javascript:history.go(-1);">返回</a>
        </div>
    </div>

    <div class="w1200 m0a">
        <div class="rightlist">
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">标题</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        @if($room['type']=='0')
                            <span class="form-control">{{ $info['title'] }}</span>
                        @else
                            <input name="title" type="text" class="form-control" value="{{ $info['title'] }}" required />
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">类型</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <select name="type" class="form-control">
                            <option value="1" @if($info['type']=='1') selected="selected" @endif>本地上传</option>
                            <option value="2" @if($info['type']=='2') selected="selected" @endif>腾讯视频</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">链接地址</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        @if($room['type']=='0')
                            <span class="form-control">{{ $info['media_url'] }}</span>
                        @else
                            <input name="media_url" type="text" class="form-control" value="{{ $info['media_url'] }}" required />
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">视频时长</label>
                    <div class="col-md-10 col-sm-9 col-xs-12 form-inline">
                        <div class="input-group form-group col-sm-3" style="padding: 0">
                            <input type="number" name="minute" class="form-control" value="{{ $info['minute'] }}" required />
                            <span class="input-group-addon">分钟</span>
                        </div>
                        <div class="input-group form-group col-sm-3" style="padding: 0">
                            <input type="number" name="second" class="form-control" value="{{ $info['second'] }}" required />
                            <span class="input-group-addon">秒</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">发布时间</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        {!! tpl_form_field_date('publish_time', date('Y-m-d H:i', ($info['publish_time'] ? $info['publish_time'] : time())), true) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">预览图片</label>
                    <div class="col-md-9 col-sm-9 col-xs-12 detail-logo">
                        @if($room['type']=='0')
                            <div class="input-group " style="margin-top:.5em;">
                                <img src="{!! tomedia($info['cover_img']) !!}" onerror="this.src='/addons/yun_shop/static/resource/images/nopic.jpg'; this.title='图片未找到.'" class="img-responsive img-thumbnail" width="150">
                            </div>
                        @else
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img', $info['cover_img']) !!}
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">内容概述</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <textarea name="intro" rows="5" class="form-control">{{ $info['intro'] }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label"></label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input type="hidden" name="id" value="{{ $info['id'] }}" />
                        <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        var ueditoroption = {
            'toolbars' : [['source', 'preview', '|', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', '|',
                'justifyleft', 'justifycenter', 'justifyright', '|', 'insertorderedlist', 'insertunorderedlist', 'blockquote', 'emotion',
                'link', 'removeformat', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight','indent', 'paragraph', 'fontsize', '|',
                'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol',
                'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|', 'anchor', 'map', 'print', 'drafts']],
        };
    </script>

@endsection
