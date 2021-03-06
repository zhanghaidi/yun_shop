<div class="fe-panel-editor-title">店招设置<span class="tips">Tips:背景图建议尺寸：375px*233px，商城名称与商城logo读取系统设置</span></div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">选择样式</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="1" ng-model="Edit.params.style" /> 样式一</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="2" ng-model="Edit.params.style" /> 样式二</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">商城logo</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_logo" value="1" ng-model="Edit.params.logo" /> 显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_logo" value="2" ng-model="Edit.params.logo" /> 不显示</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">商城名称</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_name" value="1" ng-model="Edit.params.name" /> 显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_name" value="2" ng-model="Edit.params.name" /> 不显示</label>
    </div>
</div>

<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">按钮颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.navcolor">
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景图片</div>
    <div class="fe-panel-editor-con">
        <div class="fe-panel-editor-upload" ng-click="shopImg(Edit.id)">
            <img ng-src="@{{Edit.params.bgimg}}" width="100%" ng-show="Edit.params.bgimg" />
            <div class="fe-panel-editor-upload-choose2" ng-show="Edit.params.bgimg">重新选择背景图片</div>
            <div class="fe-panel-editor-upload-choose1" ng-show="!Edit.params.bgimg"><i class="fa fa-plus-circle"></i> 选择图片</div>
        </div>
    </div>
</div>