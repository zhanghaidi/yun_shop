<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">供应商信息填写(选中则显示)</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline">
            <input type="checkbox" name="setdata[info][company_bank]" value="1"
                   @if ($set['info']['company_bank'] || !$set['info'])
                   checked
                    @endif
            >银行账号
        </label>
        <label class="radio-inline">
            <input type="checkbox" name="setdata[info][bank_username]" value="1"
                   @if ($set['info']['bank_username'] || !$set['info'])
                   checked
                    @endif
            >开户人姓名
        </label>
        <label class="radio-inline">
            <input type="checkbox" name="setdata[info][bank_of_accounts]" value="1"
                   @if ($set['info']['bank_of_accounts'] || !$set['info'])
                   checked
                    @endif
            >开户行
        </label>
        <label class="radio-inline">
            <input type="checkbox" name="setdata[info][opening_branch]" value="1"
                   @if ($set['info']['opening_branch'] || !$set['info'])
                   checked
                    @endif
            >开户支行
        </label>
        <label class="radio-inline">
            <input type="checkbox" name="setdata[info][company_ali]" value="1"
                   @if ($set['info']['company_ali'] || !$set['info'])
                   checked
                    @endif
            >企业支付宝账号
        </label>
        <label class="radio-inline">
            <input type="checkbox" name="setdata[info][company_ali_username]" value="1"
                   @if ($set['info']['company_ali_username'] || !$set['info'])
                   checked
                    @endif
            >企业支付宝用户名
        </label>
        <label class="radio-inline">
            <input type="checkbox" name="setdata[info][ali]" value="1"
                   @if ($set['info']['ali'] || !$set['info'])
                   checked
                    @endif
            >支付宝账号
        </label>
        <label class="radio-inline">
            <input type="checkbox" name="setdata[info][ali_username]" value="1"
                   @if ($set['info']['ali_username'] || !$set['info'])
                   checked
                    @endif
            >支付宝用户名
        </label>
        <label class="radio-inline">
            <input type="checkbox" name="setdata[info][wechat]" value="1"
                   @if ($set['info']['wechat'] || !$set['info'])
                   checked
                    @endif
            >微信账号
        </label>
    </div>
</div>