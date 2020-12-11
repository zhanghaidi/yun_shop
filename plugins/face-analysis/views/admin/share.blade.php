@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<style>
.title-key > i {
    margin-right: 20px;
    font-size: 20px;
    color: orange;
    font-style: normal;
}
</style>
<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li @if(\YunShop::request()->route == 'plugin.face-analysis.admin.face-analysis-set.index') class="active" @endif><a href="{{yzWebUrl('plugin.face-analysis.admin.face-analysis-set.index')}}">基础设置</a></li>
                    <li @if(\YunShop::request()->route == 'plugin.face-analysis.admin.face-analysis-set.share') class="active" @endif><a href="{{yzWebUrl('plugin.face-analysis.admin.face-analysis-set.share')}}">分享设置</a></li>
                    <li @if(\YunShop::request()->route == 'plugin.face-analysis.admin.face-analysis-set.rule') class="active" @endif><a href="{{yzWebUrl('plugin.face-analysis.admin.face-analysis-set.rule')}}">规则内容设置</a></li>
                </ul>
            </div>

            <form id="setform" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

            <div><b>有数据后的分享:</b></div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
                <div class="col-sm-9 col-xs-12">
                    <textarea name="setdata[share][title]" class="form-control" placeholder="请输入分享文本" rows="5">{{$set['share']['title']}}</textarea>
                    <span class='help-block'>分享标题中支持变量，请双击下面变量，增加到内容尾部</span>
                    <span class='help-block title-key'><i>{昵称}</i><i>{年龄}</i><i>{颜值}</i><i>{超越百分比}</i></span>
                </div>
            </div>


            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图片 - 主空白背景图</label>
                <div class="col-sm-9 col-xs-12">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[share][image][main]', $set['share']['image']['main'])!!}
                    <span class='help-block'>主背景图修改后，需要后端开发人员修改程序，确认背景图上叠加的元素内容及其定位</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图片 - 背景上所需元素图</label>
                <div class="col-sm-9 col-xs-12">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[share][image][addition]', $set['share']['image']['addition'])!!}
                    <span class='help-block'>元素图修改后，需要后端开发人员修改程序，确认背景图上叠加的元素内容及其定位</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">所用COS源站域名</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="setdata[share][image][domain]" class="form-control" value="{{$set['share']['image']['domain']}}" placeholder="请输入数据万象源站域名">
                    <span class='help-block'>水印图片与源图片必须位于同一个存储桶下</span>
                    <span class='help-block'>URL 需使用数据万象源站域名（不能使用 CDN 加速、COS 源站域名），例如examplebucket-1250000000.image.myqcloud.com属于 CDN 加速域名，不能在水印 URL 中使用</span>
                    <span class='help-block'>URL 必须以http://开始，不能省略 HTTP 头，也不能填 HTTPS 头，例如examplebucket-1250000000.cos.ap-shanghai.myqcloud.com/shuiyin_2.png，https://examplebucket-1250000000.cos.ap-shanghai.myqcloud.com/shuiyin_2.png 为非法的水印 URL</span>
                </div>
            </div>

            <hr>
            <div><b>无数据时的分享:</b></div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
                <div class="col-sm-9 col-xs-12">
                    <textarea name="setdata[share][title-none]" class="form-control" placeholder="请输入分享文本" rows="5">{{$set['share']['title-none']}}</textarea>
                </div>
            </div>


            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图片</label>
                <div class="col-sm-9 col-xs-12">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[share][image-none]', $set['share']['image-none'])!!}
                </div>
            </div>

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
        $(function () {
            $('.title-key > i').on('dblclick', function(){
                _title = $('textarea[name="setdata[share][title]"]').val();
                _title += $(this).html();
                $('textarea[name="setdata[share][title]"]').val(_title);
            });
        });
    </script>
@endsection
