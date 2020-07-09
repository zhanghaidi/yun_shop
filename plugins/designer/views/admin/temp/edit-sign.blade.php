<div class="fe-panel-editor-title">签到设置
    <span class="tips">Tips:背景图建议尺寸：375px*233px</span>
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

<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">文字颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.textcolor" />
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.bgcolor">
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
    <div class="fe-panel-editor-name">签到奖励</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_name" value="1" ng-model="Edit.params.award" /> 显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_name" value="2" ng-model="Edit.params.award" /> 不显示</label>
    </div>
</div>
