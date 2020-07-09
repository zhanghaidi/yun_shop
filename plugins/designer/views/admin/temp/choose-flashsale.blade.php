<!-- choose good start -->
<div id="floating-flashsale"  class="modal fade" tabindex="-1" style="z-index:99999">
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择开启限时购的商品</h3></div>
            <div class="modal-body" >
                <div class="row" style="padding:0px 15px;">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="select-flashsale" placeholder="请输入商品名称进行查询筛选" />
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" ng-click="selectflashsale(focus);" id="selectflashsale">搜索</button>
                        </span>
                    </div>
                </div>
                <div id="module-menus" style="padding-top:5px; overflow: auto;max-height:500px;">
                    <div ng-repeat="good in selectFlashsale">
                        <div style="height:177px; width:137px; float: left; padding: 5px; margin: 5px; background: #f4f4f4; margin-top: 5px;" ng-click="pushFlashsale(focus, good.id)">
                            <div style="height: 127px; width: 127px; background: #eee; float: left; position: relative; cursor: pointer;">
                                <img ng-src="@{{good.img}}" width="100%" height="100%" />
                                <div style="height: 24px; width: 127px; background: rgba(0,0,0,0.3); position: absolute; bottom:0px; left: 0px; color:#fff; font-size: 12px; line-height: 24px;">￥@{{good.pricenow}}<span style="text-decoration: line-through; margin-left:4px;">￥@{{good.priceold}}</span></div>
                            </div>
                            <div style="height: 40px; width: 127px; font-size: 13px; line-height: 20px; text-align: center; overflow: hidden;">@{{good.name}}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
        </div>
    </div>
</div>
<!-- choose good end -->