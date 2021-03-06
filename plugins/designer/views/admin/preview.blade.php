<!doctype html>
<html ng-app="myApp">
<head>
    <meta charset="utf-8">
    <title>{$share['title']}</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" id="viewport"
          name="viewport">
    <meta name="format-detection" content="telephone=no"/>
    <script> var require = {urlArgs: 'v=20170420111931'};;</script>
    <script language="javascript" src="{{ resource_get("static/js/require.js") }}"></script>
    <script language="javascript" src="{{ resource_get("static/js/app/config.js") }}"></script>
    <script language="javascript" src="{{ resource_get("static/js/dist/jquery-1.11.1.min.js") }}"></script>
    <script language="javascript" src="{{ resource_get("static/js/dist/jquery.gcjs.js") }}"></script>


    <link href="{{ resource_get("static/css/font-awesome.min.css") }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ resource_get("static/css/style.css") }}">
    <link rel="stylesheet" type="text/css" href="{{ resource_get("static/css/bootstrap.min.css") }}">

    <link href="{{ plugin_assets('designer', 'assets/css/designer.css') }}" rel="stylesheet">


    <style>
        body {
            margin: 0px;
            background: #f9f9f9;
        }

        .fe-mod:hover {
            border: 2px dashed rgba(0, 0, 0, 0);
            cursor: default;
        }

        .fe-mod, .fe-mod:hover {
            border: 0px;
        }

        .fe-mod-cube td {
            height: auto;
        }
    </style>
</head>
<body>
<div ng-controller="MainCtrl">
    <!-- 浮动按钮 -->
    <div class="fe-floatico" style="position: fixed;"
         ng-style="{'width':pages[0].params.floatwidth,'top':pages[0].params.floattop}"
         ng-class="{'fe-floatico-right':pages[0].params.floatstyle=='right'}" ng-show="pages[0].params.floatico==1">
        <a href="@{{pages[0].params.floathref || 'javascript:;'}}">
            <img ng-src="@{{pages[0].params.floatimg || inits+'plugins/designer/assets/images/init-data/init-image-7.png'}}"
                 style="width:100%;"/>
        </a>
    </div>
    <!-- 关注按钮 -->
    <div style="height: 50px;" ng-show="pages[0].params.guide==1"></div>
    <a href="{$guide['followurl']}">
        <div class="fe-guide" style="position: fixed;"
             ng-style="{'display':'block','background-color':pages[0].params.guidebgcolor,'opacity':pages[0].params.guideopacity}"
             ng-show="pages[0].params.guide==1">
            <div class="fe-guide-faceimg" ng-style="{'border-radius':pages[0].params.guidefacestyle}">
                <img src="{{$guide['logo']}}" ng-style="{'border-radius':pages[0].params.guidefacestyle}"/>
            </div>
            <div class="fe-guide-sub"
                 ng-style="{'color':pages[0].params.guidenavcolor,'background-color':pages[0].params.guidenavbgcolor}">@{{pages[0].params.guidesub ||'立即关注'}}</div>
            <div class="fe-guide-text"
                 ng-style="{'font-size':pages[0].params.guidesize,'color':pages[0].params.guidecolor}">
                <p {if empty($guide['title2'])} style="line-height:40px;"{/if}>{{$guide['title1']}}</p>
                <p {if empty($guide['title1'])} style="line-height:40px;"{/if}>{{$guide['title2']}}</p>
            </div>
        </div>
    </a>
    <div ng-repeat="Item in Items" class="fe-mod-repeat">
        <div ng-include="inits+'plugins/designer/views/admin/temp/show-'+Item.temp+'.blade.php'" class="fe-mod-parent" id="@{{Item.id}}" mid="@{{Item.id}}" on-finish-render-filters></div>
    </div>
    <div ng-show="Items==''" style="line-height: 300px; text-align: center; font-size: 14px; color: #999;">
        <div id="core_loading"
             style="top:50%;left:50%;margin-left:-35px;margin-top:-50%;position:absolute;width:80px;height:60px;"><img src="{{ resource_get("static/images/loading.svg") }}" width="80"/>
        </div>
    </div>
    <div style="height: 50px;" ng-show="pages[0].params.footer==2"></div>
</div>


<script type="text/javascript" src="{{ plugin_assets('designer', 'assets/js/angular.min.js') }}"></script>
<script type="text/javascript" src="{{ plugin_assets('designer', 'assets/js/hhSwipe.js') }}"></script>



<script type="text/javascript">
    function initswipe(jobj) {
        var bullets = jobj.next().get(0).getElementsByTagName('a');
        var banner = Swipe(jobj.get(0), {
            auto: 4000,
            continuous: true,
            disableScroll: false,
            callback: function (pos) {
                var i = bullets.length;
                while (i--) {
                    $(bullets[i]).css("opacity", 0.4);
                }
                $(bullets[pos]).css("opacity", 0.6);
            }
        })
    }
    var app = angular.module('myApp', []);
    app.controller('MainCtrl', ['$scope', function ($scope) {
        $scope.shop = {
            uniacid: {!! \YunShop::app()->uniacid !!}
        };
        $scope.cols = [0, 1, 2, 3];
        $scope.size = $(document.body).width() / 4;
        $scope.pages = {!! json_encode($pageinfo) !!};
        $scope.system = {!! $system !!};
        $scope.Items = {!! json_encode($data) !!};
        $scope.show = '1';
        $scope.inits = "<?php if (config('app.framework') == 'platform') echo "/"; else echo "../addons/yun_shop/";?>";

        $scope.hasCube = function (Item) {

            var has = false;
            var row = 0, col = 0;
            for (var i = row; i < 4; i++) {
                for (var j = col; j < 4; j++) {
                    if (Item.params.layout[i][j] && !Item.params.layout[i][j].isempty) {
                        has = true;
                        break;
                    }
                }
            }
            return has;


        }
        require(['tpl', 'core'], function (tpl, core) {


            $(document).on('click', '.select_place_bg', function () {
                $('.select_place_bg').hide();
                $('.index_tabBox').hide();
            });
            $scope.areaClick = function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{php echo $this->createPluginMobileUrl('area/area_list',array('op'=>'getcategory'))}",
                    success: function (data) {
                        console.log(data);
                        if (data.status == 1) {

                            $('#container').html(tpl('tpl_log', data));
                        } else {
                            alert(data.result);
                        }

                    }, error: function () {
                        alert('未检测到相关数据！');
                    }
                });
            }
        });
        $scope.$on('ngRepeatFinished', function () {
            $('.fe-mod-2 .swipe').each(function () {
                initswipe($(this));
            });
            $('.fe-mod-8-main-img img').each(function () {
                $(this).height($(this).width());
            });
            $('.fe-mod-12 img').each(function () {
                $(this).height($(this).width());
            });
            $('.fe-mod-cube table  tr').each(function () {
                if ($(this).children().length <= 0) {
                    $(this).html('<td></td>');
                }
            });
        });


    }]);

    app.directive('stringHtml', function () {
        return function (scope, el, attr) {
            if (attr.stringHtml) {
                scope.$watch(attr.stringHtml, function (html) {
                    el.html(html || '');
                });
            }
        };
    });
    app.directive("onFinishRenderFilters", function ($timeout) {
        return {
            restrict: 'A',
            link: function (scope, element, attr) {
                if (scope.$last === true) {
                    $timeout(function () {
                        scope.$emit('ngRepeatFinished');
                    });
                }
            }
        };
    });
</script>



