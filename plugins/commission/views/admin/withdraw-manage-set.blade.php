<div class='panel panel-default'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现额度</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="withdraw[manage][roll_out_limit]" class="form-control"
                   value="{{$set['roll_out_limit']}}"/>
            <span class="help-block">当前分销商的佣金达到此额度时才能提现</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现手续费</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="withdraw[manage][poundage_rate]" class="form-control"
                   value="{{$set['poundage_rate']}}"/>
            <span class="help-block">提现手续费比例</span>
        </div>
    </div>
</div>
