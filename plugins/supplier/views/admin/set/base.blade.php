<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启前端供应商入口</label>
        <div class="col-sm-9 col-xs-12">
            <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[status]" value="0" @if($set['status'] == 0) checked="checked"@endif /> 关闭</label>
            <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[status]" value="1" @if($set['status'] == 1) checked="checked"@endif /> 开启</label>
        </div>
    </div>
</div>

@if ($exist_diyform)
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">选择申请表单</label>
        <div class="col-sm-9 col-xs-12">
            <select name='setdata[diyform_id]' class='form-control'>
                <option value='0'>请选择申请表单</option>
                @foreach($diyform_list as $item)
                    <option value='{{$item->id}}'
                            @if($set['diyform_id'] == $item->id)
                            selected
                            @endif
                    >{{$item->title}}</option>
                @endforeach
            </select>
            <div class="help-block">只显示带有账号密码字段的表单数据</div>
        </div>
    </div>
@endif

<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启前端供应商商品聚合页</label>
        <div class="col-sm-9 col-xs-12">
            <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[is_open_index]" value="0" @if($set['is_open_index'] == 0) checked="checked"@endif /> 关闭</label>
            <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[is_open_index]" value="1" @if($set['is_open_index'] == 1) checked="checked"@endif /> 开启</label>
        </div>
    </div>
</div>

<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启供应商保单</label>
        <div class="col-sm-9 col-xs-12">
            <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[insurance_policy]" value="0" @if($set['insurance_policy'] == 0 || !$set['insurance_policy']) checked="checked"@endif /> 关闭</label>
            <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[insurance_policy]" value="1" @if($set['insurance_policy'] == 1) checked="checked"@endif /> 开启</label>
            <div class="help-block">供应商独立后台提交保单信息，前端供应商中心可以查看保单信息</div>
        </div>
    </div>
</div>


@if (app('plugins')->isEnabled('region-mgt'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启区域供应商管理</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[is_open_region]" value="0" @if($set['is_open_region'] == 0 || $set['is_open_region'] != 1) checked="checked"@endif /> 关闭</label>
                <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[is_open_region]" value="1" @if($set['is_open_region'] == 1) checked="checked"@endif /> 开启</label>
            </div>
        </div>
    </div>
@endif
<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label" id="Order_complete"><span style="color: red">*</span>订单完成n天后可申请提现</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="setdata[apply_day]" class="form-control" value="{{$set['apply_day']}}" />
            <div class="help-block">订单完成后 ，用户在x天后可以发起提现申请,如果不填写则订单完成就可以提现</div>
        </div>
    </div>
</div>
<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label" id="frequency"><span style="color: red">*</span>n天内只能提现一次</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="setdata[limit_day]" class="form-control" value="{{$set['limit_day']}}" />
            <div class="help-block">用户n天内只能提现一次</div>

        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现方式</label>
    <div class="col-sm-9 col-xs-12">
        <label for="totalcnf1" class="radio-inline"><input type="checkbox" name="setdata[withdraw_types][0]" value="1" id="totalcnf1" @if ($set['withdraw_types']['0']) checked="true" @endif /> 手动提现 </label>
        &nbsp;&nbsp;&nbsp;
        <label for="totalcnf2" class="radio-inline"><input type="checkbox" name="setdata[withdraw_types][1]" value="2" id="totalcnf2"  @if ($set['withdraw_types']['1']) checked="true" @endif /> 微信提现 </label>
        &nbsp;&nbsp;&nbsp;
        <label for="totalcnf3" class="radio-inline"><input type="checkbox" name="setdata[withdraw_types][2]" value="3" id="totalcnf3"  @if ($set['withdraw_types']['2']) checked="true" @endif /> 支付宝提现 </label>
        &nbsp;&nbsp;&nbsp;
        <label for="totalcnf4" class="radio-inline"><input type="checkbox" name="setdata[withdraw_types][3]" value="4" id="totalcnf4"  @if ($set['withdraw_types']['3']) checked="true" @endif /> 易宝提现 </label>

        <label for="totalcnf5" class="radio-inline"><input type="checkbox" name="setdata[withdraw_types][4]" value="5" id="totalcnf5"  @if ($set['withdraw_types']['4']) checked="true" @endif /> 汇聚提现 </label>
    </div>
</div>

<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现手续费</label>
        <div class="col-sm-9 col-xs-12">
            <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[service_type]" value="0" @if($set['service_type'] == 0) checked="checked"@endif /> 固定金额(元)</label>
            <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[service_type]" value="1" @if($set['service_type'] == 1) checked="checked"@endif /> 手续费比例(%)</label>
        </div>
        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
        <div class="col-sm-9 col-xs-12">
            <input onkeyup="value=value.replace(/[^\d]/g,'')" type="text" name="setdata[service_money]" class="form-control" value="@if($set['service_money']){{$set['service_money']}}@else{{0}}@endif" />
            <div class="help-block">只能填写正整数</div>
        </div>
    </div>
</div>

<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">提成计算方式</label>
        <div class="col-sm-9 col-xs-12">
            <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[culate_method]" value="0" @if(!$set['culate_method']) checked="checked"@endif /> 成本价+运费</label>
            <label class="radio-inline"><input type="radio" class="sendmoth" name="setdata[culate_method]" value="1" @if($set['culate_method'] == 1) checked="checked"@endif /> 商城扣点</label>
        </div>
        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                <input type='text' onkeyup="this.value= this.value.match(/\d+(\.\d{0,2})?/) ? this.value.match(/\d+(\.\d{0,2})?/)[0] : ''" name='setdata[shop_commission]' class="form-control"
                       value="{!! $set['shop_commission']?:0 !!}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
</div>

<div class="tab-pane  active">
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">供应商提现免审核</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='setdata[audit_free]' value='1' @if($set['audit_free'] == 1) checked @endif />
                开启
            </label>
            <label class='radio-inline'>
                <input type='radio' name='setdata[audit_free]' value='0' @if($set['audit_free'] == 0) checked @endif />
                关闭
            </label>
            <span class='help-block'>供应商提现自动审核、自动打款（自动打款只支持提现到汇聚一种方式！）</span>
        </div>
    </div>
</div>

<div id="wechat_withdraw_limit" @if(empty($set['withdraw_types']['1']))style="display:none"@endif>
    <div class='panel-heading'>
        微信提现限制
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">单笔付款金额</label>
        <div class="col-sm-9 col-xs-12">
            <div class="input-group">
                <div class="input-group">
                    <div class="input-group-addon">单笔最低金额</div>
                    <input type="text" name="setdata[wechat_min]" class="form-control"
                           value="{{$set['wechat_min']}}" placeholder=""/>
                    <div class="input-group-addon">单笔最高金额</div>
                    <input type="text" name="setdata[wechat_max]" class="form-control"
                           value="{{$set['wechat_max']}}" placeholder=""/>
                </div>
            </div>
            <div class="help-block">
                可设置区间0.3-20000，设置为0为空则不限制，请参考微信商户平台--产品中心--企业付款到零钱--产品设置--额度设置中设置
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">每日向同一用户付款不允许超过</label>
        <div class="col-sm-9 col-xs-12">
            <div class="input-group">
                <div class="input-group">

                    <input type="text" name="setdata[wechat_frequency]" class="form-control"
                           value="{{$set['wechat_frequency']}}" placeholder=""/>
                    <div class="input-group-addon">次</div>
                </div>
            </div>
            <div class="help-block">
                可设置1-10次,不设置或为空默认为10
            </div>
        </div>
    </div>
</div>

<div id="alipay_withdraw_limit" @if(empty($set['withdraw_types']['2']))style="display:none"@endif>
    <div class='panel-heading'>
        支付宝提现限制
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">单笔付款金额</label>
        <div class="col-sm-9 col-xs-12">
            <div class="input-group">
                <div class="input-group">
                    <div class="input-group-addon">单笔最低金额</div>
                    <input type="text" name="setdata[alipay_min]" class="form-control"
                           value="{{$set['alipay_min']}}" placeholder=""/>
                    <div class="input-group-addon">单笔最高金额</div>
                    <input type="text" name="setdata[alipay_max]" class="form-control"
                           value="{{$set['alipay_max']}}" placeholder=""/>
                </div>
            </div>
            <div class="help-block">
                不设置或为空,则不限制
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">每日向同一用户付款不允许超过</label>
        <div class="col-sm-9 col-xs-12">
            <div class="input-group">
                <div class="input-group">

                    <input type="text" name="setdata[alipay_frequency]" class="form-control"
                           value="{{$set['alipay_frequency']}}" placeholder=""/>
                    <div class="input-group-addon">次</div>
                </div>
            </div>
            <div class="help-block">
                不设置或为空,则不限制
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">签名</label>
    <div class="col-sm-9 col-xs-12">
        <textarea  name="setdata[signature]" class="form-control" >{{$set['signature']}}</textarea>
        <div class="help-block">
            供应商申请页面的说明
        </div>
    </div>
</div>