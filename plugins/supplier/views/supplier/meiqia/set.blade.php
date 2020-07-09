@extends('Yunshop\Supplier::supplier.layouts.base')
@section('content')
@section('title', trans('客服链接'))
<div class="w1200 m0a">
    <div class="rightlist">
        <form id="setform" action="" method="post" class="form-horizontal form">
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    客服链接
                </div>
                <div class="form-group"></div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">客服链接</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="setdata[meiqia]" class="form-control" value="{{ $set['meiqia']}}" />
                            <span class='help-block'>支持任何客服系统的聊天链接，例如QQ、企点、53客服、百度商桥等
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg"
                               onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection
