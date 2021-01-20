@extends('layouts.base')

@section('content')
@section('title', '添加模板')
<style>
    .form-horizontal .form-group{margin-right: -50px;}
    .col-sm-9{padding-right: 0;}
    .tm .btn { margin-bottom:5px;}
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 0;
        margin: 0;
        border: 0;
        text-overflow: clip;
    }
</style>

<div class="page-heading">
    <h2>消息模板信息</h2>
</div>

<div class="row">
    <div class="col-sm-9">
        <div class="alert alert-danger">
            订单商品详情默认打印，按照打印列格式打印。
            <br>名称&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;单价  数量 金额<br>
                商品名称1&nbsp;&nbsp;100&nbsp;&nbsp;&nbsp;&nbsp;1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;100<br>
            商品名称2&nbsp;&nbsp;100&nbsp;&nbsp;&nbsp;&nbsp;1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;100
        </div>

        <div class="alert alert-warning" style="display:none">
            <a href="#" class="close" data-dismiss="alert">
                &times;
            </a>
            <strong>警告！</strong>打印列格式设置总共长度超过32字符。
        </div>

        <form action="" method="post" class="form-horizontal form-validate" enctype="multipart/form-data">
        <div class="form-group">
            <label class="col-sm-2 control-label must" >模板名称</label>
            <div class="col-sm-9 col-xs-12">
                <input type="text" name="temp[title]" class="form-control" value="{{$temp->title}}" placeholder="小票模版名称，例：订单打印小票" data-rule-required='true' />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" >打印头部</label>
            <div class="col-sm-9 title" style='padding-right:0' >
                <input type="text" name="temp[print_title]" class="form-control" value="{{$temp->print_title}}"/>
                <span class='help-block'>打印头部信息,比如商家名称 建议不超过8个字,会进行加粗处理 </span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" >打印列格式</label>
            <div class="col-sm-9 title" style='padding-right:0' >
                <input type="text" name="temp[print_style]" class="form-control" value="{{$temp->print_style}}"/>
                <span class='help-block'>例如: <span class="text-danger">名称:16|单价:6|数量:5|金额:5</span>  解释: 名字 占据16位,单价占据6位,数量占据5位,金额占据5位;总共每行是32个字符,每个中文或中文标点占用2字符;请严格按照格式来!</span>
            </div>
        </div>

            @foreach($temp->print_data as $key => $row)
                @include('Yunshop\Printer::admin.tpl')
            @endforeach
        <div id="type-items"></div>
        <div class="form-group">
            <label class="col-sm-2 control-label" ></label>
            <div class="col-sm-9 col-xs-12">
                <a class="btn btn-default btn-add-type" href="javascript:;" onclick="addType();"><i class="fa fa-plus" title=""></i> 增加一条键</a>
                <span class='help-block'></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" >打印二维码</label>
            <div class="col-sm-9 col-xs-12">
                    <input type="text" name="temp[qr_code]" class="form-control" value="{{$temp->qr_code}}" placeholder="" id="qrcode"/>
                <span class='help-block'>为空则不显示</span>
            </div>
        </div>
        <div class="form-group"></div>
        <div class="form-group">
            <label class="col-sm-2 control-label" ></label>
            <div class="col-sm-9 col-xs-12">
                <input type="submit"  value="提交" class="btn btn-primary"  />

                <input type="button" name="back" onclick='history.back()' style='margin-left:10px;' value="返回列表" class="btn btn-default" />
            </div>
        </div>

        </form>

    </div>
    <div class="col-sm-3">
        <div class="panel panel-default" style="width:200px;margin-left:20px;" id="printer_preview">
            <h5 class="text-center"></h5>
            <table class="table">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="panel panel-default" style="width:200px;margin-left:20px;">
            <div class="panel-heading">
                <select class="form-control" onchange="$('.tm').hide();$('.tm-' + $(this).val()).show()">
                    <option value="">选择模板变量类型</option>
                    <option value="order">订单打印</option>
                </select>
            </div>
            <div class="panel-heading tm tm-order" style="display:none">订单变量</div>
            <div class="panel-body tm tm-order" style="display:none">
                <a href='JavaScript:' class="btn btn-default btn-sm">订单编号</a>
                <a href='JavaScript:' class="btn btn-default btn-sm">订单金额</a>
                <a href='JavaScript:' class="btn btn-default btn-sm">订单时间</a>
                <a href='JavaScript:' class="btn btn-default btn-sm">订单状态</a>
                <a href='JavaScript:' class="btn btn-default btn-sm">优惠金额</a>
                <a href='JavaScript:' class="btn btn-default btn-sm">抵扣金额</a>
                <a href='JavaScript:' class="btn btn-default btn-sm">收货地址</a>
                <a href='JavaScript:' class="btn btn-default btn-sm">运费</a>
                <a href='JavaScript:' class="btn btn-default btn-sm">备注</a>
                <a href='JavaScript:' class="btn btn-default btn-sm">姓名</a>
                <a href='JavaScript:' class="btn btn-default btn-sm">电话</a>
            </div>

            <div class="panel-footer">
                点击变量后会自动插入选择的文本框的焦点位置，在打印时系统会自动替换对应变量值
                <div class="text text-danger">
                    注意：以上模板消息变量只适用于小票打印
                </div>
            </div>
        </div>
    </div>
</div>


<script language='javascript'>
    var kw = {{$kw}};
    function addType() {
        $(".btn-add-type").button("loading");
        $.ajax({
            url: "{!! yzWebUrl('plugin.printer.admin.temp.tpl', ['kw' => $kw]) !!}",
            cache: false
        }).done(function (html) {
            $(".btn-add-type").button("reset");
            $("#type-items").append(html);
        });
        kw++;
    }

    $('form').submit(function(){

        if($('.key_item').length<=0){
            alert('请添加一条键!');
            $('form').attr('stop',1);
            return false;
        }
        var checkkw = true;
        $(":input[name='temp[print_data][]']").each(function(){
            if ( $.trim( $(this).val() ) ==''){
                checkkw = false;
                alert('请输入键名!');
                $(this).focus();
                $('form').attr('stop',1);
                return false;
            }
        });
        if( !checkkw){
            return false;
        }
        $('form').removeAttr('stop');
        return true;
    });
    $(function () {
        require(['jquery.caret'],function(){
            var jiaodian;
            $(document).on('focus', 'input,textarea',function () {
                jiaodian = this;
            });

            $("a[href='JavaScript:']").click(function () {
                if (jiaodian) {
                    $(jiaodian).insertAtCaret("["+this.innerText+"]" );
                }
            })

        });

        $(document).off('change','input').on('change','input',function(){
            var printer_preview = $("#printer_preview");
            var print_title = $(':input[name="temp[print_title]"]');
            var print_style = $(':input[name="temp[print_style]"]');
            var key = $(':input[name="temp[print_data][]"]');
            var print_style_array = print_style.val().split('|');
            var thead = '';
            var tbody = '';
            var array_len = 0;
            $.each(print_style_array,function (index,iteam) {
                var val_array = iteam.split(':');
                if (val_array.length > 1){
                    array_len = array_len+parseInt(val_array[1]);
                    var width = parseInt(val_array[1])*6.25;
                    thead += '<th style="width: '+width+'px">'+val_array[0]+'</th>';
                }
            });
            if (array_len>32){
                $('.alert').show();
                return false;
            }
            $.each(key,function (index,iteam) {
                tbody += '<tr><td colspan="'+print_style_array.length+'">'+iteam.value+'</td></tr>';
            });
            /*printer_preview.find("table thead").html(thead);
            printer_preview.find("h5").html(print_title.val());
            printer_preview.find("table tbody").html(tbody);*/
        });
    })

</script>
@endsection

