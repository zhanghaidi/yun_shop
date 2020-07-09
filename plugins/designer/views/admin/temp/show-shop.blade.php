<!-- 样式1 -->
<div class="fe-mod fe-mod-9" ng-class="{'fe-mod-select':Item.id == focus}" ng-show="Item.params.style == 1">
    <img ng-src="{{Item.params.bgimg}}"/>

    <div class="fe-mod-9-shopname" ng-show="Item.params.name == 1">
        <div class="fe-mod-9-name">{{system[0].shop.name|| 'XX商城'}}</div>
    </div>
    <div class="fe-mod-9-shoplogo" ng-show="Item.params.logo == 1"> 
        <div class="fe-mod-9-shoplogo-img">
            <img ng-src="{{system[0].shop.logo|| inits+'plugins/designer/assets/images/init-data/init-icon.png'}}"/>
        </div>
    </div>
</div>
<!-- 样式2 -->
<div class="fe-mod fe-mod-10" ng-class="{'fe-mod-select':Item.id == focus}" ng-show="Item.params.style == 2">
    <div class="fe-mod-del" ng-click="delItem(Item.id)">移除</div>
    <img ng-src="{{Item.params.bgimg}}"/>
    <div class="fe-mod-10-shoplogo" ng-show="Item.params.logo == 1">
        <img ng-src="{{system[0].shop.logo|| inits+'plugins/designer/assets/images/init-data/init-icon.png'}}"/>
    </div>
    <div class="fe-mod-10-shopname" ng-show="Item.params.name == 1">{{system[0].shop.name|| 'XX商城'}}</div>

</div>
