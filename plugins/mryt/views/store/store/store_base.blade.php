
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店名称</label>
    <div class="col-xs-6">
        <input type="text" name="store[store_name]" class="form-control"
               value="{{$store->store_name}}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店图片</label>
    <div class="col-sm-9 col-xs-12">
        {!! app\common\helpers\ImageHelper::tplFormFieldImage('store[thumb]',
        tomedia($store->thumb))!!}
        <span class="help-block">建议尺寸: 100*100，或正方型图片 </span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店banner图</label>
    <div class="col-sm-9 col-xs-12">
        {!! app\common\helpers\ImageHelper::tplFormFieldImage('store[banner_thumb]',
        tomedia($store->banner_thumb))!!}
        <span class="help-block">建议尺寸: 414*150，或正方型图片 </span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店地址</label>
    <div class="col-xs-6">
        <input type="hidden" id="province_id" value="{{$store->province_id?$store->province_id:0}}"/>
        <input type="hidden" id="city_id" value="{{$store->city_id?$store->city_id:0}}"/>
        <input type="hidden" id="district_id" value="{{$store->district_id?$store->district_id:0}}"/>
        <input type="hidden" id="street_id" value="{{$store->street_id?$store->street_id:0}}"/>
        {!! app\common\helpers\AddressHelper::tplLinkedAddress(['store[province_id]','store[city_id]','store[district_id]','store[street_id]'], [])!!}
    </div>
</div>

<div class="form-group" style="margin: 25px 0;">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单语音提醒</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='store[audio_open]' value='0'
                   @if($store->audio_open == 0) checked @endif
            /> 关闭
        </label>
        <label class='radio-inline' style="padding-left: 80px;">
            <input type='radio' name='store[audio_open]' value='1'
                   @if($store->audio_open == 1) checked @endif
            /> 开启
        </label>
    </div>
</div>

<div class="form-group" style="margin: 25px 0;">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">经营状态</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='store[operating_state]' value='0'
                   @if($store->operating_state == 0) checked @endif
            /> 经营
        </label>
        <label class='radio-inline' style="padding-left: 80px;">
            <input type='radio' name='store[operating_state]' value='1'
                   @if($store->operating_state == 1) checked @endif
            /> 休息
        </label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">营业时间</label>
    <div class="col-sm-3">
        <div class="input-group clockpicker">
            <input type="text" class="form-control" value="{{$store->business_hours_start}}" name="store[business_hours_start]">
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                            </span>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="input-group clockpicker">
            <input type="text" class="form-control" value="{{$store->business_hours_end}}" name="store[business_hours_end]">
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                            </span>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">资质</label>
    <div class="col-sm-9 col-xs-12">
        {!! app\common\helpers\ImageHelper::tplFormFieldMultiImage('store[aptitude_imgs]',$store->aptitude_imgs) !!}
            <span class="help-block">建议尺寸: 640 * 640 ，或正方型图片 </span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">公告</label>
    <div class="col-xs-6">
        <input type="text" name="store[affiche]" class="form-control"
               value="{{$store->affiche}}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">配送方式</label>
    <div class="col-sm-9 col-xs-12">
        <label for="totalcnf1" class="radio-inline"><input type="checkbox" id="dispatch_type1" name="store[dispatch_type][]" value="1" id="totalcnf1" @if (in_array(1, $store->dispatch_type)) checked="true" @endif /> 快递</label>
        &nbsp;&nbsp;&nbsp;
        <label for="totalcnf2" class="radio-inline"><input type="checkbox" id="dispatch_type2" name="store[dispatch_type][]" value="2" id="totalcnf2"  @if (in_array(2, $store->dispatch_type)) checked="true" @endif /> 自提</label>
        &nbsp;&nbsp;&nbsp;
        <label for="totalcnf3" class="radio-inline"><input type="checkbox" id="dispatch_type3" name="store[dispatch_type][]" value="3" id="totalcnf3"  @if (in_array(3, $store->dispatch_type)) checked="true" @endif /> 核销</label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">详细地址</label>
    <div class="col-xs-6">
        <input type="text" name="store[address]" class="form-control"
               value="{{$store->address}}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 定位</label>
    <div class="col-xs-6" id="map" style="width: 73%; margin-top:0;">
        {!! \app\common\helpers\CoordinateHelper::tpl_form_field_coordinate('store[baidumap]', ['lng' => $store->longitude, 'lat' => $store->latitude]) !!}
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">店铺电话</label>
    <div class="col-xs-6">
        <input type="text" name="store[mobile]" class="form-control"
               value="{{$store->mobile}}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店介绍</label>
    <div class="col-xs-6">
        <input type="text" name="store[store_introduce]" class="form-control"
               value="{{$store->store_introduce}}"/>
    </div>
</div>

{{--<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">任务处理通知模板ID</label>
    <div class="col-xs-6">
        <input type="text" name="store[template_id]" class="form-control"
               value="{{$store->template_id}}"/>
    </div>
</div>--}}

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">选择通知人</label>
    <div class="col-sm-9 col-xs-12">
        <div class='input-group'>
            <input type="text" id='salers' name="store[salers]" maxlength="30"
                   value="@foreach ($store->salers as $saler) {{ $saler['nickname'] }} @endforeach"
                   class="form-control" readonly/>
            <div class='input-group-btn'>
                <button class="btn btn-default" type="button"
                        onclick="popwin = $('#modal-module-menus').modal();">选择通知人
                </button>
            </div>
        </div>
        <div class="input-group multi-img-details" id='saler_container'>
            @foreach ($store->salers as $saler)
                <div class="multi-item saler-item" openid='{{ $saler['openid'] }}'>
                    <img class="img-responsive img-thumbnail" src='{{ $saler['avatar'] }}'
                         onerror="this.src='{{static_url('resource/images/nopic.jpg')}}'; this.title='图片未找到.'">
                    <div class='img-nickname'>{{ $saler['nickname'] }}</div>
                    <input type="hidden" value="{{ $saler['openid'] }}"
                           name="store[salers][{{ $saler['uid'] }}][openid]">
                    <input type="hidden" value="{{ $saler['uid'] }}"
                           name="store[salers][{{ $saler['uid'] }}][uid]">
                    <input type="hidden" value="{{ $saler['nickname'] }}"
                           name="store[salers][{{ $saler['uid'] }}][nickname]">
                    <input type="hidden" value="{{ $saler['avatar'] }}"
                           name="store[salers][{{ $saler['uid'] }}][avatar]">
                    <em onclick="remove_member(this)" class="close">×</em>
                </div>
            @endforeach
        </div>
        <div id="modal-module-menus" class="modal fade" tabindex="-1">
            <div class="modal-dialog" style='width: 920px;'>
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-hidden="true" data-dismiss="modal" class="close"
                                type="button">×
                        </button>
                        <h3>选择通知人</h3></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="input-group">
                                <input type="text" class="form-control" name="keyword" value=""
                                       id="search-kwd" placeholder="请输入粉丝昵称/姓名/手机号"/>
                                <span class='input-group-btn'><button type="button"
                                                                      class="btn btn-default"
                                                                      onclick="store_search_members();">
                                                                搜索
                                                            </button></span>
                            </div>
                        </div>
                        <div id="module-menus" style="padding-top:5px;"></div>
                    </div>
                    <div class="modal-footer"><a href="#" class="btn btn-default"
                                                 data-dismiss="modal" aria-hidden="true">关闭</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店详情</label>
    <div class="col-sm-9 col-xs-12">
        {!! yz_tpl_ueditor('store[information]', $store->information) !!}
    </div>
</div>
