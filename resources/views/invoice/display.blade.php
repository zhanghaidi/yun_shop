<div class="panel panel-default">
    <div class="panel-heading">
        发票信息
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">开票金额 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">{{$order['price']}}元</p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">发票类型 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    @if (1==$order['invoice_type'])
                        纸质发票
                    @elseif(0==$order['invoice_type'])
                        电子发票
                    @endif
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">抬头类型 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    @if(1==$order['rise_type'])
                        个人
                    @elseif(0==$order['rise_type'])
                        单位
                    @endif
                </p>
            </div>
        </div>
        <div class="form-group">
            @if(1==$order['rise_type'])
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">抬头 :</label>
            @elseif(0==$order['rise_type'])
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">单位名称 :</label>
            @endif

            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    @if(1==$order['rise_type'])
                        {{$order['rise_text']}}
                    @elseif(0==$order['rise_type'])
                        {{$order['company_name']}}
                    @endif
                </p>
            </div>
        </div>
        @if(0==$order['rise_type'])
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">纳税人识别号 :</label>
                <div class="col-sm-9 col-xs-12">
                    <p class="form-control-static">
                        {{$order['tax_number']}}
                    </p>
                </div>
            </div>
        @endif

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">用户邮箱 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    {{$order['invoice_send_to_email']}}
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">状态 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    @if(1==$order['invoice_status'])
                        <span class="label label-default">待审核</span>
                    @elseif(2==$order['invoice_status'])
                        <span class="label label-success">已开票</span>
                    @elseif(3==$order['invoice_status'])
                        <span class="label label-danger">申请被驳回 :{{$order['invoice_error']}}</span>
                    @endif
                </p>
            </div>
        </div>

        @if(0<$order['invoice_status'])
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">上传发票</label>
                <div class="col-sm-9 col-xs-12">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('basic-detail[invoice]', $order['invoice']) !!}
                </div>
            </div>
        @endif

        @if(1==$order['invoice_status'])
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <br/>
                    <button id="btnAcceptInvoice" name='' class="btn btn-default" data-toggle="modal" data-target="#modal-invoice-notice">同意开票</button>
                    <button name='' class="btn btn-danger" onclick="$('#modal-invoice-refuse').find(':input[name=order_id]').val('{{$order['id']}}')"
                        data-toggle="modal" data-target="#modal-invoice-refuse">驳回申请</button>
                </div>
            </div>

            <script language='javascript'>
                $(function () {
                    $('#btnAcceptInvoice').on('click', function () {
                        var invoice = $("[name='basic-detail[invoice]']").val().trim(); // 获取发票图片地址
                        if (invoice == '') {
                            $('#modal-invoice-notice').find('h3').html('请上传发票图片');
                            $('#modal-invoice-notice').find('button').hide();
                        } else {
                            $('#modal-invoice-notice').find('h3').html('确定同意开票吗');
                            $('#modal-invoice-notice').find('button').show();
                        }
                    });
                });
            </script>
        @endif
    </div>
</div>