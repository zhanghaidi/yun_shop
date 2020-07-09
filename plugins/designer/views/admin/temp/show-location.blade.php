<div class="search" ng-class="{'fe-mod-select':Item.id == focus}" ng-style="{'background-color':Item.params.bgcolor,'border-color':Item.params.bordercolor}" id="d1">
    <div class="location"  ng-style="{'color':Item.params.color2}">
        <i class="fa fa-map-marker" style="margin-right:2%"></i>城市<i class="fa fa-chevron-down" style="margin-left:2%"></i>
    </div>
    <div class="search-form-box" ng-style="{'background-color':Item.params.bgcolor,'color':Item.params.color}">
        <i class="fa fa-search"></i> {{Item.params.placeholder || '附近商家商品'}}
    </div>
</div>