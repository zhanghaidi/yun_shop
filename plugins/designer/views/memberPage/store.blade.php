@extends('layouts.base')
@section('title', '会员中心装修')
@section('utilJs')
    <script type="text/javascript">
        inits = "<?php if (config('app.framework') == 'platform') echo '/'; else echo '/addons/yun_shop/';?>";
        u_url = 'plugins/designer/assets/js/';
        require.config({
            paths: {
                @if(config('app.framework') == 'platform')
                utils: inits + u_url + 'utils',
                @else
                util: inits + u_url + 'util',
                @endif
            }
        });
    </script>
@endsection
@section('content')

    <script language="javascript">require(['underscore']);</script>
    @php $url = uploadUrl() @endphp
    <!-- 导入CSS样式 -->
    <link href="{{ plugin_assets('designer', 'assets/css/designer.css') }}" rel="stylesheet">
    <link href="{{ plugin_assets('designer', 'assets/css/ng-sortable.min.css') }}" rel="stylesheet">
    <!-- 导入CSS样式 -->

    <link href="{{ resource_get("static/resource/components/webuploader/webuploader.css") }}" rel="stylesheet">
    <link href="{{ resource_get("static/resource/components/webuploader/style.css") }}" rel="stylesheet">
    <style>

    </style>

    <div class="rightlist">

        <!-- 编辑页面 -->
        <div class='panel panel-default' ng-app="SZ_YIEditor">
            <div class='panel-body' ng-controller="SZ_YIController" style="padding:8px; padding-bottom:300px;">
                <div class="fe-panel-menu nui-scroll">
                    <div ng-repeat="nav in navs">
                        <nav ng-if="nav.enable == 1" ng-click="addItem(nav.id)">
                            <div class="comp-icon"><i class="fa @{{nav.icon}}"></i></div>
                            <span ng-bind="nav.name"></span></nav>
                    </div>
                </div>
                <div class="fe">
                    <div class="fe-phone">
                        <div class="fe-phone-left"></div>
                        <div class="fe-phone-center">
                            <div class="fe-phone-top"></div>
                            <div class="fe-phone-main">
                                <div id="editor">
                                   {{-- <div ng-repeat="page in pages">
                                        <div ng-include="inits+'plugins/designer/views/admin/temp/show-'+page.temp+'.blade.php'"
                                             id="@{{page.id}}" mid="@{{page.id}}" ng-click="setfocus(page.id,$event)"
                                             style="display: block;"></div>
                                    </div>--}}
                                    <div style="height: 50px;" ng-show="pages[0].params.guide==1"></div>
                                    <div ng-repeat="Item in Items" class="fe-mod-repeat" ng-mouseover="over(Item.id)"
                                         ng-mouseleave="out(Item.id)">
                                        <div class="fe-mod-move" ng-mouseover="drag(Item.id)"
                                             ng-click="setfocus(Item.id,$event)"></div>
                                        <div ng-include="inits+'plugins/designer/views/admin/temp/show-'+Item.temp+'.blade.php'"
                                             class="fe-mod-parent" id="@{{Item.id}}" ng-show="Item" mid="@{{Item.id}}"
                                             on-finish-render-filters></div>
                                        <div class="fe-mod-del" ng-click="delItem(Item.id)">移除</div>
                                    </div>
                                    <!-- 浮动按钮 -->
                                    <div class="fe-floatico" ng-show="pages[0].params.floatico==1"
                                         ng-style="{'width':pages[0].params.floatwidth,'top':pages[0].params.floattop}"
                                         ng-class="{'fe-floatico-right':pages[0].params.floatstyle=='right'}">
                                        <img ng-src="@{{pages[0].params.floatimg || inits+'plugins/designer/assets/images/init-data/init-image-7.png'}}"
                                             style="height:100%; width: 100%;" ng-click="setfocus('M0000000000000')"/>
                                    </div>

                                    <!-- 客服按钮 -->
                                    <div class="fe-kefu" ng-show="pages[0].params.kefu==1"
                                         ng-style="{'width':pages[0].params.kefuwidth,'top':pages[0].params.kefubottom, 'height': pages[0].params.kefuheight ? pages[0].params.kefuheight : '100px'}"
                                         ng-class="{'fe-floatico-right':pages[0].params.kefustyle=='right'}">
                                        <img ng-src="@{{pages[0].params.kefuimg || inits+'plugins/designer/assets/images/init-data/init-image-7.png'}}"
                                             style="height:100%; width: 100%;" ng-click="setfocus('M0000000000000')"/>
                                    </div>

                                    <!-- 电话按钮 -->
                                    <div class="fe-kefu" ng-show="pages[0].params.tel==1"
                                         ng-style="{'width':pages[0].params.telwidth,'bottom':pages[0].params.telbottom, 'height': pages[0].params.telheight ? pages[0].params.telheight : '100px'}"
                                         ng-class="{'fe-floatico-right':pages[0].params.telstyle=='right'}">
                                        <img ng-src="@{{pages[0].params.telimg || inits+'plugins/designer/assets/images/init-data/init-image-7.png'}}"
                                             style="height:100%; width: 100%;" ng-click="setfocus('M0000000000000')"/>
                                    </div>

                                </div>
                            </div>
                            <div class="fe-phone-bottom"></div>
                        </div>
                        <div class="fe-phone-right"></div>
                    </div>


                    <div class="fe-panel">

                        <!-- editor start -->
                        <div class="fe-panel-editor" ng-show="focus">
                            <div class="fe-panel-editor-ico"></div>
                           {{-- <div ng-repeat="Edit in pages">
                                <div ng-include="'edit-'+Edit.temp+'.html'" ng-show="focus==Edit.id"
                                     Editid="@{{Edit.id}}" class="pedit"></div>
                            </div>--}}
                            <div ng-repeat="Edit in Items">
                                <div class="arrow"></div>
                                <div ng-include="'edit-'+Edit.temp+'.html'" ng-show="focus==Edit.id"
                                     Editid="@{{Edit.id}}" tab-index="-1" class="pedit"></div>
                            </div>
                        </div>
                        <!-- editor end -->
                    </div>
                </div>

                <!-- 页面底部保存栏 -->
                <div class="fe-save">
                    <div class="fe-save-main">
                        <div class="fe-save-info">
                            @if($type == 9)
                                <input name="pagetype" id="pagetype" type="hidden" value="9"/>
                            @else
                            <div class="fe-save-info-type fe-save-info-type-ok">
                                @if(in_array(1, $designerModel->page_type_cast))
                                    <div class="fe-save-main-radio office_page fe-save-main-radio2">√</div>
                                @else
                                    <div class="fe-save-main-radio office_page"></div>
                                @endif
                                <div class="fe-save-main-text">公众号</div>
                            </div>
                            <div class="fe-save-info-type fe-save-info-type-ok">
                                @if(in_array(2, $designerModel->page_type_cast))
                                    <div class="fe-save-main-radio min_page fe-save-main-radio2">√</div>
                                @else
                                    <div class="fe-save-main-radio min_page"></div>
                                @endif
                                <div class="fe-save-main-text">小程序</div>
                            </div>
                            <div class="fe-save-info-type fe-save-info-type-ok">
                                @if(in_array(7, $designerModel->page_type_cast))
                                    <div class="fe-save-main-radio app_page fe-save-main-radio2">√</div>
                                @else
                                    <div class="fe-save-main-radio app_page"></div>
                                @endif
                                <div class="fe-save-main-text">APP</div>
                            </div>
                            <div class="fe-save-info-type fe-save-info-type-ok">
                                @if(in_array(8, $designerModel->page_type_cast))
                                    <div class="fe-save-main-radio alipay_page fe-save-main-radio2">√</div>
                                @else
                                    <div class="fe-save-main-radio alipay_page"></div>
                                @endif
                                <div class="fe-save-main-text">支付宝</div>
                            </div>
                            <div class="fe-save-info-type fe-save-info-type-ok">
                                @if(in_array(5, $designerModel->page_type_cast))
                                    <div class="fe-save-main-radio wap_page fe-save-main-radio2">√</div>
                                @else
                                    <div class="fe-save-main-radio wap_page"></div>
                                @endif
                                <div class="fe-save-main-text">WAP</div>
                            </div>


                            <input name="pagetype" id="pagetype" type="hidden"
                                   value="{{ $designerModel->page_type  or 0}}"/>

                            @endif
                            <input name="pagename" type="text"
                                   style="height: 30px; width: 175px; border: 1px solid #bbb; border-radius: 3px; margin: 4px 10px; outline: none; padding-left: 10px;"
                                   placeholder="页面名称：输入页面名称" value="{{ $designerModel->page_name or '' }}"/>


                                <label style="color: #fff;margin-right: 10px;font-size: 20px;margin-left: 10px">是否默认启用</label>
                                <label><input type="radio" name="is_default" value="1" @if($designerModel->is_default == 1) checked @endif><span style="color: #fff;margin-right: 10px;">是</span></label>
                                <label><input type="radio" name="is_default" value="0" @if($designerModel->is_default == 0) checked @endif><span style="color: #fff;margin-right: 10px;">否</span></label>



                        </div>
                        {{--<div class="fe-save-submit2" ng-click="save(2)">保存并预览</div>--}}
                        <div class="fe-save-submit save-submit" ng-click="save(3)">保存</div>
                        <div class="fe-save-submit" onclick="history.back()">返回列表</div>
                    </div>
                    <div class="fe-save-fold" onclick="fold()"></div>
                    <div class="fe-save-gotop" onclick="$(document.body).animate({scrollTop:0},500)"><i
                                class="fa fa-angle-up"></i><br>返回顶部
                    </div>

                    <?php if ($type != '9') { ?>
                    @include('public.admin.link')
                    <?php } else {?>
                    @include('Yunshop\Designer::public.weChatAppletLink')
                    <?php }?>
                    @include('Yunshop\Designer::admin.temp.choose-goods')
                    @include('Yunshop\Designer::admin.temp.choose-article')
                    @include('Yunshop\Designer::admin.temp.choose-coupon')
                    @include('Yunshop\Designer::admin.temp.choose-flashsale')
                    @include('Yunshop\Designer::admin.temp.choose-assemble')
                    @include('Yunshop\Designer::admin.temp.choose-form')

                </div>
                <script type="text/ng-template"
                        id="edit-tbk.html">@include('Yunshop\Designer::admin.temp.edit-tbk')</script>

               {{-- <script type="text/ng-template"
                        id="edit-topbar.html">@include('Yunshop\Designer::admin.temp.edit-topbar')</script>--}}
                <script type="text/ng-template"
                        id="edit-membercenter.html">@include('Yunshop\Designer::admin.temp.edit-membercenter')</script>
                <script type="text/ng-template"
                        id="edit-membertool.html">@include('Yunshop\Designer::admin.temp.edit-membertool')</script>
                <script type="text/ng-template"
                        id="edit-membermerchant.html">@include('Yunshop\Designer::admin.temp.edit-membermerchant')</script>
                <script type="text/ng-template"
                        id="edit-membermarket.html">@include('Yunshop\Designer::admin.temp.edit-membermarket')</script>
                <script type="text/ng-template"
                        id="edit-memberasset.html">@include('Yunshop\Designer::admin.temp.edit-memberasset')</script>
                <script type="text/ng-template"
                        id="edit-membercarorder.html">@include('Yunshop\Designer::admin.temp.edit-membercarorder')</script>
                <script type="text/ng-template"
                        id="edit-memberhotelorder.html">@include('Yunshop\Designer::admin.temp.edit-memberhotelorder')</script>
                <script type="text/ng-template"
                        id="edit-memberleaseorder.html">@include('Yunshop\Designer::admin.temp.edit-memberleaseorder')</script>
                <script type="text/ng-template"
                        id="edit-membergrouporder.html">@include('Yunshop\Designer::admin.temp.edit-membergrouporder')</script>
                <script type="text/ng-template"
                        id="edit-shop.html">@include('Yunshop\Designer::admin.temp.edit-shop')</script>
                <script type="text/ng-template"
                        id="edit-notice.html">@include('Yunshop\Designer::admin.temp.edit-notice')</script>
                <script type="text/ng-template"
                        id="edit-menu.html">@include('Yunshop\Designer::admin.temp.edit-menu')</script>
                <script type="text/ng-template"
                        id="edit-banner.html">@include('Yunshop\Designer::admin.temp.edit-banner')</script>
                <script type="text/ng-template"
                        id="edit-picture.html">@include('Yunshop\Designer::admin.temp.edit-picture')</script>
                <script type="text/ng-template"
                        id="edit-title.html">@include('Yunshop\Designer::admin.temp.edit-title')</script>
                <script type="text/ng-template"
                        id="edit-search.html">@include('Yunshop\Designer::admin.temp.edit-search')</script>
                <script type="text/ng-template"
                        id="edit-line.html">@include('Yunshop\Designer::admin.temp.edit-line')</script>
                <script type="text/ng-template"
                        id="edit-blank.html">@include('Yunshop\Designer::admin.temp.edit-blank')</script>
                <script type="text/ng-template"
                        id="edit-goods.html">@include('Yunshop\Designer::admin.temp.edit-goods')</script>
                <script type="text/ng-template" id="edit-nearbygoods.html">@include('Yunshop\Designer::admin.temp.edit-nearbygoods')</script>
                <script type="text/ng-template"
                        id="edit-richtext.html">@include('Yunshop\Designer::admin.temp.edit-richtext')</script>
                <script type="text/ng-template"
                        id="edit-cube.html">@include('Yunshop\Designer::admin.temp.edit-cube')</script>
                <script type="text/ng-template"
                        id="edit-area.html">@include('Yunshop\Designer::admin.temp.edit-area')</script>
                <script type="text/ng-template"
                        id="edit-location.html">@include('Yunshop\Designer::admin.temp.edit-location')</script>
                <script type="text/ng-template"
                        id="edit-store.html">@include('Yunshop\Designer::admin.temp.edit-store')</script>
                <script type="text/ng-template"
                        id="edit-sign.html">@include('Yunshop\Designer::admin.temp.edit-sign')</script>
                <script type="text/ng-template"
                        id="edit-headline.html">@include('Yunshop\Designer::admin.temp.edit-headline')</script>
                <script type="text/ng-template"
                        id="edit-article.html">@include('Yunshop\Designer::admin.temp.edit-article')</script>
                <script type="text/ng-template"
                        id="edit-coupon.html">@include('Yunshop\Designer::admin.temp.edit-coupon')</script>
                <script type="text/ng-template"
                        id="edit-flashsale.html">@include('Yunshop\Designer::admin.temp.edit-flashsale')</script>
                <script type="text/ng-template"
                        id="edit-business.html">@include('Yunshop\Designer::admin.temp.edit-business')</script>
                <script type="text/ng-template" id="edit-assemble.html">@include('Yunshop\Designer::admin.temp.edit-assemble')</script>
                <script type="text/ng-template"
                        id="edit-video.html">@include('Yunshop\Designer::admin.temp.edit-video')</script>
                <script type="text/ng-template" id="edit-diyform.html">@include('Yunshop\Designer::admin.temp.edit-diyform')</script>
                <script type="text/ng-template" id="edit-livestreaming.html">@include('Yunshop\Designer::admin.temp.edit-livestreaming')</script>
            </div>
        </div>
        <script type="text/javascript"
                src="{{ plugin_assets('designer', 'assets/js/angular.min.js?v=' . time()) }}"></script>
        <script type="text/javascript"
                src="{{ plugin_assets('designer', 'assets/js/angular-ueditor.js?v=' . time() ) }}"></script>
        <script type="text/javascript"
                src="{{ plugin_assets('designer', 'assets/js/ng-sortable.js?v=' . time() ) }}"></script>
        <script type="text/javascript" src="{{ plugin_assets('designer', 'assets/js/hhSwipe.js') }}"></script>
        <script type="text/javascript"
                src="{{ resource_get("static/resource/components/ueditor/ueditor.config.js") }}"></script>
        <script type="text/javascript"
                src="{{ resource_get("static/resource/components/ueditor/ueditor.all.min.js") }}"></script>
        <script type="text/javascript"
                src="{{ resource_get("static/resource/components/ueditor/ueditor.parse.min.js") }}"></script>
        <script type="text/javascript"
                src="{{ resource_get("static/resource/components/ueditor/ueditor.parse.min.js") }}"></script>
        <script type="text/javascript">
            var imgUrl3 = '{!! request()->getSchemeAndHttpHost().plugin_assets('designer', 'assets/images/init-data/init-image-3.jpg') !!}';
            var imgUrl2 = '{!! plugin_assets('designer', 'assets/images/init-data/init-image-2.jpg') !!}';
            var imgUrl6 = '{!! plugin_assets('designer', 'assets/images/init-data/init-image-6.jpg') !!}';
            var imgUrl4 = '{!! plugin_assets('designer', 'assets/images/init-data/init-image-4.jpg') !!}';
            var imgUrTWOl4 = '{!! request()->getSchemeAndHttpHost().plugin_assets('designer', 'assets/images/init-data/init-image-4.jpg') !!}';

            var imgUrl1 = '{!! plugin_assets('designer','assets/images/init-data/init-image-1.jpg') !!}';
            var imgUrlIcon = '{!! plugin_assets('designer','assets/images/init-data/init-icon.png') !!}';

            var searchGoodsUrl = '{!! yzWebUrl('plugin.designer.admin.member-list.searchGoods') !!}';
            var yz_uniacid = '{{Yunshop::app()->uniacid}}';
            var goodsUrl = '{!! yzAppUrl('goods') !!}';
            var system = "{{$system or ''}}";
            var navEnable = "<?php if ($type == '9') echo '0'; else echo '1';?>";
            var nearEnable = "<?php if (app('plugins')->isEnabled('nearby-store-goods')) echo '1'; else echo '0';?>";
            var weChatAppletNavEnable = "<?php if ($type == '9') echo '1'; else echo '0';?>";
            var morePicNavEnable = 0;
            var locateEnable = 0;
            var navTbk = "<?php if (!$isEnabledTbk) echo '0'; else echo '1';?>";
            var navSignName = "<?php if ($signName) echo $signName; else echo "签到";?>"
            var pageinfo = {!! $pageinfo or '' !!};
            var yzItems = [{!! $data or '' !!}];
            var yzMenuList = {!! json_encode($menuList) !!};
            var yzTopMenuList = {!! json_encode($topMenuList) !!};
            var keyWordUrl = "{!! yzWebUrl('plugin.designer.admin.member-list.retrievalKeyword') !!}";
            var designerId = "{{ $designerModel->id  or '' }}";
            var selectGoodsUrl = "{!! yzWebUrl('plugin.designer.admin.member-list.searchGoods') !!}";
            var selectArticleUrl = "{!! yzWebUrl('plugin.designer.Backend.Modules.MemberPage.Controllers.search-article.index') !!}";
            var selectCouponUrl = "{!! yzWebUrl('plugin.designer.admin.member-list.searchCoupon') !!}";
            var selectFormUrl = "{!! yzWebUrl('plugin.designer.admin.list.searchForm') !!}";
            var selectFlashsaleUrl = "{!! yzWebUrl('plugin.designer.admin.member-list.searchFlashsale') !!}";
            var myLinkGoodsUrl = "{!! yzWebUrl('goods.goods.getMyLinkGoods') !!}";
            var storeUrl = "{!! $designerModel->id ? yzWebUrl('plugin.designer.admin.member-list.update') : yzWebUrl('plugin.designer.admin.member-list.store') !!}";
            var designerIndex = "{!! yzWebUrl('plugin.designer.Backend.Modules.MemberPage.Controllers.records') !!}";
            var selectCategoryGoods = "{!! yzWebUrl('plugin.designer.admin.member-list.selectCategoryGoods') !!}";
            var selectSearchGoods = "{!! yzWebUrl('plugin.designer.admin.member-list.selectSearchGoods') !!}";
            var hrefType = "{{$type}}";
            var util_name = "<?php if (config('app.framework') == 'platform') echo 'utils'; else echo 'util';?>";
            var url = {!! json_encode($url) !!};
        </script>

        <script type="text/javascript" src="{{ plugin_assets('designer', 'assets/js/designer.min.js?522') }}"></script>


    </div>

@endsection
