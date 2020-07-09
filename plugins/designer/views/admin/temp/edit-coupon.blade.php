<div class="fe-panel-editor-title">文章设置</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.bgcolor"></div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">优惠券颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.couponcolor"></div>
</div>
{{--<div class="fe-panel-editor-line">--}}
    {{--<div class="fe-panel-editor-name">隐藏已抢完券</div>--}}
    {{--<div class="fe-panel-editor-con">--}}
        {{--<label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_hidemethod" value="1" ng-model="Edit.params.hidemethod" /> 是</label>--}}
        {{--<label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_hidemethod" value="0" ng-model="Edit.params.hidemethod" /> 否</label>--}}
    {{--</div>--}}
{{--</div>--}}
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">添加方式</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_addmethod" value="0" ng-model="Edit.params.addmethod" ng-click="pushAllCoupon(Edit.id)"/> 自动获取</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_addmethod" value="1" ng-model="Edit.params.addmethod" /> 手动获取</label>
    </div>
</div>
<div ng-show="Edit.params.addmethod == 0" class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">优惠券显示数量</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input3" placeholder="" ng-model="Edit.params.shownum" /><br>
        {{--<span style="font-size: 12px; margin-left: 10px;">系统自动获取文章，按照文章ID大小排列显示</span>--}}
    </div>
</div>
<div ng-show="Edit.params.addmethod == 1">
    <div ng-repeat="coupon in Edit.data" class="fe-panel-editor-relative" >
        <div class="fe-panel-editor-line2">
            <div class="fe-panel-editor-del" title="移除" ng-click="delCoupon(Edit.id, coupon.id)">×</div>
            <div class="fe-panel-editor-line2-right">
                <div class="fe-panel-editor-line">
                    <div class="fe-panel-editor-name2">优惠券：</div>
                    <div class="fe-panel-editor-con1">
                        @{{coupon.name}}(
                        <label ng-show="coupon.enough > 0">满@{{coupon.enough}}可用</label>
                        <label ng-show="coupon.enough <= 0">无门槛</label>
                        <label ng-show="coupon.coupon_method == 1">立减 @{{coupon.deduct}} 元</label>
                        <label ng-show="coupon.coupon_method == 2">打 @{{coupon.discount}} 折</label>)
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="fe-panel-editor-sub1" ng-click="addCoupon('', Edit.id, '')"><i class="fa fa-plus-circle"></i> 选择优惠券</div>
</div>
