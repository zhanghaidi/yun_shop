<style>
    .article {
        height: 140px;padding:5px 0 5px 0;
    }
    .article-img {
        width: 150px;max-height: 120px; min-height: 120px;float:left;margin-right: 10px;
    }
    .article-p {
        height: 85px;overflow: hidden;line-height: 1.5em;
    }
</style>

<div class="fe-mod fe-mod-12" ng-class="{'fe-mod-select':Item.id == focus}" ng-style="{'background-color':Item.params.bgcolor}">
    <div style="line-height: 40px; text-align: center; color: #999; font-size: 16px;" ng-show="Item.data == '' && Item.params.addmethod == 1">一篇文章都没有...</div>
    <div ng-repeat="article in Item.data" ng-show="Item.params.addmethod == 1">
        <div class="article">
            <div style="float: left; width: 150px; display: inline">
                <p class="article-p">
                    <a href="{{article.hrefurl|| 'javascript:;'}}">
                        <span>{{article.title}}</span>
                    </a>
                </p>
            </div>
            <img style="float: right;" class="article-img" ng-src="{{article.thumb|| inits+'plugins/designer/assets/images/init-data/init-icon.png'}}">
        </div>
    </div>
    <div ng-repeat="article in Item.alldata | limitTo:Item.params.shownum" ng-show="Item.params.addmethod == 0">
        <div class="article">
            <div style="float: left; width: 150px; display: inline">
                <p class="article-p">
                    <a href="{{article.hrefurl|| 'javascript:;'}}">
                        <span>{{article.title}}</span>
                    </a>
                </p>
            </div>
            <img style="float: right;" class="article-img" ng-src="{{article.thumb|| inits+'plugins/designer/assets/images/init-data/init-icon.png'}}">
        </div>
    </div>
</div>

