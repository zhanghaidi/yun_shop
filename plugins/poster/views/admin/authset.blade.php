<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">开放权限</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline">
            <input type="radio" name="poster[is_open]" value="0" checked/> 不允许
        </label>
        <label class="radio-inline">
            <input type="radio" name="poster[is_open]" value="1" @if($poster['is_open']==1)checked @endif/> 允许
        </label>
        <span class='help-block'>是否允许没有发展下线资格的用户生成自己的海报</span>
        <span class='help-block'><span style="color:red">*</span>由于每个公众号的永久二维码数量有限,建议不要放开权限</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">未开放权限时的提示</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="poster_supplement[not_open_reminder]" class="form-control" value="{{$poster['supplement']['not_open_reminder']}}" />
        <span class='help-block'>例如: 您还没有发展下线的资格，努力去拥有资格，获得您的专属海报吧!</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">未开放权限时的说明链接</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="poster_supplement[not_open_reminder_url]" class="form-control" value="{{$poster['supplement']['not_open_reminder_url']}}"/>
    </div>
</div>