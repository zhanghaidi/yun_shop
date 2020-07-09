<div class="fe-mod fe-mod-12" ng-class="{'fe-mod-select':Item.id == focus}"  ng-style="{'background-color':Item.params.bgcolor}">
    <div ng-repeat="menu in Item.data" ng-hide="$index == 4 && Item.params.num == '25%'">
        <a href="{{menu.hrefurl|| 'javascript:;'}}">
            <div class="fe-mod-12-nav" ng-style="{'width':Item.params.num}">
                <div class="fe-mod-12-img">
                    <img ng-src="{{menu.imgurl|| inits+'plugins/designer/assets/images/init-data/init-icon.png'}}" ng-style="{'border-radius':Item.params.style}" />
                </div>
                <div ng-if="menu.text" class="fe-mod-12-text" ng-style="{'color':menu.color,'font-size': Item.params.fontsize, 'font-weight': Item.params.fontweight}">{{menu.text|| '按钮文字'}}</div>
            </div>
        </a>
    </div>
</div>