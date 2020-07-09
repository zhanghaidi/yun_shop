@extends('layouts.base')

@section('content')
@section('title', trans('打印设置'))
<div class="w1200 m0a">
    <div class="rightlist">
        <form action="" method="post">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-cogs"></i> 打印设置</div>
                <div class="panel-body table-responsive">
                    打印控件下载 <br>    
                    <a href="{{resource_get('plugins/exhelper/src/common/lodop/CLodop_Setup_for_Win32NT.exe', 1)}}">CLodop_Setup_for_Win32NT.exe</a><br>
                    <a href="{{resource_get('plugins/exhelper/src/common/lodop/install_lodop32.exe', 1)}}">install_lodop32.exe(请根据操作系统位数选择下载)</a><br>
                    <a href="{{resource_get('plugins/exhelper/src/common/lodop/install_lodop64.exe', 1)}}">install_lodop64.exe(请根据操作系统位数选择下载)</a> <br>
                    <div class="alert alert-info" style="width: 500px;">提示：请在连有打印的电脑上安装控件并进行打印。</div>
                    
                    <div class="input-group" style="width: 500px;">
                        <div style="border-right:none" class="input-group-addon">本地打印机IP</div>
                        <input type="text" value="@if (empty($set->ip))localhost @else {{$set->ip}} @endif" class="form-control" name="data[ip]">
                        <div style="border-right:none; border-left: none;" class="input-group-addon"> 打印机端口</div>
                        <input type="tel" value="@if (empty($set->port))8000 @else {{$set->port}} @endif" class="form-control" name="data[port]">
                        <div style="border-right:none; border-left: none;" class="input-group-addon"> 打印机名称</div>
                        <input type="tel" value="@if (empty($set->name))8000 @else {{$set->name}} @endif" class="form-control" name="data[name]">
                    </div>
                </div>
                <div class="panel-body table-responsive" style="width: 500px;"></div>

                <div class="panel-heading"><i class="fa fa-cogs"></i>快递鸟 APIkey</div>
                <div class="panel-body table-responsive">
                    <!-- <div class="alert alert-info" style="width: 500px;"></div> -->
                    <div class="input-group" style="width: 500px;">
                        <div style="border-right:none" class="input-group-addon">APIkey</div>
                        <input type="text" value="@if (empty($set->apikey))  @else {{$set->apikey}} @endif" class="form-control" name="data[apikey]">
                    </div>
                </div>

                <div class="panel-body table-responsive">
                    <!-- <div class="alert alert-info" style="width: 500px;"> 商户ID</div> -->
                    <div class="input-group" style="width: 500px;">
                        <div style="border-right:none" class="input-group-addon"> 商户ID</div>
                        <input type="text" value="@if (empty($set->merchant_id))  @else {{$set->merchant_id}} @endif" class="form-control" name="data[merchant_id]">
                    </div>
                </div>

                <div class="panel-footer">
                    {{--<input type="hidden" name="token" value="{$_W['token']}" />--}}
                    <button class="btn btn-primary">保存设置</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection