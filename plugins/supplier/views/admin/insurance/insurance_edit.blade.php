@extends('layouts.base')

@section('content')
@section('title', '保单编辑')

<style>
    .select select{height:34px;border:#ccc 1px solid;width: 16.7%; margin-left: 15px;}
    #adress_baoxie{
        margin-left: 175px !important;
    }
    .form-group input.form-control, button.btn.btn-default {
        height: 34px;
        width: 550px;
    }
    .filter-option {
        margin-left: 0px;
        overflow-x: hidden;
        overflow-y: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        height: 24px !important;
        line-height: 24px !important;
        width: 100%;
    }
</style>
<div class="w1200 m0a">
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">保单编辑</a></li>
        </ul>
    </div>
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-body">

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label ">序号<span style="color:red;">*</span></label>
                    <div class="col-sm-9 col-xs-12">
                        <input  type="text" id="domain" name="data[serial_number]" class="form-control num" value="{{$data['serial_number'] ?: ''}}" placeholder="请输入序号"  />

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">店面名称<span style="color:red;">*</span></label>
                    <div class="col-sm-9 col-xs-12">
                        <input  type="text" id="domain" name="data[shop_name]" minlength="2" maxlength="30" class="form-control name" value="{{$data['shop_name'] ?: ''}}" placeholder="请输入店面名称"  />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">被保险人<span style="color:red;">*</span></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text"  id="uniacid" name="data[insured]" minlength="2" class="form-control user_name" value="{{$data['insured'] ?: ''}}" placeholder="请输入营业执照公司名称或法人姓名"  />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">证件号码</label>
                    <div class="col-sm-9 col-xs-12">
                        
                        <input type="text"  id="company_bank" name="data[identification_number]" maxlength="23" class="form-control card_index" value="{{$data['identification_number'] ?: ''}}" placeholder="请输入营业执照信用代码、法人身份证号码"  />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">被保险人联系方式</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" id="company_bank" name="data[phone]" minlength="11" class="form-control phone" value="{{$data['phone'] ?: ''}}" placeholder="请输入保险人联系方式"  />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">保险地址<span style="color:red;">*</span></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" id="bank_username" name="data[address]" class="form-control" value="{{$data['address'] ?: ''}}" placeholder="**省**市**区/县**乡/镇**路**号"  />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">投保财产<span style="color:red;">*</span></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" id="bank_of_accounts" name="data[insured_property]" class="form-control" value="{{$data['insured_property'] ?: ''}}" placeholder="需如实填写,尽可能全面"  />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">用户类型<span style="color:red;">*</span></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" id="opening_branch" name="data[customer_type]" class="form-control" value="{{$data['customer_type'] ?: ''}}" placeholder="例如（店铺、仓库、工厂、金店）"  />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">保额（万元）<span style="color:red;">*</span></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" id="company_ali" name="data[insured_amount]" class="form-control" value="{{$data['insured_amount'] ?: ''}}" placeholder="请输入保额"  />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">保险期限（年）<span style="color:red;">*</span></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" id="company_ali_username" name="data[guarantee_period]" class="form-control" value="{{$data['guarantee_period'] ?: ''}}" placeholder="请输入保险期限"  />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">保费（元）<span style="color:red;">*</span></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" id="company_ali_username"  name="data[premium]" class="form-control company_ali_username" value="{{$data['premium'] ?: ''}}" placeholder="请输入保费"  />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">投保险种<span style="color:red;">*</span></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" id="wechat" name="data[insurance_coverage]" class="form-control" value="{{$data['insurance_coverage'] ?: ''}}" placeholder="请输入投保险种"  />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">附加玻璃险（35元保1万）份<span style="color:red;">*</span></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" id="wechat" name="data[additional_glass_risk]" class="form-control" value="{{$data['additional_glass_risk'] ?: ''}}" placeholder="请输入玻璃险金额，1万35元，2万70元"  />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">投保人<span style="color:red;">*</span></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" id="wechat" name="data[insurance_company]" class="form-control" value="{{$data['insurance_company'] ?: ''}}" placeholder="请输入投保人"  />
                    </div>
                </div>

                @if($is_company == 1)
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">保险公司<span style="color:red;"></span></label>
                    <div  class="col-sm-9 col-xs-12">
                        <!-- 保险公司 -->
                        <input type="hidden" id="company_id" name="data[company_id]" value="{{$data->company_id}}">
                        <div class="input-group">
                        <select class="form-control selectpicker" data-live-search="true" name="company_list" id="company_list">
                            <option value="" data-name="">其他公司</option>
                            @foreach ($company_list as $item)
                                <option value="{{$item['id']}}" data-name="{{$item['name']}}">{{$item['name']}}</option>
                            @endforeach
                        </select>
                        <input type='hidden' name='company' id='company'/>

                            {{--<input type="text" name="company" maxlength="30"
                                    value="@if(isset($data['hasOneCompany'])) [{{$data['hasOneCompany']['id']}}]{{$data['hasOneCompany']['name']}} @endif"
                                    id="company" class="form-control" readonly="">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="button"
                                        onclick="popwin = $('#modal-module-menus-company').modal();">
                                    选择保险公司
                                </button>
                                <button class="btn btn-danger" type="button"
                                        onclick="$('#company_id').val('');$('#company').val('');">
                                    清除选择
                                </button>
                            </div>--}}
                        </div>
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">备注信息</label>
                    <div class="col-sm-9 col-xs-6">
                        <textarea name="data[note]" rows="5" class="form-control" placeholder="请输入备注信息">{{$data['note'] ?: ''}}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="submit" name="submit" value="提交" class="btn btn-success"
                        onclick="return func()"   />
                        <input type="button" name="back" onclick='history.back()' style=''
                               value="返回列表"
                               class="btn btn-default back"/>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- 选择弹出框 -->
    <div class="form-group">
        <div class="col-sm-9">
            <div id="modal-module-menus-company" class="modal fade" tabindex="-1">
                <div class="modal-dialog" style='width: 920px;'>
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                            <h3>选择保险公司</h3></div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="keyword" value="" id="search-kwd-ins-company" placeholder="请输入保险公司名称"/>
                                    <span class='input-group-btn'>
                                        <button type="button" class="btn btn-default" onclick="search_company();">搜索</button>
                                    </span>
                                </div>
                            </div>
                            <div id="module-menus-company" style="padding-top:5px;"></div>
                        </div>
                        <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
    <script language='javascript'>
        let datas = {!! json_encode($data)?json_encode($data):'{}' !!};
            let company_id = datas.company_id?datas.company_id:'';

            $(function(){
                if(company_id != '') {
                    $("#company_list").val(company_id)
                }
            });
        var province_id = $('#province_id').val();
        var city_id = $('#city_id').val();
        var district_id = $('#district_id').val();
        var street_id = $('#street_id').val();
        console.log('监听'+street_id);
        cascdeInit(province_id, city_id, district_id, street_id);
        // cascdeInit();
        $('.umphp').hover(function () {
                var url = $(this).attr('data-url');
                $(this).addClass("selected");
            },
            function () {
                $(this).removeClass("selected");
            })
        $('.js-clip').each(function () {
            util.clip(this, $(this).attr('data-url'));
        });
        function func(){
            if ($('.num').val().trim()=='') {
                alert('请输入序号')
                return false;
            }else{
                if ($('.name').val().trim()=='') {
                    alert('请输入店面名称')
                    return false;
                }else{
                    if ($('.user_name').val().trim()=='') {
                        alert('请输入营业执照公司名称或法人姓名')
                        return false;
                    }else{
                        var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
                        var card=$('.card_index').val();
                        if($('.card_index').val()==''){
                            alert('请输入营业执照信用代码、法人身份证号码')
                            return false;
                        }
                        // else if(reg.test(card) === false){
                        //     alert('请输入正确的营业执照信用代码、法人身份证号码')
                        //     return false;
                        // }
                        else{
                            var reg1=/^1[34578]\d{9}$/;
                            var phone=$('.phone').val();
                            if($('.phone').val()==''){
                                alert('请输入联系方式')
                                return false;
                            }else if(reg1.test(phone) === false){
                                alert('请输入保险人联系方式')
                                return false;
                            }else{
                                if ($('#bank_username').val().trim()=='') {
                                    alert('请输入地址、**省**市**区/县**乡/镇**路**号')
                                    return false;
                                }else{
                                    if ($('#bank_of_accounts').val().trim()=='') {
                                        alert('请输入投保财产，需如实填写,尽可能全面')
                                        return false;
                                    }else{
                                        if ($('#opening_branch').val().trim()=='') {
                                            alert('请输入用户类型，例如（店铺、仓库、工厂、金店）')
                                            return false;
                                        }else{
                                            if($('#company_ali').val()==''){
                                                alert('请输入保额')
                                                return false;
                                            }
                                            var reg2=/^[0-9]*$/;
                                            if (reg2.test($('#company_ali').val())==false ) {
                                                alert('请输入数字类型的保额')
                                                return false;
                                            }else{
                                                if($('#company_ali_username').val()==''){
                                                    alert('请输入保险期限')
                                                    return false;
                                                }
                                                if (reg2.test($('#company_ali_username').val())==false) {
                                                    alert('请输入数字类型的保险期限')
                                                    return false;
                                                }else{
                                                    if($('.company_ali_username').val()==''){
                                                        alert('请输入保费')
                                                        return false;
                                                    }
                                                    if (reg2.test($('.company_ali_username').val())==false) {
                                                        alert('请输入数字类型的保费')
                                                        return false;
                                                    }else{
                                                        if ($('#wechat').val().trim()=='') {
                                                            alert('请输入保险公司')
                                                            return false;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $("#company_id").val($("#company_list option:selected").val());
        }

        function search_company() {
            if ($.trim($('#search-kwd-ins-company').val()) == '') {
                Tip.focus('#search-kwd-ins-company', '请输入关键词');
                return;
            }
            $("#module-menus-company").html("正在搜索....");
            $.get('{!! yzWebUrl('plugin.supplier.admin.controllers.insurance.insurance.get-search-ins-company') !!}', {
                    keyword: $.trim($('#search-kwd-ins-company').val())
                }, function (dat) {
                    $('#module-menus-company').html(dat);
                }
            )
            ;
        }

        function select_company(o) {
            $("#company_id").val(o.id);
            $("#company").val("[" + o.id + "]" + o.name);
            $("#modal-module-menus-company .close").click();
        }
    </script>


@endsection

