<div class="fe-panel-editor-title">营销互动设置<span class="tips">Tips:如果该板块没有显示内容，则整个板块不显示</span></div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">板块标题</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" ng-model="Edit.params.markettitle" />
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">页面风格</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_marketstyle" value="1" ng-model="Edit.params.marketstyle"> 九宫格</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_marketstyle" value="2" ng-model="Edit.params.marketstyle"> 列表</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">文字颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.markettitlecolor" />
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_marketbg" value="1" ng-model="Edit.params.marketbg"> 背景颜色</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_marketbg" value="2" ng-model="Edit.params.marketbg"> 背景图</label>
    </div>
</div>
<div ng-show="Edit.params.marketbg == 1" class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.marketbgcolor" />
    </div>
</div>
<div ng-show="Edit.params.marketbg == 2" class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景图片</div>
    <div class="fe-panel-editor-con">
        <div class="fe-panel-editor-upload" ng-click="memberbgImg(Edit.id)">
            <img ng-src="@{{Edit.params.bgimg}}" width="100%" ng-show="Edit.params.bgimg" />
            <div class="fe-panel-editor-upload-choose2" ng-show="Edit.params.bgimg">重新选择背景图片</div>
            <div class="fe-panel-editor-upload-choose1" ng-show="!Edit.params.bgimg"><i class="fa fa-plus-circle"></i> 选择图片</div>
        </div>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">板块内容</div>
    <div class="fe-panel-editor-con">
        <label ng-repeat="icon in Edit.data.part" style="cursor:pointer; margin-right: 10px; margin-top: 8px;">
            <input type="checkbox" ng-checked="icon.is_open" ng-click="memberselect(Edit.id, icon.id, icon.is_open)"/> @{{icon.title}}
        </label>
    </div>
</div>
<div ng-repeat="market in Edit.data.more" class="fe-panel-editor-relative">
    <div class="fe-panel-editor-del" title="移除" ng-click="delMemberChild(Edit.id, market.id)">×</div>
    <div class="fe-panel-editor-line2">
        <div class="fe-panel-editor-goodimg" ng-click="uploadMemberImgChild(Edit.id, market.id)">
            <img ng-src="@{{market.imgurl}}" width="100%" ng-show="market.imgurl" />
            <div class="fe-panel-editor-goodimg-t1" ng-show="!market.imgurl"><i class="fa fa-plus-circle"></i> 选择图片</div>
            <div class="fe-panel-editor-goodimg-t2" ng-show="market.imgurl">重新选择图片</div>
        </div>
        <div class="fe-panel-editor-line2-right">
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2">按钮文字</div>
                <div class="fe-panel-editor-con">
                    <input class="fe-panel-editor-input3"  value="" ng-model="market.title" placeholder="请输入按钮文字" />
                </div>
            </div>
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2">选择链接</div>
                <div class="fe-panel-editor-con">
                    <input class="fe-panel-editor-input3"  value="" ng-model="market.hrefurl" placeholder="{{ $type == 9 ? '请输入小程序路径或选择链接' : '请输入https://开头链接或选择系统链接'}}" />
                    <div class="fe-panel-editor-input4" ng-click="chooseUrl(Edit.id, market.id,'member')">系统连接</div>
                </div>
            </div>
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2">选择列表图标</div>
                <div class="fe-panel-editor-con">
                    <div class="col-xs-12 col-sm-9 col-md-10 ">
                        <a class="btn btn-sm" ng-class="{'btn-default':market.icon=='','btn-warning':market.icon!=''}"
                           title="选择图标" ng-click="selectIcon(market,$event)" href="javascript:;"><i
                                    class="fa fa-github-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fe-panel-editor-sub1" ng-click="addMemberItemChild('market', Edit.id)"><i class="fa fa-plus-circle"></i> 添加一个板块内容</div>