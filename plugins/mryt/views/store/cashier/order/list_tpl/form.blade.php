<form action="" method="get" class="form-horizontal" role="form" id="form1">
    <input type="hidden" name="is_store" value="{{$is_store}}"/>
    <input type="hidden" name="c" value="site"/>
    <input type="hidden" name="a" value="entry"/>
    <input type="hidden" name="m" value="yun_shop"/>
    <input type="hidden" name="do" value="cashier" id="form_do"/>
    <input type="hidden" name="route" value="{{\Yunshop\StoreCashier\admin\OrderController::INDEX_URL}}" id="route"/>

    <div class='form-group col-sm-8 col-lg-5 col-xs-12'>
        <select name="search[ambiguous][field]" id="ambiguous-field"
                class="form-control">
            <option value="order"
                    @if(array_get($search,'ambiguous.field','') =='order')  selected="selected"@endif >
                订单号/支付号
            </option>
            <option value="member"
                    @if( array_get($search,'ambiguous.field','')=='member')  selected="selected"@endif>
                用户姓名/ID/昵称/手机号
            </option>
        </select>
        <input class="form-control" name="search[ambiguous][string]" type="text"
               value="{{array_get($search,'ambiguous.string','')}}"
               placeholder="订单号/支付单号">
    </div>

    <div class='form-group form-group col-sm-8 col-lg-2 col-xs-12'>
        <select name="search[pay_type]" class="form-control">
            <option value=""
                    @if($search['pay_type'] == '')  selected="selected"@endif>
                支付方式
            </option>
            <option value="1"
                    @if($search['pay_type'] == '1')  selected="selected"@endif>
                微信支付
            </option>
            <option value="2"
                    @if($search['pay_type'] == '2')  selected="selected"@endif>
                支付宝支付
            </option>
            <option value="3"
                    @if($search['pay_type'] == '3')  selected="selected"@endif>
                余额支付
            </option>
            <option value="5"
                    @if($search['pay_type'] == '5')  selected="selected"@endif>
                现金支付
            </option>
        </select>
    </div>

    <div class='form-group form-group col-sm-8 col-lg-2 col-xs-12'>
        <select name="search[status]" class="form-control">
            <option value=""
                    @if($search['status'] === '')  selected="selected"@endif>
                订单状态
            </option>
            <option value="0"
                    @if($search['status'] === '0')  selected="selected"@endif>
                待支付
            </option>
            <option value="3"
                    @if($search['status'] == '3')  selected="selected"@endif>
                已完成
            </option>
        </select>
    </div>

    <div class='form-group col-sm-8 col-lg-5 col-xs-12'>
        <select name="search[time_range][field]" class="form-control form-time">
            <option value=""
                    @if( array_get($search,'time_range.field',''))selected="selected"@endif >
                操作时间
            </option>
            <option value="create_time"
                    @if( array_get($search,'time_range.field','')=='create_time')  selected="selected"@endif >
                下单
            </option>
            <option value="pay_time"
                    @if( array_get($search,'time_range.field','')=='pay_time')  selected="selected"@endif>
                付款
            </option>
            <option value="finish_time"
                    @if( array_get($search,'time_range.field','')=='finish_time')  selected="selected"@endif>
                完成
            </option>
        </select>
        {!!
            app\common\helpers\DateRange::tplFormFieldDateRange('search[time_range]', [
    'starttime'=>array_get($search,'time_range.start',0),
    'endtime'=>array_get($search,'time_range.end',0),
    'start'=>0,
    'end'=>0
    ], true)!!}

    </div>

    <input type="hidden"  id="store_id" class="form-control"  name="search[store][store_id]" value=""/>
    <div class="form-group notice">
        <div class="col-sm-4">
            <input type='hidden' id='noticeopenid' name='search[store][cashier_id]' value="{{$search['store']['cashier_id']}}" />
            <div class='input-group'>
                <input type="text" name="memeber" maxlength="30" value="@if ($store){{$store->store_name}}@endif" id="saler" class="form-control" readonly />
                <div class='input-group-btn'>
                    <button class="btn btn-default" type="button" onclick="popwin = $('#modal-module-menus-notice').modal();">选择门店</button>
                    <button class="btn btn-danger" type="button" onclick="$('#noticeopenid').val('');$('#saler').val('');$('#saleravatar').hide()">清除选择</button>
                </div>
            </div>
            <div id="modal-module-menus-notice"  class="modal fade" tabindex="-1">
                <div class="modal-dialog" style='width: 920px;'>
                    <div class="modal-content">
                        <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择角色</h3></div>
                        <div class="modal-body" >
                            <div class="row">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="keyword" value="" id="search-kwd-notice" placeholder="请输入门店名称/门店角色信息" />
                                    <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_members();">搜索</button></span>
                                </div>
                            </div>
                            <div id="module-menus-notice" style="padding-top:5px;"></div>
                        </div>
                        <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-7 col-lg-9 col-xs-12">
            <button class="btn btn-success" id="search"><i class="fa fa-search"></i> 搜索</button>
            <button type="submit" name="export" value="1" id="export" class="btn btn-default">导出
                Excel
            </button>
        </div>
    </div>
</form>