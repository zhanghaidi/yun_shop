<div class="fe-mod fe-mod-13" ng-class="{'fe-mod-select':Item.id == focus}" ng-style="{'background-color':Item.params.bgcolor}">
    <div style="line-height: 40px; text-align: center; color: #999; font-size: 16px;" ng-show="Item.data.hrefurl == ''">一个视频都没有...</div>
    <div ng-repeat="d in Item.data">
        <video class="custom_video_cover" poster="{{d.option == 1 ? d.imgurl : ''}}" width="100%" src="{{d.hrefurl}}" controls="controls">
        </video>
    </div>
</div>