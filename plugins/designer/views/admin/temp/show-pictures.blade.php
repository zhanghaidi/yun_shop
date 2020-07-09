<div class=" fe-mod-9" ng-class="{'fe-mod-select':Item.id == focus}">
    <div class="big_box  fe-mod-9">
        <div ng-style="{'width':Item.params.style}" ng-repeat="img in Item.data">
            <a href="{{img.hrefurl|| 'javascript:;'}}">
                <img ng-src="{{img.imgurl}}" ng-show="img.imgurl">
            </a>
        </div>
    </div>
</div>
<style>
    /* 清楚默认边距 */
    body,
    div,
    ol,
    ul,
    li,
    dl,
    dt,
    dd,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    pre,
    form,
    fieldset,
    legend,
    input,
    textarea,
    p,
    blockquote,
    th,
    td,
    img {
        margin: 0;
        padding: 0
    }
    /* 规则 */
    .big_box {
        display: flex;
        flex-wrap: wrap;
    }

    .big_box div img {
        width: 100%;
        height: 100%;
    }
</style>
