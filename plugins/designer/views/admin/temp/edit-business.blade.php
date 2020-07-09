<div class="fe-panel-editor-title">
    选项卡设置<span class="tips">警告：未开启门店插件不要使用选项卡，未开启一卡通插件不要开启折扣选项</span>
</div>
<div ng-repeat="ka in Edit.data">
    <div class="fe-panel-editor-line" ng-hide="ka.sheer">
        <div class="fe-panel-editor-name">@{{ka.text}}选项</div>
        <div class="fe-panel-editor-con">
            <label style="cursor:pointer; margin-right: 10px;"><input ng-click="selectOpenClose(Edit.id, ka.id, 1)" type="radio" name="@{{ka.id}}" value="1" ng-checked="ka.is_open == 1"> 显示</label>
            <label style="cursor:pointer; margin-right: 10px;"><input ng-click="selectOpenClose(Edit.id, ka.id, 0)" type="radio" name="@{{ka.id}}" value="0" ng-checked="ka.is_open != 1"> 不显示</label>
        </div>
    </div>
</div>
{{--<div class="fe-panel-editor-line">--}}
    {{--<div class="fe-panel-editor-name">折扣选项</div>--}}
    {{--<div class="fe-panel-editor-con">--}}
        {{--<label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="1" ng-model="Edit.params.style"> 显示</label>--}}
        {{--<label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="0" ng-model="Edit.params.style"> 不显示</label>--}}
    {{--</div>--}}
{{--</div>--}}
{{--<div class="fe-panel-editor-line">--}}
    {{--<div class="fe-panel-editor-name">优惠卷选项</div>--}}
    {{--<div class="fe-panel-editor-con">--}}
        {{--<label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_num" value="1" ng-model="Edit.params.num" ng-change="setimg(Edit.id, Edit.params.num, Edit)"> 显示</label>--}}
        {{--<label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_num" value="0" ng-model="Edit.params.num" ng-change="setimg(Edit.id, Edit.params.num, Edit)"> 不显示</label>--}}
    {{--</div>--}}
{{--</div>--}}

{{--<div class="fe-panel-editor-line">--}}
    {{--<div class="fe-panel-editor-name">礼包选项</div>--}}
    {{--<div class="fe-panel-editor-con">--}}
        {{--<label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_num" value="1" ng-model="Edit.params.num" ng-change="setimg(Edit.id, Edit.params.num, Edit)"> 显示</label>--}}
        {{--<label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_num" value="0" ng-model="Edit.params.num" ng-change="setimg(Edit.id, Edit.params.num, Edit)"> 不显示</label>--}}
    {{--</div>--}}
{{--</div>--}}

