@extends('layouts.base')
@section('title', trans('基础设置'))
@section('content')
    <!-- import stylesheet -->
    {{--<link rel="stylesheet" href="//unpkg.com/iview/dist/styles/iview.css">--}}
    <link href="{{ static_url('yunshop/iview/css/iview_2.14.0_styles_iview.css') }}" rel="stylesheet">
    <!-- import iView -->
    <style>
        .navbar-minimize h4 {margin-top: 15%;font-size: 1.5em;}
        .pull-left a {font-size: 1.2em;}
    </style>
    <div class="w1200 m0a">
        <iframe src="https://app.yunzmall.com/wechat/serve?appid={{$set['key']}}&uniacid={{$set['uniacid']}}&host={{$set['host']}}" id="myiframe" scrolling="no" style="width:100%;height:800px;" frameborder="0"></iframe>
    </div>
    <script>
        /*function changeFrameHeight(){
            var ifm= document.getElementById("iframepage");
            ifm.height=document.documentElement.clientHeight;
            console.log('testifm:', ifm.height);
        }

        window.onresize=function(){
            changeFrameHeight();
        }*/

    </script>
@endsection
