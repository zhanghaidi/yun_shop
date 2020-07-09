<div class="fe-panel-editor-title">
    <span>橱窗设置</span>
    <span class="tips">Tips:参考高度为40像素</span>
</div>

<div class="fe-panel-editor-relative">
    <div class="fe-panel-editor-line2">
        <div class="fe-panel-editor-goodimg" ng-click="uploadImgChildWechat(Edit.id, picture.id,1)">
            <img ng-src="@{{Edit.params.imageOne}}" width="100%" ng-show="Edit.params.imageOne"/>
            <div class="fe-panel-editor-goodimg-t1" ng-show="!Edit.params.imageOne">
                <i class="fa fa-plus-circle"></i>
                <span>选择图片</span>
            </div>
            <div class="fe-panel-editor-goodimg-t2" ng-show="Edit.params.imageOne">
                <span>重新选择图片</span>
            </div>
        </div>
        <div class="fe-panel-editor-line2-right">
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2">链接地址</div>
                <div class="fe-panel-editor-con">
                    <input class="fe-panel-editor-input3" value="" ng-model="Edit.params.imageOneUrl" placeholder="{{ $type == 9 ? '请输入小程序路径或选择链接' : '请输入https://开头链接或选择系统链接'}}"/>
                    <div class="fe-panel-editor-input4" ng-click="chooseUrl(Edit.id, picture.id,1)">系统连接</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fe-panel-editor-relative">
    <div class="fe-panel-editor-line2">
        <div class="fe-panel-editor-goodimg" ng-click="uploadImgChildWechat(Edit.id, picture.id,2)">
            <img ng-src="@{{Edit.params.imageTwo}}" width="100%" ng-show="Edit.params.imageTwo"/>
            <div class="fe-panel-editor-goodimg-t1" ng-show="!Edit.params.imageTwo">
                <i class="fa fa-plus-circle"></i>
                <span>选择图片</span>
            </div>
            <div class="fe-panel-editor-goodimg-t2" ng-show="Edit.params.imageTwo">
                <span>重新选择图片</span>
            </div>
        </div>
        <div class="fe-panel-editor-line2-right">
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2">链接地址</div>
                <div class="fe-panel-editor-con">
                    <input class="fe-panel-editor-input3" value="" ng-model="Edit.params.imageTwoUrl" placeholder="{{ $type == 9 ? '请输入小程序路径或选择链接' : '请输入https://开头链接或选择系统链接'}}"/>
                    <div class="fe-panel-editor-input4" ng-click="chooseUrl(Edit.id, picture.id,2)">系统连接</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fe-panel-editor-relative">
    <div class="fe-panel-editor-line2">
        <div class="fe-panel-editor-goodimg" ng-click="uploadImgChildWechat(Edit.id, picture.id,3)">
            <img ng-src="@{{Edit.params.imageThree}}" width="100%" ng-show="Edit.params.imageThree"/>
            <div class="fe-panel-editor-goodimg-t1" ng-show="!Edit.params.imageThree">
                <i class="fa fa-plus-circle"></i>
                <span>选择图片</span>
            </div>
            <div class="fe-panel-editor-goodimg-t2" ng-show="Edit.params.imageThree">
                <span>重新选择图片</span>
            </div>
        </div>
        <div class="fe-panel-editor-line2-right">
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2">链接地址</div>
                <div class="fe-panel-editor-con">
                    <input class="fe-panel-editor-input3" value="" ng-model="Edit.params.imageThreeUrl" placeholder="{{ $type == 9 ? '请输入小程序路径或选择链接' : '请输入https://开头链接或选择系统链接'}}"/>
                    <div class="fe-panel-editor-input4" ng-click="chooseUrl(Edit.id, picture.id,3)">系统连接</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fe-panel-editor-relative">
    <div class="fe-panel-editor-line2">
        <div class="fe-panel-editor-goodimg" ng-click="uploadImgChildWechat(Edit.id, picture.id,4)">
            <img ng-src="@{{Edit.params.imageFour}}" width="100%" ng-show="Edit.params.imageFour"/>
            <div class="fe-panel-editor-goodimg-t1" ng-show="!Edit.params.imageFour">
                <i class="fa fa-plus-circle"></i>
                <span>选择图片</span>
            </div>
            <div class="fe-panel-editor-goodimg-t2" ng-show="Edit.params.imageFour">
                <span>重新选择图片</span>
            </div>
        </div>
        <div class="fe-panel-editor-line2-right">
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2">链接地址</div>
                <div class="fe-panel-editor-con">
                    <input class="fe-panel-editor-input3" value="" ng-model="Edit.params.imageFourUrl" placeholder="{{ $type == 9 ? '请输入小程序路径或选择链接' : '请输入https://开头链接或选择系统链接'}}"/>
                    <div class="fe-panel-editor-input4" ng-click="chooseUrl(Edit.id, picture.id,4)">系统连接</div>
                </div>
            </div>
        </div>
    </div>
</div>
