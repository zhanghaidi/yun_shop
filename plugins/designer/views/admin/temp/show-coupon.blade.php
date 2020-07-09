<style>
    .coupon {
        position: relative;height: 120px;text-align: left;display:flex;display: -webkit-flex;align-items:center;width: 1000px;
    }
    .coupon_red {
        width: 100px;height: 90px;margin:0 3px;text-align: center;color: #ffffff;float: left;
    }
    .coupon_gray {
        background: gray;width: 100px;height: 90px;margin:0 3px;text-align: center;color: #ffffff;float: left;
    }
</style>

<div class="fe-mod fe-mod-12" ng-class="{'fe-mod-select':Item.id == focus}" ng-style="{'background-color':Item.params.bgcolor}">
    <div class="coupon" ng-show="Item.params.addmethod == 1">
        <div style="line-height: 40px; text-align: center; color: #999; font-size: 16px;" ng-show="Item.data == ''">一张优惠券都没有...</div>
        <div ng-repeat="coupon in Item.data" ng-show="Item.params.hidemethod == 1">
            <div class="coupon_red" ng-show="coupon.lasttotal != 0" ng-style="{'background':Item.params.couponcolor}">
                <br>
                <p ng-show="coupon.coupon_method == 1"><span style="font-size: 2em">{{coupon.deduct}}</span> 元</p>
                <p ng-show="coupon.coupon_method == 2"><span style="font-size: 2em">{{coupon.discount}}</span>折</p>
                <p ng-show="coupon.enough > 0">满{{coupon.enough}}可用</p>
                <p ng-show="coupon.enough <= 0">无门槛</p>
            </div>
        </div>
        <div ng-repeat="coupon in Item.data" ng-show="Item.params.hidemethod == 0">
            <div class="coupon_red" ng-show="coupon.lasttotal != 0" ng-style="{'background':Item.params.couponcolor}">
                <br>
                <p ng-show="coupon.coupon_method == 1"><span style="font-size: 2em">{{coupon.deduct}}</span> 元</p>
                <p ng-show="coupon.coupon_method == 2"><span style="font-size: 2em">{{coupon.discount}}</span>折</p>
                <p ng-show="coupon.enough > 0">满{{coupon.enough}}可用</p>
                <p ng-show="coupon.enough <= 0">无门槛</p>
            </div>
            <div class="coupon_gray" ng-show="coupon.lasttotal == 0">
                <br>
                <p ng-show="coupon.coupon_method == 1"><span style="font-size: 2em">{{coupon.deduct}}</span> 元</p>
                <p ng-show="coupon.coupon_method == 2"><span style="font-size: 2em">{{coupon.discount}}</span>折</p>
                <p ng-show="coupon.enough > 0">满{{coupon.enough}}可用</p>
                <p ng-show="coupon.enough <= 0">无门槛</p>
            </div>
        </div>
    </div>
    <div class="coupon" ng-show="Item.params.addmethod == 0">
        <div ng-repeat="coupon in Item.alldata|limitTo:Item.params.shownum" ng-show="Item.params.hidemethod == 1">
            <div class="coupon_red" ng-show="coupon.lasttotal != 0" ng-style="{'background':Item.params.couponcolor}">
                <br>
                <p ng-show="coupon.coupon_method == 1"><span style="font-size: 2em">{{coupon.deduct}}</span> 元</p>
                <p ng-show="coupon.coupon_method == 2"><span style="font-size: 2em">{{coupon.discount}}</span>折</p>
                <p ng-show="coupon.enough > 0">满{{coupon.enough}}可用</p>
                <p ng-show="coupon.enough <= 0">无门槛</p>
            </div>
        </div>
        <div ng-repeat="coupon in Item.alldata|limitTo:Item.params.shownum" ng-show="Item.params.hidemethod == 0">
            <div class="coupon_red" ng-show="coupon.lasttotal != 0" ng-style="{'background':Item.params.couponcolor}">
                <br>
                <p ng-show="coupon.coupon_method == 1"><span style="font-size: 2em">{{coupon.deduct}}</span> 元</p>
                <p ng-show="coupon.coupon_method == 2"><span style="font-size: 2em">{{coupon.discount}}</span>折</p>
                <p ng-show="coupon.enough > 0">满{{coupon.enough}}可用</p>
                <p ng-show="coupon.enough <= 0">无门槛</p>
            </div>
            <div class="coupon_gray" ng-show="coupon.lasttotal == 0">
                <br>
                <p ng-show="coupon.coupon_method == 1"><span style="font-size: 2em">{{coupon.deduct}}</span> 元</p>
                <p ng-show="coupon.coupon_method == 2"><span style="font-size: 2em">{{coupon.discount}}</span>折</p>
                <p ng-show="coupon.enough > 0">满{{coupon.enough}}可用</p>
                <p ng-show="coupon.enough <= 0">无门槛</p>
            </div>
        </div>
    </div>
</div>

