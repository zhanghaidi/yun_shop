<div class=" fe-mod-10" ng-class="{'fe-mod-select':Item.id == focus}">
    <div class=" fe-mod-10 img_chuang">
        <div class="first_box  fe-mod-10 " style="width: 50%;">
            <a href="{{Item.params.imageOneUrl|| 'javascript:;'}}">
                <img ng-src="{{Item.params.imageOne}}"/>
            </a>
        </div>
        <div class="se_box_two " style="width:50%;height:50%;display: flex;flex-wrap: wrap;">
            <a href="{{Item.params.imageTwoUrl|| 'javascript:;'}}">
                <img ng-src="{{Item.params.imageTwo}}">
            </a>
            <div class="se_box_three " style="width:50%;height:50%;">
                <a href="{{Item.params.imageThreeUrl|| 'javascript:;'}}">
                    <img ng-src="{{Item.params.imageThree}}">
                </a>
            </div>
            <div class="se_box_four " style="width:50%;height:50%;">
                <a href="{{Item.params.imageFourUrl|| 'javascript:;'}}">
                    <img ng-src="{{Item.params.imageFour}}">
                </a>
            </div>
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

    /* 不规则 */
    .img_chuang {
        display: flex;
        flex-wrap: wrap;
        /* height: 127px; */
    }
    .img_chuang div {
        display: inline-block;
    }

    .img_chuang div img {
        width: 100%;
        height: 100%;
    }
</style>
