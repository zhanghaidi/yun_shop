<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">店铺名称</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" id="store_name" name="data[store_name]" class="form-control" value="{{ $supplier->store_name ? $supplier->store_name : $supplier->username }}" placeholder="请输入店铺名称"  />
        <span class="">如不填写，默认显示供应商账户名 </span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">LOGO</label>
    <div class="col-sm-9 col-xs-12">
        {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[logo]',
        $supplier->logo)!!}
        <span class="help-block">建议尺寸: 100*100，或正方型图片 </span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">真实姓名</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" id="realname" name="data[realname]" class="form-control" value="{{$supplier->realname}}" placeholder="请输入姓名"  />
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">手机号码</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" id="mobile" name="data[mobile]" class="form-control" value="{{$supplier->mobile}}" placeholder="请输入手机号码"  />
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">所在地址</label>
    <div class="col-xs-6">
        <input type="hidden" id="province_id" value="{{$supplier->province_id?$supplier->province_id:0}}"/>
        <input type="hidden" id="city_id" value="{{$supplier->city_id?$supplier->city_id:0}}"/>
        <input type="hidden" id="district_id" value="{{$supplier->district_id?$supplier->district_id:0}}"/>
        <input type="hidden" id="street_id" value="{{$supplier->street_id?$supplier->street_id:0}}"/>
        {!! app\common\helpers\AddressHelper::tplLinkedAddress(['data[province_id]','data[city_id]','data[district_id]','data[street_id]'], [])!!}
    </div>
</div>


<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">详细地址</label>
    <div class="col-xs-6">
        <input type="text" name="data[address]" class="form-control"
               value="{{$supplier->address}}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 定位</label>
    <div class="col-sm-8 col-xs-12" id="map" style="margin-top:0px;width: 70%;">
        {!! \app\common\helpers\CoordinateHelper::tpl_form_field_coordinate('data', ['lng' => $supplier->lng, 'lat' => $supplier->lat]) !!}
    </div>
</div>