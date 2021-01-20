<!-- 平台提现 -->
<div id="thawing-funds" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">

        <input type="hidden" name="order_pay_id" value=""/>
        <input type="hidden" name="order_ids" value=""/>

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="text-align: center;">
                    {{--<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>--}}
                    <p>会员余额：<span id="member_credit2"></span>元</p>
                    <p>支付流水号：<span id="pay_sn"></span></p>
                    <p>支付金额：<span id="amount" style="color: red"></span>元</p>

                </div>
                <div class="modal-body" style="text-align: center;">
                    <div id="pay_btn">
                        <button style="margin: 0 15px" type="button" class="btn btn-warning" onclick="credit2()">余额支付</button>
                        <button  type="button" class="btn btn-youtube" onclick="cod()">货到付款</button>
                    </div>
                    <div id="pay_msg" style="color: red;font-size: 18px;display: none;">
                        支付中....
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true" onclick="shua()">取消</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script language="javascript">

    function shua() {
        window.location.reload();
    }
    
    function pay_wait(type) {

        if (type == 'yes') {
            $('#pay_btn').hide();
            $('#pay_msg').show();
        } else  {
            $('#pay_msg').hide();
            $('#pay_btn').show();
        }


    }

    function credit2() {

        pay_wait('yes');

        if ($(':input[name="order_pay_id"]').val() == '') {
            alert('支付id为空，无法支付');
            return false;
        }

        $.get("{!! yzWebUrl('plugin.help-user-buying.admin.user-merge-pay.credit2') !!}",{order_pay_id:$(':input[name="order_pay_id"]').val()}, function(json){
            if (json.result == 1) {

                alert('支付成功');
                shua();
            } else {
                console.log(json);
                alert(json.msg);

            }

        });
    }

    function cod() {

        pay_wait('yes');

        if ($(':input[name="order_pay_id"]').val() == '') {
            alert('支付id为空，无法支付');
            return false;
        }

        $.get("{!! yzWebUrl('plugin.help-user-buying.admin.user-merge-pay.COD') !!}",{order_pay_id:$(':input[name="order_pay_id"]').val()}, function(json){
            if (json.result == 1) {
                alert('支付成功');
                shua();
            } else {
                console.log(json);
                alert(json.msg);

            }

        });
    }

</script>