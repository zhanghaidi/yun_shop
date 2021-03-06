<div class="fe-panel-editor-cube">
    <div class="fe-panel-editor-title">魔方设置</div>
    <div class="fe-panel-editor-line">
        <div class="fe-panel-editor-name">背景颜色</div>
        <div class="fe-panel-editor-con">
            <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.bgcolor" />
        </div>
    </div>
    
    <div class="fe-panel-editor-line">
        <div class="fe-panel-editor-name">布局</div>
        <div class="fe-panel-editor-con">
            <table id="cube-editor" style ="margin-top:10px">
                <tr ng-repeat="(x, row) in Edit.params.layout  track by $index">
                    <td ng-repeat="(y, col) in row track by $index" ng-if="col.cols" class="{[{col.classname}} rows-@{{col.rows}} cols-@{{col.cols}}" ng-click="col['isempty'] ? showSelection(Edit, x, y) : changeItem(Edit, x, y)" ng-class="{'empty' : col.isempty, 'not-empty' : !col.isempty}" rowspan="@{{col.rows}}" colspan="@{{col.cols}}"  x="@{{x}}" y="@{{y}}">
                        <div ng-if="col.isempty">+</div>
                        <div ng-if="!col.imgurl && !col.isempty">@{{col.cols * 160}} * @{{col.rows * 160}}</div>
                        <div ng-if="!col.isempty && col.imgurl"><img ng-src="@{{col.imgurl}}" width="@{{col.cols * 85}}" height="@{{col.rows * 85}}" /></div>
                    </td>
                </tr>
            </table>
            <span class="help-block">点击"+",添加内容</span><img ng-src="@{{col.imgurl}}" width="@{{col.cols * 60}}" height="@{{col.cols * 60}}" />
        </div>
    </div>
    <div class="fe-panel-editor-line2" ng-show="Edit.params.currentLayout.isempty == false" style="position: relative;">
        <div class="fe-panel-editor-del" title="移除" ng-click="delCube(Edit,Edit.params.currentLayout.classname,Edit.params.currentLayout.cols,Edit.params.currentLayout.rows)" style="top: 0; right: 0; border-radius: 0 0 0 20px; padding-left: 5px; padding-bottom: 5px;">×</div>
        <div ng-click="uploadImgChild(Edit.id, Edit.params.currentLayout.classname,'cube')" class="fe-panel-editor-goodimg" style="min-height: 100px; width: 100px;">
            <img width="100%" ng-show="Edit.params.currentLayout.imgurl" ng-src="@{{Edit.params.currentLayout.imgurl}}">
            <div ng-show="!Edit.params.currentLayout.imgurl" class="fe-panel-editor-goodimg-t1 ng-hide" style="line-height: 100px;"><i class="fa fa-plus-circle"></i> 选择图片</div>
            <div ng-show="Edit.params.currentLayout.imgurl" class="fe-panel-editor-goodimg-t2" style="width: 100%; height: 20px; line-height: 20px;">重新选择图片</div>
        </div>
        <div class="fe-panel-editor-line2-right">
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name">图片尺寸:</div>
                <div class="fe-panel-editor-con">@{{Edit.params.currentLayout.cols * 160}} * @{{Edit.params.currentLayout.rows * 160}} 像素</div>
            </div>
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name">链接地址</div>
                <div class="fe-panel-editor-con">
                    <input placeholder="{{ $type == 9 ? '请输入小程序路径或选择链接' : '请输入https://开头链接或选择系统链接'}}" ng-model="Edit.params.currentLayout.url" value="" class="fe-panel-editor-input3 ng-pristine ng-untouched ng-valid">
                    <div ng-click="chooseUrl(Edit.id, Edit.params.currentLayout.classname,'cube')" class="fe-panel-editor-input4">系统连接</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="@{{Edit.id}}-modal-cube-layout" class="modal fade in" role="dialog" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3>选择布局</h3>
            </div>
            <div class="modal-body text-center">
                <div class="layout-table">
                    <ul class="layout-cols layout-rows-@{{col.rows}} clearfix" ng-repeat="row in Edit.params.selection  track by $index">
                        <li data-cols="@{{col.cols}}" data-rows="@{{col.rows}}" ng-click="selectLayout(Edit, Edit.params.currentPos.row, Edit.params.currentPos.col, col.rows, col.cols)" ng-repeat="col in row  track by $index"></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>