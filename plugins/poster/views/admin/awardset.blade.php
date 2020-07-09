<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">推荐者获得</label>
        <div class="col-sm-5 col-xs-10">
            <div class="input-group">
                <input type="text" name="poster_supplement[recommender_credit]" class="form-control " style="width: 100px" value="{{intval($poster['supplement']['recommender_credit'])}}" />
                <div class="input-group-addon">
                    积分
                </div>
                <input type="text" name="poster_supplement[recommender_bonus]" class="form-control " style="width: 100px" value="@if(!empty($poster['supplement']['recommender_bonus'])){{$poster['supplement']['recommender_bonus']}}@else{{0.00}}@endif" />
                <div class="input-group-addon">
                    元现金.&nbsp;&nbsp;&nbsp;&nbsp;送优惠券
                </div>
                <div class='input-group-btn recgroup'>
                    <input type='hidden' name="poster_supplement[recommender_coupon_id]" data-name="recid" id="reccouponid" value="{{intval($poster['supplement']['recommender_coupon_id'])}}" />
                    <input type='hidden' name="poster_supplement[recommender_coupon_name]" data-name="recname" id="reccouponname" value="{{intval($poster['supplement']['recommender_coupon_name'])}}" />
                    <button id ='presenter'type='button' onclick='selectCoupon("rec")'  class='btn btn-default' style='border-radius:0'>
                        @if(empty($poster['supplement']['recommender_coupon_id']))
                            请选择
                        @else
                            [{{$poster['supplement']['recommender_coupon_id']}}]&nbsp;{{$poster['supplement']['recommender_coupon_name']}}
                        @endif
                    </button>
                </div>
                <input type="text" name="poster_supplement[recommender_coupon_num]" class="form-control" style="width: 100px" value="{{intval($poster['supplement']['recommender_coupon_num'])}}" />
                <div class="input-group-addon">张</div>
                <div class='input-group-btn'>
                <button type="button" class="btn btn-danger" onclick="clearAway(1)">清除选择</button>
                </div>
            </div>

        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">关注者获得</label>
        <div class="col-sm-5 col-xs-10">
            <div class="input-group">
                <input type="text" name="poster_supplement[subscriber_credit]" class="form-control"  style="width: 100px" value="{{intval($poster['supplement']['subscriber_credit'])}}" />
                <div class="input-group-addon">
                    积分
                </div>
                <input type="text" name="poster_supplement[subscriber_bonus]" class="form-control"  style="width: 100px" value="@if(!empty($poster['supplement']['subscriber_bonus'])){{$poster['supplement']['subscriber_bonus']}}@else{{0.00}}@endif" />
                <div class="input-group-addon">
                    元现金.&nbsp;&nbsp;&nbsp;&nbsp;送优惠券
                </div>
                <div class='input-group-btn subgroup'>
                    <input type='hidden' name="poster_supplement[subscriber_coupon_id]" data-name="subid" id='subcouponid' value="{{intval($poster['supplement']['subscriber_coupon_id'])}}" />
                            <input type='hidden' name="poster_supplement[subscriber_coupon_name]" data-name="subname" id='subcouponname' value="{{$poster['supplement']['subscriber_coupon_name']}}" />
                            <button type='button' id="follower" onclick='selectCoupon("sub")' class='btn btn-default' style='border-radius:0'>
                                @if(empty($poster['supplement']['subscriber_coupon_id']))
                                    请选择
                        @else
                            [{{$poster['supplement']['subscriber_coupon_id']}}]&nbsp;{{$poster['supplement']['subscriber_coupon_name']}}
                        @endif
                    </button>
                </div>
                <input type="text" name="poster_supplement[subscriber_coupon_num]" class="form-control"  style="width: 100px" value="{{intval($poster['supplement']['subscriber_coupon_num'])}}" />
                <div class="input-group-addon">张</div>
                <div class='input-group-btn'>
                <button type="button" class="btn btn-danger" onclick="clearAway(2)">清除选择</button>
                </div>
            </div>

        </div>
    </div>


    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">奖励现金方式</label>
        <div class="col-sm-9 col-xs-12">
            <label class="radio-inline">
                <input type="radio" name="poster_supplement[bonus_method]" value="1" checked/> 余额
            </label>
            <label class="radio-inline">
                <input type="radio" name="poster_supplement[bonus_method]" value="2" @if($poster['supplement']['bonus_method'] == 2)checked @endif/> 微信钱包
            </label>
            <span class='help-block'>如果奖励现金, 可以选择打款到用户余额或者是微信钱包 (微信钱包需要开通微信支付，并在后台上传证书)</span>
        </div>
    </div>
</div>

{{--"选择优惠券"的弹窗--}}
<div id="modal-module-menus-coupon"  class="modal fade" tabindex="-1">
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择优惠券</h3>
            </div>
            <div class="modal-body" >
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="search-kwd-coupons" placeholder="请输入优惠券名称" />
                        <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_coupons();">搜索</button></span>
                    </div>
                </div>
                <div id="module-menus-coupon" style="padding-top:5px;">
                </div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
        </div>
    </div>
</div>
<script>
    function clearAway(result)
    {
        if(result == 1){
            $('input[name="poster_supplement[recommender_coupon_id]"]').val('');
            $('input[name=" poster_supplement[recommender_coupon_name]"]').val('');
            $('#presenter').text('请选择');
            $('input[name="poster_supplement[recommender_coupon_num]"]').val('0');
        }
        if(result == 2){
            $('input[name="poster_supplement[subscriber_coupon_id]"]').val('');
            $('input[name="poster_supplement[subscriber_coupon_name]"]').val('');
            $('#follower').text('请选择');
            $('input[name="poster_supplement[subscriber_coupon_num]"]').val('0');
        }
    }
</script>