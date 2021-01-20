<div id="modal-lease" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"
     style="width:620px;margin:0px auto;">
    <form class="form-horizontal form" id="form-lease" action="" method="post" enctype="multipart/form-data">
        <input type='hidden' name='refund_id' value='{{$order['has_one_refund_apply']['id']}}'/>
        <input type='hidden' name='lease_id' value='{{$order['has_one_lease_toy_order']['id']}}'/>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>处理归还申请</h3></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-xs-10 col-sm-3 col-md-3 control-label">处理结果</label>
                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                            <label class='radio-inline'>
                                <input type='radio' class="refund-action"
                                       data-action="{{yzWebUrl('plugin.lease-toy.admin.lease-return.refuse')}}" value="-1"
                                       name='return_status'>驳回申请
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' value='3' name='return_status' class="refund-action" data-action="{{yzWebUrl('plugin.lease-toy.admin.lease-return.waitSendBack')}}" checked >通过申请(需客户寄回商品)
                            </label>

                            <label class='radio-inline'>
                                <input type='radio' value='4' name='return_status' class="refund-action" data-action="{{yzWebUrl('plugin.lease-toy.admin.lease-return.directPass')}}" >通过申请(无需客户寄回商品)
                            </label>

                        </div>
                    </div>

                    <div id="module-menus"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary span2 " id="lease_return" name="return" value="yes">
                        确认
                    </button>
                    <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">关闭</a></div>
            </div>
        </div>
    </form>
</div>

<script>
    $('#form-lease').submit(function () {
        var route = $('input[name="return_status"]:checked').attr('data-action');
        $(this).attr('action', route);

        return true;
    });
</script>