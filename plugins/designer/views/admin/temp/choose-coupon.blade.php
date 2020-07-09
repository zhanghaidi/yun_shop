<!-- choose good start -->
<style>
    .article-table {
        width: 840px;
        margin: auto;
    }
    .article-table th{
        text-align: center;
        background-color: #eeeeee;
        height: 40px;
    }
    .article-table td{
        text-align: center;
        border-bottom: 1px #eeeeee solid;
        height: 60px;
        table-layout:fixed;WORD-BREAK:break-all;WORD-WRAP:break-word;
    }
</style>
<div id="floating-coupon"  class="modal fade" tabindex="-1" style="z-index:99999">
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择优惠券</h3></div>
            <div class="modal-body" >
                <div class="row" style="padding:0px 15px;">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="select-coupon" placeholder="请输入优惠券名称进行查询筛选" />
                        <span class='input-group-btn'><button type="button" class="btn btn-default" ng-click="selectcoupon(focus);" id="selectcoupon">搜索</button></span>
                    </div>
                </div>
                <div id="module-menus" style="padding-top:5px; overflow: auto;max-height:500px;">
                    <table class="article-table">
                        <thead>
                        <tr>
                            <th style="width: 3%">选择</th>
                            <th width="6%">优惠券名称</th>
                            <th width="12%">使用条件/优惠</th>
                            <th width="10%">已使用/已发出/剩余数量</th>
                            <th width="15%">创建时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="coupon in selectCoupon" >
                            <td><input type="checkbox" ng-click="pushCoupon(focus, coupon.id,$event)"></td>
                            <td>@{{coupon.name}}</td>
                            <td>
                                <label class="label label-danger" ng-show="coupon.enough > 0">满@{{coupon.enough}}可用</label>
                                <label class="label label-warning" ng-show="coupon.enough <= 0">无门槛</label>
                                <br/>
                                <label ng-show="coupon.coupon_method == 1">立减 @{{coupon.deduct}} 元</label>
                                <label ng-show="coupon.coupon_method == 2">打 @{{coupon.discount}} 折</label>
                            </td>
                            <td>@{{coupon.usetotal}} / @{{coupon.gettotal}} / @{{coupon.lasttotal}}</td>
                            <td>@{{coupon.time}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
        </div>
    </div>
</div>
<!-- choose good end -->