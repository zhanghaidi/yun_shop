@extends('layouts.base')
@section('title', trans('房间设置'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">房间设置</a></li>
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

                @if($info['type']=='0')
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">房间名称</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <span class="form-control">{{ $info['name'] }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">房间封面</label>
                        <div class="col-sm-9 col-xs-12 col-md-10">
                            <div class="input-group " style="margin-top:.5em;">
                                <img src="{!! tomedia($info['cover_img']) !!}" onerror="this.src='/addons/yun_shop/static/resource/images/nopic.jpg'; this.title='图片未找到.'" class="img-responsive img-thumbnail" width="150">
                            </div>
                        </div>
                    </div>
                @endif

                @if($info['type']=='1')
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">房间名称</label>
                        <div class="col-sm-9 col-xs-12 col-md-11">
                            <input name="name" type="text" class="form-control" value="{{ $info['name'] }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">房间封面</label>
                        <div class="col-sm-9 col-xs-12 col-md-10">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img', $info['cover_img']) !!}
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">房间介绍</label>
                    <div class="col-sm-9 col-xs-12 col-md-11">
                        {!! yz_tpl_ueditor('desc', $info['desc']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="hidden" name="id" value="{{$rid}}" />
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