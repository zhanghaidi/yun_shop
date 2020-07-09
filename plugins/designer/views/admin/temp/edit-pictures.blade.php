<div class="fe-panel-editor-title">
    <span>多图设置</span>
    <span class="tips">Tips:图片最低高度为40像素</span>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">选择样式</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_style" value="50%" ng-model="Edit.params.style"/>
            <span>堆积两列</span>
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_style" value="33.3%" ng-model="Edit.params.style"/>
            <span>堆积三列</span>
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_style" value="25%" ng-model="Edit.params.style"/>
            <span>堆积四列</span>
        </label>
    </div>
</div>
<div ng-repeat="picture in Edit.data" class="fe-panel-editor-relative">
    <div class="fe-panel-editor-del" title="移除" ng-click="delItemChild(Edit.id, picture.id)">×</div>
    <div class="fe-panel-editor-line2">
        <div class="fe-panel-editor-goodimg" ng-click="uploadImgChild(Edit.id, picture.id)">
            <img ng-src="@{{picture.imgurl}}" width="100%" ng-show="picture.imgurl"/>
            <div class="fe-panel-editor-goodimg-t1" ng-show="!picture.imgurl">
                <i class="fa fa-plus-circle"></i>
                <span>选择图片</span>
            </div>
            <div class="fe-panel-editor-goodimg-t2" ng-show="picture.imgurl">重新选择图片</div>
        </div>
        <div class="fe-panel-editor-line2-right">
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2">链接地址</div>
                <div class="fe-panel-editor-con">
                    <input class="fe-panel-editor-input3" value="" ng-model="picture.hrefurl" placeholder="{{ $type == 9 ? '请输入小程序路径或选择链接' : '请输入https://开头链接或选择系统链接'}}"/>
                    <div class="fe-panel-editor-input4" ng-click="chooseUrl(Edit.id, picture.id)">系统连接</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fe-panel-editor-sub1" ng-click="addItemChild('picture', Edit.id)" ng-show="Edit.params.style != 'special'">
    <i class="fa fa-plus-circle"></i>
    <span>添加一个单图</span>
</div>
