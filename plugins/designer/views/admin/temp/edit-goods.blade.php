<div class="fe-panel-editor-title">商品设置<span class="tips">提示: 商品组的图标可通过替换目录文件自定义</span></div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">样式选择</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="100%" ng-model="Edit.params.style" ng-change="changeImg(Edit.id, Edit.params.style)" />
            单排显示
        </label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="49.5%" ng-model="Edit.params.style" ng-change="changeImg(Edit.id, Edit.params.style)" />
            双排显示
        </label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="33.3%" ng-model="Edit.params.style" ng-change="changeImg(Edit.id, Edit.params.style)" />
            三排显示
        </label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_style" value="hp" ng-model="Edit.params.style" ng-change="changeImg(Edit.id, Edit.params.style)" />
            横排显示
        </label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">显示标题</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_showtitle" value="0" ng-model="Edit.params.showtitle" /> 显示
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_showtitle" value="1" ng-model="Edit.params.showtitle" /> 不显示
        </label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.showtitle == 0">
    <div class="fe-panel-editor-name">分组标题</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="搜索框默认提示文字，超出屏幕宽度将自动隐藏" ng-model="Edit.params.title" />
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.showtitle == 0">
    <div class="fe-panel-editor-name">标题颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.titlecolor">
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.bgcolor">
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">商品属性</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_option" value="" ng-model="Edit.params.option" /> 无
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_option" value="sale-tj" ng-model="Edit.params.option" /> 推荐
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_option" value="sale-rx" ng-model="Edit.params.option" /> 热销
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_option" value="sale-xp" ng-model="Edit.params.option" /> 新上
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_option" value="sale-by" ng-model="Edit.params.option" /> 包邮
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_option" value="sale-xs" ng-model="Edit.params.option" /> 限时
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_option" value="sale-cx" ng-model="Edit.params.option" /> 促销
        </label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.showname == 1">
    <div class="fe-panel-editor-name">购买按钮</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_buysub" value="" ng-model="Edit.params.buysub" /> 不显示
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_buysub" value="buy-1" ng-model="Edit.params.buysub" /> 样式一
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_buysub" value="buy-2" ng-model="Edit.params.buysub" /> 样式二
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_buysub" value="buy-3" ng-model="Edit.params.buysub" /> 样式三
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_buysub" value="buy-4" ng-model="Edit.params.buysub" /> 样式四
        </label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">商品价格</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_price" value="0" ng-model="Edit.params.price" /> 不显示
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_price" value="1" ng-model="Edit.params.price" /> 原价+现价
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_price" value="2" ng-model="Edit.params.price" /> 只显示现价
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_price" value="3" ng-model="Edit.params.price" /> 原价+现价+会员价
        </label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-hide="Edit.params.style=='hp'">
    <div class="fe-panel-editor-name">商品名称</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_showname" value="0" ng-model="Edit.params.showname" /> 不显示
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_showname" value="1" ng-model="Edit.params.showname" /> 显示
        </label>
        <span>Tips:隐藏商品名称将默认隐藏购买按钮</span>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">显示商品数量</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;">
            <input style="width:100%" class="fe-panel-editor-input1" type="text" name="@{{Edit.id}}_displaynum" value="0" ng-model="Edit.params.displaynum" />
        </label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">选择商品</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" ng-checked="true" name="@{{Edit.id}}_search" value="0" ng-model="Edit.params.search" /> 单个选择
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_search" value="1" ng-model="Edit.params.search" /> 选择分类
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_search" value="2" ng-model="Edit.params.search" /> 选择标签
        </label>
    </div>
</div>
@if(1 == $love && 1 == $love_set['award'])
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">营销活动</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_love" value="0" ng-model="Edit.params.love" /> 不显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_love" value="1" ng-model="Edit.params.love" /> {{ $love_set['name'] ?: "爱心值"}}</label>
    </div>
</div>
@endif

<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">失效商品</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_lowershelf" value="1" ng-model="Edit.params.lowershelf" /> 显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_lowershelf" value="0" ng-model="Edit.params.lowershelf" /> 隐藏</label>
    </div>
</div>

<div id="board" ng-controller="MainCtrl" data-ng-model="Edit.data" as-sortable="dragControlListeners">
    <div ng-repeat="good in Edit.data" class="fe-panel-editor-relative" as-sortable-item>
        <div class="fe-panel-editor-line2" as-sortable-item-handle>
            <div class="fe-panel-editor-del" title="移除" ng-click="delGood(Edit.id, good.id)">×</div>
            <div class="fe-panel-editor-goodimg" style="height:120px; width: 120px; position: relative;" ng-click="addGood('replace', Edit.id, good.id)">
                <img ng-src="@{{good.img}}" width="100%" height="100%" />
                <div style="height:24px; width:100%; color:#fff; line-height:24px; font-size:14px; background:rgba(0,0,0,0.4); text-align:center; left:0px; bottom:0px; position: absolute;">
                    重新选择商品
                </div>
            </div>
            <div class="fe-panel-editor-line2-right">
                <div class="fe-panel-editor-line">
                    <div class="fe-panel-editor-name2">商品名称</div>
                    <div class="fe-panel-editor-con1">@{{good.name}}</div>
                </div>
                <div class="fe-panel-editor-line">
                    <div class="fe-panel-editor-name2">商品价格</div>
                    <div class="fe-panel-editor-con"><span style="font-size: 16px;">￥@{{good.pricenow}}</span> <span style="text-decoration: line-through;">￥@{{good.priceold}}</span></div>
                </div>
                @if(1 == $love && 1 == $love_set['award'])
                <div ng-show="good.award == '1'" class="fe-panel-editor-line">
                    <div ng-show="Edit.params.love == '1'">
                        <div class="fe-panel-editor-name2">{{$love_set['name']}}</div>
                        <div class="fe-panel-editor-con1">@{{good.award_proportion}}%</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="fe-panel-editor-line">
    <div ng-show="Edit.params.search
        != '1' && Edit.params.search 
        != '2'" class="fe-panel-editor-sub1" ng-click="addGood('', Edit.id, '')"><i class="fa fa-plus-circle"></i>
        添加一个商品
    </div>
    <div ng-show="Edit.params.search == '1'" class="fe-panel-editor-sub1" ng-click="addCategory(Edit.params.displaynum, Edit.id)"><i class="fa fa-plus-circle"></i> 添加分类商品
    </div>
    <div ng-show="Edit.params.search == '2'" class="fe-panel-editor-sub1" ng-click="addLabel(Edit.params.displaynum, Edit.id)"><i class="fa fa-plus-circle"></i> 添加标签商品
    </div>
</div>