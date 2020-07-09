<div class="fe-mod fe-mod-8" ng-class="{'fe-mod-select':Item.id == focus}" ng-style="{'background-color':Item.params.bgcolor}">
    <div class="fe-mod-8-title" ng-show="Item.params.showtitle==1" ng-style="{'color':Item.params.titlecolor,'background-color':Item.params.bgcolor}">1</div>
    <div style="line-height: 170px; text-align: center; color: #999; font-size: 16px;" ng-show="Item.data == ''">一个拼团都没有...</div>
    <!-- 默认两种样式 -->
    <div ng-show="Item.params.style != 'hp'" class="new-goods">
        <div ng-repeat="good in Item.data">
            <div class="fe-mod-8-good good-new" ng-style="{'width':Item.params.style}">
                <a href="{{good.url}}">
                    <div class="fe-mod-8-main">
                        <div class="fe-mod-8-main-img">
                            <img ng-src="{{good.goods_img}}" />
                            <div class="saleimg" ng-class="Item.params.option"></div>
                            <div class="countdown" ng-show="Item.params.countdown" ng-if="Item.params.style == '100%'" style="padding-top: 6%;">
                                <span style="font-size: 1.4em;" ng-if="good.status == 0">活动未开始</span>
                                <span style="font-size: 1.4em;" ng-if="good.status == 1">距活动结束还剩：{{interval(good.end_time*1000, good.start_time*1000)}}</span>
                                <span style="font-size: 1.4em;" ng-if="good.status == 2">活动已结束</span>
                            </div>
                            <div class="countdown" ng-show="Item.params.countdown" ng-if="Item.params.style == '49.5%'" style="padding-top: 4%;">
                                <span style="font-size: 1.2em;" ng-if="good.status == 0">活动未开始</span>
                                <span style="font-size: 1.2em;" ng-if="good.status == 1">距活动结束还剩：{{interval(good.end_time*1000, good.start_time*1000)}}</span>
                                <span style="font-size: 1.2em;" ng-if="good.status == 2">活动已结束</span>
                            </div>
                            <div class="countdown" ng-show="Item.params.countdown" ng-if="Item.params.style == '33.3%'" style="padding-top: 2%;">
                                <span style="font-size: 0.9em;" ng-if="good.status == 0">活动未开始</span>
                                <span style="font-size: 0.9em;" ng-if="good.status == 1">距活动结束还剩：{{interval(good.end_time*1000, good.start_time*1000)}}</span>
                                <span style="font-size: 0.9em;" ng-if="good.status == 2">活动已结束</span>
                            </div>
                        </div>
                        <div class="fe-mod-8-main-name" ng-show="Item.params.showname == 1">
                            <div class="fe-mod-8-main-name-name">{{good.goods_title}}</div>
                        </div>
                        <div class="card-price" ng-show="Item.params.price != 0">
                            ￥{{good.min_price}} <span style="text-decoration: line-through; font-size: 12px; color: #808080;" ng-if="Item.params.price == 1 && good.max_price > 0" ng-show="good.max_price != good.min_price">￥{{good.max_price}}</span>
                        </div>
                        <div class="card-overage" ng-if="Item.params.style == '49.5%' || Item.params.style == '100%'">
                            <label ng-show="Item.params.overage" style="margin-top: 10px; font-size: 12.5px;">仅剩<span style="color: red">{{good.member_request}}</span>人成团</label>
                            <div class="add-cart-2" ng-class="Item.params.buysub" ng-show="Item.params.buysub">去开团 ></div>
                        </div>
                        <div class="card-overage-3" ng-if="Item.params.style == '33.3%'">
                            <label ng-show="Item.params.overage" style="margin-top: 10px;">仅剩<span style="color: red">{{good.member_request}}</span>人成团</label>
                            <div class="add-cart-4" ng-class="Item.params.buysub" ng-show="Item.params.buysub">去开团 ></div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <!-- 横幅样式 -->
    <div ng-show="Item.params.style == 'hp'" class="new-goods">
        <div ng-repeat="good in Item.data">
            <a href="{{good.url}}">
                <div class="fe-mod-8-hp-line-2">
                    <div class="fe-mod-8-hp-line-img-2">
                        <div class="saleimg" ng-class="Item.params.option"></div>
                        <img ng-src="{{good.goods_img}}"/>
                        <div class="countdown" ng-show="Item.params.countdown">
                            <span style="font-size: 0.9em;" ng-if="good.status == 0">活动未开始</span>
                            <span style="font-size: 0.9em;" ng-if="good.status == 1">距活动结束还剩：{{interval(good.end_time*1000, good.start_time*1000)}}</span>
                            <span style="font-size: 0.9em;" ng-if="good.status == 2">活动已结束</span>
                        </div>
                    </div>
                    <div class="fe-mod-8-hp-line-info-2">
                        <div class="title">{{good.goods_title}}</div>
                        <div style="position: absolute;" class="price fe-mod-8-main-name" ng-show="Item.params.price != 0">
                            <div class="p1" ng-style="{'color':Item.params.titlecolor}"><span style="color:#ff6600;">￥{{good.min_price}} </span></div>
                            <div class="p2" ng-if="Item.params.price == 1 && good.max_price > 0" ng-show="good.max_price != good.min_price">￥{{good.max_price}} </div>
                        </div>
                        <div style="position: relative; top: 26px;">
                            <label style="line-height: 25px;" ng-show="Item.params.overage">仅剩<span style="color: red">{{good.member_request}}</span>人成团</label>
                            <div class="add-cart-3" ng-show="Item.params.buysub" ng-class="Item.params.buysub">去开团 ></div>
                        </div>
                        <div class="overage">
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

</div>

