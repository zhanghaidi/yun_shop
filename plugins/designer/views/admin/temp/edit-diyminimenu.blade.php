<div class="fe-panel-editor-ico"></div>
<div class="fe-panel-editor-title">主菜单样式<span style="font-size: 12px; margin-left: 10px;"></span></div>
<div class="fe-panel-editor-line2" style="margin-top:10px">
    <div class="fe-panel-editor-name">菜单名称</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" name="menuname" placeholder="菜单名称，便于搜索"
               value="{{ $menuName or '' }}"/>
    </div>
    <div class="fe-panel-editor-name">预览背景色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.previewbg"/>
    </div>
    <div class="fe-panel-editor-name">(制作时可见)</div>
</div>
<div class="fe-panel-editor-line2">

    <div class="fe-panel-editor-name">文字设置</div>
    <div class="fe-panel-editor-con">

        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="showtext"
                                                                  ng-model="params.showtext" value="1"/> 显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="showtext"
                                                                  ng-model="params.showtext" value="0"/> 不显示</label>
    </div>

    <div class="fe-panel-editor-name1">颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.textcolor" ng-change="clear()"/>
    </div>
    <div class="fe-panel-editor-name1">选中色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.textcolorhigh" ng-change="clear()"/>
    </div>
</div>
<div class="fe-panel-editor-line2">

    <div class="fe-panel-editor-name">图标设置</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" value="0" name="showicon" ng-model="params.showicon"/> 不显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" value="1" name="showicon" ng-model="params.showicon"/> 左侧</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" value="2" name="showicon" ng-model="params.showicon"/> 上侧</label>
    </div>

    <div class="fe-panel-editor-name1">颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.iconcolor" ng-change="clear()"/>
    </div>
    <div class="fe-panel-editor-name1">选中色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.iconcolorhigh" ng-change="clear()"/>
    </div>
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
    <div class="fe-panel-editor-name1">选中色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.bordercolorhigh" ng-change="clear()"/>
    </div>
</div>

<div class="fe-panel-editor-line2">
    <div class="fe-panel-editor-name2">背景设置</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.bgcolor" ng-change="clear()"/>
    </div>
    <div class="fe-panel-editor-name1">选中色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.bgcolorhigh" ng-change="clear()"/>
    </div>
    <div class="fe-panel-editor-name1">透明度</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" style="width:100px;" type="text" ng-model="params.bgalpha"
               ng-change="clear()" placeholder="0.1-1"/>
    </div>
</div>

<div class="fe-panel-editor-title">二级菜单样式<span style="font-size: 12px; margin-left: 10px;"></span></div>

<div class="fe-panel-editor-line2">
    <div class="fe-panel-editor-name2">文字颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.textcolor2"/>
    </div>

    <div class="fe-panel-editor-name2">边框颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.bordercolor2"/>
    </div>

    <div class="fe-panel-editor-name2">背景设置</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="params.bgcolor2"/>
    </div>
</div>

<div class="fe-panel-editor-title">菜单结构<span style="font-size: 12px; margin-left: 10px;"></span></div>

<div class="fe-panel-editor-line2">
    <div class="fe-panel-editor-name">菜单设计</div>
    <div class="table-responsive panel-body" style="height:auto; padding-bottom: 15px;">


        <table>
            <tbody class="ui-sortable">
            <tr ng-repeat="menu in menus" class="hover ng-scope">
                <td style="border-top:none;">
                    <div class="parentmenu" style="position: relative">
                        <input type="hidden" class="icon" ng-model="menu.icon"/>
                        <input type="hidden" class="url" ng-model="menu.url"/>
                        <input type="text" ng-model="menu.title" style="display:inline-block;width:250px;"
                               class="fe-panel-editor-input1 ng-pristine ng-untouched ng-valid" id="@{{menu.id}}">
                        <a class="btn btn-default btn-sm btn-move" title="拖动调整此菜单位置" href="javascript:;"><i
                                    class="fa fa-arrows"></i></a>
                        <a class="btn btn-sm" ng-class="{'btn-default':menu.icon=='','btn-warning':menu.icon!=''}"
                           title="选择图标" ng-click="selectIcon(menu,$event)" href="javascript:;"><i
                                    class="fa fa-github-alt"></i></a>
                        <a class="btn  btn-sm" ng-class="{'btn-default':menu.url=='','btn-warning':menu.url!=''}"
                           title="选择连接" ng-show="menu.subMenus.length<=0" ng-click="selectUrl(menu,$event)"
                           href="javascript:;"><i class="fa fa-link"></i></a>

                        <div class="popovermenu  bottom">
                            <div class="arrow1" style="left: 50%;"></div>
                            <h3 class="popovermenu-title"></h3>
                            <div class="popovermenu-content">
                                <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{menu.id}}_hrefChoice" value="1" ng-model="menu.hrefChoice" /> 跳转链接</label>
                                <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{menu.id}}_hrefChoice" value="2" ng-model="menu.hrefChoice" /> 跳转小程序</label>
                                <div  ng-show="!menu.hrefChoice || menu.hrefChoice == 1">
                                    <input type="text" style="width: 310px;margin-top: 10px" class="form-control" ng-model='menu.url'/>
                                    <button class="btn btn-default" style="margin-top: 10px" type="button" ng-click='chooseUrl(menu)'>选择</button>
                                </div>
                                <div  ng-show="menu.hrefChoice == 2">
                                    <input type="text" style="width: 310px;margin-top: 10px" class="form-control" ng-model='menu.appID' placeholder="请填写小程序的APPID"/>
                                    <input type="text" style="width: 310px;margin-top: 10px" class="form-control" ng-model='menu.miniUrl' placeholder="请填写跳转页面的小程序访问路径"/>
                                </div>
                                <div ng-show="!menu.hrefChoice || menu.hrefChoice == 1">
                                    <button class="btn btn-danger save" type="button" ng-click="clearUrl(menu,$event)">
                                        清除
                                    </button>
                                    <button class="btn btn-success save" type="button"
                                            ng-click="confirmUrl(menu,$event)">确定
                                    </button>
                                </div>
                                <div  ng-show="menu.hrefChoice == 2">
                                    <button class="btn btn-danger save" style="width: 80px;"  type="button" ng-click="clearMiniUrl(menu,$event)">
                                        清除
                                    </button>
                                    <button class="btn btn-success save" style="width: 80px;" type="button"
                                            ng-click="confirmUrl(menu,$event)">确定
                                    </button>
                                </div>
                            </div>
                        </div>


                        <a class="btn btn-danger btn-sm" title="删除此菜单" ng-click="deleteMenu(menu)"
                           href="javascript:;"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-default btn-sm" title="添加子菜单" ng-click="addSubMenu(menu, this);"
                           href="javascript:;"><i class="fa fa-plus"></i> 子菜单</a>


                    </div>
                    <div class="designer ui-sortable-sub" style="position: relative;">
                        @if(config('app.framework') == 'platform')
                            <div style="position: relative;margin-top:5px;padding-left:50px;background:url('/static/resource/images/bg_repno.gif') no-repeat -245px -545px;"
                        @else
                            <div style="position: relative;margin-top:5px;padding-left:50px;background:url('./resource/images/bg_repno.gif') no-repeat -245px -545px;"
                                 @endif
                                 ng-repeat="sub in menu.subMenus" class="ng-scope">
                                <input type="hidden" class="url" ng-model="sub.url"/>
                                <input type="text" ng-model="sub.title" style="display:inline-block;width:200px;" class="fe-panel-editor-input1 ng-pristine ng-untouched ng-valid" id="@{{sub.id}}">
                                <a class="btn btn-default btn-sm btn-move" title="拖动调整此菜单位置" href="javascript:;"><i class="fa fa-arrows"></i></a>
                                <a class="btn btn-sm" ng-class="{'btn-default':sub.url=='','btn-warning':sub.url!=''}" title="选择连接" ng-click="selectUrl(sub,$event)" href="javascript:;"><i class="fa fa-link"></i></a>

                                <div class="popovermenu popovermenu-sub bottom">
                                    <div class="arrow" style="left: 50%;"></div>
                                    <h3 class="popovermenu-title"></h3>
                                    <div class="popovermenu-content">
                                        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{sub.id}}_hrefChoice" value="1" ng-model="sub.hrefChoice" /> 跳转链接</label>
                                        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{sub.id}}_hrefChoice" value="2" ng-model="sub.hrefChoice" /> 跳转小程序</label>
                                        <div  ng-show="!sub.hrefChoice || sub.hrefChoice == 1">
                                       <input type="text" style="width: 310px;margin-top: 10px" class="form-control" ng-model='sub.url'/>
                                        <button class="btn btn-default" style="margin-top: 10px" type="button" ng-click='chooseUrl(sub)'>选择</button>
                                        </div>
                                        <div  ng-show="sub.hrefChoice == 2">
                                            <input type="text" style="width: 310px;margin-top: 10px" class="form-control" ng-model='sub.appID' placeholder="请填写小程序的APPID"/>
                                            <input type="text" style="width: 310px;margin-top: 10px" class="form-control" ng-model='sub.miniUrl' placeholder="请填写跳转页面的小程序访问路径"/>
                                        </div>
                                        <div ng-show="!sub.hrefChoice || sub.hrefChoice == 1">
                                        <button class="btn btn-danger save" type="button" ng-click="clearUrl(sub,$event)">
                                            清除
                                        </button>
                                        <button class="btn btn-success save" type="button"
                                                ng-click="confirmUrl(sub,$event)">确定
                                        </button>
                                        </div>
                                        <div  ng-show="sub.hrefChoice == 2">
                                            <button class="btn btn-danger save" style="width: 80px;"  type="button" ng-click="clearMiniUrl(sub,$event)">
                                                清除
                                            </button>
                                            <button class="btn btn-success save" style="width: 80px;" type="button"
                                                    ng-click="confirmUrl(sub,$event)">确定
                                            </button>
                                        </div>
                                    </div>
                                </div>


                                <a class="btn btn-danger btn-sm" title="删除此菜单" ng-click="deleteMenu(menu, sub, this);"
                                   href="javascript:;"><i class="fa fa-remove"></i></a>


                            </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <table>
            <tr>
                <td style="padding-top:10px;"><a class="btn btn-primary" ng-click="addMenu()"><i class="fa fa-plus"></i>添加主菜单</a></td>
            </tr>
        </table>


    </div>
</div>


