<div class="fe-panel-editor-title">文章设置</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.bgcolor"></div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">添加方式</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_addmethod" value="0" ng-model="Edit.params.addmethod" ng-click="pushAllArticle(Edit.id)"/> 自动获取</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_addmethod" value="1" ng-model="Edit.params.addmethod" /> 手动获取</label>
    </div>
</div>
<div ng-show="Edit.params.addmethod == 0" class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">文章显示数量</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input3" placeholder="" ng-model="Edit.params.shownum" /><br>
        <span style="font-size: 12px; margin-left: 10px;">系统自动获取文章，按照文章ID大小排列显示</span>
    </div>
</div>
<div ng-show="Edit.params.addmethod == 1" >
    <div ng-repeat="article in Edit.data" class="fe-panel-editor-relative">
        <div class="fe-panel-editor-line2">
            <div class="fe-panel-editor-del" title="移除" ng-click="delArticle(Edit.id, article.id)">×</div>
            <div class="fe-panel-editor-line1-right">
                <div class="fe-panel-editor-line">
                    <div class="fe-panel-editor-name2">文章标题</div>
                    <div class="fe-panel-editor-con1">@{{article.title}}</div>
                </div>
                {{--<div class="fe-panel-editor-line">--}}
                    {{--<div class="fe-panel-editor-name2">链接地址</div>--}}
                    {{--<div class="fe-panel-editor-con">--}}
                        {{--<input class="fe-panel-editor-input3" data-id="headline-@{{headline.id}}"  value="{{article.hrefurl}}" ng-model="headline.hrefurl" placeholder="请手动输入链接(请以http://开头)或选择系统链接" />--}}
                        {{--<div class="fe-panel-editor-input4 nav-link" ng-click="chooseUrl(Edit.id, article.id)" data-id="menu-@{{headline.id}}">系统连接</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            </div>
        </div>
    </div>
    <div class="fe-panel-editor-sub1" ng-click="addArticle('', Edit.id, '')"><i class="fa fa-plus-circle"></i> 选择文章</div>
</div>

