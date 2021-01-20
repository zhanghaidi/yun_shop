@extends('layouts.base')

@section('content')
@section('title', '淘宝客设置')
<div class="page-heading"><h2>淘宝客设置</h2></div>
<div class="w1200 m0a">
    <div class="rightlist">
        <form id="setform" action="" method="post" class="form-horizontal form">
            <div class='panel panel-default'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">APPKEY</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" value="{{$set[appkey]}}" name='setdata[appkey]' />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">密钥secret</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" value="{{$set[secret]}}" name='setdata[secret]' />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">AD_ZONE_ID</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" value="{{$set[ad_zone_id]}}" name='setdata[ad_zone_id]' />
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="submit" value="提交" class="btn btn-primary"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection