<div class="fe-panel-editor-title">视频设置</div>
<div ng-repeat="video in Edit.data" class="fe-panel-editor-relative">
    <div class="fe-panel-editor-line2">
        <div class="fe-panel-editor-line2-right">
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2" style="margin-left:10px; margin-top: 8px; margin-right: 35px;">视频</div>
                <div class="fe-panel-editor-con">
                    <input id="custom_video" class="fe-panel-editor-input3" ng-model="video.hrefurl"/>
                    {{--<input  ng-model="video.hrefurl"/>--}}
                    <div class="fe-panel-editor-input4" style="width: 100px;" ng-click="showVideoDialog(Edit.id, video.id)">选择媒体文件</div>
                </div>
            </div>
        </div>

        <div class="fe-panel-editor-line2-right">
            <div class="fe-panel-editor-line">
                <div class="fe-panel-editor-name2" style="margin-left:10px; margin-right: 22px;">封面图</div>
                <div class="fe-panel-editor-con" ng-click="showVideoImg(Edit.id, picture.id)">
                    <label style="cursor:pointer; margin-right: 40px;"><input type="radio" name="" value="0" ng-model="video.option" >原视频封面</label>
                    <label style="cursor:pointer; margin-right: 210px;"><input type="radio" name="" value="1" ng-model="video.option" >自定义视频封面</label>
                </div>
                <div class="fe-panel-editor-name2" style="margin-left:20px; margin-right: 22px; color: #7a7f83; font-size: 12px;">只有本地上传的视频和浏览视频-选择视频才可以使用自定义视频封面</div>
            </div>
        </div>

        <div ng-show="video.option =='0' ? false : true" class="fe-panel-editor-line2" style="width: 100%; border: 0px;">
            <div class="fe-panel-editor-goodimg" ng-click="uploadImgChild(Edit.id, video.id)">
                <img ng-src="@{{video.imgurl}}" width="100%" ng-show="video.imgurl" />
                <div class="fe-panel-editor-goodimg-t1" ng-show="!video.imgurl"><i class="fa fa-plus-circle"></i> 选择图片</div>
                <div class="fe-panel-editor-goodimg-t2" ng-show="video.imgurl">重新选择图片</div>
            </div>

            <div class="fe-panel-editor-line2-top">
                <div class="fe-panel-editor-line">
                    <div class="fe-panel-editor-name2" style="margin: 25px;">建议图片宽高 375*210 </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>

</script>
