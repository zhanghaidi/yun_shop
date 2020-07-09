<div class="fe-mod fe-mod-12" ng-class="{'fe-mod-select':Item.id == focus}"  ng-style="{'background-color':Item.params.bgcolor}">
    <div ng-repeat="menu in Item.data">
        <div class="fe-mod-12-nav" ng-style="{'width':Item.params.style}" ng-show="menu.is_open">
            <div ng-if="menu.text" class="fe-mod-12-text" ng-style="{'color':menu.color,'font-size': Item.params.fontsize, 'font-weight': Item.params.fontweight}">{{menu.text}}</div>
        </div>
    </div>
</div>