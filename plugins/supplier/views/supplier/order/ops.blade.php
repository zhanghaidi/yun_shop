
@if ($order['status'] == 0)
<a class="label label-default">等待付款</a>
@endif

@if ($order['status'] == 1)

    <div>
        <input class='addressdata' type='hidden' value='{!! json_encode($order['address']) !!}' />
        <input class='itemid' type='hidden' value="{{$order['id']}}"/>
        <a class="btn btn-success btn-sm disbut" href="javascript:;" onclick="send(this)"  data-toggle="modal"
           data-target="#modal-confirmsend">确认发货</a>
    </div>

@endif

@if ($order['status'] == 2)
<a class="btn btn-danger btn-sm disbut" href="javascript:;"
   onclick="$('#modal-cancelsend').find(':input[name=order_id]').val('{{$order['id']}}')" data-toggle="modal"
   data-target="#modal-cancelsend">取消发货</a>
<a class="btn btn-default btn-sm disbut">等待收货</a>
@endif



