<div class="fe-panel-editor-title">商品设置</div>
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
    <div class="fe-panel-editor-name">是否显示距离</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_distance" value="0" ng-model="Edit.params.distance" /> 不显示
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_distance" value="1" ng-model="Edit.params.distance" /> 显示
        </label>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">每页显示商品数量</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;">
            <input style="width:100%" class="fe-panel-editor-input1" type="text" name="@{{Edit.id}}_displaynum" value="0" ng-model="Edit.params.displaynum" />
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

