

{{--<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝登录</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline">
            <input type="radio" name="setdata[]" value="1"
                   @if($setdata[''] == 1) checked="checked" @endif /> 开启</label>
        <label class="radio-inline">
            <input type="radio" name="setdata[]" value="0"
                   @if($setdata[''] == 0) checked="checked" @endif /> 关闭</label>
    </div>
</div>--}}
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">插件提示：</label>
    <div class="col-sm-9 col-xs-12">
        <div class='input-group'>
        </div>
        <span style="color:red" class='help-block'>
          1、开启该插件默认会开启商城<a target="_blank" href="{!! yzWebUrl('setting.shop.member') !!}">强制绑定手机</a>功能,如不想开启可在保存好设置后, 去系统->商城设置->会员里找到(<a target="_blank" href="{!! yzWebUrl('setting.shop.member') !!}">强制绑定手机</a>) 重新设置。
        </span>
         <span style="color:red" class='help-block'>
          2、(同步信息)对新注册绑定手机的会员有效,已注册未绑定手机的会员则无法同步。
        </span>
    </div>

</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">同步信息(默认关闭)</label>
    <div class="col-sm-9 col-xs-12">
        <div class='input-group'>
          <label class="radio-inline">
              <input type="radio" name="setdata[bind_mobile]" value="1"
                     @if($setdata['bind_mobile'] == 1) checked="checked" @endif /> 开启</label>
          <label class="radio-inline">
              <input type="radio" name="setdata[bind_mobile]" value="0"
                     @if($setdata['bind_mobile'] == 0) checked="checked" @endif /> 关闭</label>
        </div>
        <span style="color:red" class='help-block'>
          1、不开启无法同步会员，支付宝账号是一个会员、微信账号是一个会员，两个会员信息。
        </span>
        <span style="color:red" class='help-block'>
          2、会员是根据手机号来同步的，只有绑定过手机号的会员能同步。
        </span>
    </div>

</div>

<!-- <div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">起租金额</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input type='text' name='setdata[min_money]' class="form-control discounts_value"
                   value="{{$setdata['min_money']}}"/>
            <div class='input-group-addon waytxt'>元</div>
        </div>
        <span  class='help-block'>如果总租金小于起租金额，则无法下单</span>
    </div>
</div> -->

<div class='right-titpos'>
    支付宝应用 @if (!empty($setdata['app']))<a href="{!! yzWebUrl('plugin.alipay-onekey-login.admin.set.delAlipaySet') !!}">清空重置</a>@endif
</div>
@if (empty($setdata['app']))
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">APPID</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="setdata[app][alipay_appid]" class="form-control"
               value=""/>
        <!-- <span class='help-block'></span> -->
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">RSA密钥类型</label>
    <div class="col-sm-9 col-xs-12">
        <div class='input-group'>
          <label class="radio-inline">
              <input type="radio" name="setdata[app][rsa]" value="1" /> RSA</label>
          <label class="radio-inline">
              <input type="radio" name="setdata[app][rsa]" value="0" checked="checked"/> RSA2(推荐)</label>
        </div>
        <span  class='help-block'><a href="https://docs.open.alipay.com/291/106097" target="_blank">如何生成密钥?</a></span>
    </div>

</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">应用私钥</label>
    <div class="col-sm-9 col-xs-12">
        <textarea name="setdata[app][private_key]" rows="8" class="form-control"></textarea>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝公钥</label>
    <div class="col-sm-9 col-xs-12">
        <textarea name="setdata[app][alipay_public_key]" rows="6" class="form-control"></textarea>
    </div>
</div>
@endif
<!-- <script type="text/javascript">
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
</script> -->