<div class="fe-panel-editor-title">会员信息设置</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">选择样式</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberportrait" value="1" ng-model="Edit.params.memberportrait" /> 居左</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberportrait" value="2" ng-model="Edit.params.memberportrait" /> 居中</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberportrait" value="3" ng-model="Edit.params.memberportrait" /> 居右</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">昵称文字颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.membernamecolor" />
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberbg" value="1" ng-model="Edit.params.memberbg"> 背景颜色</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberbg" value="2" ng-model="Edit.params.memberbg"> 背景图</label>
    </div>
</div>
<div ng-show="Edit.params.memberbg == 1" class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.memberbgcolor" />
    </div>
</div>
<div ng-show="Edit.params.memberbg == 2" class="fe-panel-editor-line">
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
    <div class="fe-panel-editor-name">会员ID显示</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberID" value="1" ng-model="Edit.params.memberID"> 显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberID" value="0" ng-model="Edit.params.memberID"> 隐藏</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">会员等级显示</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberlevel" value="1" ng-model="Edit.params.memberlevel"> 显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberlevel" value="0" ng-model="Edit.params.memberlevel"> 隐藏</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">会员等级类型</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberleveltype" value="1" ng-model="Edit.params.memberleveltype" /> 会员等级</label>
        <label style="cursor:pointer; margin-right: 10px;" ng-show="Edit.params.judgeteamdividend == true"><input type="radio" name="@{{Edit.id}}_memberleveltype" value="2" ng-model="Edit.params.memberleveltype" /> 经销商等级</label>
        <label style="cursor:pointer; margin-right: 10px;" ng-show="Edit.params.judgecommission == true"><input type="radio" name="@{{Edit.id}}_memberleveltype" value="3" ng-model="Edit.params.memberleveltype" /> 分销商等级</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">会员等级样式</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberlevelstyle" value="1" ng-model="Edit.params.memberlevelstyle" /> 样式一</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberlevelstyle" value="2" ng-model="Edit.params.memberlevelstyle" /> 样式二</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_memberlevelstyle" value="3" ng-model="Edit.params.memberlevelstyle" /> 样式三</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">等级文字颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.memberlevelcolor" />
    </div>
</div>
<div class="fe-panel-editor-title">资产数据设置</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">菜单项显示</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;" ng-show="Edit.params.judgeintegral == true"><input type="checkbox" name="@{{Edit.id}}_memberintegral" value="1" ng-model="Edit.params.memberintegral" /> 消费积分</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;" ng-show="Edit.params.judgelove == true"><input type="checkbox" name="@{{Edit.id}}_memberwhitelove" value="1" ng-model="Edit.params.memberwhitelove" /> 白爱心值</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;" ng-show="Edit.params.judgelove == true"><input type="checkbox" name="@{{Edit.id}}_memberredlove" value="1" ng-model="Edit.params.memberredlove" /> 爱心值</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="checkbox" name="@{{Edit.id}}_membercredit" value="1" ng-model="Edit.params.membercredit" /> 余额</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="checkbox" name="@{{Edit.id}}_memberpoint" value="1" ng-model="Edit.params.memberpoint" /> 积分</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="checkbox" name="@{{Edit.id}}_memberincome" value="1" ng-model="Edit.params.memberincome" /> 提现</label>
    </div>
</div>
<div class="fe-panel-editor-title">我的订单设置</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">菜单名称</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="不填写默认我的订单" ng-model="Edit.params.memberordername" />
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