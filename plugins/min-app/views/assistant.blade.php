@extends('layouts.base')
@section('title', trans('基础设置'))
<style>
    .panel-heading1{padding:10px 15px;border-bottom:0px solid transparent;border-top-left-radius:3px;border-top-right-radius:3px}
    .app-qrcode-img{padding:10px 15px;border-bottom:0px solid transparent;width: 13%;height:200px;background: transparent url({{ plugin_assets('min-app', 'assets/images/qrCode.png') }}) no-repeat 100% 100%;background-size:contain;float: left;}
    .data{width: 18%;height: 450px;background: #0AC0D2;margin-left: 130px;float: left;
        border-style: ridge;
        background: transparent url({{ plugin_assets('min-app', 'assets/images/overview.png') }}) no-repeat 0 0;background-size:contain;
    }
    .data1{width: 18%;height: 450px;margin-left: 35px;float: left; border-style: ridge}
    .tu2{ background: transparent url({{ plugin_assets('min-app', 'assets/images/analysis.png') }}) no-repeat 0 0;background-size:contain;}
    .tu3{ background: transparent url({{ plugin_assets('min-app', 'assets/images/analysis-source.png') }}) no-repeat 0 0;background-size:contain;}
    .tu4{ background: transparent url({{ plugin_assets('min-app', 'assets/images/portrait.png') }}) no-repeat 0 0;background-size:contain;}
</style>
@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class='panel-heading'>小程序数据助手</div>
                    <div class='panel-heading1'>
                       <div class="app-qrcode-img"></div>
         <span style="float: left;margin: 30px 0 0 20px">
             <b>功能概述</b><br>
            “小程序数据助手” 支持相关的开发和运营人员查看自身小程序的运营数据；<br>
包括数据概况、访问基础分析（用户趋势、来源分析、留存分析、时长分析、页面详情）、实时统计和用户画像（年龄性别、省份城市、终端机型）。<br>
             <b>使用说明</b><br>
小程序管理员：扫码即可打开“小程序数据助手”，可以选择查看已绑定小程序的数据。<br>
其他微信用户：经管理员授权后可以查看已授权小程序的数据。
         </span>
                    </div>

                    <div style="clear:both;margin:0 100px 0 100px"  class='panel-heading1'></div>
                    <div class="data"></div>
                    <div class="data1 tu2"></div>
                    <div class="data1 tu3"></div>
                    <div class="data1 tu4"></div>
                </div>
            </form>
        </div>
    </div>
@endsection
