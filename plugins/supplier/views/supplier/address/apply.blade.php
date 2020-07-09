@extends('layouts.base')

@section('css')
    <style>
        select{width: 25%; height: 34px;}
        #saleravatar img{width: 200px; height: 200px;}
    </style>

@endsection
@section('content')
@section('title', '地址更换')
    <div class="rightlist">
        <form action="" method='post' class='form-horizontal'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <span>地址更换</span>
                </div>
                <div class='panel-body'>
                    @if ($data)
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">当前所在区域：</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='input-group'>
                                </div>
                                <span style="font-size:18px" class='help-block'>{{$data->current_address}}</span>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">申请更改区域：</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='input-group'>
                                </div>
                                <span style="color:blue;font-size:18px" class='help-block'>{{$data->province_name}} {{$data->city_name}} {{$data->district_name}}</span>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">状态：</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='input-group'>
                                </div>
                                <span style="color:red;font-size:18px" class='help-block'>
                                    待审核
                                </span>
                            </div>

                        </div>
                    @else
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">地址区域</label>
                            <div class="col-xs-6">
                                {!! app\common\helpers\AddressHelper::tplLinkedAddress(['data[province_id]','data[city_id]','data[district_id]'], [])!!}
                            </div>
                        </div>

                        <div class="form-group"></div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"  />
                            </div>
                        </div>
                    @endif

                </div>
        </form>
    </div>
<script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
    <script language='javascript'>
        cascdeInit();
        $('form').submit(function(){

            var province = $('#sel-provance option:selected');
            var city = $('#sel-city option:selected');

            if (province.val() == 0 || city.val() == 0) {
                Tip.focus($('#mobile'),'请选择省市区!');
                return false;
            }

            return true;
        })

    </script>
@endsection