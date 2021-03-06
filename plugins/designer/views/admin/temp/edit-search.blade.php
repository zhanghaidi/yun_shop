<div class="fe-panel-editor-title">搜索框设置</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">提示文字</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="搜索框默认提示文字，超出屏幕宽度将自动隐藏" ng-model="Edit.params.placeholder" />
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">选择样式</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="style1" ng-model="Edit.params.style"> 样式一</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="style2" ng-model="Edit.params.style"> 样式二</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">文字颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.color" />
        <span class="tips">提示: 输入文字颜色</span>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">边框颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.bordercolor" />
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.bgcolor" />
    </div>
</div>