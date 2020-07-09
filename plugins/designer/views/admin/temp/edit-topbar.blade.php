<div class="fe-panel-editor-title">页面信息设置<span style="font-size: 12px; margin-left: 10px;"></span></div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">页面标题</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="页面标题，手机端的页面标题，空则使用系统默认" ng-model="Edit.params.title" />
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">页面描述</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="页面描述，手机端分享时显示，空则使用系统默认" ng-model="Edit.params.desc" />
    </div>
</div>
{{--<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">触发关键字</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1 keyword" placeholder="触发关键字" ng-model="Edit.params.kw" ng-change="keyword(Edit.params.kw, Edit.id)" />
    </div>
</div>--}}
<?php if ($type != 9) {?>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">封面图片</div>
    <div class="fe-panel-editor-con">
        <div class="fe-panel-editor-upload" ng-click="pageImg(Edit.id, 'img')" style="height:120px; width: 120px;">
            <img ng-src="@{{Edit.params.img}}" width="100%;" height="100%" ng-show="Edit.params.img" />
            <div class="fe-panel-editor-upload-choose2" ng-show="Edit.params.img">重新选择封面图</div>
            <div class="fe-panel-editor-upload-choose1" ng-show="!Edit.params.img" style="line-height:116px;"><i class="fa fa-plus-circle"></i> 选择图片</div>
        </div>
    </div>
</div>
<div class="fe-panel-editor-title" style="margin-top: 15px;">页面功能开关</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">悬浮按钮</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_floatico" value="0" ng-model="Edit.params.floatico" /> 不显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_floatico" value="1" ng-model="Edit.params.floatico" /> 显示</label>
        <span style="font-size: 12px; margin-left: 10px;">提示:在线客服推荐使用<a href="http://qiao.baidu.com" target="_blank">百度商桥</a>可完美接入</span>
    </div>
</div>
<div class="fe-panel-editor-title" ng-show="Edit.params.floatico == 1">悬浮按钮设置</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.floatico == 1">
    <div class="fe-panel-editor-name">图标位置</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_floatstyle" value="left" ng-model="Edit.params.floatstyle" /> 居左</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_floatstyle" value="right" ng-model="Edit.params.floatstyle" /> 居右</label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.floatico == 1">
    <div class="fe-panel-editor-name">图标宽度</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_floatwidth" value="30px" ng-model="Edit.params.floatwidth" /> 30像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_floatwidth" value="40px" ng-model="Edit.params.floatwidth" /> 40像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_floatwidth" value="50px" ng-model="Edit.params.floatwidth" /> 50像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_floatwidth" value="60px" ng-model="Edit.params.floatwidth" /> 60像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_floatwidth" value="80px" ng-model="Edit.params.floatwidth" /> 80像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" value="" ng-model="Edit.params.floatwidth" /></label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.floatico == 1">
    <div class="fe-panel-editor-name">顶部间距</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_floattop" value="100px" ng-model="Edit.params.floattop" /> 100像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_floattop" value="120px" ng-model="Edit.params.floattop" /> 120像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="{edit.id}_floattop" value="150px" ng-model="Edit.params.floattop" /> 150像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="{edit.id}_floattop" value="180px" ng-model="Edit.params.floattop" /> 180像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" value="" ng-model="Edit.params.floattop" /></label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.floatico == 1">
    <div class="fe-panel-editor-name">目标链接</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="{{ $type == 9 ? '请输入小程序路径或选择链接' : '请输入https://开头链接或选择系统链接'}}" ng-model="Edit.params.floathref" />
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.floatico == 1">
    <div class="fe-panel-editor-name">图标图片</div>
    <div class="fe-panel-editor-con">
        <div class="fe-panel-editor-upload" ng-click="pageImg(Edit.id, 'floatimg')" style="min-height:120px; width: 50px;">
            <img ng-src="@{{Edit.params.floatimg}}" width="100%;" ng-show="Edit.params.floatimg" />
            <div class="fe-panel-editor-upload-choose2" ng-show="Edit.params.floatimg">重选</div>
            <div class="fe-panel-editor-upload-choose1" ng-show="!Edit.params.floatimg" style="line-height:116px;"><i class="fa fa-plus-circle"></i></div>
        </div>
    </div>
</div>

<div class="fe-panel-editor-line" >
    <div class="fe-panel-editor-name" style="padding-top:10px">顶部菜单</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;line-height:40px">
            <input type="radio" name="@{{Edit.id}}_top_menu" value="0" ng-model="Edit.params.top_menu" />
            不显示
        </label>
        <span ng-if="topMenuList.length > 0">
            <label style="cursor:pointer; margin-right: 10px;line-height:40px">
                <input type="radio" name="@{{Edit.id}}_top_menu" value="1" ng-model="Edit.params.top_menu" /> 
                显示
            </label>
            <select ng-show="Edit.params.top_menu==1" name="@{{Edit.id}}_top_menu_id"  class="fe-panel-editor-input1" ng-model="Edit.params.top_menu_id" style="width:200px;">
            <option ng-repeat="item in topMenuList" value="@{{item.id}}" ng-selected="Edit.params.top_menu_id == item.id">
                @{{item.menu_name}}
            </option>

            </select>
        </span>
    </div>
</div>

<div class="fe-panel-editor-title" ng-show="Edit.params.guide == 1">关注按钮设置</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.guide == 1">
    <div class="fe-panel-editor-name">默认标题1</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="默认标题1，用户访问商城首页或者邀请人不存在的时候显示" ng-model="Edit.params.guidetitle1" />
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.guide == 1">
    <div class="fe-panel-editor-name">默认标题2</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="默认标题2，用户访问商城首页或者推荐人不存在的时候显示" ng-model="Edit.params.guidetitle2" />
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.guide == 1">
    <div class="fe-panel-editor-name">邀请标题1</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="邀请标题1，用户被邀请进入商城时显示，可调用变量 [邀请人]、[访问者]" ng-model="Edit.params.guidetitle1s" />
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.guide == 1">
    <div class="fe-panel-editor-name">邀请标题2</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="邀请标题2，用户被邀请进入商城时显示，可调用变量 [邀请人]、[访问者]" ng-model="Edit.params.guidetitle2s" />
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.guide == 1">
    <div class="fe-panel-editor-name">按钮文字</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input1" placeholder="按钮文字" ng-model="Edit.params.guidesub" />
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.guide == 1">
    <div class="fe-panel-editor-name">标题大小</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_guidesize" value="8px" ng-model="Edit.params.guidesize" /> 8像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_guidesize" value="10px" ng-model="Edit.params.guidesize" /> 10像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_guidesize" value="12px" ng-model="Edit.params.guidesize" /> 12像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_guidesize" value="14px" ng-model="Edit.params.guidesize" /> 14像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" value="" ng-model="Edit.params.guidesize" /></label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.guide == 1">
    <div class="fe-panel-editor-name">透明度</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="text" ng-model="Edit.params.guideopacity">
        <span class="tips">例:0.8 (请填写0-1之间的数字) 建议填写0.9</span>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.guide == 1">
    <div class="fe-panel-editor-name">标题颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.guidecolor">
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.guide == 1">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.guidebgcolor">
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.guide == 1">
    <div class="fe-panel-editor-name">按钮背景颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.guidenavbgcolor">
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.guide == 1">
    <div class="fe-panel-editor-name">按钮文字颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.guidenavcolor">
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.guide == 1">
    <div class="fe-panel-editor-name">头像样式</div>
    <div class="fe-panel-editor-con">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_guidefacestyle" value="0px" ng-model="Edit.params.guidefacestyle" /> 正方形</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_guidefacestyle" value="40px" ng-model="Edit.params.guidefacestyle" /> 正圆形</label>
    </div>
</div>
<?php } else {?>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">背景颜色</div>
    <div class="fe-panel-editor-con">
        <input class="fe-panel-editor-input2" type="color" ng-model="Edit.params.bgcolor" />
        <span class="tips">提示: 小程序中显示的是完整的背景,预览不完整</span>

    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">文字颜色</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" value="#ffffff" ng-model="Edit.params.color" /> 白色</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" value="#000000" ng-model="Edit.params.color" /> 黑色</label>
        <span class="tips">提示: 小程序前景颜色仅支持黑色或白色</span>
    </div>
</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">客服按钮</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_kefu" value="1" ng-model="Edit.params.kefu" /> 显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_kefu" value="0" ng-model="Edit.params.kefu" /> 不显示</label>
    </div>
</div>
<div class="fe-panel-editor-title" ng-show="Edit.params.kefu == 1">客服按钮设置</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.kefu == 1">
    <div class="fe-panel-editor-name">图标位置</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_kefustyle" value="left" ng-model="Edit.params.kefustyle" /> 居左</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_kefustyle" value="right" ng-model="Edit.params.kefustyle" /> 居右</label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.kefu == 1">
    <div class="fe-panel-editor-name">图标宽度</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_kefuwidth" value="30px" ng-model="Edit.params.kefuwidth" /> 30像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_kefutwidth" value="40px" ng-model="Edit.params.kefuwidth" /> 40像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_kefutwidth" value="50px" ng-model="Edit.params.kefuwidth" /> 50像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_kefuwidth" value="60px" ng-model="Edit.params.kefuwidth" /> 60像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_kefuwidth" value="80px" ng-model="Edit.params.kefuwidth" /> 80像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" value="" ng-model="Edit.params.kefuwidth" placeholder="80px" /></label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.kefu == 1">
    <div class="fe-panel-editor-name">图标高度</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_kefuheight" value="30px" ng-model="Edit.params.kefuheight" /> 30像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_kefuheight" value="50px" ng-model="Edit.params.kefuheight" /> 50像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_kefuheight" value="80px" ng-model="Edit.params.kefuheight" /> 80像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_kefuheight" value="100px" ng-model="Edit.params.kefuheight" /> 100像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_kefuheight" value="120px" ng-model="Edit.params.kefuheight" /> 120像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" value="" ng-model="Edit.params.kefuheight" placeholder="120px" /></label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.kefu == 1">
    <div class="fe-panel-editor-name">顶部间距</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_kefubottom" value="10px" ng-model="Edit.params.kefubottom" /> 10像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_kefubottom" value="20px" ng-model="Edit.params.kefubottom" /> 20像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="{edit.id}_kefubottom" value="30px" ng-model="Edit.params.kefubottom" /> 30像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="{edit.id}_kefubottom" value="50px" ng-model="Edit.params.kefubottom" /> 50像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" placeholder="80px" value="" ng-model="Edit.params.kefubottom" /></label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.kefu == 1">
    <div class="fe-panel-editor-name">图标图片</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <div class="fe-panel-editor-upload" ng-click="pageImg(Edit.id, 'kefuimg')" style="min-height:120px; width: 50px;">
            <img ng-src="@{{Edit.params.kefuimg}}" width="100%;" ng-show="Edit.params.kefuimg" />
            <div class="fe-panel-editor-upload-choose2" ng-show="Edit.params.kefuimg">重选</div>
            <div class="fe-panel-editor-upload-choose1" ng-show="!Edit.params.kefuimg" style="line-height:116px;"><i class="fa fa-plus-circle"></i></div>
        </div>
    </div>
</div>


<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">电话按钮</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_tel" value="1" ng-model="Edit.params.tel" /> 显示</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_tel" value="0" ng-model="Edit.params.tel" /> 不显示</label>
    </div>
</div>
<div class="fe-panel-editor-title" ng-show="Edit.params.tel == 1">电话按钮设置</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.tel == 1">
    <div class="fe-panel-editor-name">电话号码</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <label style="cursor:pointer; margin-right: 10px;"><input type="input" name="@{{Edit.id}}_telnum" value="left" ng-model="Edit.params.telnum" /> </label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.tel == 1">
    <div class="fe-panel-editor-name">图标位置</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_telstyle" value="left" ng-model="Edit.params.telstyle" /> 居左</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{Edit.id}}_telstyle" value="right" ng-model="Edit.params.telstyle" /> 居右</label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.tel == 1">
    <div class="fe-panel-editor-name">图标宽度</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_telwidth" value="30px" ng-model="Edit.params.telwidth" /> 30像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_telwidth" value="40px" ng-model="Edit.params.telwidth" /> 40像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_telwidth" value="50px" ng-model="Edit.params.telwidth" /> 50像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_telwidth" value="60px" ng-model="Edit.params.telwidth" /> 60像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_telwidth" value="80px" ng-model="Edit.params.telwidth" /> 80像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" value="" ng-model="Edit.params.telwidth" placeholder="80px" /></label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.tel == 1">
    <div class="fe-panel-editor-name">图标高度</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_telheight" value="30px" ng-model="Edit.params.telheight" /> 30像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_telheight" value="50px" ng-model="Edit.params.telheight" /> 50像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_telheight" value="80px" ng-model="Edit.params.telheight" /> 80像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_telheight" value="100px" ng-model="Edit.params.telheight" /> 100像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_telheight" value="120px" ng-model="Edit.params.telheight" /> 120像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" value="" ng-model="Edit.params.telheight" placeholder="120px" /></label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.tel == 1">
    <div class="fe-panel-editor-name">底部间距</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_telbottom" value="10px" ng-model="Edit.params.telbottom" /> 10像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="@{{edit.id}}_telbottom" value="20px" ng-model="Edit.params.telbottom" /> 20像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="{edit.id}_telbottom" value="30px" ng-model="Edit.params.telbottom" /> 30像素</label>
        <label style="cursor:pointer; margin-right: 10px;"><input type="radio" name="{edit.id}_telbottom" value="50px" ng-model="Edit.params.telbottom" /> 50像素</label>
        <label style="cursor:pointer; margin-right: 10px;">自定义：<input class="fe-panel-editor-input2" style="line-height: 20px;" placeholder="80px" value="" ng-model="Edit.params.telbottom" /></label>
    </div>
</div>
<div class="fe-panel-editor-line" ng-show="Edit.params.tel == 1">
    <div class="fe-panel-editor-name">图标图片</div>
    <div class="fe-panel-editor-con" style="padding-top: 10px;">
        <div class="fe-panel-editor-upload" ng-click="pageImg(Edit.id, 'telimg')" style="min-height:120px; width: 50px;">
            <img ng-src="@{{Edit.params.telimg}}" width="100%;" ng-show="Edit.params.telimg" />
            <div class="fe-panel-editor-upload-choose2" ng-show="Edit.params.telimg">重选</div>
            <div class="fe-panel-editor-upload-choose1" ng-show="!Edit.params.telimg" style="line-height:116px;"><i class="fa fa-plus-circle"></i></div>
        </div>
    </div>
</div>
<?php }?>

<div class="fe-panel-editor-title" style="margin-top: 15px;">底部导航设置</div>
<div class="fe-panel-editor-line">
    <div class="fe-panel-editor-name">底部导航</div>
    <div class="fe-panel-editor-con">

        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_footer" value="0" ng-model="Edit.params.footer" />
            <span>不显示</span>
        </label>
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_footer" value="1" ng-model="Edit.params.footer" />
            <span>系统默认</span>
        </label>

        <span ng-if="menuList.length > 0">
        <label style="cursor:pointer; margin-right: 10px;">
            <input type="radio" name="@{{Edit.id}}_footer" value="2" ng-model="Edit.params.footer" />
            <span>自定义菜单</span>
        </label>
        <select ng-show="Edit.params.footer==2" name="@{{Edit.id}}_footer_menu"  class="fe-panel-editor-input1" ng-model="Edit.params.footermenu" style="width:200px;">
           <option ng-repeat="item in menuList" value="@{{item.id}}" ng-selected="Edit.params.footermenu == item.id">
               @{{item.menu_name}}
           </option>
        </select>
        </span>
    </div>
</div>
