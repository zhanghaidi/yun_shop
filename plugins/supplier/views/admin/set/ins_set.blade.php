<div class='panel panel-default'>
    <div class="'panel-body">
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">保险公司开启</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline"><input type="radio" class="" name="setdata[ins_company_status]" value="0" @if($set['ins_company_status'] == 0) checked="checked"@endif /> 关闭</label>
                <label class="radio-inline"><input type="radio" class="" name="setdata[ins_company_status]" value="1" @if($set['ins_company_status'] == 1) checked="checked"@endif /> 开启</label>
                <span class="help-block" style="margin: 10px">开启后添加保单页面增加保险公司选择</span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">保费支付</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline"><input type="radio" class="" name="setdata[ins_pay_status]" value="0" @if($set['ins_pay_status'] == 0) checked="checked"@endif /> 关闭</label>
                <label class="radio-inline"><input type="radio" class="" name="setdata[ins_pay_status]" value="1" @if($set['ins_pay_status'] == 1) checked="checked"@endif /> 开启</label>
                <span class="help-block" style="margin: 10px">开启后供应商添加保单后可以进行保费支付</span>
            </div>

        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">一键续保</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline"><input type="radio" class="" name="setdata[ins_renew_status]" value="0" @if($set['ins_renew_status'] == 0) checked="checked"@endif /> 关闭</label>
                <label class="radio-inline"><input type="radio" class="" name="setdata[ins_renew_status]" value="1" @if($set['ins_renew_status'] == 1) checked="checked"@endif /> 开启</label>
                <span class="help-block" style="margin: 10px">开启后供应商前端点击一键续保可以复制原有的信息，重新生成一个新的保单</span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">联系方式</label>
            <div class="input-group" style="margin-top:5px; width: 40%">
                <input type="text" class="form-control" name='setdata[ins_phone_1]'
                       value='{{ $set['ins_phone_1'] or '' }}'/>
                <span class="input-group-addon">或</span>
                <input type="text" class="form-control" name='setdata[ins_phone_2]'
                       value='{{ $set['ins_phone_2'] or '' }}'/>
            </div>
        </div>
    </div>
</div>
