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
                </ul>
            </div>

            <form id="setform" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
                <div class="col-sm-9 col-xs-12">
                    <textarea name="setdata[share][title]" class="form-control" placeholder="请输入大于等于0的整数" rows="5">{{$set['share']['title']}}</textarea>
                    <span class='help-block'>分享标题中支持变量，请双击下面变量，增加到内容尾部</span>
                    <span class='help-block title-key'><i>{昵称}</i><i>{年龄}</i><i>{颜值}</i><i>{超越百分比}</i></span>
                </div>
            </div>


            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图片 - 主空白背景图</label>
                <div class="col-sm-9 col-xs-12">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[share][image]', $set['share']['image'])!!}
                    <span class='help-block'>主背景图修改后，需要后端开发人员修改程序，确认背景图上叠加的元素内容及其定位</span>
                    <span class='help-block'>鉴于主背景图千变万化，所需细节元素各有不同，主背景上如需叠加图片元素，不便于进行设置，请通过程序自行解决</span>
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
