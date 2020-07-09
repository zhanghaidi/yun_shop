@if($set['info']['company_bank'] || !$set['info'])
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">银行账号@if($set['info']['company_bank'])<span style="color:red;">*</span>@endif</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" id="company_bank" name="data[company_bank]" class="form-control" value="{{$supplier->company_bank}}" placeholder="请输入银行账号"  />
        </div>
    </div>
@endif
@if($set['info']['bank_username'] || !$set['info'])
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">开户人姓名@if($set['info']['bank_username'])<span style="color:red;">*</span>@endif</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" id="bank_username" name="data[bank_username]" class="form-control" value="{{$supplier->bank_username}}" placeholder="请输入开户人姓名"  />
        </div>
    </div>
@endif
@if($set['info']['bank_of_accounts'] || !$set['info'])
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">开户行@if($set['info']['bank_of_accounts'])<span style="color:red;">*</span>@endif</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" id="bank_of_accounts" name="data[bank_of_accounts]" class="form-control" value="{{$supplier->bank_of_accounts}}" placeholder="请输入开户行"  />
        </div>
    </div>
@endif
@if($set['info']['opening_branch'] || !$set['info'])
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">开户支行@if($set['info']['opening_branch'])<span style="color:red;">*</span>@endif</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" id="opening_branch" name="data[opening_branch]" class="form-control" value="{{$supplier->opening_branch}}" placeholder="请输入开户支行"  />
        </div>
    </div>
@endif
@if($set['info']['company_ali'] || !$set['info'])
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">企业支付宝账号@if($set['info']['company_ali'])<span style="color:red;">*</span>@endif</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" id="company_ali" name="data[company_ali]" class="form-control" value="{{$supplier->company_ali}}" placeholder="请输入企业支付宝账号"  />
        </div>
    </div>
@endif
@if($set['info']['company_ali_username'] || !$set['info'])
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">企业支付宝用户名@if($set['info']['company_ali_username'])<span style="color:red;">*</span>@endif</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" id="company_ali_username" name="data[company_ali_username]" class="form-control" value="{{$supplier->company_ali_username}}" placeholder="请输入企业支付宝用户名"  />
        </div>
    </div>
@endif
@if($set['info']['ali'] || !$set['info'])
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝账号@if($set['info']['ali'])<span style="color:red;">*</span>@endif</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" id="ali" name="data[ali]" class="form-control" value="{{$supplier->ali}}" placeholder="请输入支付宝账号"  />
        </div>
    </div>
@endif
@if($set['info']['ali_username'] || !$set['info'])
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝用户名@if($set['info']['ali_username'])<span style="color:red;">*</span>@endif</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" id="ali_username" name="data[ali_username]" class="form-control" value="{{$supplier->ali_username}}" placeholder="请输入支付宝用户名"  />
        </div>
    </div>
@endif
@if($set['info']['wechat'] || !$set['info'])
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信账号@if($set['info']['wechat'])<span style="color:red;">*</span>@endif</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" id="wechat" name="data[wechat]" class="form-control" value="{{$supplier->wechat}}" placeholder="请输入微信账号"  />
        </div>
    </div>
@endif