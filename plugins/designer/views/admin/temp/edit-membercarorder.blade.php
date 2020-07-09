<div class="fe-panel-editor-title">网约车订单设置</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">菜单名称</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="不填写默认网约车订单" ng-model="Edit.params.memberordername" />
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberorderbg" value="1" ng-model="Edit.params.memberorderbg"> 背景颜色</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberorderbg" value="2" ng-model="Edit.params.memberorderbg"> 背景图</label>
    </div>
</div>
<div ng-show="Edit.params.memberorderbg == 1" class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.memberordercolor" />
    </div>
</div>
<div ng-show="Edit.params.memberorderbg == 2" class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景图片</div>
    <div class="fe-panel-editor-con">
        <div class="fe-panel-editor-upload" ng-click="memberorderImg(Edit.id)" style="height:120px; width: 120px;">
            <img ng-src="@{{Edit.params.memberorderimg}}" width="100%;" height="100%" ng-show="Edit.params.memberorderimg" />
            <div class="fe-panel-editor-upload-choose2" ng-show="Edit.params.memberorderimg">重新选择封面图</div>
            <div class="fe-panel-editor-upload-choose1" ng-show="!Edit.params.memberorderimg" style="line-height:116px;"><i class="fa fa-plus-circle"></i> 选择图片</div>
        </div>
    </div>
</div>
<div ng-repeat="member in Edit.data" ng-hide="$index == 4 && Edit.params.num == '25%'">
    <div class="fe-panel-editor-line2">
        <div class="fe-panel-editor-goodimg" ng-click="uploadImgChild(Edit.id, member.id)" style="height:120px; width:120px;">
            <img ng-src="@{{member.imgurl}}" width="100%" height="100%" ng-show="member.imgurl" />
            <div class="fe-panel-editor-goodimg-t1" ng-show="!member.imgurl" style="line-height:120px;"><i class="fa fa-plus-circle"></i> 选择图标</div>
            <div class="fe-panel-editor-goodimg-t2" ng-show="member.imgurl" style="width:118px;">重新选择图标</div>
        </div>
        <div class="fe-panel-editor-line2-right">
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2">按钮文字</div>
                <div class="fe-panel-editor-con">
                    <input class="fe-panel-editor-input1" style="width:380px;" value="" ng-model="member.text"/>
                </div>
            </div>
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2">文字颜色</div>
                <div class="fe-panel-editor-con">
                    <input class="fe-panel-editor-input2" type="color" ng-model="member.color" />
                </div>
            </div>
        </div>
    </div>
</div>