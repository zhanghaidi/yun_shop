<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">扫码关注成为下线</label>
        <div class="col-sm-9 col-xs-12">
            <label class="radio-inline">
                <input type="radio" name="poster[auto_sub]" value="1" checked> 是
            </label>
            <label class="radio-inline">
                <input type="radio" name="poster[auto_sub]" value="0" @if(isset($poster['auto_sub']) && ($poster['auto_sub'] == 0))checked @endif/> 否
            </label>
            <span class='help-block'>如果扫码用户之前不存在分销关系, 那么扫码关注后直接成为推荐人的下线</span>
        </div>
    </div>
</div>