

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">租赁系统</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline">
            <input type="radio" name="setdata[is_lease_toy]" value="1"
                   @if($setdata['is_lease_toy'] == 1) checked="checked" @endif /> 开启</label>
        <label class="radio-inline">
            <input type="radio" name="setdata[is_lease_toy]" value="0"
                   @if($setdata['is_lease_toy'] == 0) checked="checked" @endif /> 关闭</label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">起租金额</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input type='text' name='setdata[min_money]' class="form-control discounts_value"
                   value="{{$setdata['min_money']}}"/>
            <div class='input-group-addon waytxt'>元</div>
        </div>
        <span  class='help-block'>如果总租金小于起租金额，则无法下单</span>
    </div>
</div>


<div class='right-titpos'>
    租赁协议
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">标题</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="setdata[pact_title]" class="form-control"
               value="{{ $setdata['pact_title'] }}"/>
        <!-- <span class='help-block'></span> -->
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">内容</label>
    <div class="col-sm-9 col-xs-12">
        {!! yz_tpl_ueditor('setdata[lease_toy_pact]', $setdata['lease_toy_pact']) !!}
    </div>
</div>

<script type="text/javascript">
  require(['bootstrap'], function ($) {
    $(document).scroll(function () {
      var toptype = $("#edui1_toolbarbox").css('position');
      if (toptype == "fixed") {
        $("#edui1_toolbarbox").addClass('top_menu');
      }
      else {
        $("#edui1_toolbarbox").removeClass('top_menu');
      }
    });
  });
</script>