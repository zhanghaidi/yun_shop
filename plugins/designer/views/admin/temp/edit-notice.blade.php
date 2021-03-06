<div class="fe-panel-editor-title">公告设置<span class="tips">Tips:文字不滚动时超出宽度将隐藏</span></div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">公告内容</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="这里填写公告内容，可设置是否滚动显示" ng-model="Edit.params.notice" />
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">公告链接</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="{{ $type == 9 ? '请输入小程序路径' : '请输入https://开头链接或选择系统链接'}}" ng-model="Edit.params.noticehref" />
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">滚动显示</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_scroll" value="0" ng-model="Edit.params.scroll"> 不滚动</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_scroll" value="1" ng-model="Edit.params.scroll"> 滚动显示</label>
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
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.bgcolor" />
    </div>
</div>