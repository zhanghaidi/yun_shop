<div class="panel panel-default">
    <div class="panel-heading">
        租赁信息
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">租赁天数 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    {{$order['lease_toy']['days']}}
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">租赁到期时间 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    @if (empty($order['lease_toy']['end_time']))
                        {{$order['status_name']}}
                    @else
                        {{$order['lease_toy']['end_time']}}
                    @endif
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">租赁状态 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    {{$order['status_name']}}
                    
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">押金状态 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    @if ($order['status'] >= 2 )
                        <span class='label label-info'>{{$order['has_one_lease_toy_order']['return_name']}}</span> 
                    @else
                        {{$order['status_name']}}
                    @endif        
                </p>
            </div>
        </div>
        @if ($order['has_one_lease_toy_order']['return_status'] == 1)
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"> </label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    <a class="btn btn-danger btn-sm" href="javascript:;"
                       onclick="$('#modal-lease').find(':input[name=id]').val('{{$order['id']}}')"
                       data-toggle="modal"
                       data-target="#modal-lease">处理归还申请</a>
                </p>
            </div>
        </div>

            @include('Yunshop\LeaseToy::admin.order.lease-return-modal')
        @elseif ($order['has_one_lease_toy_order']['return_status'] == 4 && $order['status'] >= 2)
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"> </label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    <a class="btn btn-danger btn-sm" href="javascript:;" onclick="$('#modal-lease-return').find(':input[name=order_id]').val('{{$order['id']}}')" data-toggle="modal" data-target="#modal-lease-return">确认退还</a>
                </p>
            </div>
        </div>
            @include('Yunshop\LeaseToy::admin.order.confirm-return')
        @elseif ($order['has_one_lease_toy_order']['return_status'] == 2)
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"> </label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    <a class="btn btn-sm " href="javascript:;" title="等待买家退还">等待买家寄回</a>
                </p>
            </div>
        </div>
        @endif
        @if(isset($order['has_one_lease_toy_order']['lease_address']))
            <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">退还地址：</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="form-control-static">
                            {{$order['has_one_lease_toy_order']['lease_address']['address']}}
                        </div>

                    </div>
            </div>
        @endif
        @if(isset($order['has_one_lease_toy_order']['lease_express']))

                @if (!empty($order['has_one_lease_toy_order']['lease_express']['express_company_name']))
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">快递名称 :</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static">{{$order['has_one_lease_toy_order']['lease_express']['express_company_name']}}</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">快递单号 :</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static">{{$order['has_one_lease_toy_order']['lease_express']['express_sn']}}
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <button type='button' class='btn btn-default'
                                        onclick='refundexpress_find(this,"{{$order['id']}}",1)'>查看物流
                                </button>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">填写快递单号时间 :</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static">{{$order['has_one_lease_toy_order']['lease_express']['created_at']}}</div>
                        </div>
                    </div>
                @endif
        @endif
    </div>
</div>

@if($order['has_one_lease_toy_order']['return_status'] == 3)
<div class="panel panel-default">
    <div class="panel-heading">
        归还押金
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">退还金额 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    <span class='label label-success'>{{$order['has_one_lease_toy_order']['return_deposit']}}</span>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"> 逾期扣款 :</label>
            <div class="col-sm-9 col-xs-12">
            <p class="form-control-static">{{$order['has_one_lease_toy_order']['be_overdue']}}
            </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">破损扣款 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">{{$order['has_one_lease_toy_order']['be_damaged']}}</p>
            </div>
        </div>

       <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                说明 :
            </label>
            <div class="col-sm-6 col-xs-12">
                <div class="input-group col-md-9">
                    <p class="form-control-static">{{$order['has_one_lease_toy_order']['explain']}}</p>
                </div>
            </div>
        </div>

    </div>
</div>            

@endif 
