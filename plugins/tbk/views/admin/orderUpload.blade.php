@extends('layouts.base')

@section('content')
@section('title', '订单上传')
<div class="page-heading"><h2>订单上传</h2></div>
<div class="w1200 m0a">
    <div class="rightlist">
        <form id="setform" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default'>

                <div class="form-group">
                    <div class="col-sm-9 col-xs-12">
                        <input type="file" name="file" value="选择订单文件"/>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="submit" value="上传" class="btn btn-primary"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection