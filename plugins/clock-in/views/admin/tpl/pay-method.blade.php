

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付方式</label>
    <div class="col-sm-6 col-xs-6">
        @foreach($payMethod as $key => $method)
        <div class='input-group'>
            <label class="radio-inline">
                <input type="checkbox" name="setdata[pay_method][{{$key}}]" value="{{$key}}"
                       @if($set['pay_method'][$key] == $key) checked="checked" @endif /> {{$method}}</label>

        </div>
        @endforeach

    </div>
</div>

