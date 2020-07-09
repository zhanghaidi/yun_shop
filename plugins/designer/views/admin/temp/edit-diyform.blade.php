<div class="fe-panel-editor-title">表单设置</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">显示标题</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_showtitle" value="1"
                                                                  ng-model="Edit.params.showtitle"/> 显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_showtitle" value="0"
                                                                  ng-model="Edit.params.showtitle"/> 不显示</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">标题内容</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="表单标题，超出屏幕宽度将自动隐藏" ng-model="Edit.params.title"/>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">主标题大小</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_titlesize"
                                                                  value="12px" ng-model="Edit.params.titlesize"/>
            12像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_titlesize"
                                                                  value="14px" ng-model="Edit.params.titlesize"/>
            14像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_titlesize"
                                                                  value="16px" ng-model="Edit.params.titlesize"/>
            16像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_titlesize"
                                                                  value="18px" ng-model="Edit.params.titlesize"/>
            18像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_titlesize"
                                                                  value="20px" ng-model="Edit.params.titlesize"/>
            20像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2"
                                                                      style="line-height: 20px;" value=""
                                                                      ng-model="Edit.params.titlesize"/></label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">对齐方向</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_titleposition"
                                                                  value="left" ng-model="Edit.params.titleposition"/> 居左</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_titleposition"
                                                                  value="center" ng-model="Edit.params.titleposition"/>
            居中</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_titleposition"
                                                                  value="right" ng-model="Edit.params.titleposition"/>
            居右</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">文字颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.titlecolor"/>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.bgcolor"/>
    </div>
</div>


<div class="fe-panel-editor-line2">
    <div class="fe-panel-editor-line2-right">
        <div class="fe-panel-editor-line">
            <div class="fe-panel-editor-name2">表单名称：@{{Edit.data.title}}</div>
        </div>
    </div>
</div>

<div class="fe-panel-editor-sub1" ng-click="addForm('', Edit.id, '')"><i class="fa fa-plus-circle"></i> 选择表单</div>
