<table class='table'
       style='float:left;margin-bottom:0;table-layout: fixed;line-height: 40px;height: 40px'>
    <tr class='trhead'>
        <td colspan='8' style="text-align: left;">
            订单数: <span id="total">{{$list->total()}}</span>
            订单金额: <span id="totalmoney" style="color:red">{{$total_price}}</span>元&nbsp;<br>
            {{--<input id="get_statistic" class="get_statistic" type="button" onclick="getStatistic()" class="btn btn-default back" value="统计详情">
            <div style="display:none" id="statistics">
                已提现金额:<span class="has_settlement"></span>元
                未提现金额:<span class="no_settlement"></span>元<br>

                积分抵扣金额:<span class="deduct_point"></span>元
                @if($is_open_love)
                    爱心值抵扣金额:<span class="deduct_love"></span>元
                @endif
                优惠券抵扣金额:<span class="deduct_coupon"></span>元<br>

                会员积分奖励数量:<span class="remard_buyer_point"></span>
                @if($is_open_love)
                    会员爱心值奖励数量:<span class="remard_buyer_love"></span>
                @endif
                会员优惠券奖励数量:<span class="remard_buyer_coupon"></span>
                商家积分奖励数量:<span class="remard_store_point"></span>
                @if($is_open_love)
                    商家爱心值奖励数量:<span class="remard_store_love"></span>
                @endif
            </div>--}}
        </td>
    </tr>
</table>