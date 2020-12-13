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

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label" style="width: 10%">活动规则</label>
                <div class="col-sm-9 col-xs-12">
                    {!! yz_tpl_ueditor('setdata[rule]', $set['rule']) !!}
                    {{--{!! tpl_ueditor('setdata[rule]', $set['rule']) !!}--}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label" style="width: 10%">讨论社区</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="input-group">
                        <select name="setdata[sns][id]" class="form-control">
                            <option value="">请选择讨论社区</option>
                            @foreach($sns as $value)
                            <option value="{{$value['id']}}" @if($set['sns']['id'] == $value['id']) selected="selected" @endif>{{$value['name']}}</option>
                            @endforeach 
                        </select>
                    </div>
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
    </script>
@endsection
