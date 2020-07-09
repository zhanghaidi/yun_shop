@extends('layouts.base')

@section('content')
@section('title', trans('单个打印'))
<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">查找订单</div>
        <div style="padding: 10px; padding-bottom: 0px;" class="panel-body">
            <div class="alert alert-info">数据量大可能会引起卡顿，请在搜索前根据需要选择您的搜索条件。</div>
            <form id="form1" role="form" class="form-horizontal" method="post" action="">
                <div class="form-group">
                    <div class="col-sm-8 col-lg-12 col-xs-12">
                        <div class="input-group">
                            <div class="input-group-addon">订单号</div>
                            <input type="text" placeholder="订单号" value="" name="search[order_sn]" class="form-control">
                            <div class="input-group-addon">快递单号</div>
                            <input type="text" placeholder="快递单号" value="" name="search[express_sn]" class="form-control">
                            <div class="input-group-addon">用户信息</div>
                            <input type="text" placeholder="用户手机号/姓名/昵称" value="" name="search[member]" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 col-lg-12 col-xs-12">
                        <div class="input-group">
                            <div class="input-group-addon">订单状态</div>
                            <select class="form-control" name="search[order_status]">
                                <option value="">全部</option>
                                <option value="0">待付款</option>
                                <option value="1" selected="">待发货</option>
                                <option value="2">已发货</option>
                                <option value="3">已完成</option>
                                <option value="-1">已关闭</option>
                            </select>
                            <div class="input-group-addon">快递单打印状态</div>
                            <select class="form-control" name="search[express_print_status]">
                                <option value="">全部</option>
                                <option value="no">未打印</option>
                                {{--<option value="1">部分打印</option>--}}
                                <option value="end">打印完成</option>
                            </select>
                            <div class="input-group-addon">发货单打印状态</div>
                            <select class="form-control" name="search[send_print_status]">
                                <option selected="" value="">全部</option>
                                <option value="no" >未打印</option>
                                {{--<option value="1">部分打印</option>--}}
                                <option value="end">打印完成</option>
                            </select>
                            <div class="input-group-addon">电子面单打印状态</div>
                            <select class="form-control" name="search[panel_print_status]">
                                <option selected="" value="">全部</option>
                                <option value="no" >未打印</option>
                                {{--<option value="1">部分打印</option>--}}
                                <option value="end">打印完成</option>
                            </select>
                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-8 col-lg-12 col-xs-12">
                        <div class="input-group">
                            <div class="input-group-addon">下单时间</div>
                            <div class="col-sm-3">
                                <label class="radio-inline">
                                    <input type="radio" name="search[create_order_time]" value="0" checked="checked">不搜索
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="search[create_order_time]" value="1">搜索
                                </label>
                            </div>
                            <div style="height: 34px; float: left;">
                                <script type="text/javascript">
                                    require(["daterangepicker"], function($) {
                                        $(function() {
                                            $(".daterange.daterange-time").each(function() {
                                                var elm = this;
                                                $(this).daterangepicker({
                                                    startDate: $(elm).prev().prev().val(),
                                                    endDate: $(elm).prev().val(),
                                                    format: "YYYY-MM-DD HH:mm",
                                                    timePicker: true,
                                                    timePicker12Hour: false,
                                                    timePickerIncrement: 1,
                                                    minuteStep: 1
                                                }, function(start, end) {
                                                    $(elm).find(".date-title").html(start.toDateTimeStr() + " 至 " + end.toDateTimeStr());
                                                    $(elm).prev().prev().val(start.toDateTimeStr());
                                                    $(elm).prev().val(end.toDateTimeStr());
                                                });
                                            });
                                        });
                                    });
                                </script>

                                <input type="hidden" value="{!! date('Y-m-d H:m', strtotime('-1 month')) !!}" name="time[start]">
                                <input type="hidden" value="{!! date('Y-m-d H:m') !!}" name="time[end]">
                                <button type="button" class="btn btn-default daterange daterange-time" data-original-title="" title=""><span class="date-title"><?php echo date('Y-m-d H:i:s', strtotime("-1 month"))?> 至 <?php echo date('Y-m-d H:i:s',time())?></span> <i class="fa fa-calendar"></i></button>
                            </div>
                            <div style="height: 34px; width: 150px; float: left; padding-left: 5px;">
                                <button id="search-btn" onclick="search()" class="btn btn-primary" type="button" data-original-title="" title=""><i class="fa fa-search"></i> 搜索</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="orderresult" style="height: auto; overflow: hidden;"></div>
</div>
</div>
<!--  -->
 

<script language="javascript">
    var LODOP=getCLodop();
    // 执行搜索
    function search(){
        var data = {
            order_sn: $.trim($(":input[name='search[order_sn]']").val()),
            express_sn: $.trim($(":input[name='search[express_sn]']").val()),
            member: $.trim($(":input[name='search[member]']").val()),
            order_status: $.trim($("select[name='search[order_status]']").val()),
            express_print_status: $.trim($("select[name='search[express_print_status]']").val()),
            send_print_status: $.trim($("select[name='search[send_print_status]']").val()),
            panel_print_status: $.trim($("select[name='search[panel_print_status]']").val()),
            create_order_time: $(":input[name='search[create_order_time]']:checked").val(),
            time: {start: $(":input[name='time[start]']").val(),end: $(":input[name='time[end]']").val()}
        };
        $('#search-btn').html("<i class='fa fa-spinner fa-spin'></i> 正在搜索...");
        $.ajax({
            url: "{!! yzWebUrl('plugin.exhelper.admin.print-once.search') !!}",
            data: {search:data},
            success:function(html){
                $('#search-btn').html("<i class='fa fa-search'></i> 搜索")
                $('#orderresult').html(html);

                $('.order_item').click(function(){
                    $('#orders').find('.panel-body').html("<i class='fa fa-spinner fa-spin'></i> 正在加载...")
                    $.ajax({
                        url: "{!! yzWebUrl('plugin.exhelper.admin.print-once.detail') !!}",
                        data: {orderids: $(this).data('orderids')},
                        success:function(html){
                            $('#orders').html(html);
                        }
                    });
                })

            }
        });
    }
</script>
@endsection