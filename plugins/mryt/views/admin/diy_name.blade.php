<div class="panel-heading">自定义名称</div>
<div class="panel-body">
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">插件名称</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="set[name]" class="form-control" value="{{$set['name']}}" autocomplete="off" placeholder="MRYT">
            <span class='help-block'>空白默认为MRYT</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">默认级别名称</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="set[default_level]" class="form-control" value="{{$set['default_level']}}" autocomplete="off" placeholder="VIP会员">
            <span class="help-block">会员获得推广资格即可默认成为MRYT推广员，默认级别名称VIP会员</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">直推奖名称</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="set[referral_name]" class="form-control" value="{{$set['referral_name']}}" autocomplete="off" placeholder="直推奖">
            <span class='help-block'>空白默认为直推奖</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">团队管理奖名称</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="set[teammanage_name]" class="form-control" value="{{$set['teammanage_name']}}" autocomplete="off" placeholder="团队管理奖">
            <span class='help-block'>空白默认为团队管理奖</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">团队奖名称</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="set[team_name]" class="form-control" value="{{$set['team_name']}}" autocomplete="off" placeholder="团队奖">
            <span class='help-block'>空白默认为团队奖</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">育人奖名称</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="set[parenting_name]" class="form-control" value="{{$set['parenting_name']}}" autocomplete="off" placeholder="育人奖">
            <span class='help-block'>空白默认为育人奖</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">感恩奖名称</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="set[thanksgiving_name]" class="form-control" value="{{$set['thanksgiving_name']}}" autocomplete="off" placeholder="感恩奖">
            <span class='help-block'>空白默认为感恩奖</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">平级奖名称</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="set[tier_name]" class="form-control" value="{{$set['tier_name']}}" autocomplete="off" placeholder="平级奖">
            <span class='help-block'>空白默认为平级奖</span>
        </div>
    </div>
</div>