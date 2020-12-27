@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

                <div class='panel panel-default form-horizontal form'>
                    <div class='panel-heading'>基础设置</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">打卡入口链接</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="clock_link" class="form-control"
                                       value="{{ $set['clock_link'] }}"/>
                            </div>
                        </div>
                        <div class="form-group"></div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="保存" class="btn btn-primary col-lg-1"/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

