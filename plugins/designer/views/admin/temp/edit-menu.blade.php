<div class="fe-panel-editor-title">按钮组设置<span class="tips">Tips:建议尺寸：64px*64px，图片必须是正方形或者正圆形</span></div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">图标样式</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="0" ng-model="Edit.params.style"> 正方形</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="100%" ng-model="Edit.params.style"> 圆形</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">按钮数量</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_num" value="25%" ng-model="Edit.params.num" ng-change="setimg(Edit.id, Edit.params.num, Edit)"> 四个按钮</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_num" value="20%" ng-model="Edit.params.num" ng-change="setimg(Edit.id, Edit.params.num, Edit)"> 五个按钮</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">文字大小</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_fontsize1" value="12px" ng-model="Edit.params.fontsize" /> 12像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_fontsize1" value="14px" ng-model="Edit.params.fontsize" /> 14像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_fontsize1" value="16px" ng-model="Edit.params.fontsize" /> 16像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_fontsize1" value="18px" ng-model="Edit.params.fontsize" /> 18像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_fontsize1" value="20px" ng-model="Edit.params.fontsize" /> 20像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" value="12px" ng-model="Edit.params.fontsize" /></label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">文字粗细</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_align" value="normal" ng-model="Edit.params.fontweight" /> 正常</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_align" value="bold" ng-model="Edit.params.fontweight" /> 加粗</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_align" value="lighter" ng-model="Edit.params.fontweight" /> 偏细</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.bgcolor" />
    </div>
</div>
<div id="board" ng-controller="MainCtrl" data-ng-model="Edit.data" as-sortable="dragControlListeners">
    <div ng-repeat="menu in Edit.data"  as-sortable-item >
        <div class="fe-panel-editor-line2">
            <div class="fe-panel-editor-goodimg" ng-click="uploadImgChild(Edit.id, menu.id)" style="height:120px; width:120px;">
                <img ng-src="@{{menu.imgurl}}" width="100%" height="100%" ng-show="menu.imgurl" />
                <div class="fe-panel-editor-goodimg-t1" ng-show="!menu.imgurl" style="line-height:120px;"><i class="fa fa-plus-circle"></i> 选择图片</div>
                <div class="fe-panel-editor-goodimg-t2" ng-show="menu.imgurl" style="width:118px;">重新选择图片</div>
            </div>
            <div class="fe-panel-editor-line2-right"  as-sortable-item-handle>
                <div class="fe-panel-editor-line">
                    <div class="fe-panel-editor-name2">按钮文字</div>
                    <div class="fe-panel-editor-con">
                        <input class="fe-panel-editor-input1" style="width:380px;" value="" ng-model="menu.text" placeholder="请填写按钮文字" />
                    </div>
                </div>
                <div class="fe-panel-editor-line" ng-show="{{$type == 9}}">
                    <div class="fe-panel-editor-name">跳转</div>
                    <div class="fe-panel-editor-con">
                        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{menu.id}}_hrefChoice" value="1" ng-model="menu.hrefChoice" /> 跳转链接</label>
                        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{menu.id}}_hrefChoice" value="2" ng-model="menu.hrefChoice" /> 跳转小程序</label>
                    </div>
                </div>
                <div class="fe-panel-editor-line" ng-show="!menu.hrefChoice || menu.hrefChoice == 1 || hrefType != 9">
                    <div class="fe-panel-editor-name2">链接地址</div>
                    <div class="fe-panel-editor-con">
                        <input class="fe-panel-editor-input3" data-id="menu-@{{menu.id}}"  value="" ng-model="menu.hrefurl" placeholder="{{ $type == 9 ? '请输入小程序路径或选择链接' : '请输入https://开头链接或选择系统链接'}}" />
                        <div class="fe-panel-editor-input4 nav-link" ng-click="chooseUrl(Edit.id, menu.id)" data-id="menu-@{{menu.id}}">系统连接</div>
                    </div>
                </div>
                <div class="fe-panel-editor-line" ng-show="menu.hrefChoice == 2 && hrefType == 9">
                    <div class="fe-panel-editor-name2">APPID</div>
                    <div class="fe-panel-editor-con">
                        <input class="fe-panel-editor-input1" style="width:380px;" value="" ng-model="menu.appID" placeholder="请填写小程序的APPID" />
                    </div>
                </div>
                <div class="fe-panel-editor-line" ng-show="menu.hrefChoice == 2 && hrefType == 9">
                    <div class="fe-panel-editor-name2">&nbsp;&nbsp;&nbsp;页面</div>
                    <div class="fe-panel-editor-con">
                        <input class="fe-panel-editor-input1" style="width:380px;" value="" ng-model="menu.miniUrl" placeholder="请填写跳转页面的小程序访问路径" />
                    </div>
                </div>
                <div class="fe-panel-editor-line">
                    <div class="fe-panel-editor-name2">文字颜色</div>
                    <div class="fe-panel-editor-con">
                        <input class="fe-panel-editor-input2" type="color" ng-model="menu.color" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.as-sortable-drag {
    left: none !important;
}
</style>