<!-- 提交商品弹出模态框 -->
<div id="modal-add-goods" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:50vw;margin:0px auto;">
    <div class="form-horizontal form">
        <div class="modal-dialog" style="width:100%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>添加到商品库</h3>
                </div>
                <div class="modal-body">
                    <form action="" method="post" class="form-horizontal form" onsubmit="return false;">
                        <div class="form-group">
                            <label class="col-md-2 col-sm-3 col-xs-12 control-label">商品名称</label>
                            <div class="col-md-10 col-sm-9 col-xs-12">
                                <input id="formName" name="name" type="text" class="form-control" value="" required />
                                <span class="help-block">商品名称不得超过14个汉字</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 col-sm-3 col-xs-12 control-label">预览图片</label>
                            <div class="col-md-9 col-sm-9 col-xs-12 thumb-img">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img_url', '') !!}
                                <span class="help-block">图片规则：图片尺寸最大300像素*300像素</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 col-sm-3 col-xs-12 control-label">价格类型</label>
                            <div class="col-md-10 col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input id="priceType1" type="radio" name="price_type" value="1" checked /> 一口价
                                </label>
                                <label class="radio radio-inline">
                                    <input id="priceType2" type="radio" name="price_type" value="2" /> 价格区间
                                </label>
                                <label class="radio radio-inline">
                                    <input id="priceType3" type="radio" name="price_type" value="3" /> 折扣价
                                </label>
                            </div>
                        </div>

                        <div class="form-group price1">
                            <label class="col-md-2 col-sm-3 col-xs-12 control-label">价格</label>
                            <div class="col-md-10 col-sm-9 col-xs-12">
                                <input id="formPrice" name="price" type="number" step="0.01" class="form-control" value="" required />
                            </div>
                        </div>

                        <div class="form-group price2" style="display:none;">
                            <label class="col-md-2 col-sm-3 col-xs-12 control-label">价格(左边界)</label>
                            <div class="col-md-10 col-sm-9 col-xs-12">
                                <input id="formPrice1" name="price1" type="number" step="0.01" class="form-control" value="" required />
                            </div>
                        </div>

                        <div class="form-group price2" style="display:none;">
                            <label class="col-md-2 col-sm-3 col-xs-12 control-label">价格(右边界)</label>
                            <div class="col-md-10 col-sm-9 col-xs-12">
                                <input id="formPrice2" name="price2" type="number" step="0.01" class="form-control" value="" required />
                            </div>
                        </div>

                        <div class="form-group price3" style="display:none;">
                            <label class="col-md-2 col-sm-3 col-xs-12 control-label">原价</label>
                            <div class="col-md-10 col-sm-9 col-xs-12">
                                <input id="formPrice3" name="price3" type="number" step="0.01" class="form-control" value="" required />
                            </div>
                        </div>

                        <div class="form-group price3" style="display:none;">
                            <label class="col-md-2 col-sm-3 col-xs-12 control-label">现价</label>
                            <div class="col-md-10 col-sm-9 col-xs-12">
                                <input id="formPrice4" name="price4" type="number" step="0.01" class="form-control" value="" required />
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="submit" id="submitAddGoods" class="btn btn-primary">确定</button>
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 删除商品弹出模态框 -->
<div id="modal-delete-warning" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:40vw;margin:0px auto;">
    <div class="form-horizontal form">
        <div class="modal-dialog" style="width:100%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3></h3>
                </div>
                <div class="modal-body hide">
                    <div class="form-horizontal form live-list">
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="submitDelGoods" data-url="" class="btn btn-primary">确定</button>
                    <button id="submitDelGoodsForce" data-url="" class="btn btn-primary hide">确定</button>
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 撤销提审弹出模态框 -->
<div id="modal-reset-audit-warning" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:40vw;margin:0px auto;">
    <div class="form-horizontal form">
        <div class="modal-dialog" style="width:100%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>确定撤销提审吗</h3>
                </div>
                <div class="modal-body hide">
                </div>
                <div class="modal-footer">
                    <button id="sureResetAuditGoods" class="btn btn-primary" data-url="">确定</button>
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 重新提审弹出模态框 -->
<div id="modal-audit-warning" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:40vw;margin:0px auto;">
    <div class="form-horizontal form">
        <div class="modal-dialog" style="width:100%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>确定重新提审吗</h3>
                </div>
                <div class="modal-body hide">
                </div>
                <div class="modal-footer">
                    <button id="sureAuditGoods" class="btn btn-primary" data-url="">确定</button>
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 使用直播间弹出模态框 -->
<div id="modal-use-liveroom" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:40vw;margin:0px auto;">
    <div class="form-horizontal form">
        <div class="modal-dialog" style="width:100%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>确定使用该直播间吗</h3>
                </div>
                <div class="modal-body hide">
                </div>
                <div class="modal-footer">
                    <button id="sureUseThisLiveroom" class="btn btn-primary">确定</button>
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>
        </div>
    </div>
</div>
