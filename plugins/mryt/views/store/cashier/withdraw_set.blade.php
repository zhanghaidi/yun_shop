<div class='panel panel-default'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现额度</label>
        <div class="col-sm-9 col-xs-12">
            <input type="text" name="withdraw[Mryt][roll_out_limit]" class="form-control"
                   value="{{$set['roll_out_limit']}}"/>
            <span class="help-block">当前门店收银台提现达到此额度时才能提现</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现手续费</label>
        <div class="col-sm-9 col-xs-12">
            <div class="switch">
                <label class='radio-inline'>
                    <input type='radio' name='withdraw[Mryt][poundage_type]' value='1'
                           @if($set['poundage_type'] == 1) checked @endif />
                    固定金额
                </label>
                <label class='radio-inline'>
                    <input type='radio' name='withdraw[Mryt][poundage_type]' value='0'
                           @if(empty($set['poundage_type'])) checked @endif />
                    手续费比例
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
        <div class="col-sm-9 col-xs-12">
            <div class="cost">
                <label class='radio-inline'>
                    <div class="input-group">
                        <div class="input-group-addon" id="cashier_poundage_hint"
                             style="width: 120px;">@if($set['poundage_type'] == 1) 固定金额 @else
                                手续费比例 @endif</div>
                        <input type="text" name="withdraw[Mryt][poundage_rate]"
                               class="form-control" value="{{ $set['poundage_rate'] or '' }}"
                               placeholder="请输入提现手续费计算值"/>
                        <div class="input-group-addon" id="cashier_poundage_unit">@if($set['poundage_type'] == 1) 元 @else
                                % @endif</div>
                    </div>
                </label>
            </div>
        </div>
    </div>
</div>
<script language="javascript">
    $(function () {
        $(":radio[name='withdraw[Mryt][poundage_type]']").click(function () {
            if ($(this).val() == 1) {
                $("#cashier_poundage_unit").html('元');
                $("#cashier_poundage_hint").html('固定金额');
            }
            else {
                $("#cashier_poundage_unit").html('%');
                $("#cashier_poundage_hint").html('手续费比例')
            }
        });
    })
</script>
