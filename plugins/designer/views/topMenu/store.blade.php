@extends('layouts.base')
@section('title', '店铺装修')
@section('content')
<div class="w1200 m0a">
        <!-- 导入CSS样式 -->
        <link href="{{ plugin_assets('designer', 'assets/css/designer.css') }}" rel="stylesheet">
        <link href="{{ plugin_assets('designer', 'assets/css/menu.css') }}" rel="stylesheet">
        <div class="rightlist">
            <!-- 编辑页面 -->
            <div class='panel panel-default' ng-app="YunShop1" style="">
                <div class='panel-body' ng-controller="YunShopController1" style="padding:8px">
                    <div class="fe" style="margin-left:0">
                        <div class="fe-phone">
                            <div class="fe-phone-left"></div>
                            <div class="fe-phone-center">
                                <div class="fe-phone-top"></div>
                                <div class="fe-phone-main fe-phone-main-menu">
                                    <div id="editor" ng-style="{background: params.previewbg}" on-finish-render-filters>
                                        @include('Yunshop\Designer::admin.temp.show-diytopmenu')
                                    </div>
                                </div>
                                <div class="fe-phone-bottom"></div>
                            </div>
                            <div class="fe-phone-right"></div>
                        </div>
                        <div class="fe-panel" style="width:600px !important">

                            <!-- editor start -->
                            <div class="fe-panel-editor" ng-show="focus">
                                @include('Yunshop\Designer::admin.temp.edit-diytopmenu')
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
                        @include('public.admin.link')
                    </div>
                </div>
            </div>
            <script type="text/javascript" src="{{ plugin_assets('designer', 'assets/js/angular.min.js') }}"></script>
            <script type="text/javascript" src="{{ plugin_assets('designer', 'assets/js/angular-ueditor.js') }}"></script>
            <script type="text/javascript" src="{{ plugin_assets('designer', 'assets/js/hhSwipe.js') }}"></script>
            <script type="text/javascript">
                var topMenuId = "{!! $menuModel->id !!}";
                var jsonMenus = {!! $menus !!};
                var jsonParams = {!! $params !!};
                var myLinkGoods = "{!! yzWebUrl('goods.goods.getMyLinkGoods') !!}";
                var storeUrl = "{!! yzWebUrl('plugin.designer.Backend.Modules.TopMenu.Controllers.store.index') !!}";
                var topMenuIndex = "{!! yzWebUrl('plugin.designer.Backend.Modules.TopMenu.Controllers.records.index') !!}";
                var inits = "<?php if (config('app.framework') == 'platform') echo '/'; else echo '../addons/yun_shop/';?>";
            </script>
            <script type="text/javascript" src="{{ plugin_assets('designer', 'assets/js/topmenu.js') }}"></script>
        </div>
    </div>
@endsection