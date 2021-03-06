<div class="fe-panel-editor-title">区域设置</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">区域内容</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="区域内容，超出屏幕宽度将自动隐藏" ng-model="Edit.params.title1" />
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">内容大小</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_fontsize1" value="12px" ng-model="Edit.params.fontsize1" /> 12像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_fontsize1" value="14px" ng-model="Edit.params.fontsize1" /> 14像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_fontsize1" value="16px" ng-model="Edit.params.fontsize1" /> 16像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_fontsize1" value="18px" ng-model="Edit.params.fontsize1" /> 18像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_fontsize1" value="20px" ng-model="Edit.params.fontsize1" /> 20像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" value="" ng-model="Edit.params.fontsize1" /></label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">区域说明</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_showtitle2" value="1" ng-model="Edit.params.showtitle2" /> 显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_showtitle2" value="0" ng-model="Edit.params.showtitle2" /> 不显示</label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.showtitle2 == 1">
    <div class="fe-panel-editor-name">说明内容</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="区域说明，超出屏幕宽度将自动隐藏" ng-model="Edit.params.title2" />
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.showtitle2 == 1">
    <div class="fe-panel-editor-name">内容大小</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_fontsize2" value="12px" ng-model="Edit.params.fontsize2" /> 12像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_fontsize2" value="14px" ng-model="Edit.params.fontsize2" /> 14像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_fontsize2" value="16px" ng-model="Edit.params.fontsize2" /> 16像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_fontsize2" value="18px" ng-model="Edit.params.fontsize2" /> 18像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_fontsize2" value="20px" ng-model="Edit.params.fontsize2" /> 20像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" value="" ng-model="Edit.params.fontsize2" /></label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">对齐方向</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_align" value="left" ng-model="Edit.params.align" /> 居左</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_align" value="center" ng-model="Edit.params.align" /> 居中</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_align" value="right" ng-model="Edit.params.align" /> 居右</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">文字颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.color" />
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color"  ng-model="Edit.params.bgcolor" />
    </div>
</div>