<div class="fe-panel-editor-title">轮播设置<span class="tips">Tips:建议尺寸：375px*233px，轮播图片的大小必须一样哦~</span></div>

<div id="board" ng-controller="MainCtrl" data-ng-model="Edit.data" as-sortable="dragControlListeners">
    <div ng-repeat="banner in Edit.data" class="fe-panel-editor-relative" as-sortable-item>
        <div class="fe-panel-editor-del" title="移除" ng-click="delItemChild(Edit.id, banner.id)">×</div>
        <div class="fe-panel-editor-line2" as-sortable-item-handle>
            <div class="fe-panel-editor-goodimg" ng-click="uploadImgChild(Edit.id, banner.id)">
                <img ng-src="@{{banner.imgurl}}" width="100%" ng-show="banner.imgurl" />
                <div class="fe-panel-editor-goodimg-t1" ng-show="!banner.imgurl"><i class="fa fa-plus-circle"></i> 选择图片</div>
                <div class="fe-panel-editor-goodimg-t2" ng-show="banner.imgurl">重新选择图片</div>
            </div>
            <div class="fe-panel-editor-line2-right">
                <div class="fe-panel-editor-line" ng-show="{{$type == 9}}">
                    <div class="fe-panel-editor-name">跳转</div>
                    <div class="fe-panel-editor-con">
                        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{banner.id}}_hrefChoice" value="1" ng-model="banner.hrefChoice" /> 跳转链接</label>
                        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{banner.id}}_hrefChoice" value="2" ng-model="banner.hrefChoice" /> 跳转小程序</label>
                    </div>
                </div>
                <div class="fe-panel-editor-line"  ng-show="!banner.hrefChoice || banner.hrefChoice == 1 || hrefType != 9">
                    <div class="fe-panel-editor-name2">选择链接</div>
                    <div class="fe-panel-editor-con">
                        <input class="fe-panel-editor-input3" value="" ng-model="banner.hrefurl" placeholder="{{ $type == 9 ? '请输入小程序路径或选择链接' : '请输入https://开头链接或选择系统链接'}}" />
                        <div class="fe-panel-editor-input4" ng-click="chooseUrl(Edit.id, banner.id)">系统连接</div>
                    </div>
                </div>
                <div class="fe-panel-editor-line" ng-show="banner.hrefChoice == 2 && hrefType == 9">
                    <div class="fe-panel-editor-name2">APPID</div>
                    <div class="fe-panel-editor-con">
                        <input class="fe-panel-editor-input1" style="width:380px;" value="" ng-model="banner.appID" placeholder="请填写小程序的APPID" />
                    </div>
                </div>
                <div class="fe-panel-editor-line" ng-show="banner.hrefChoice == 2 && hrefType == 9">
                    <div class="fe-panel-editor-name2">&nbsp;&nbsp;&nbsp;页面</div>
                    <div class="fe-panel-editor-con">
                        <input class="fe-panel-editor-input1" style="width:380px;" value="" ng-model="banner.miniUrl" placeholder="请填写跳转页面的小程序访问路径" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fe-panel-editor-sub1" ng-click="addItemChild('banner', Edit.id)"><i class="fa fa-plus-circle"></i> 添加一个轮播</div>