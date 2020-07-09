@extends('layouts.base')

@section('content')
@section('title', '打印设置')
<div class="page-heading"><h2>打印设置</h2></div>
<div class="w1200 m0a">
    <div class="rightlist">
        <form id="setform" action="" method="post" class="form-horizontal form">
            <div class='panel panel-default'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">选择打印模板</label>
                    <div class="col-sm-9 col-xs-12">
                        <select class='form-control print_select' name='setdata[temp_id]'>
                            <option value="0">选择您需要的订单打印模板</option>
                            @foreach($temps as $temp)
                                <option value="{{$temp->id}}" @if ($print_set->temp_id == $temp->id) selected @endif>{{$temp->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">选择打印机</label>
                    <div class="col-sm-9 col-xs-12">
                        <select class='form-control print_select' name='setdata[printer_id]'>
                            <option value="0">选择您需要的打印机</option>
                            @foreach($printers as $print)
                                <option value="{{$print->id}}" @if ($print_set->printer_id == $print->id) selected @endif>{{$print->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单打印方式<br>(不选不打印)</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="checkbox-inline">
                            <input type="checkbox" value="1" name='setdata[print_type][]' @if (in_array('1',$print_set->print_type)) checked="true" @endif /> 下单打印
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="2" name='setdata[print_type][]' @if (in_array('2',$print_set->print_type)) checked="true" @endif /> 付款打印
                        </label>
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
<script type="text/javascript">
    require(['select2'], function () {
        $('.print_select').select2();
    })
</script>
@endsection