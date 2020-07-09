<div class="fe-panel-editor-title">头条动态设置
    <span class="tips">Tips:头条logo建议尺寸：52px*30px,建议按5：3比列</span>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.bgcolor"></div>
    </div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">显示数量</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" value="1" ng-model="Edit.params.shownum" /> 1</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" value="2" ng-model="Edit.params.shownum" /> 2</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">头条logo</div>
    <div class="fe-panel-editor-con">
        <div class="fe-panel-editor-upload" ng-click="shopImg(Edit.id)">
            <img ng-src="@{{Edit.params.bgimg}}" width="100%" ng-show="Edit.params.bgimg" />
            <div class="fe-panel-editor-upload-choose2" ng-show="Edit.params.bgimg">重新选择logo</div>
            <div class="fe-panel-editor-upload-choose1" ng-show="!Edit.params.bgimg"><i class="fa fa-plus-circle"></i> 选择图片</div>
        </div>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">头条标题</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="头条动态页面的标题，超出屏幕宽度将自动隐藏" ng-model="Edit.params.toptitle" />
    </div>
</div>
<div ng-repeat="headline in Edit.data" class="fe-panel-editor-relative">
    <div class="fe-panel-editor-line2">
        <div class="fe-panel-editor-del" title="移除" ng-click="delArticle(Edit.id, headline.id)">×</div>
        <div class="fe-panel-editor-line1-right">
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2">文章标题</div>
                <div class="fe-panel-editor-con1">@{{headline.title}}</div>
            </div>
        </div>
    </div>
</div>
<div class="fe-panel-editor-sub1" ng-click="addArticle('', Edit.id, '')"><i class="fa fa-plus-circle"></i> 选择文章</div>
