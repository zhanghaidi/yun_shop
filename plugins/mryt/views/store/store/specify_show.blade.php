<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">指定点击门店进入页面</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='store[specify_show]' value='0'
                   @if($store->specify_show == 0) checked @endif
            /> 门店首页
        </label>
        <label class='radio-inline'>
            <input type='radio' name='store[specify_show]' value='1'
                   @if($store->specify_show == 1) checked @endif
            /> 门店商品页
        </label>
        {{--<label class='radio-inline'>--}}
            {{--<input type='radio' name='store[specify_show]' value='2'--}}
                   {{--@if($store->specify_show == 2) checked @endif--}}
            {{--/> 收银台--}}
        {{--</label>--}}
    </div>
</div>