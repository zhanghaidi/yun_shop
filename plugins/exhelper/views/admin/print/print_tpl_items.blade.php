<div class="edit-left">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="javascript:;" class="btn btn-default" onclick="addInput()"><i class="fa fa-plus"> 添加内容框</i></a>
            @if($cate==2)<a href="javascript:;" class="btn btn-default" onclick="addInput(1)"><i class="fa fa-plus"> 添加发货商品列表</i></a>@endif
            <a href="javascript:;" class="btn btn-default deleteinput" onclick="delInput()" style="display: none;"><i class="fa fa-times"> 移除内容框</i></a>
        </div>
        <div class="panel-body">
            <p class="item-tip">请先选中您要编辑的内容框</p>
            <div class="items" style="display: none;">
                <div class="form-group cate1">
                    <div class="col-sm-12">
                        <label class="checkbox-inline">
                            <input type="checkbox" value="sendername" title="发件人"> 发件人</label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="sendertel" title="发件人电话"> 发件人电话</label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="senderaddress" title="发件地址"> 发件地址</label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="sendersign" title="发件人签名"> 发件人签名</label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="sendercode" title="发件邮编"> 发件邮编</label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="sendertime" title="发件日期"> 发件日期</label>
                        @if($cate==1)
                        <label class="checkbox-inline">
                            <input type="checkbox" value="sendinfo" title="商品明细"> 商品明细</label>
                        @endif
                        <label class="checkbox-inline">
                            <input type="checkbox" value="shopname" title="商城名称"> 商城名称</label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="sender_city" title="发件城市"> 发件城市</label>
                    </div>
                </div>
                <div class="form-group cate1">
                    <div class="col-sm-12">
                        <label class="checkbox-inline">
                            <input type="checkbox" value="realname" title="收件人"> 收件人</label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="mobile" title="收件人电话"> 收件人电话</label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="province" title="收件省份"> 收件省份</label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="city" title="收件人城市"> 收件人城市</label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="area" title="收件人区域"> 收件人区域</label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="address" title="收件人地址"> 收件人地址</label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="nickname" title="买家昵称"> 买家昵称</label>
                    </div>
                </div>
                <div class="form-group cate2">
                    <div class="col-sm-12">
                        <label class="checkbox-inline"><input type="checkbox" value="printsn" title="序号" data-vd="number"> 序号</label>
                        <label class="checkbox-inline"><input type="checkbox" value="odsn" title="订单编号" data-vd="1036413147"> 订单编号</label>
                        <label class="checkbox-inline"><input type="checkbox" value="goodname" title="商品名称" data-vd="虚拟数据-商品全称"> 商品名称</label>
                        <label class="checkbox-inline"><input type="checkbox" value="goodshortname" title="商品简称" data-vd="商品"> 商品简称</label>
                        <label class="checkbox-inline"><input type="checkbox" value="goodoption" title="商品规格" data-vd="规格0"> 商品规格</label>
                        <label class="checkbox-inline"><input type="checkbox" value="goodunit" title="商品单位" data-vd="件"> 商品单位</label>
                        <label class="checkbox-inline"><input type="checkbox" value="goodssn" title="商品编码" data-vd="100000"> 商品编码</label>
                        <label class="checkbox-inline"><input type="checkbox" value="productsn" title="商品条码" data-vd="693016124515"> 商品条码</label>
                        <label class="checkbox-inline"><input type="checkbox" value="total" title="商品数量" data-vd="1"> 商品数量</label>
                        <label class="checkbox-inline"><input type="checkbox" value="marketprice" title="商品原价(元)" data-vd="12"> 商品原价</label>
                        <label class="checkbox-inline"><input type="checkbox" value="productprice" title="商品现价(元)" data-vd="1"> 商品现价</label>
                        <label class="checkbox-inline"><input type="checkbox" value="realprice" title="商品折后价格(元)" data-vd="1"> 商品折后价</label>
                        <label class="checkbox-inline"><input type="checkbox" value="allprice" title="商品总价(元)" data-vd="21"> 商品总价</label>
                        <label class="checkbox-inline"><input type="checkbox" value="goodweight" title="商品重量(克)" data-vd="1"> 商品重量</label>

                        <label class="checkbox-inline"><input type="checkbox" value="goodsprice" title="商品小计" data-vd="1"> 商品小计</label>
                        <label class="checkbox-inline"><input type="checkbox" value="dispatchprice" title="运费" data-vd="1"> 运费</label>
                        <label class="checkbox-inline"><input type="checkbox" value="discountprice" title="优惠券金额" data-vd="1"> 优惠券金额</label>
                        <label class="checkbox-inline"><input type="checkbox" value="deductprice" title="抵扣金额" data-vd="1"> 抵扣金额</label>
                        <label class="checkbox-inline"><input type="checkbox" value="vipdiscount" title="会员抵扣" data-vd="1"> 会员抵扣</label>
                        {{--<label class="checkbox-inline"><input type="checkbox" value="deductcredit2" title="余额抵扣" data-vd="1"> 余额抵扣</label>
                        <label class="checkbox-inline"><input type="checkbox" value="deductenough" title="满额立减" data-vd="1"> 满额立减</label>
                        <label class="checkbox-inline"><input type="checkbox" value="changeprice2" title="改价优惠" data-vd="1"> 改价优惠</label>
                        <label class="checkbox-inline"><input type="checkbox" value="changedispatchprice" title="运费改价" data-vd="1"> 运费改价</label>
                        <label class="checkbox-inline"><input type="checkbox" value="couponprice" title="优惠券优惠" data-vd="1"> 优惠券优惠</label>--}}
                        <label class="checkbox-inline"><input type="checkbox" value="price" title="实付费" data-vd="1"> 实付费</label>
                        <label class="checkbox-inline"><input type="checkbox" value="note" title="备注" data-vd=""> 手工备注栏</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <div class="input-group-addon">字体</div>
                            <select class="form-control" id="item-font">
                                <option value="微软雅黑">微软雅黑</option>
                                <option value="黑体">黑体</option>
                                <option value="宋体">宋体</option>
                                <option value="新宋体">新宋体</option>
                                <option value="幼圆">幼圆</option>
                                <option value="华文细黑">华文细黑</option>
                                <option value="隶书">隶书</option>
                                <option value="Arial">Arial</option>
                                <option value="Arial Narrow">Arial Narrow</option>
                            </select>

                            <div class="input-group-addon">大小</div>
                            <select class="form-control" id="item-size">
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                                <option value="24">24</option>
                            </select>

                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <div class="input-group-addon">对齐</div>
                            <select class="form-control" id="item-align">
                                <option value="1">居左</option>
                                <option value="2">居中</option>
                                <option value="3">居右</option>
                            </select>
                            <div class="input-group-addon">加粗</div>
                            <select class="form-control" id="item-bold">
                                <option value="">不加粗</option>
                                <option value="bold">加粗</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="input-group" id="item-color">
                            <div class="input-group-addon">颜色</div>

                            <script type="text/javascript">
                                require(["jquery", util_js], function($, util) {
                                    $(function() {
                                        $(".colorpicker").each(function() {
                                            var elm = this;
                                            util.colorpicker(elm, function(color) {
                                                $(elm).parent().prev().prev().val(color.toHexString());
                                                $(elm).parent().prev().css("background-color", color.toHexString());
                                            });
                                        });
                                        $(".colorclean").click(function() {
                                            $(this).parent().prev().prev().val("");
                                            $(this).parent().prev().css("background-color", "#FFF");
                                        });
                                    });
                                });
                            </script>
                            <div class="row row-fix">
                                <div class="col-xs-8 col-sm-8" style="padding-right:0;">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="color" placeholder="请选择颜色" value="">
                                        <span class="input-group-addon" style="width:35px;border-left:none;background-color:"></span>
                                        <span class="input-group-btn">
															<button class="btn btn-default colorpicker" type="button">选择颜色 <i class="fa fa-caret-down"></i></button>
															<button class="btn btn-default colorclean" type="button"><span><i class="fa fa-remove"></i></span></button>
										</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group cate1">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <div class="input-group-addon">前文字</div>
                            <input type="text" id="item-pre" class="form-control">
                            <div class="input-group-addon">后文字</div>
                            <input type="text" id="item-last" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>