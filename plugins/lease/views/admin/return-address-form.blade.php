@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">归还地址</a></li>
            </ul>
        </div>


        @include('layouts.tabs')
        <form action="" method="post" class="form-horizontal form">
            {{--@if(isset($returnAddress->id) && !empty($returnAddress->id))--}}

            <input type="hidden" name="id" class="form-control" value="{{$id}}"/>
            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>联系人</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" placeholder="请输入联系人姓名" name="address[contact_name]" class="form-control" value="{{$returnAddress->contact_name}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>手机</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" placeholder="请输入联系人手机号码" name="address[mobile]" class="form-control" value="{{$returnAddress->mobile}}"/>
                        </div>
                    </div>
                     <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">邮编</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="address[zip_code]" class="form-control" value="{{$returnAddress->zip_code}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                        style="color:red">*</span>归还地址</label>
                        <div class="col-xs-6">
                            <input type="hidden" id="province_id" value="{{$returnAddress->province_id?$returnAddress->province_id:0}}"/>
                            <input type="hidden" id="city_id" value="{{$returnAddress->city_id?$returnAddress->city_id:0}}"/>
                            <input type="hidden" id="district_id" value="{{$returnAddress->district_id?$returnAddress->district_id:0}}"/>
                            {!! app\common\helpers\AddressHelper::tplLinkedAddress(['address[province_id]','address[city_id]','address[district_id]'], [])!!}
                            <input type="text"  placeholder="请填写详细地址" name="address[address]" class="form-control" value="{{$returnAddress->address}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否为默认地址</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="address[is_default]" value="1" @if ($returnAddress->is_default == 1) checked @endif /> 是
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="address[is_default]" value="0" @if ($returnAddress->is_default == 0 ) checked @endif/> 否
                            </label>
                        </div>
                    </div>


                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"
                                   onclick="return formcheck()"/>
                            <input type="button" name="back" onclick='history.back()' value="返回列表"
                                   class="btn btn-default back"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script> 
    <script language='javascript'>
        var province_id = $('#province_id').val();
        var city_id = $('#city_id').val();
        var district_id = $('#district_id').val();
        cascdeInit(province_id,city_id,district_id);
    </script>
    @include('public.admin.mylink')
@endsection('content')

