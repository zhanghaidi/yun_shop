@extends('layouts.base')
@section('content')
@section('title', trans('物理路径修改'))
<div class="w1200 m0a">
    <div class="rightlist">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#"><i class="fa fa-circle-o" style="color: #33b5d2;"></i>站点设置</a></li>
            </ul>
        </div>
        @include('layouts.tabs')
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class="panel panel-default">
                <div class='panel-body'>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">旧路径</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="physics[old_url]" class="form-control" value=""/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">新路径</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="physics[new_url]" class="form-control" value=""/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
<script>

</script>
@endsection
