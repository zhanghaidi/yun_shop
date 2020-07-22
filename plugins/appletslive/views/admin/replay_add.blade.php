@extends('layouts.base')
@section('title', trans('视频设置'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">添加录播视频</a></li>
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
                        <input name="title" type="text" class="form-control" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">链接地址</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input name="media_url" type="text" class="form-control" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">预览图片</label>
                    <div class="col-md-9 col-sm-9 col-xs-12 detail-logo">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img', '') !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">内容概述</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <textarea name="intro" rows="5" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label"></label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input type="hidden" name="rid" value="{{ $rid }}" />
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