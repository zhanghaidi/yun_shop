<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分最高抵扣</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[sale][max_point_deduct]' class="form-control discounts_value"
                   value="{{$store->hasOneCashier->hasOneSale->max_point_deduct?str_replace('%', '', $store->hasOneCashier->hasOneSale->max_point_deduct):0}}"/>
            <div class='input-group-addon waytxt'>%</div>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分最少抵扣</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[sale][min_point_deduct]' class="form-control discounts_value"
                   value="{{$store->hasOneCashier->hasOneSale->min_point_deduct?str_replace('%', '', $store->hasOneCashier->hasOneSale->min_point_deduct):0}}"/>
            <div class='input-group-addon waytxt'>%</div>
        </div>
    </div>
</div>
@if(array_key_exists('love', $exist_plugins))
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启{{$love_name}}抵扣</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='widgets[love][deduction]' value='1'
                    @if($exist_plugins['love']['love_goods']['deduction'] == 1) checked @endif
                /> 是
            </label>
            <label class='radio-inline'>
                <input type='radio' name='widgets[love][deduction]' value='0'
                    @if($exist_plugins['love']['love_goods']['deduction'] == 0) checked @endif
                /> 否
            </label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$love_name}}最高抵扣</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                {{--  <input type="hidden" name="widgets[love][deduction]" value="1">  --}}
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[love][deduction_proportion]' class="form-control discounts_value"
                       value="{{$exist_plugins['love']['love_goods']['deduction_proportion']?$exist_plugins['love']['love_goods']['deduction_proportion']:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
@endif

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员奖励积分</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[sale][point]' class="form-control discounts_value"
                   value="{{str_replace('%', '', $store->hasOneCashier->hasOneSale->point)?str_replace('%', '', $store->hasOneCashier->hasOneSale->point):0}}"/>
            <div class='input-group-addon waytxt'>%</div>
        </div>
    </div>
</div>

@if(array_key_exists('love', $exist_plugins))
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启{{$love_name}}奖励</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='widgets[love][award]' value='1'
                    @if($exist_plugins['love']['love_goods']['award'] == 1) checked @endif
                /> 是
            </label>
            <label class='radio-inline'>
                <input type='radio' name='widgets[love][award]' value='0'
                    @if($exist_plugins['love']['love_goods']['award'] == 0) checked @endif
                /> 否
            </label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员奖励{{$love_name}}</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                {{--  <input type="hidden" name="widgets[love][award]" value="1">  --}}
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[love][award_proportion]' class="form-control discounts_value"
                       value="{{$exist_plugins['love']['love_goods']['award_proportion']?$exist_plugins['love']['love_goods']['award_proportion']:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启上级{{$love_name}}奖励</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='widgets[love][parent_award]' value='1'
                       @if($exist_plugins['love']['love_goods']['parent_award'] == 1) checked @endif
                /> 是
            </label>
            <label class='radio-inline'>
                <input type='radio' name='widgets[love][parent_award]' value='0'
                       @if($exist_plugins['love']['love_goods']['parent_award'] == 0) checked @endif
                /> 否
            </label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级会员奖励{{$love_name}}</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[love][parent_award_proportion]' class="form-control discounts_value"
                       value="{{$exist_plugins['love']['love_goods']['parent_award_proportion']?:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">二级会员奖励{{$love_name}}</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[love][second_award_proportion]' class="form-control discounts_value"
                       value="{{$exist_plugins['love']['love_goods']['second_award_proportion']?:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">三级会员奖励{{$love_name}}</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[love][third_award_proportion]' class="form-control discounts_value"
                       value="{{$exist_plugins['love']['love_goods']['third_award_proportion']?:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
@endif

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">赠送优惠券</label>
    <div class="col-sm-6 col-xs-6">
    <div class='input-group'>
        <div id="category" >
            <table class="table">
                <tbody id="param-itemscategory">
                @if($store->hasOneCashier->hasOneCashierGoods->coupon)
                    @foreach($store->hasOneCashier->hasOneCashierGoods->coupon as $k=>$v)
                        <tr>
                            <td>
                                <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
                            </td>
                            <td  colspan="2">
                                <input id="categoryid" type="hidden" class="form-control" name="widgets[cashier][coupon_ids][]" data-id="{{$v['id']}}" data-name="coupon_ids"  value="{{$v['id']}}" style="width:200px;float:left"  />
                                <input id="categoryname" class="form-control" type="text" name="widgets[cashier][coupon_names][]" data-id="{{$v['name']}}" data-name="coupon_names" value="{{$v['name']}}" style="width:200px;float:left" readonly="true">
                                <span class="input-group-btn">
                                    <button class="btn btn-default nav-link" type="button" data-id="{{$v['id']}}" onclick="$('#modal-module-menus-categorys').modal();$(this).parent().parent().addClass('focuscategory')" >选择优惠券</button>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
                <tbody>
                <tr>
                    <td colspan="3">
                        <a href="javascript:;" id='add-param_category' onclick="addParam('category')"
                           style="margin-top:10px;" class="btn btn-primary"  title="添加优惠券"><i class='fa fa-plus'></i> 添加优惠券</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>

<div id="modal-module-menus-categorys" class="modal fade" tabindex="-1"> {{--搜索优惠券的弹窗--}}
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                    ×
                </button>
                <h3>选择优惠券</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value=""
                               id="search-kwd-categorys" placeholder="请输入优惠券名称"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_categorys();">搜索
                            </button>
                        </span>
                    </div>
                </div>
                <div id="module-menus-categorys" style="padding-top:5px;"></div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default"
                                         data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员折扣</label>
    <input type="hidden" name="widgets[discount][discount_method]" value="1">
</div>

<div id="ismember">
    @foreach ($levels as $level)
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
            <div class="col-sm-6 col-xs-6">
                <div class='input-group'>
                    <div class='input-group-addon'>{{$level['level_name']}}</div>

                    <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[discount][discount_value][{{$level["id"] }}]'
                           class="form-control discounts_value"
                           value="@if (!empty($discountValue)){{ $discountValue[$level["id"]] }}@endif"/>
                    <div class='input-group-addon waytxt'>折</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if(array_key_exists('love', $exist_plugins))
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">商家奖励{{$love_name}}</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[cashier][plugins][love][award_shop]' class="form-control discounts_value"
                       value="{{$store->hasOneCashier->hasOneCashierGoods->plugins['love']['award_shop']?$store->hasOneCashier->hasOneCashierGoods->plugins['love']['award_shop']:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级商家奖励{{$love_name}}</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[cashier][plugins][love][award_shop1]' class="form-control discounts_value"
                       value="{{$store->hasOneCashier->hasOneCashierGoods->plugins['love']['award_shop1']?:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">二级商家奖励{{$love_name}}</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[cashier][plugins][love][award_shop2]' class="form-control discounts_value"
                       value="{{$store->hasOneCashier->hasOneCashierGoods->plugins['love']['award_shop2']?:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
@endif

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">奖励商家积分</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[cashier][shop_award_point]' class="form-control discounts_value"
                   value="{{$store->hasOneCashier->hasOneCashierGoods->shop_award_point?$store->hasOneCashier->hasOneCashierGoods->shop_award_point:0}}"/>
            <div class='input-group-addon waytxt'>%</div>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级商家奖励积分</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[cashier][shop_award_point1]' class="form-control discounts_value"
                   value="{{$store->hasOneCashier->hasOneCashierGoods->shop_award_point1?:0}}"/>
            <div class='input-group-addon waytxt'>%</div>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">二级商家奖励积分</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[cashier][shop_award_point2]' class="form-control discounts_value"
                   value="{{$store->hasOneCashier->hasOneCashierGoods->shop_award_point2?:0}}"/>
            <div class='input-group-addon waytxt'>%</div>
        </div>
    </div>
</div>
<script language='javascript'>
    function deleteParam(o) {
        $(o).parent().parent().remove();
    }
</script>