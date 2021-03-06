<div class="fe-panel-editor-title">辅助空白设置</div>
<div class="fe-panel-editor-line2 fe-panel-editor-line">
    <div class="fe-panel-editor-name">选择高度</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_height" value="10px" ng-model="Edit.params.height" /> 10像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_height" value="20px" ng-model="Edit.params.height" /> 20像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_height" value="50px" ng-model="Edit.params.height" /> 50像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_height" value="100px" ng-model="Edit.params.height" /> 100像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_height" value="150px" ng-model="Edit.params.height" /> 150像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" ng-model="Edit.params.height" /></label>
    </div>
</div>
<div class="fe-panel-editor-line2 fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.bgcolor" />
    </div>
</div>