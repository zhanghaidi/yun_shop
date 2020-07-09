@extends('Yunshop\Supplier::supplier.layouts.base')

@section('content')
<script type="text/javascript">
    var merchantType;
    window.optionchanged = false;
    require(['bootstrap'], function () {
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
    });
</script>
<div class="rightlist">
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">供应商信息设置</a></li>
            </ul>
        </div>
        @include('Yunshop\Supplier::supplier.tabs')
        <section>
            <form id="setform" action="" method="post" class="form-horizontal form">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane active"
                                 id="tab_basic">
                            @include('Yunshop\Supplier::supplier.tpl.basic')
                        </div>
                            <div class="tab-pane"
                                 id="tab_pay">
                            @include('Yunshop\Supplier::supplier.tpl.pay')
                        </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-xs-12 col-sm-9 col-md-10">
                                <input type="submit" name="submit" value="提交" class="btn btn-success"
                                       onclick="return formcheck()"/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
<script language='javascript'>

    var province_id = $('#province_id').val();
    var city_id = $('#city_id').val();
    var district_id = $('#district_id').val();
    var street_id = $('#street_id').val();
    cascdeInit(province_id, city_id, district_id, street_id);

    var set = {!! json_encode($set) !!};
    $('form').submit(function(){
                if($('#realname').val() == ''){
                    alert('请输入姓名!');
                    return false;
                }
                if($('#mobile').val() == ''){
                    alert('请输入手机号!');
                    return false;
                }
                if($('#company_bank').val() == '' && set.info.company_bank == 1){
                    alert('请输入银行账号!');
                    return false;
                }
                if($('#bank_username').val() == '' && set.info.bank_username == 1){
                    alert('请输入开户人姓名!');
                    return false;
                }
                if($('#bank_of_accounts').val() == '' && set.info.bank_of_accounts == 1){
                    alert('请输入开户行!');
                    return false;
                }
                if($('#opening_branch').val() == '' && set.info.opening_branch == 1){
                    alert('请输入开户支行!');
                    return false;
                }
                if($('#company_ali').val() == '' && set.info.company_ali == 1){
                    alert('请输入企业支付宝账号!');
                    return false;
                }
                if($('#company_ali_username').val() == '' && set.info.company_ali_username == 1){
                    alert('请输入企业支付宝用户名!');
                    return false;
                }
                if($('#ali_username').val() == '' && set.info.ali_username == 1){
                    alert('请输入支付宝用户名!');
                    return false;
                }
                if($('#ali').val() == '' && set.info.ali == 1){
                    alert('请输入支付宝账号!');
                    return false;
                }
                if($('#wechat').val() == '' && set.info.wechat == 1){
                    alert('请输入微信账号!');
                    return false;
                }
                return true;
            })
</script>
@endsection