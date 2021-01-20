@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">添加租期</a></li>
            </ul>
        </div>


        @include('layouts.tabs')
        <form action="" method="post" class="form-horizontal form">
            {{--@if(isset($s->id) && !empty($leaseTerm->id))--}}
            <input type="hidden" name="id" class="form-control" value="{{$leaseTerm_id}}"/>
            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                        <div class="col-sm-9 col-xs-12 ">
                            <input type="text" name="term[sequence]" class="form-control" value="{{$leaseTerm->sequence}}"/>
                            <span class='help-block' style="margin-top: 5px">数字越大越靠前</span>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>名称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="term[term_name]" class="form-control" value="{{$leaseTerm->term_name}}"/>
                        </div>
                    </div>
                    <div class="form-group">
                         <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>天数</label>
                        <div class="col-sm-9 col-xs-12 form-inline">
                            <div class='input-group col-sm-6'>

                                <input type="text" name="term[term_days]" class="form-control"
                                       value="{{ $leaseTerm->term_days }}"/>
                                <span class='input-group-addon'>天</span>
                            </div>
                            <!-- <span class='help-block'></span> -->
                        </div>
                    </div>
                     <div class="form-group">
                         <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠</label>
                        <div class="col-sm-9 col-xs-12 form-inline">
                            <div class='input-group col-sm-6'>

                                <input type="text" name="term[term_discount]" class="form-control"
                                       value="{{ $leaseTerm->term_discount }}"/>
                                <span class='input-group-addon'>%</span>
                            </div>
                            <!-- <span class='help-block'></span> -->
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
    <script language='javascript'>
       
    </script>
@include('public.admin.mylink')
@endsection('content')

