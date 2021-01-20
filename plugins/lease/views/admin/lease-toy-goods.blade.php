<div class='panel panel-default'>
    <div class='panel-heading'>
        租赁管理
    </div>
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否租赁商品</label>
            <div class="col-sm-4 col-xs-6">

                <label class="radio-inline">
                    <input type="radio" name="widgets[lease_toy][is_lease]" value="1"
                           @if($lease_toy['is_lease'] == '1') checked="checked" @endif /> 是</label>
                <label class="radio-inline">
                    <input type="radio" name="widgets[lease_toy][is_lease]" value="0"
                           @if($lease_toy['is_lease'] == '0') checked="checked" @endif /> 否</label>
            </div>
        </div>
    </div>
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否支持等级权益</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline">
                    <input type="radio" name="widgets[lease_toy][is_rights]" value="1"
                           @if($lease_toy['is_rights'] == '1') checked="checked" @endif /> 是</label>
                <label class="radio-inline">
                    <input type="radio" name="widgets[lease_toy][is_rights]" value="0"
                           @if($lease_toy['is_rights'] == '0') checked="checked" @endif /> 否</label>
            </div>
        </div>
    </div>
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">押金</label>
            <div class="col-sm-9 col-xs-12">
                <div class="input-group">
                    <div class="input-group">
                        <input type="text" name="widgets[lease_toy][goods_deposit]" class="form-control" value="{{ $lease_toy['goods_deposit'] }}" placeholder=""/>
                        <div class="input-group-addon">元</div>
                    </div>
                </div>
                <div class="help-block">租赁商品的押金</div>
            </div>
        </div>
    </div>
     <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">购买商品ID</label>
            <div class="col-sm-9 col-xs-12">
                <div class="input-group">
                    <div class="input-group">
                        <input type="text" name="widgets[lease_toy][immed_goods_id]" class="form-control" value="{{ $lease_toy['immed_goods_id'] }}" placeholder=""/>
                        <!-- <div class="input-group-addon"></div> -->
                    </div>
                </div>
                <!-- <div class="help-block"></div> -->
            </div>
        </div>
    </div>
</div>

<script>

</script>



