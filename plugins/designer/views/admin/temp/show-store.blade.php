<div class="store-list" ng-repeat="d in Item.data" ng-style="{'background-color':Item.params.bgcolor}">
    <div class="store-img">
        <a class="store-logo" >
            <img ng-src="{{d.imgurl}}" ng-show="d.imgurl">
        </a>
        <div class="store-category" >
            <span class="da" ng-style="{'color':Item.params.catecolor}">门店分类</span>
        </div>
    </div>
    <div class="store-intro">
        <div class="a8q ">
            <h2 class="" ng-style="{'color':Item.params.namecolor}">门店名称</h2>

        </div>
        <div class="a06 a8r">
            <strong class="i8">
                <span class="da" ng-style="{'color':Item.params.shipcolor}">支持快递</span>
            </strong>
        </div>
        <div class="a8v">
            <p class="a8d">
                <span class="a79 clearfix"><i></i><i></i><i></i><i></i><i class="zo"></i></span>
            </p>
            <p class="a8w" ng-show="Item.params.showsale == 1">
                <span class="line_split"></span>月售101单
            </p>
        </div>
        <div class="a8v" ng-show="Item.params.showscore == 1">
            <p class="a8w" ng-style="{'color':Item.params.salecolor}">
                <span class=""></span>赠送N%积分，最高抵扣M%
            </p>
        </div>
        <div class="a8v" ng-show="Item.params.showlove == 1">
            <p class="a8w" ng-style="{'color':Item.params.salecolor}">
                <span class=""></span>赠送N%爱心值，最高抵扣M%
            </p>
        </div>
    </div>
    <div class="store_honor_box">
        <div class="store_honor_icon">
            <i class="fa fa-phone" aria-hidden="true" ng-style="{'color':Item.params.telcolor}" style="font-size: 16px;margin-right: 15px;"></i>
            <i class="fa fa-location-arrow" aria-hidden="true"  ng-style="{'color':Item.params.navcolor}" style="font-size: 16px;"></i>
        </div>
        <span class="store_honor_icon"  ng-style="{'color':Item.params.discolor}" style="font-size: 12px;">距离 xxxkm</span>
    </div>
</div>