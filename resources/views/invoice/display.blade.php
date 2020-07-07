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
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">状态 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    @if(1==$order['invoice_status'])
                        <span class="label label-default">待审核</span>
                        <button name='' onclick="sub('invoice')" class='btn btn-default'>同意开票</button>
                    @elseif(2==$order['invoice_status'])
                        <span class="label label-success">已开票</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>