@extends('layouts.base')

@section('content')
<div class="rightlist">
    <form action="" method='post' class='form-horizontal'>
        <div class='panel panel-default'>
            <div class='panel-heading'>
                <span>供应商详细信息</span>
            </div>
            <div class='panel-body'>
                <div class="form-group notice">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信角色</label>
                    <div class="col-sm-4">
                        <input type='hidden' id='noticeopenid' name='data[member_id]' value="{{$supplier['member_id']}}" />
                        <div class='input-group'>
                            <input type="text" name="saler" maxlength="30" value="@if ($supplier['has_one_member']){{$supplier['has_one_member']['nickname']}}/{{$supplier['has_one_member']['realname']}}/{{$supplier['has_one_member']['mobile']}}@endif" id="saler" class="form-control" readonly />
                            <div class='input-group-btn'>
                                <button class="btn btn-default" type="button" onclick="popwin = $('#modal-module-menus-notice').modal();">选择角色</button>
                                <button class="btn btn-danger" type="button" onclick="$('#noticeopenid').val('');$('#saler').val('');$('#saleravatar').hide()">清除选择</button>
                            </div>
                        </div>
                    <span id="saleravatar" class='help-block' @if (!$supplier['has_one_member'])style="display:none"@endif><img  style="width:100px;height:100px;border:1px solid #ccc;padding:1px" src="{{$supplier['has_one_member']['avatar']}}"/></span>

                    <div id="modal-module-menus-notice"  class="modal fade" tabindex="-1">
                        <div class="modal-dialog" style='width: 920px;'>
                            <div class="modal-content">
                                <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择角色</h3></div>
                                <div class="modal-body" >
                                    <div class="row">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="keyword" value="" id="search-kwd-notice" placeholder="请输入昵称/姓名/手机号" />
                                            <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_members();">搜索</button></span>
                                        </div>
                                    </div>
                                    <div id="module-menus-notice" style="padding-top:5px;"></div>
                                </div>
                                <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                            </div>

                        </div>
                    </div>
                        @if ($supplier)<span style="color:red">更改微信角色，原角色相应数据全部变更为新角色</span>@endif
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">账号</label>
                <div class="col-sm-9 col-xs-12">
                    @if (!$is_add)
                        {{$supplier['has_one_wq_user']['username']}}
                    @else
                        <input type="text" id="username" name="data[username]" class="form-control" value="" placeholder="请输入账号"  />
                    @endif
                </div>
            </div>
            @if ($is_add)
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">密码</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="password" id="password" name="data[password]" class="form-control" value="" placeholder="请输入密码"  />
                    </div>
                </div>
            @endif
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">LOGO</label>
                <div class="col-sm-9 col-xs-12">
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('data[logo]',
                    $supplier['logo'])!!}
                    <span class="help-block">建议尺寸: 100*100，或正方型图片 </span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">真实姓名</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" id="realname" name="data[realname]" class="form-control" value="{{$supplier['realname']}}" placeholder="请输入姓名"  />
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">手机号码</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" id="mobile" name="data[mobile]" class="form-control" value="{{$supplier['mobile']}}" placeholder="请输入手机号码"  />
                </div>
            </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城扣点</label>
                    <div class="col-sm-6 col-xs-6">
                        <div class='input-group'>
                            <input type='text' onkeyup="this.value= this.value.match(/\d+(\.\d{0,2})?/) ? this.value.match(/\d+(\.\d{0,2})?/)[0] : ''" name='data[shop_commission]' class="form-control"
                                   value="{!! $supplier['shop_commission']?:0 !!}"/>
                            <div class='input-group-addon waytxt'>%</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">所在地址</label>
                    <div class="col-xs-6">
                        <input type="hidden" id="province_id" value="{{$supplier['province_id']?$supplier['province_id']:0}}"/>
                        <input type="hidden" id="city_id" value="{{$supplier['city_id']?$supplier['city_id']:0}}"/>
                        <input type="hidden" id="district_id" value="{{$supplier['district_id']?$supplier['district_id']:0}}"/>
                        <input type="hidden" id="street_id" value="{{$supplier['street_id']?$supplier['street_id']:0}}"/>
                        {!! app\common\helpers\AddressHelper::tplLinkedAddress(['data[province_id]','data[city_id]','data[district_id]','data[street_id]'], [])!!}
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">详细地址</label>
                    <div class="col-xs-6">
                        <input type="text" name="data[address]" class="form-control"
                               value="{{$supplier['address']}}"/>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 定位</label>
                    <div class="col-sm-8 col-xs-12" id="map" style="margin-top:0px;width: 70%;">
                        {!! \app\common\helpers\CoordinateHelper::tpl_form_field_coordinate('data', ['lng' => $supplier['lng'], 'lat' => $supplier['lat']]) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">银行账号</label>
                    <div class="col-sm-9 col-xs-12">
                        {{$supplier['company_bank']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">开户人姓名</label>
                    <div class="col-sm-9 col-xs-12">
                        {{$supplier['bank_username']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">开户行</label>
                    <div class="col-sm-9 col-xs-12">
                        {{$supplier['bank_of_accounts']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">开户支行</label>
                    <div class="col-sm-9 col-xs-12">
                        {{$supplier['opening_branch']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">企业支付宝账号</label>
                    <div class="col-sm-9 col-xs-12">
                        {{$supplier['company_ali']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">企业支付宝用户名</label>
                    <div class="col-sm-9 col-xs-12">
                        {{$supplier['company_ali_username']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝账号</label>
                    <div class="col-sm-9 col-xs-12">
                        {{$supplier['ali']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝用户名</label>
                    <div class="col-sm-9 col-xs-12">
                        {{$supplier['ali_username']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信账号</label>
                    <div class="col-sm-9 col-xs-12">
                        {{$supplier['wechat']}}
                    </div>
                </div>

                @if($set['insurance_policy'])
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">保单开启</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio-inline"><input type="radio" class="" name="data[insurance_status]" value="0" @if($supplier['insurance_status'] == 0) checked="checked"@endif /> 关闭</label>
                        <label class="radio-inline"><input type="radio" class="" name="data[insurance_status]" value="1" @if($supplier['insurance_status'] == 1) checked="checked"@endif /> 开启</label>
                    </div>
                </div>
                @endif

            <div class="form-group"></div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"  />
                    <input type="hidden" name="token" value="{{$var['token']}}" />
                    <input type="button" name="back" onclick='history.back()'  value="返回列表" class="btn btn-default" />
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
<script language='javascript'>
    var province_id = $('#province_id').val();
    var city_id = $('#city_id').val();
    var district_id = $('#district_id').val();
    var street_id = $('#street_id').val();
    cascdeInit(province_id, city_id, district_id, street_id);

            $('form').submit(function(){
                if($('#saler').val() == ''){
                    Tip.focus($('#saler'),'请选择微信角色!');
                    return false;
                }
                if($('#username').val() == ''){
                    Tip.focus($('#username'),'请输入账号!');
                    return false;
                }
                if($('#password').val() == ''){
                    Tip.focus($('#password'),'请输入密码!');
                    return false;
                }
                if($('#realname').val() == ''){
                    Tip.focus($('#realname'),'请输入姓名!');
                    return false;
                }
                if($('#mobile').val() == ''){
                    Tip.focus($('#mobile'),'请输入手机号!');
                    return false;
                }
                return true;
            })

    function search_members() {
        if( $.trim($('#search-kwd-notice').val())==''){
            $('#search-kwd-notice').attr('placeholder','请输入关键词');
            return;
        }
        $("#module-menus-notice").html("正在搜索....")
        $.get('{!! yzWebUrl('member.query.index') !!}', {
            keyword: $.trim($('#search-kwd-notice').val())
        }, function(dat){
            $('#module-menus-notice').html(dat);
        });
    }
    function select_member(o) {
        $("#noticeopenid").val(o.uid);
        $("#saleravatar").show();
        $("#saleravatar").find('img').attr('src',o.avatar);
        $("#saler").val( o.nickname+ "/" + o.realname + "/" + o.mobile );
        $("#modal-module-menus-notice .close").click();
    }
</script>
@endsection