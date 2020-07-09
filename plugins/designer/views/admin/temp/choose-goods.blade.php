<-- choose good start -->
<div id="floating-good" class="modal fade" tabindex="-1" style="z-index:99999">
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h3>选择商品</h3></div>
            <div class="modal-body">
                <div class="row" style="padding:0px 15px;">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="select-kw"
                               placeholder="请输入商品名称进行查询筛选"/>
                        <span class='input-group-btn'><button type="button" class="btn btn-default"
                                                              ng-click="selectgood(focus);" id="selectgood">搜索</button></span>
                    </div>
                </div>
                <div id="module-menus" style="padding-top:5px; overflow: auto;max-height:500px;">
                    <div ng-repeat="good in selectGoods">
                        <div style="height:177px; width:137px; float: left; padding: 5px; margin: 5px; background: #f4f4f4; margin-top: 5px;"
                             ng-click="pushGood(focus, good.id)">
                            <div style="height: 127px; width: 127px; background: #eee; float: left; position: relative; cursor: pointer;">
                                <img ng-src="@{{good.img}}" width="100%" height="100%"/>
                                <div style="height: 24px; width: 127px; background: rgba(0,0,0,0.3); position: absolute; bottom:0px; left: 0px; color:#fff; font-size: 12px; line-height: 24px;">
                                    ￥@{{good.pricenow}}<span style="text-decoration: line-through; margin-left:4px;">￥@{{good.priceold}}</span>
                                </div>
                            </div>
                            <div style="height: 40px; width: 127px; font-size: 13px; line-height: 20px; text-align: center; overflow: hidden;">
                                @{{good.name}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>
    </div>
</div>
<!-- choose good end -->

<!-- choose category start -->
<div id="floating-category" class="modal fade" tabindex="-1" style="z-index:99999">
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h3>选择分类</h3></div>
            <div class="modal-body">
                <!-- <div class="row" style="padding:0px 15px;">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="select-category-kw" placeholder="请输入分类名称进行查询筛选" />
                        <span class='input-group-btn'><button type="button" class="btn btn-default" ng-click="selectcategory(focus)" id="selectgood">搜索</button></span>
                    </div>
                </div> -->
                <div id="module-menus" style="padding-top:5px; overflow: auto;max-height:500px;">

                    <div role="tabpanel" class="tab-pane link_cate">
                        {!! app\common\helpers\CategoryHelper::tplGoodsCategoryShow() !!}
                    </div>


                </div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>
    </div>
</div>

<!-- choose category end -->

<!-- choose category start -->
<div id="floating-label" class="modal fade" tabindex="-1" style="z-index:99999">
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h3>选择标签</h3></div>
            <div class="modal-body">
                <!-- <div class="row" style="padding:0px 15px;">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="select-category-kw" placeholder="请输入标签名称进行查询筛选" />
                        <span class='input-group-btn'><button type="button" class="btn btn-default" ng-click="selectcategory(focus)" id="selectgood">搜索</button></span>
                    </div>
                </div> -->
                <div id="module-menus" style="padding-top:5px; overflow: auto;max-height:500px;">
                    <div role="tabpanel" class="tab-pane link_cate">
                        <?php $all_filtering = \app\common\models\SearchFiltering::getAllFiltering(); ?>
                        <div class="mylink-con">
                            @if (!is_null($all_filtering))
                                @foreach ($all_filtering as $group)
                                    <div class="mylink-line">
                                    {{ $group['name'] }}
                                    <!-- <div class="mylink-sub">
                                                <a href="javascript:;" id="" class="" ng-click="">选择</a>
                                            </div> -->
                                    </div>

                                    @if (!is_null($group['value']))
                                        @foreach ($group['value'] as $value)
                                            <div class="mylink-line">
                                                <span style='height:10px; width: 10px; margin-left: 10px; margin-right: 10px; display:inline-block; border-bottom: 1px dashed #ddd; border-left: 1px dashed #ddd;'></span>
                                                {{ $value['name'] }}
                                                <div class="mylink-sub">
                                                    <a href="javascript:;" class="mylink-nav"
                                                       ng-click="selectSearchGoods(focus,'{{ $value['id'] }}')">选择</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>
    </div>
</div>


<!-- choose category end -->
