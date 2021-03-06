<div class="fe-panel-editor-title">辅助线设置</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">选择样式</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="solid" ng-model="Edit.params.style" /> 实线</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="dashed" ng-model="Edit.params.style" /> 虚线</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="dotted" ng-model="Edit.params.style" /> 圆点</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">选择高度</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_height" value="1px" ng-model="Edit.params.height" /> 1像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_height" value="2px" ng-model="Edit.params.height" /> 2像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_height" value="5px" ng-model="Edit.params.height" /> 5像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_height" value="10px" ng-model="Edit.params.height" /> 10像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_height" value="20px" ng-model="Edit.params.height" /> 20像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" value="" ng-model="Edit.params.height" /></label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">设置颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.color">
    </div>
</div>