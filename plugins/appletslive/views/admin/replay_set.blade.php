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
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">标题</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <input name="title" type="text" class="form-control" value="{{ $info['title'] }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">预览图片</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        <input name="title" type="text" class="form-control" value="{{ $info['title'] }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span >*</span>预览图片</label>
                    <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img', $info['cover_img']) !!}
                        @if (!empty($info['cover_img']))
                            <a href='{{yz_tomedia($info['cover_img'])}}' target='_blank'>
                                <img src="{{yz_tomedia($info['cover_img'])}}" style='width:100px;border:1px solid #ccc;padding:1px' />
                            </a>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">内容概述</label>
                    <div class="col-sm-9 col-xs-12">
                        <textarea name="intro" rows="5" class="form-control">{{ $info['intro'] }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
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
