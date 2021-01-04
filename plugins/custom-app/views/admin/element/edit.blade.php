@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">页面元素的值</div>
        <div class="panel-body">

            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[sort_id]" value="{{$sort['id']}}">

            @if($sort['type'] == 1)

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">文本内容</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <textarea name="data[content]" class="form-control" placeholder="请输入考试名称" rows="4">{{$data['content']}}</textarea>
                </div>
            </div>

            @elseif($sort['type'] == 2)

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">图片网址</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[content]', $data['content'])!!}
                </div>
            </div>

            @elseif($sort['type'] == 3)
            
            @if($data['content'][0])
            @foreach($data['content'] as $item)
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">文本内容</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <textarea name="data[content][]" class="form-control" placeholder="请输入考试名称" rows="4">{{$data['content']}}</textarea>
                </div>
            </div>
            @endforeach
            @else
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">文本内容</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <textarea name="data[content][]" class="form-control" placeholder="请输入考试名称" rows="4">{{$data['content']}}</textarea>
                </div>
            </div>
            @endif
            <div class="form-group addOptions">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <button type="button" class="btn btn-warning" onclick="addTxtItem()" style="margin-bottom: 5px">
                        <i class="fa fa-plus"></i> 新增一项
                    </button>
                </div>
            </div>

            @elseif($sort['type'] == 4)

            @if($data['content'][0])
            @foreach($data['content'] as $item)
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">图片网址</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[content][]', $item)!!}
                </div>
            </div>
            @endforeach
            @else
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">图片网址</label>
                <div class="col-xs-12 col-sm-9 col-md-10">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[content][]')!!}
                </div>
            </div>
            @endif
            <div class="form-group addOptions">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <button type="button" class="btn btn-warning" onclick="addUrlItem()" style="margin-bottom: 5px">
                        <i class="fa fa-plus"></i> 新增一项
                    </button>
                </div>
            </div>

            @endif

            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-2">
                        <input type="submit" name="submit" value="提交" class="btn btn-success" onclick="return formcheck()"/>
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>

<script language="JavaScript">
function addTxtItem() {
    _html = '<div class="form-group">';
    _html += '<label class="col-xs-12 col-sm-3 col-md-2 control-label">文本内容</label>';
    _html += '<div class="col-xs-12 col-sm-9 col-md-10">';
    _html += '<textarea name="data[content][]" class="form-control" placeholder="请输入文本内容" rows="4"></textarea>';
    _html += '</div>';
    _html += '</div>';
    $('.addOptions').before(_html);
}
function addUrlItem() {
    _html = '<div class="form-group">';
    _html += '<label class="col-xs-12 col-sm-3 col-md-2 control-label">图片网址</label>';
    _html += '<div class="col-xs-12 col-sm-9 col-md-10">';
    _html += '<div class="input-group ">';
    _html += '<input type="text" name="data[content][]" value="" class="form-control" autocomplete="off">';
    _html += '<span class="input-group-btn">';
    _html += '<button class="btn btn-default" type="button" onclick="showImageDialog(this);">选择图片</button>';
    _html += '</span>';
    _html += '</div>';
    _html += '<div class="input-group " style="margin-top:.5em;">';
    _html += '<img src="/addons/yun_shop/static/resource/images/nopic.jpg" onerror="this.src=\'/addons/yun_shop/static/resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail"  width="150" />';
    _html += '<em class="close" style="position:absolute; top: 0px; right: -14px;" title="删除这张图片" onclick="deleteImage(this)">×</em>';
    _html += '</div>';
    _html += '</div>';
    _html += '</div>';
    $('.addOptions').before(_html);
}
</script>
@endsection
