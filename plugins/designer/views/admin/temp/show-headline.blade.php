<style>
    .headline {
        position: relative;height: 70px;text-align: left;display:flex;display: -webkit-flex;align-items:center;
    }
    .headline-logo {
        float: left;
    }
    .headline-logo .headline-img {
        width: 50px;height: 50px;
    }
    .headline-title {
        width: 100%;
    }
    .headline-p {
        margin:auto;width: 20em;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;padding: 5px
    }
    .headline-icon {
        margin:0 20px; border: red 1px solid;color: red; padding: 2px; border-radius:5px;
    }


</style>

<div class="fe-mod fe-mod-12" ng-class="{'fe-mod-select':Item.id == focus}" ng-style="{'background-color':Item.params.bgcolor}">
    <div class="headline">
        <div class="headline-logo">
            <img class="headline-img" ng-src="{{Item.params.bgimg|| inits+'plugins/designer/assets/images/init-data/init-icon.png'}}">
        </div>
        <div class="">
            <div style="line-height: 170px; text-align: center; color: #999; font-size: 16px;" ng-show="Item.data == ''">没有选择文章...</div>
            <div ng-repeat="headline in Item.data| limitTo:Item.params.shownum">
                <p class="headline-p">
                    <span class="headline-icon">热议</span>
                    <a href="{{headline.hrefurl|| 'javascript:;'}}">
                        <span>{{headline.title}}</span>
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

