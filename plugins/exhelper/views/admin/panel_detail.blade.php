@extends('layouts.base')
@section('title', trans('面单管理'))
@section('content')
<!-- <div class="w1200 m0a"> -->
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading">编辑信息</div>
            <div class="panel-body">
                <div class="form-group">
                    <p> <b>快递公司支持情况</b>:  顺丰速运、宅急送、圆通速递、百世快递、中通快递、韵达速递、申通快递、德邦快递、优速快递、京东快递、信丰物流、安能快递、国通快递、天天快递、跨越速运、邮政快递包裹、中铁快运、邮政国内标快、远成快运、全一快递、速尔快递、品骏快递。 <br> <b>快运公司支持情况</b>:  德邦快运、安能快运、京东快运、龙邦快运。</p>
                    <p><b>无需申请直接打单</b>: 顺丰（SF）、宅急送（ZJS）、中铁快运（ZTKY）、全一快递（UAPEX）</p>
                    <p><b>月结账号直接打单</b>: 德邦（DBL）</p>
                    <p><b>快递鸟后台申请账号</b>: 优速（UC）、韵达（YD）、圆通（YTO）、远成（YCWL）、安能（ANE）、百世快递（HTKY）</p>
                    <p><b>线下（网点）申请账号</b>： 
                    EMS（广东省内发件不需要, 广东省外EMS发货，需联系当地EMS网点，在85系统里面申请大客户和APP_SECRET）、中通（ZTO）、申通（STO）、德邦（DBL）、京东（JD）、信丰（XFEX）、国通（GTO）、天天快递（HHTT）、速尔快递（SURE）、品骏快递（PJ）</p>
                    <p><b>快运电子面单</b>： 京东快运（JDKY）,安能快运（ANEKY）,德邦快运（DBLKY），龙邦快运（LB） </p>
                    <p><b>顺丰、宅急送等直营型的可以直接使用快递鸟的账户请求电子面单接口。中通，圆通，申通，百世快递，韵达，优速等加盟型的需要客户去当地物流快递网点申请电子面单账户，将相应的参数传入快递鸟电子面单接口进行电子面单请求。</b> </p> 
                    <p><b>将订单号、收寄件地址等信息通过电子面单API传递给快递公司，快递公司会通过接口返回物流单号给到用户端，打印在面单上，就是面单上的运单号。加盟快递公司需要预先充值单号，请联系当地合作网点办理。直营类快递公司，如顺丰、宅急送等，审核后无需预充值，随用随取。</b>
                    </p>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 电子面单名称</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="user[panel_name]" class="form-control" value="@if($item) {{$item->panel_name}} @endif" />
                        <!-- <span class="help-block">如小张，xx商城</span> -->
                    </div>
                </div> 
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'></span> 电子面单客户账号</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="user[panel_no]" class="form-control" value="@if($item) {{$item->panel_no}} @endif"/>
                        <span class="help-block">可输入商家ID、客户简称、商家编码、客户平台ID、客户帐号、客户号、操作编码、商家代码、客户编号、商家cp、大客户号中的一种</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'></span> 电子面单密码</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="user[panel_pass]" class="form-control" value="@if($item) {{$item->panel_pass}} @endif" />
                        <span class="help-block">可输入客户平台验证码、客户密码、接口联调密码、ERP秘钥、客户平台验证码、APP_SECRET中的一种</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>快递类型</label>
                    <div class="col-sm-9 col-xs-12">
                        <select id="exhelper_style" name="user[exhelper_style]" class="form-group">
                            <option value="0">------</option>
                            @foreach($company as $k=>$v)
                            <option value={{$k}} @if ($item->exhelper_style == $k) selected @endif>---{{$v}}---</option>
                            @endforeach
                        </select>
                        <span class="help-block">
                            <p>
                                顺丰、宅急送可以直接使用快递鸟的账户请求电子面单接口。
                            </p>
                            <p>
                                京东，德邦，中通，圆通，申通，百世快递，韵达，安能，优速，快捷等需要客户去当地物流快递网点申请电子面单账户，将相应的参数传入快递鸟电子面单接口进行电子面单请求。
                            </p>
                            </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">模板样式</label>
                    <div class="col-sm-9 col-xs-12">
                        <select id="panel_style" name="user[panel_style]" class="form-group">
                            {{--<option value="@if($item) {{$item->panel_style}} @endif">@if($item) {{$item->panel_style}} @endif</option>--}}
                        </select>
                        <span class="help-block">除品骏快递(PJ)为一联宽80mm、佳吉快运(CNEX)为一联宽90mm、德邦快运(DBLKY)为三联宽100mm,其余快递公司模板样式均为二联,宽100mm,展示的为模板高度</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'></span> 月结编码</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="user[panel_sign]" class="form-control" value="@if($item) {{$item->panel_sign}} @endif" />
                        <!-- <span class="help-block"></span> -->
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'></span>收件网点标识</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="user[panel_code]" class="form-control" value="@if($item) {{$item->panel_code}} @endif" />
                        <span class="help-block">可输入仓库ID、网点名称、网点编号(仓库号)中的一种</span>
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
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否通知快递员上门揽件</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio-inline">
                            <input type="radio" name='user[isself]'  value="1" @if($item->isself == 1) checked @endif /> 是
                        </label>
                        <label class="radio-inline">
                            <input type="radio" id="no" name='user[isself]'  value="0" @if($item->isself == 0) checked @endif /> 否
                        </label>
                    </div>
                </div>

                <div id="time_save" class="form-group" 
                @if (empty($item) || $item->isself != 1 ) style="display: none;"  
                    @endif>
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">通知快递员上门揽件时间</label>'
                        <div class="col-sm-6 col-xs-6">
                            <div class="input-group">
                               <select name="user[date]" id="date">
                                   <option value="{{date('Y-m-d')}}" @if(explode(' ', $item['begin_time'])[0] == date('Y-m-d') ) checked @endif>{{date('Y-m-d')}}</option>
                                   <option value="{{date('Y-m-d', strtotime('-1 day'))}}" @if(explode(' ', $item['begin_time'])[0] == date('Y-m-d', strtotime('-1 day'))) checked @endif>{{date('Y-m-d', strtotime('-1 day'))}}</option>
                                   <option value="{{date('Y-m-d', strtotime('+1 day'))}}" @if(explode(' ', $item['begin_time'])[0] == date('Y-m-d', strtotime('+1 day'))) checked @endif>{{date('Y-m-d', strtotime('+1 day'))}}</option>
                               </select>'
                                <select name="user[begin_time]" id="user[begin_time]">
                                    <option value="8:00" @if(explode(" ", $item["begin_time"])[1] == "8:00:00") selected="selected" @endif>8:00</option>
                                    <option value="9:00" @if(explode(" ", $item["begin_time"])[1] == "9:00:00") selected="selected" @endif>9:00</option>
                                    <option value="10:00" @if(explode(" ", $item["begin_time"])[1] == "10:00:00") selected="selected" @endif>10:00</option>
                                    <option value="11:00" @if(explode(" ", $item["begin_time"])[1] == "11:00:00") selected="selected" @endif>11:00</option>
                                    <option value="12:00" @if(explode(" ", $item["begin_time"])[1] == "12:00:00") selected="selected" @endif>12:00</option>
                                    <option value="13:00" @if(explode(" ", $item["begin_time"])[1] == "13:00:00") selected="selected" @endif>13:00</option>
                                    <option value="14:00" @if(explode(" ", $item["begin_time"])[1] == "14:00:00") selected="selected" @endif>14:00</option>
                                    <option value="15:00" @if(explode(" ", $item["begin_time"])[1] == "15:00:00") selected="selected" @endif>15:00</option>
                                    <option value="16:00" @if(explode(" ", $item["begin_time"])[1] == "16:00:00") selected="selected" @endif>16:00</option>
                                    <option value="17:00" @if(explode(" ", $item["begin_time"])[1] == "17:00:00") selected="selected" @endif>17:00</option>
                                </select>  ---
                                <select name="user[end_time]" id="user[end_time]">
                                    <option value="8:00" @if(explode(" ", $item["end_time"])[1] == "8:00:00") selected="selected" @endif>8:00</option>
                                    <option value="9:00" @if(explode(" ", $item["end_time"])[1] == "9:00:00") selected="selected" @endif>9:00</option>
                                    <option value="10:00" @if(explode(" ", $item["end_time"])[1] == "10:00:00") selected="selected" @endif>10:00</option>
                                    <option value="11:00" @if(explode(" ", $item["end_time"])[1] == "11:00:00") selected="selected" @endif>11:00</option>
                                    <option value="12:00" @if(explode(" ", $item["end_time"])[1] == "12:00:00") selected="selected" @endif>12:00</option>
                                    <option value="13:00" @if(explode(" ", $item["end_time"])[1] == "13:00:00") selected="selected" @endif>13:00</option>
                                    <option value="14:00" @if(explode(" ", $item["end_time"])[1] == "14:00:00") selected="selected" @endif>14:00</option>
                                    <option value="15:00" @if(explode(" ", $item["end_time"])[1] == "15:00:00") selected="selected" @endif>15:00</option>
                                    <option value="16:00" @if(explode(" ", $item["end_time"])[1] == "16:00:00") selected="selected" @endif>16:00</option>
                                    <option value="17:00" @if(explode(" ", $item["end_time"])[1] == "17:00:00") selected="selected" @endif>17:00</option>
                                </select>
                            </div>
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
<!-- </div> -->
<script type="text/javascript">
    $(function(){
        // var html = '';
        let company = {!! json_encode($company) !!};
        let item = {!! json_encode($item) !!};
        console.log(company)
        console.log(item)
        if(!item){
            return;
        }
        if(item.exhelper_style == 'SF') {
            let html = `<option value='150mm' @if ($item->panel_style == '150mm') selected @endif>150mm</option>`;
            html += `<option value='210' @if ($item->panel_style == '210') selected @endif>210mm</option>`;
            html += `<option value='15001' @if ($item->panel_style == '15001') selected @endif>150mm(新二联)</option>`;
            html += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(新二联)</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'EMS') {
            let html = `<option value='150mm' @if ($item->panel_style == '150mm') selected @endif>150mm</option>`;
            html += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'ZJS') {
            let html = `<option value='120mm' @if ($item->panel_style == '120mm') selected @endif>120mm</option>`;
            html += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm</option>`;
            html += `<option value='120' @if ($item->panel_style == '120') selected @endif>120mm(新二联)</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'YTO') {
            let html = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            html += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(三联)</option>`;
            html += `<option value='18001' @if ($item->panel_style == '18001') selected @endif>180mm(新二联)</option>`;
            html += `<option value='130' @if ($item->panel_style == '130') selected @endif>130mm(一联)</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'HTKY') {
            let html = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            html += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(新二联)</option>`;
            html += `<option value='130' @if ($item->panel_style == '130') selected @endif>130mm(一联)</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'ZTO') {
            let html = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            html += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(新二联)</option>`;
            html += `<option value='130' @if ($item->panel_style == '130') selected @endif>130mm(一联)</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'YD') {
            let html = `<option value='203mm' @if ($item->panel_style == '203mm') selected @endif>203mm</option>`;
            html += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm</option>`;
            html += `<option value='130' @if ($item->panel_style == '130') selected @endif>130mm</option>`;
            $("#panel_style").append($(html))
        }
        if(item.exhelper_style == 'STO') {
            let html = `<option value='150' @if ($item->panel_style == '150') selected @endif>150mm</option>`;
            html += `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            html += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(新二联)</option>`;
            html += `<option value='18003' @if ($item->panel_style == '18003') selected @endif>180mm(新三联)</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'DBL') {
            let html = `<option value='177mm' @if ($item->panel_style == '177mm') selected @endif>177mm</option>`;
            html += `<option value='18001' @if ($item->panel_style == '18001') selected @endif>177mm(新二联)</option>`;
            html += `<option value='18002' @if ($item->panel_style == '18002') selected @endif>177mm(新三联)</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'UC') {
            let html = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            $("#panel_style").append($(html))
        }
        if(item.exhelper_style == 'JD') {
            let html1 = `<option value='110mm' @if ($item->panel_style == '110mm') selected @endif>110mm</option>`;
            html1 += `<option value='110' @if ($item->panel_style == '110') selected @endif>110mm(新二联)</option>`;
            $("#panel_style").append($(html))
        }
        if(item.exhelper_style == 'XFEX') {
            let html = `<option value='150mm' @if ($item->panel_style == '150mm') selected @endif>150mm</option>`;
            $("#panel_style").append($(html))
        }
        if(item.exhelper_style == 'ANE') {
            let html = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'HHTT') {
            let html = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'KYSY') {
            let html = `<option value='137mm' @if ($item->panel_style == '137mm') selected @endif>137mm</option>`;
            html += `<option value='210' @if ($item->panel_style == '210') selected @endif>210mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'YZPY') {
            let html = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            html += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(新二联)</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'YZBK') {
            let html = `<option value='150mm' @if ($item->panel_style == '150mm') selected @endif>150mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'YCWL') {
            let html = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'UAPEX') {
            let html = `<option value='150mm' @if ($item->panel_style == '150mm') selected @endif>150mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'SURE') {
            let html = `<option value='150mm' @if ($item->panel_style == '150mm') selected @endif>150mm</option>`;
            html += `<option value='150' @if ($item->panel_style == '150') selected @endif>150mm(新二联)</option>`;
            html += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(新二联)</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'PJ') {
            let html = `<option value='120mm' @if ($item->panel_style == '120mm') selected @endif>120mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'DBLKY') {
            let html = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'ANEKY') {
            let html = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'JDKY') {
            let html = `<option value='110mm' @if ($item->panel_style == '110mm') selected @endif>110mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'LB') {
            let html = `<option value='104mm' @if ($item->panel_style == '104mm') selected @endif>104mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'CND') {
            let html = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'HTKYKY') {
            let html = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'ZTOKY') {
            let html = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'SX') {
            let html = `<option value='105mm' @if ($item->panel_style == '105mm') selected @endif>105mm</option>`;
            $("#panel_style").append($(html));
        }
        if(item.exhelper_style == 'SX') {
            let html = `<option value='130' @if ($item->panel_style == '130') selected @endif>130mm</option>`;
            html += `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
            $("#panel_style").append($(html));
        }
    });
    $(":radio[name='user[isself]']").click(function () {
        if ($(this).val() == 1) {
            console.log('3');
            //追加元素
            $('#time_save').show();
        }
            else {
            console.log('4');
            $("#time_save").hide();
        }
    });

    $('#exhelper_style').change(function(){

        var expressCode = $('#exhelper_style option:selected').val();
        console.log(expressCode);
        var panel_style = $('#panel_style').children();
        console.log(panel_style.text());
        switch(expressCode) {
            case 'SF' :  
                $("#panel_style").empty();
                html1 = `<option value='150mm' @if ($item->panel_style == '150mm') selected @endif>150mm</option>`;
                html1 += `<option value='210' @if ($item->panel_style == '210') selected @endif>210mm</option>`;
                html1 += `<option value='15001' @if ($item->panel_style == '15001') selected @endif>150mm(新二联)</option>`;
                html1 += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(新二联)</option>`;
                $("#panel_style").append($(html1));
                // $('#panel_style').children().val('150mm'); 
                // $('#panel_style').children().text('150mm'); 
                $('#no').parent().hide();
                $("input[name='user[isself]']").attr('checked', true);
                $('#time_save').show();
                // $("input[name='user[isself]']").parent().parent().parent().hide();
            break;
            case 'DBL' :  
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm'); 
                // $('#panel_style').children().text('180mm'); 
                $("#panel_style").empty();
                html1 = `<option value='177mm' @if ($item->panel_style == '177mm') selected @endif>177mm</option>`;
                html1 += `<option value='18001' @if ($item->panel_style == '18001') selected @endif>177mm(新二联)</option>`;
                html1 += `<option value='18002' @if ($item->panel_style == '18002') selected @endif>177mm(新三联)</option>`;
                $("#panel_style").append($(html1));
            break;
            case 'ZTO' :  
                $('#no').parent().show();
                // $('#panel_style').children().val('130mm');
                // $('#panel_style').children().text('130mm');
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('180mm');
                $("#panel_style").empty();
                html1 = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                html1 += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(新二联)</option>`;
                html1 += `<option value='130' @if ($item->panel_style == '130') selected @endif>130mm(一联)</option>`;
                $("#panel_style").append($(html1));
            break;
            case 'ANE' :  
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm'); 
                // $('#panel_style').children().text('180mm'); 
                $("#panel_style").empty();
                html1 = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                $("#panel_style").append($(html1));
            break;
            case 'EMS' :  
                // $('#panel_style').children().val('150mm'); 
                // $('#panel_style').children().text('150mm'); 
                $("#panel_style").empty();
                html1 = `<option value='150mm' @if ($item->panel_style == '150mm') selected @endif>150mm</option>`;
                html1 += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm</option>`;
                $("#panel_style").append($(html1));
            break;
            case 'GTO' :  
                $('#no').parent().show();
                $('#panel_style').children().val('180mm'); 
                $('#panel_style').children().text('180mm'); 
            break;
            case 'HHTT' :
                $('#no').parent().show();  
                // $('#panel_style').children().val('180mm'); 
                // $('#panel_style').children().text('180mm'); 
                $("#panel_style").empty();
                html1 = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                $("#panel_style").append($(html1));
            break;
            case 'HTKY' :
                $('#no').parent().show();  
                // $('#panel_style').children().val('180mm'); 
                // $('#panel_style').children().text('180mm'); 
                $("#panel_style").empty();
                html1 = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                html1 += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(新二联)</option>`;
                html1 += `<option value='130' @if ($item->panel_style == '130') selected @endif>130mm(一联)</option>`;
                $("#panel_style").append($(html1));
            break;
            case 'JD' :
                $('#no').parent().show();  
                // $('#panel_style').children().val('110mm'); 
                // $('#panel_style').children().text('110mm'); 
                $("#panel_style").empty();
                html1 = `<option value='110mm' @if ($item->panel_style == '110mm') selected @endif>110mm</option>`;
                html1 += `<option value='110' @if ($item->panel_style == '110') selected @endif>110mm(新二联)</option>`;
                $("#panel_style").append($(html1));
            break;
            case 'KYSY' :
                $('#no').parent().show();  
                // $('#panel_style').children().val('150mm'); 
                // $('#panel_style').children().text('150mm'); 
                $("#panel_style").empty();
                html1 = `<option value='137mm' @if ($item->panel_style == '137mm') selected @endif>137mm</option>`;
                html1 += `<option value='210' @if ($item->panel_style == '210') selected @endif>210mm</option>`;
                $("#panel_style").append($(html1));
            break;
            case 'STO' :
                $('#no').parent().show();
                $("#panel_style").empty();
                html1 = `<option value='150' @if ($item->panel_style == '150') selected @endif>150mm</option>`;
                html1 += `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                html1 += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(新二联)</option>`;
                html1 += `<option value='18003' @if ($item->panel_style == '18003') selected @endif>180mm(新三联)</option>`;
                $("#panel_style").append($(html1))
                // $('#panel_style').children().val('130mm');
                // $('#panel_style').children().text('130mm');
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('180mm');
            break;
            case 'UC' :
                $('#no').parent().show();  
                // $('#panel_style').children().val('180mm'); 
                // $('#panel_style').children().text('180mm'); 
                $("#panel_style").empty();
                html1 = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                $("#panel_style").append($(html1))
            break;
            case 'XFEX' :
                $('#no').parent().show();  
                // $('#panel_style').children().val('150mm'); 
                // $('#panel_style').children().text('150mm');
                $("#panel_style").empty();
                html1 = `<option value='150mm' @if ($item->panel_style == '150mm') selected @endif>150mm</option>`;
                $("#panel_style").append($(html1)) 
            break;
            case 'YCSY' :
                $('#no').parent().show();  
                $('#panel_style').children().val('180mm'); 
                $('#panel_style').children().text('180mm'); 
            break;
            case 'YD' :
                $('#no').parent().show();
                // $('#panel_style').children().val('210mm');
                // $('#panel_style').children().text('210mm');
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('180mm');
                
                $("#panel_style").empty();
                html1 = `<option value='203mm' @if ($item->panel_style == '203mm') selected @endif>203mm</option>`;
                html1 += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm</option>`;
                html1 += `<option value='130' @if ($item->panel_style == '130') selected @endif>130mm</option>`;
                $("#panel_style").append($(html1))
            break;
            case 'YTO' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('180mm');
                $("#panel_style").empty();
                html1 = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                html1 += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(三联)</option>`;
                html1 += `<option value='18001' @if ($item->panel_style == '18001') selected @endif>180mm(新二联)</option>`;
                html1 += `<option value='130' @if ($item->panel_style == '130') selected @endif>130mm(一联)</option>`;
                $("#panel_style").append($(html1));
            break;
            case 'YZPY' :
                $('#no').parent().show();  
                // $('#panel_style').children().val('180mm'); 
                // $('#panel_style').children().text('180mm'); 
                $("#panel_style").empty();
                html1 = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                html1 += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(新二联)</option>`;
                $("#panel_style").append($(html1));
            break;
            case 'ZTKY' :
                $('#no').parent().show();  
                $('#panel_style').children().val('150mm'); 
                $('#panel_style').children().text('150mm'); 
            break;
            case 'ZJS' :
                $('#no').parent().show();  
                // $('#panel_style').children().val('120mm'); 
                // $('#panel_style').children().text('120mm'); 
                $("#panel_style").empty();
                html1 = `<option value='120mm' @if ($item->panel_style == '120mm') selected @endif>120mm</option>`;
                html1 += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm</option>`;
                html1 += `<option value='120' @if ($item->panel_style == '120') selected @endif>120mm(新二联)</option>`;
                $("#panel_style").append($(html1));
            break;
            case '0':
                $('#no').parent().show();                    
                $('#panel_style').children().val('');
                $('#panel_style').children().text('');
            case 'JD' :  
                $('#panel_style').children().val('110mm'); 
                $('#panel_style').children().text('110mm'); 
            break;

            case 'JTSD' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='130' @if ($item->panel_style == '130') selected @endif>130mm</option>`;
                html1 += `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                $("#panel_style").append($(html1));
                break;
            case 'YZBK' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='150mm' @if ($item->panel_style == '150mm') selected @endif>150mm</option>`;
                $("#panel_style").append($(html1));
                break;
            case 'YCWL' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                $("#panel_style").append($(html1));
                break;
            case 'UAPEX' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='150mm' @if ($item->panel_style == '150mm') selected @endif>150mm</option>`;
                $("#panel_style").append($(html1));
                break;
            case 'SURE' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='150mm' @if ($item->panel_style == '150mm') selected @endif>150mm</option>`;
                html1 += `<option value='150' @if ($item->panel_style == '150') selected @endif>150mm(新二联)</option>`;
                html1 += `<option value='180' @if ($item->panel_style == '180') selected @endif>180mm(新二联)</option>`;
                $("#panel_style").append($(html1));
                break;
            case 'PJ' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='120mm' @if ($item->panel_style == '120mm') selected @endif>120mm</option>`;
                $("#panel_style").append($(html1));
                break;
            case 'DBLKY' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                $("#panel_style").append($(html1));
                break;
            case 'ANEKY' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                $("#panel_style").append($(html1));
                break;
            case 'JDKY' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='110mm' @if ($item->panel_style == '110mm') selected @endif>110mm</option>`;
                $("#panel_style").append($(html1));
                break;
            case 'LB' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='104mm' @if ($item->panel_style == '104mm') selected @endif>104mm</option>`;
                $("#panel_style").append($(html1));
                break;
            case 'CND' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                $("#panel_style").append($(html1));
                break;
            case 'HTKYKY' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                $("#panel_style").append($(html1));
                break;
            case 'ZTOKY' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='180mm' @if ($item->panel_style == '180mm') selected @endif>180mm</option>`;
                $("#panel_style").append($(html1));
                break;
            case 'SX' :
                $('#no').parent().show();
                // $('#panel_style').children().val('180mm');
                // $('#panel_style').children().text('10mm');
                $("#panel_style").empty();
                html1 = `<option value='105mm' @if ($item->panel_style == '105mm') selected @endif>105mm</option>`;
                $("#panel_style").append($(html1));
                break;
        }
    });
</script>
@endsection