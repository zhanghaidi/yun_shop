<div class="fe-mod fe-mod-7" ng-class="{'fe-mod-select':Item.id == focus}" ng-style="{'background-color':Item.params.bgcolor}">
    <div string-html="Item.content"></div>
    <div ng-show="!Item.content">
        <p><span style="font-size: 20px;">哈喽大家好！这里是『富文本』区域</span></p>
        <p>你可以对文字进行<strong>加粗</strong>、<em>斜体</em>、<span style="text-decoration: underline;">下划线</span>、<span style="text-decoration: line-through;">删除线</span>、文字<span style="color: rgb(0, 176, 240);">颜色</span>、<span style="background-color: rgb(255, 192, 0); color: rgb(255, 255, 255);">背景色</span>、以及字号<span style="font-size: 20px;">大</span><span style="font-size: 14px;">小</span>等简单排版操作。
        </p>
        <p>也可在这里插入图片</p>
        <p><img src={{inits+"plugins/designer/assets/images/init-data/init-icon.png"}}></p>
        <p style="text-align: left;"><span style="text-align: left;">还可给文字加上<a href="http://www.baidu.com">超级链接</a>，方便用户点击。</span></p>
    </div>
</div>
