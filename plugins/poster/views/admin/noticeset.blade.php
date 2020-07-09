<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">推荐者的奖励通知</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="poster_supplement[recommender_award_title]" class="form-control"
               value="{{$poster['supplement']['recommender_award_title']}}" />
        <span class="help-block">标题: 默认'推荐关注奖励通知'</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-9 col-xs-12">
        <textarea name="poster_supplement[recommender_award_notice]" class="form-control"
                 >{{$poster['supplement']['recommender_award_notice']}}</textarea>
        <span class="help-block">例如: [nickname] 通过您的二维码关注了公众号,
                            您因此获得了 [credit] 个{{$shop_credit1}} [money] 元奖励!</span>
        <span class="help-block">变量: [nickname]为扫码者昵称, [credit]为奖励的{{$shop_credit1}}个数,
                            [money]为奖励的现金数目(单位为"元"),[couponname]为优惠券名称, [couponnum]为优惠券张数</span>
        <span class="help-block">文字内容只能保存140个字符，含中文、数字以及字符等等</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">关注者的奖励通知</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="poster_supplement[subscriber_award_title]" class="form-control"
               value="{{$poster['supplement']['subscriber_award_title']}}" />
        <span class="help-block">标题: 默认'关注奖励通知'</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-9 col-xs-12">
        <textarea name="poster_supplement[subscriber_award_notice]" class="form-control">{{$poster['supplement']['subscriber_award_notice']}}</textarea>
        <span class="help-block">例如: 您扫描了 [nickname] 的二维码关注了公众号,
                            因此获得了 [credit] 个{{$shop_credit1}} [money] 元奖励!</span>
        <span class="help-block">变量: [nickname]为推荐者昵称, [credit]为奖励的{{$shop_credit1}}个数,
                            [money]为奖励的现金数目(单位为"元"),[couponname]为优惠券名称, [couponnum]为优惠券张数 </span>
        <span class="help-block">文字内容只能保存140个字符，含中文、数字以及字符等等</span>
    </div>
</div>