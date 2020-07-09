@if ($order)
<div class='panel-heading'>
    自定义表单
</div>
<div class='panel-body'>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义表单信息：</label>
        <div class="col-sm-9 col-xs-12">
                <span class="help-block" style="padding-top: 5px;color: black;font-size: 14px">

                  <a href="  {{yzWebUrl('plugin.diyform.admin.diyform.getFormDataByOderId', array('order_id' => $order_id))}}">查看详情</a>
                </span>
        </div>
    </div>


</div>

@endif