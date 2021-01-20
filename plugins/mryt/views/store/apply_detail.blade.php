@extends('layouts.base')

@section('content')
@section('title', '门店申请信息')
<div class="w1200 ">
    <div class=" rightlist ">
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">门店申请信息</a></li>
            </ul>
        </div>

        <div class="right-addbox"><!-- 此处是右侧内容新包一层div -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <form id="store_form" action="" method="post" class="form-horizontal form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店名称</label>
                            <div class="col-xs-6">
                                <input type="text" name="store[store_name]" class="form-control"
                                       value="{{$apply->information['store_name']}}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店图片</label>
                            <div class="col-sm-9 col-xs-12">
                                <img src='{{$apply->information['thumb']}}' style='width:50px;height:50px;padding:1px;border:1px solid #ccc' />
                            </div>
                        </div>

                        <div class="form-group notice">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信角色</label>
                            <div class="col-sm-4">
                                <input type='hidden' id='noticeopenid' name='store[uid]' value="{{$apply['uid']}}" />
                                <div class='input-group'>
                                    <input type="text" name="memeber" maxlength="30" value="@if ($apply->hasOneMember){{$apply->hasOneMember->nickname}}/{{$apply->hasOneMember->realname}}/{{$apply->hasOneMember->mobile}}@endif" id="saler" class="form-control" readonly />
                                </div>
                                <span id="saleravatar" class='help-block' @if (!$apply->hasOneMember)style="display:none"@endif><img  style="width:100px;height:100px;border:1px solid #ccc;padding:1px" src="{{$apply->hasOneMember->avatar}}"/></span>

                                <div id="modal-module-menus-notice"  class="modal fade" tabindex="-1">
                                    <div class="modal-dialog" style='width: 920px;'>
                                        <div class="modal-content">
                                            <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择角色</h3></div>
                                            <div class="modal-body" >
                                                <div class="row">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="keyword" value="" id="search-kwd-notice" placeholder="请输入昵称/姓名/手机号" />
                                                        <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_members();">搜索</button></span>
                                                    </div>
                                                </div>
                                                <div id="module-menus-notice" style="padding-top:5px;"></div>
                                            </div>
                                            <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类</label>
                            <div class="col-sm-9 col-xs-12">
                                <select name='store[category_id]' class='form-control'>
                                    @foreach($category_list as $category)
                                        <option value='{{$category->id}}'
                                                @if($apply->information['category_id'] == $category->id)
                                                selected
                                                @endif
                                        >{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店地址</label>
                            <div class="col-xs-6">
                                <input type="hidden" id="province_id" value="{{$apply->information['province_id']?:0}}"/>
                                <input type="hidden" id="city_id" value="{{$apply->information['city_id']?:0}}"/>
                                <input type="hidden" id="district_id" value="{{$apply->information['district_id']?:0}}"/>
                                <input type="hidden" id="street_id" value="{{$apply->information['street_id']?:0}}"/>
                                {!! app\common\helpers\AddressHelper::tplLinkedAddress(['store[province_id]','store[city_id]','store[district_id]','store[street_id]'], [])!!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">营业时间</label>
                            <div class="col-sm-3">
                                <div class="input-group clockpicker">
                                    <input type="text" class="form-control" value="{{$apply->information['business_hours_start']}}" name="store[business_hours_start]">
                                    <span class="input-group-addon">
                            <span class="fa fa-clock-o"></span>
                            </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group clockpicker">
                                    <input type="text" class="form-control" value="{{$apply->information['business_hours_end']}}" name="store[business_hours_end]">
                                    <span class="input-group-addon">
                            <span class="fa fa-clock-o"></span>
                            </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">资质</label>
                            <div class="col-sm-9 col-xs-12">
                                @foreach($apply->information['aptitude_imgs'] as $img)
                                    <img src='{{$img}}' style='width:100px;height:100px;padding:1px;border:1px solid #ccc' />
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">详细地址</label>
                            <div class="col-xs-6">
                                <input type="text" name="store[address]" class="form-control"
                                       value="{{$apply->information['address']}}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">姓名</label>
                            <div class="col-xs-6">
                                <input type="text" name="store[realname]" class="form-control"
                                       value="{{$apply->realname}}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">店铺电话</label>
                            <div class="col-xs-6">
                                <input type="text" name="store[mobile]" class="form-control"
                                       value="{{$apply->mobile}}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店介绍</label>
                            <div class="col-xs-6">
                                <input type="text" name="store[store_introduce]" class="form-control"
                                       value="{{$apply->information['remark']}}"/>
                            </div>
                        </div>

                        @if($set['enter_time_limit'] == 1)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">入驻订单</label>
                                <div class="col-sm-9 col-xs-12">
                                    @if($apply->order_id)
                                        <a href="{!! yzWebUrl('order.detail',array('id'=>$apply->order_id)) !!}"> {{$apply->order->order_sn}}</a>
                                    @else
                                        {{没有购买指定商品}}
                                    @endif
                                </div>
                            </div>

                            @if($apply->order_id)
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店入驻期限</label>
                                    <div class="col-sm-6 col-xs-6">
                                        <div class='input-group'>
                                            <input onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" type='text' name='store[validity]' class="form-control discounts_value"
                                                   value="{{$apply->validity}}"/>
                                            <div class='input-group-addon waytxt'>年</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
<script language='javascript'>
    var province_id = $('#province_id').val();
    var city_id = $('#city_id').val();
    var district_id = $('#district_id').val();
    var street_id = $('#street_id').val();
    cascdeInit(province_id, city_id, district_id, street_id);
</script>
@endsection