<div class="fe-panel-editor-title">限时抢购设置</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">样式选择</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_style" value="100%" ng-model="Edit.params.style" ng-change="changeImg(Edit.id, Edit.params.style)" /> 单排显示</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_style" value="49.5%" ng-model="Edit.params.style" ng-change="changeImg(Edit.id, Edit.params.style)" /> 双排显示</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_style" value="33.3%" ng-model="Edit.params.style" ng-change="changeImg(Edit.id, Edit.params.style)" /> 三排显示</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_style" value="hp" ng-model="Edit.params.style" ng-change="changeImg(Edit.id, Edit.params.style)" /> 横排显示</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">显示标题</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_showtitle" value="0" ng-model="Edit.params.showtitle" /> 不显示</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_showtitle" value="1" ng-model="Edit.params.showtitle" /> 显示</label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.showtitle == 1">
    <div class="fe-panel-editor-name">分组标题</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="搜索框默认提示文字，超出屏幕宽度将自动隐藏" ng-model="Edit.params.title" />
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.showtitle == 1">
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
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_option" value="" ng-model="Edit.params.option" /> 无</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_option" value="sale-tj" ng-model="Edit.params.option" /> 推荐</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_option" value="sale-rx" ng-model="Edit.params.option" /> 热销</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_option" value="sale-xp" ng-model="Edit.params.option" /> 新上</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_option" value="sale-by" ng-model="Edit.params.option" /> 包邮</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_option" value="sale-xs" ng-model="Edit.params.option" /> 限时</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_option" value="sale-cx" ng-model="Edit.params.option" /> 促销</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">购买按钮</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_buysub" value="" ng-model="Edit.params.buysub" /> 不显示</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_buysub" value="buy-5" ng-model="Edit.params.buysub" /> 样式一</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_buysub" value="buy-6" ng-model="Edit.params.buysub" /> 样式二</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_buysub" value="buy-7" ng-model="Edit.params.buysub" /> 样式三</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_buysub" value="buy-8" ng-model="Edit.params.buysub" /> 样式四</label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">商品价格</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_price" value="0" ng-model="Edit.params.price" /> 不显示</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_price" value="1" ng-model="Edit.params.price" /> 原价+现价</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_price" value="2" ng-model="Edit.params.price" /> 只显示现价</label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-hide="Edit.params.style=='hp'">
    <div class="fe-panel-editor-name">商品名称</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_showname" value="0" ng-model="Edit.params.showname" /> 不显示</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="radio" name="@{{Edit.id}}_showname" value="1" ng-model="Edit.params.showname" /> 显示</label>
        <span>Tips:隐藏商品名称将默认隐藏购买按钮</span>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">显示内容</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="checkbox" name="@{{Edit.id}}_countdown" value="1" ng-model="Edit.params.countdown" /> 抢购倒计时</label>
        <label style="cursor:pointer; margin-right: 10px; margin-top: 8px;"><input type="checkbox" name="@{{Edit.id}}_overage" value="1" ng-model="Edit.params.overage" /> 剩余库存</label>
    </div>
</div>
<div ng-repeat="good in Edit.data" class="fe-panel-editor-relative">
    <div class="fe-panel-editor-line2">
        <div class="fe-panel-editor-del" title="移除" ng-click="delFlashsale(Edit.id, good.id)">×</div>
        <div class="fe-panel-editor-goodimg" style="height:120px; width: 120px; position: relative;" ng-click="addFlashsale('replace', Edit.id, good.id)">
            <img ng-src="@{{good.img}}" width="100%" height="100%" />
            <div style="height:24px; width:100%; color:#fff; line-height:24px; font-size:14px; background:rgba(0,0,0,0.4); text-align:center; left:0px; bottom:0px; position: absolute;">重新选择商品</div>
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
        </div>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-sub1" ng-click="addFlashsale('', Edit.id, '')"><i class="fa fa-plus-circle"></i> 添加一个商品</div>
</div>