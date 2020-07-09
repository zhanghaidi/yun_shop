@extends('layouts.base')
@section('title', '小程序导航')
@section('content')
    <script type="text/javascript">
        inits = "<?php if (config('app.framework') == 'platform') echo '/'; else echo '/addons/yun_shop/';?>";
        u_url = 'plugins/designer/assets/js/';
        require.config({
            paths:{
                @if(config('app.framework') == 'platform')
                utils:inits+u_url+'utils',
                @else
                util:inits+u_url+'util',
                @endif
            }
        });
    </script>
    <div class="w1200 m0a">
        <!-- 导入CSS样式 -->
        <link href="{{ plugin_assets('designer', 'assets/css/designer.css') }}" rel="stylesheet">
        <link href="{{ plugin_assets('designer', 'assets/css/menu.css') }}" rel="stylesheet">
        <!-- <link rel="stylesheet" href="//at.alicdn.com/t/font_1041083_4h8bbucdcud.css"> -->
        <link rel="stylesheet" href="//at.alicdn.com/t/font_432132_j235h8fr7f.css">

        <div class="rightlist">
            <!-- 编辑页面 -->
            <div class='panel panel-default' ng-app="YunShop" style="">
                <div class='panel-body' ng-controller="YunShopController" style="padding:8px">
                    <div class="fe" style="margin-left : 30px">

                        <div class="fe-phone">
                            <div class="fe-phone-left"></div>
                            <div class="fe-phone-center">
                                <div class="fe-phone-top"></div>
                                <div class="fe-phone-main fe-phone-main-menu">
                                    <div id="editor" ng-style="{background: params.previewbg}" on-finish-render-filters>
                                        @include('Yunshop\Designer::admin.temp.show-diymenu')
                                    </div>
                                </div>
                                <div class="fe-phone-bottom"></div>
                            </div>
                            <div class="fe-phone-right"></div>
                        </div>
                        <div class="fe-panel" style="width:600px !important">

                            <!-- editor start -->
                            <div class="fe-panel-editor" ng-show="focus">
                                @if($ingress == 'weChatApplet')
                                    @include('Yunshop\Designer::admin.temp.edit-diyminimenu')
                                @else
                                    @include('Yunshop\Designer::admin.temp.edit-diymenu')
                                @endif
                            </div>
                            <!-- editor end -->
                        </div>
                    </div>
                    <!-- 页面底部保存栏 -->
                    <div class="fe-save">
                        <div class="fe-save-main">
                            <div class="fe-save-submit3 fr">返回列表</div>
                            <div class="fe-save-submit fr" ng-click="save()" style="margin-left:160px">保存菜单</div>
                        </div>
                    </div>
                    @if($ingress == 'weChatApplet')
                        @include('Yunshop\Designer::public.weChatAppletLink')
                    @else
                        @include('public.admin.link')
                    @endif
                </div>
            </div>



            <script type="text/javascript" src="{{ plugin_assets('designer', 'assets/js/angular.min.js') }}"></script>
            <script type="text/javascript" src="{{ plugin_assets('designer', 'assets/js/angular-ueditor.js') }}"></script>
            <script type="text/javascript" src="{{ plugin_assets('designer', 'assets/js/hhSwipe.js') }}"></script>

            <script type="text/javascript">
                var menuId = "{!! $menuId !!}";
                var menuStore = "{!! $storeUrl !!}";
                var menuIndex = "{!! $jumpUrl !!}";
                var jsonMenus = {!! $menuInfo !!};
                var jsonParams = {!! $menuParams !!};
                var myLinkGoods = "{!! yzWebUrl('goods.goods.getMyLinkGoods') !!}";
                var util_name = "<?php if (config('app.framework') == 'platform') echo 'utils'; else echo 'util';?>";
            </script>
            <script type="text/javascript" src="{{ plugin_assets('designer', 'assets/js/menu.js?525') }}"></script>

        </div>
    </div>

@endsection
