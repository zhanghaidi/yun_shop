<div class="fe-panel-editor-ico"></div>
<div class="fe-panel-editor-title">主菜单样式<span style="font-size: 12px; margin-left: 10px;"></span></div>
<div class="fe-panel-editor-line2" style="margin-top:10px">
    <div class="fe-panel-editor-name">菜单名称</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" name="menuname" placeholder="菜单名称，便于搜索" value="{{ $menuModel->menu_name or '' }}"/>
    </div>
    <div class="fe-panel-editor-name">预览背景色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.previewbg"/>
    </div>
    <div class="fe-panel-editor-name">(制作时可见)</div>
</div>
<div class="fe-panel-editor-line2">

    <div class="fe-panel-editor-name">文字设置</div>
    <div class="fe-panel-editor-name1">颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.textcolor" ng-change="clear()"/>
    </div>
    {{--<div class="fe-panel-editor-name1">选中色</div>--}}
    {{--<div class="fe-panel-editor-con">--}}
        {{--<input class="fe-panel-editor-input2" type="color" ng-model="params.textcolorhigh" ng-change="clear()"/>--}}
    {{--</div>--}}
</div>
<div class="fe-panel-editor-line2">
    <div class="fe-panel-editor-name">边框设置</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="showborder" value="1" ng-model="params.showborder"/> 显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="showborder" value="0" ng-model="params.showborder"/> 不显示</label>
    </div>
    <div class="fe-panel-editor-name1">颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.bordercolor" ng-change="clear()"/>
    </div>
</div>

<div class="fe-panel-editor-line2">
    <div class="fe-panel-editor-name2">背景设置</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.bgcolor" ng-change="clear()"/>
    </div>
    {{--<div class="fe-panel-editor-name1">选中色</div>--}}
    {{--<div class="fe-panel-editor-con">--}}
        {{--<input class="fe-panel-editor-input2" type="color" ng-model="params.bgcolorhigh" ng-change="clear()"/>--}}
    {{--</div>--}}
    <div class="fe-panel-editor-name1">透明度</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" style="width:100px;" type="text" ng-model="params.bgalpha" ng-change="clear()" placeholder="0.1-1"/>
    </div>
</div>
<div class="fe-panel-editor-title">菜单结构<span style="font-size: 12px; margin-left: 10px;"></span></div>

<div class="fe-panel-editor-line2">
<div class="fe-panel-editor-name">搜索框文字</div>
    <div class="fe-panel-editor-con search-word">
        <input class="fe-panel-editor-input1" name="menuname" placeholder="搜索：输入关键字在店内搜索" ng-model="params.searchword"/>
    </div>
    <div class=" menu-setting" >菜单设计</div>
    <div class="table-responsive panel-body" style="height:auto; padding-bottom: 15px;padding-top:30px;">
        <table>
            <tbody class="ui-sortable">
            <tr ng-repeat="menu in menus" class="hover ng-scope">
                <td style="border-top:none;">
                    <div class="parentmenu" style="position: relative;width:100%">
                        <input type="hidden" class="icon" ng-model="menu.icon"/>
                        <input type="hidden" class="url" ng-model="menu.url"/>
                        <input type="text" ng-model="menu.title" style="display:inline-block;width:250px;"
                               class="fe-panel-editor-input1 ng-pristine ng-untouched ng-valid" id="@{{menu.id}}">
                        <a class="btn btn-default btn-sm btn-move" title="拖动调整此菜单位置" href="javascript:;"><i
                                    class="fa fa-arrows"></i></a>
                        <a class="btn  btn-sm" ng-class="{'btn-default':menu.url=='','btn-warning':menu.url!=''}"
                           title="选择链接"  ng-click="selectUrl(menu,$event)"
                           href="javascript:;"><i class="fa fa-link"></i></a>

                        <div class="popovermenu  bottom">
                            <div class="arrow1" style="left: 50%;"></div>
                            <h3 class="popovermenu-title"></h3>
                            <div class="popovermenu-content">
                                <input type="text" style="width: 320px;" class="form-control" ng-model="menu.url">
                                <button class="btn btn-default" type="button" ng-click='chooseUrl(menu)'>选择</button>
                                &nbsp;
                                <button class="btn btn-danger save" type="button" ng-click="clearUrl(menu,$event)">清除
                                </button>
                                <button class="btn btn-success save" type="button" ng-click="confirmUrl(menu,$event)">
                                    确定
                                </button>

                            </div>
                        </div>
                        <a class="btn btn-danger btn-sm" title="删除此菜单" ng-click="deleteMenu(menu)"
                           href="javascript:;"><i class="fa fa-remove"></i>
                        </a>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <table>
        <tr>
                <td style="padding-top:10px;"><a class="btn btn-primary" ng-click="addTopMenu()"><i class="fa fa-plus"></i>添加主菜单</a></td>
            </tr>
        </table>
    </div>
</div>


