<div class="fe-panel-editor-title">按钮组设置<span class="tips">Tips:图片必须是正方形或者正圆形</span></div>
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
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_num" value="25%" ng-model="Edit.params.num" ng-change="setimg(Edit.id, Edit.params.num)"> 四个按钮</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_num" value="20%" ng-model="Edit.params.num" ng-change="setimg(Edit.id, Edit.params.num)"> 五个按钮</label>
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
<div ng-repeat="menu in Edit.data" ng-hide="$index == 4 && Edit.params.num == '25%'">
    <div class="fe-panel-editor-line2">
        <div class="fe-panel-editor-goodimg" ng-click="uploadImgChild(Edit.id, menu.id)" style="height:120px; width:120px;">
            <img ng-src="@{{menu.imgurl}}" width="100%" height="100%" ng-show="menu.imgurl" />
            <div class="fe-panel-editor-goodimg-t1" ng-show="!menu.imgurl" style="line-height:120px;"><i class="fa fa-plus-circle"></i> 选择图片</div>
            <div class="fe-panel-editor-goodimg-t2" ng-show="menu.imgurl" style="width:118px;">重新选择图片</div>
        </div>
        <div class="fe-panel-editor-line2-right">
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2">按钮文字</div>
                <div class="fe-panel-editor-con">
                    <input class="fe-panel-editor-input1" style="width:380px;" value="" ng-model="menu.text" placeholder="请填写按钮文字" />
                </div>
            </div>
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2">链接地址</div>
                <div class="fe-panel-editor-con">
                    <input class="fe-panel-editor-input3" data-id="menu-@{{menu.id}}"  value="" ng-model="menu.hrefurl" placeholder="{{ $type == 9 ? '请输入小程序路径或选择链接' : '请输入https://开头链接或选择系统链接'}}" />
                    <div class="fe-panel-editor-input4 nav-link" ng-click="chooseUrl(Edit.id, menu.id)" data-id="menu-@{{menu.id}}">系统连接</div>
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