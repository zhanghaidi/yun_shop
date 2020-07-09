@extends('layouts.base')

@section('content')
@section('title', trans('发货人信息'))
<div class="w1200 m0a">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
    <div class="panel panel-default">
        <div class="panel-heading">发货人信息</div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 发件人</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="user[sender_name]" class="form-control" value="{{$item->sender_name}}" />
                    <span class="help-block">如小张，xx商城</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 联系电话</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="user[sender_tel]" class="form-control" value="{{$item->sender_tel}}" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 发件地邮编</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="user[sender_code]" class="form-control" value="{{$item->sender_code}}" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 发件地址</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="user[sender_address]" class="form-control" value="{{$item->sender_address}}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 发件省份</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="user[sender_province]" class="form-control" value="{{$item->sender_province}}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 发件城市</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="user[sender_city]" class="form-control" value="{{$item->sender_city}}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 发件区域</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="user[sender_area]" class="form-control" value="{{$item->sender_area}}" />
                </div>
            </div>
             <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 发件街道</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="user[sender_street]" class="form-control" value="{{$item->sender_street}}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>发件人签名</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="user[sender_sign]" class="form-control" value="{{$item->sender_sign}}" />
                    <span class="help-block">如小张，小王</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否为默认模板</label>
                <div class="col-sm-9 col-xs-12">
                    <label class="radio-inline">
                        <input type="radio" name='user[isdefault]' value="1" @if($item->isdefault == 1) checked @endif /> 是
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name='user[isdefault]' value="0" @if($item->isdefault == 0) checked @endif /> 否
                    </label>
                </div>
            </div>
            <div class='panel-body'>
                <div class="form-group"></div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
                        <input type="button" name="back" onclick='history.back()' style='margin-left:10px;' value="返回列表" class="btn btn-default col-lg" />
                    </div>
                </div>

            </div>
        </div>
        </div>
        </form>
    </div>
</div>
@endsection