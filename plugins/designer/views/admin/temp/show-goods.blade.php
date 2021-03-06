<div class="fe-mod fe-mod-8" ng-class="{'fe-mod-select':Item.id == focus}" ng-style="{'background-color':Item.params.bgcolor}">
    <div class="fe-mod-8-title" ng-show="Item.params.showtitle == 0" ng-style="{'color':Item.params.titlecolor,'background-color':Item.params.bgcolor}">{{Item.params.title|| '请填写商品组标题'}}</div>
    <div style="line-height: 170px; text-align: center; color: #999; font-size: 16px;" ng-show="Item.data == ''">
        一个商品都没有...
    </div>
    <!-- 默认两种样式 -->
    <div ng-show="Item.params.style != 'hp'" class="new-goods">
        <div ng-repeat="good in Item.data">
            <div class="fe-mod-8-good good-new" ng-style="{'width':Item.params.style}">
                <a href="{{Item.params.goodhref}}&id={{good.goodid}}">
                    <div class="fe-mod-8-main">
                        <div class="fe-mod-8-main-img">
                            <img ng-src="{{good.img}}" class="goodimg"/>
                            <div class="saleimg" ng-class="Item.params.option"></div>
                        </div>
                        <div class="fe-mod-8-main-name" ng-show="Item.params.showname == 1">
                            <div class="fe-mod-8-main-name-name">{{good.name}}</div>
                        </div>
                        <div class="card-price" ng-show="Item.params.price != 0">
                            ￥{{good.pricenow}}
                            <span style="text-decoration: line-through; font-size: 12px; color: #808080;" ng-show="(Item.params.price == 1 || Item.params.price == 3) && good.priceold!=good.pricenow && good.priceold > 0">
                                ￥{{good.priceold}}
                            </span>

                            <div class="fe-mod-8-main-name-buy add-cart" ng-class="Item.params.buysub" ng-show="Item.params.buysub"></div>
                        </div>
                        <div ng-show="Item.params.love == '1'">
                            <div ng-show="good.award == '1'" class="card-price" style="font-size: 12px">
                               赠送{{good.award_proportion}}%{{good.love_name}}
                            </div>
                        </div>
                        <div class="card-price" ng-show="Item.params.price == 3">
                            会员价：(前端运算)
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <!-- 横幅样式 -->
    <div ng-show="Item.params.style == 'hp'" class="new-goods">
        <div ng-repeat="good in Item.data">
            <a href="{{Item.params.goodhref}}&id={{good.goodid}}">
                <div class="fe-mod-8-hp-line">
                    <div class="fe-mod-8-hp-line-img">
                        <div class="saleimg" ng-class="Item.params.option"></div>
                        <img ng-src="{{good.img}}"/>
                    </div>
                    <div class="fe-mod-8-hp-line-info">
                        <div class="title">{{good.name}}</div>
                        <div class="price fe-mod-8-main-name">
                            <div ng-show="Item.params.price != 0">
                                <div class="p1" ng-style="{'color':Item.params.titlecolor}">
                                    ￥{{good.pricenow}}
                                </div>
                                <div class="p2" ng-show="Item.params.price == 1 || Item.params.price == 3" ng-hide="Item.params.price == 1 && good.pricenow == good.priceold">
                                    ￥{{good.priceold}}
                                </div>
                                <div ng-show="Item.params.love == '1'">
                                    <div ng-show="good.award == '1'" class="p3">
                                        赠送{{good.award_proportion}}%{{good.love_name}}
                                    </div>
                                </div>
                                <div class="p3" ng-style="{'color':Item.params.titlecolor}" ng-show="Item.params.price == 3">
                                    会员价：(前端运算)
                                </div>
                            </div>
                            <div class="p3" ng-show="good.sales">销量:{{good.sales}}{{good.unit}}</div>
                            <div class="fe-mod-8-main-name-buy  buy-1" style="margin-top: 6px;" ng-show="Item.params.buysub" ng-class="Item.params.buysub"></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
