<!-- 样式1 -->
<div class="fe-mod fe-mod-9" ng-class="{'fe-mod-select':Item.id == focus}">
    <img ng-src="{{Item.params.bgimg}}"/>

    <div class="fe-mod-9-shopname" ng-show="Item.params.award == 1">
        <div class="fe-mod-9-name">'今日{{navSignName}}奖励：积分 +10.00 优惠卷：3张'</div>
    </div>
    <div class="fe-mod-9-shoplogo" ng-style="{'background-color':Item.params.bgcolor}">
        <br>
        <div ng-if="Item.params.text" class="fe-mod-12-text" ng-style="{'color':Item.params.textcolor,'font-size': Item.params.fontsize, 'font-weight': Item.params.fontweight}">{{Item.params.text|| '签到'}}</div>
        <div class="fe-mod-12-text">连续N天</div>
    </div>
</div>

