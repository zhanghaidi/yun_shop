<!-- 确认退还 -->
<div id="modal-lease-return" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"
     style="width:900px;margin:0px auto;">
    <form class="form-horizontal form" action="{!! yzWebUrl('plugin.lease-toy.admin.lease-return.lease-refund') !!}" method="post" enctype="multipart/form-data">
        <input type='hidden' name='order_id' value=''/>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <div class="panel-heading panel-default">
                        租赁信息
                        @if ($order['has_one_area_lease_log'])
                        <div style="float:right;margin-right:20px">
                            分站管理员:
                            <span>{{$order['has_one_area_lease_log']['as_name']}}</span>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-body" style="text-align: left;">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">退还金额:</label>
                        <div class="col-sm-6  col-xs-12">
                            <div class="input-group col-md-9">
                                <input readonly type="text" name="return_deposit" class="form-control" value="{!!$order['has_one_area_lease_log']?$order['has_one_area_lease_log']['return_deposit']:0!!}">
                                <span class="input-group-addon">元</span>
                            </div>
                            <div class="help-block">最终退还押金 = 押金金额({{$order['lease_toy']['deposit_total']}}) - 逾期 - 破损</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">逾期:</label>
                        <div class="col-sm-6  col-xs-12">
                            <div class="input-group col-md-9">
                                <input type="text" name="be_overdue" class="form-control" value="{!!$order['has_one_area_lease_log']?$order['has_one_area_lease_log']['be_overdue']:0!!}">
                                <span class="input-group-addon">元</span>
                            </div>
                            <div class="help-block">押金扣除</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">破损:</label>
                        <div class="col-sm-6  col-xs-12">
                            <div class="input-group col-md-9">
                                <input type="text" name="be_damaged" class="form-control" value="{!!$order['has_one_area_lease_log']?$order['has_one_area_lease_log']['be_damaged']:0!!}">
                                <span class="input-group-addon">元</span>
                            </div>
                            <div class="help-block">押金扣除</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">说明:</label>
                        <div class="col-sm-6  col-xs-12">
                            <div class="input-group col-md-9">
                                <textarea style="height:150px;width: 300px" class="form-control" name="explain"
                              autocomplete="off">{!!$order['has_one_area_lease_log']?$order['has_one_area_lease_log']['explain']:''!!}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">退还方式：</label>
                        <div class="col-sm-9 col-xs-12">
                            <label for="totalcnf1" class="radio-inline"><input type="radio" name="return_pay_type_id" value="3" id="totalcnf1" checked="true"> 余额</label>
                            &nbsp;&nbsp;&nbsp;
                            <!-- <label for="totalcnf2" class="radio-inline"><input type="radio" name="return_pay_type_id" value="0" id="totalcnf2"> 原路返回存</label>
                            &nbsp;&nbsp;&nbsp; -->
                            <label for="totalcnf3" class="radio-inline"><input type="radio" name="return_pay_type_id" value="-1" id="totalcnf3">手动退款</label>
                        </div>
                    </div>
                    <div class="help-group">
                        <span class="help-block">余额： 会返回到商城用户余额</span>
                        <!-- <span class="help-block">原路返回： 微信支付、支付宝支付会返回原支付账号,其他支付方式返回到商城用户余额</span> -->
                        <span class="help-block">手动退款： 订单会完成退款处理，您用其他方式进行退款</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary span2" name="return" value="yes">确认退还</button>
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>
        </div>
    </form>
</div>