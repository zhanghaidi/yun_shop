
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>套餐名称</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="package[title]" id="package_title" class="form-control" value="{{$package['title']}}" />
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">套餐图片</label>
    <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
        {!! app\common\helpers\ImageHelper::tplFormFieldImage('package[thumb]', $package['thumb']) !!}
        <span class="help-block">建议尺寸: 640 * 640 ，或正方型图片 </span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>套餐商品</label>
    <div class="col-sm-9 usetype usetype2">
        <div class='input-group'>
            <div id="goods">
                <table class="table">
                    <th class="col-sm-1">排序</th>
                    <th class="col-sm-2">栏目名称</th>
                    <th class="col-sm-8">商品</th>
                    <th class="col-sm-1">删除</th>
                    <tbody id="param-itemsgoods">
                        @foreach($package['has_many_category'] as $category)
                        <tr>
                            <td>
                                <input class="form-control" type="hidden" name="package[category][id][]" value="{{$category['id']}}"  />
                                <input class="form-control" type="hidden" name="package[category][category_package_id][]" value="{{$category['category_package_id']}}"  />
                                <input class="form-control" id="goods_sort" name="package[category][sort][]" data-id="{{$category['category_sort']}}" value="{{$category['category_sort']}}" style="width:50px;float:left"  />
                            </td>
                            <td>
                                <input class="form-control" id="cate_name" name="package[category][cate_name][]" data-id="{{$category['category_name']}}"  value="{{$category['category_name']}}" style="width:120px;float:left"  />
                            </td>
                            <td>
                                <input id="goodsid" type="hidden" class="form-control" name="package[category][goods_ids][]" data-id="{{$category['category_goods_ids']}}" data-name="goodsids"  value="{{$category['category_goods_ids']}}" style="width:200px;float:left"  />
                                <input id="goodsname" class="form-control" type="text" name="package[category][goods_names][]" data-id="{{$category['category_goods_names']}}" data-name="goodsnames" value="{{$category['category_goods_names']}}" style="width:450px;float:left" readonly="true">
                                <span class="input-group-btn">
                                    <button class="btn btn-default nav-link-goods" type="button" data-id="{{$v}}" onclick="$('#modal-module-menus-goods').modal();$(this).parent().parent().addClass('focusgood')">选择商品</button>
                                </span>
                            </td>
                            <td>
                                <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tbody>
                    <tr>
                        <td colspan="3">
                            <a href="javascript:;" id='add-param_goods' onclick="addParam('goods')"
                               style="margin-top:10px;" class="btn btn-primary" title="添加商品"><i class='fa fa-plus'></i> 添加商品</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal-module-menus-goods" class="modal fade" tabindex="-1"> {{--搜索商品的弹窗--}}
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                    ×
                </button>
                <h3>选择商品</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value=""
                               id="search-kwd-goods" placeholder="请输入商品名称"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_goods();">搜索</button>
                        </span>
                    </div>
                </div>
                <div id="module-menus-goods" style="padding-top:5px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="confirm_select();">确认</button>
                <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>

    </div>
</div>



<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>套餐优惠价格</label>
    <div class="col-sm-6 col-xs-6">
        <input type="text" name="package[on_sale_price]" id="unit" class="form-control" value="{{$package['on_sale_price']}}" />
        <span class="help-block">套餐商品的现价总和减去套餐优惠价格，得出最终套餐商品订单价格</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">限时开关</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='package[limit_time_status]' value='1' onclick='showLimitStatus(1)' @if(!empty($package['limit_time_status'])) checked="checked" @endif/>
            开启
        </label>
        <label class='radio-inline'>
            <input type='radio' name='package[limit_time_status]' value='0' onclick='hideLimitStatus(0)' @if(empty($package['limit_time_status']))  checked="checked" @endif/>
            关闭
        </label>
    </div>
    <div class = "limit_time_status" @if(empty($package['limit_time_status']))style='display:none' @endif>
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">限时时间</label>
        <div>
            {!! app\common\helpers\DateRange::tplFormFieldDateRange('package[limit_time]', [
            'starttime'=>date('Y-m-d H:i', array_get($package,'start_time',time())),
            'endtime'=>date('Y-m-d H:i',array_get($package,'end_time',time())),
            'start'=>0,
            'end'=>0
            ], true) !!}
        </div>
    </div>

</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">其他搭配套餐显示设置</label>
    <div class="col-sm-9 col-xs-12">
        <div style="float: left" id="ttttype">
            <label for="isshow3" class="radio-inline"><input type="radio" name="package[other_package_status]" onclick='showOtherPackage()' @if($package['other_package_status']) checked="checked" @endif value="1" />开启</label>
            <label for="isshow4" class="radio-inline"><input type="radio" name="package[other_package_status]" onclick='hideOtherPackage()' @if(empty($package['other_package_status'])) checked="checked" @endif value="0" />关闭</label>
        </div>
    </div>
</div>

{{--隐藏窗口 - 指定套餐--}}
<div class="form-group other_package_status" @if(empty($package['other_package_status']))style='display:none' @endif>
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-7 usetype usetype1">
        <div class='input-group'>
            <div id="package" >
                <table class="table">
                    <tbody id="param-itemspackage">
                    @foreach($package['other_package_ids'] as $k => $otherPackageId)
                    <tr>
                        <td>
                            <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
                        </td>
                        <td  colspan="2">
                            <input id="packageids" type="hidden" class="form-control" name="package[other_package_ids][]" data-id="{{$v}}" data-name="packageids"  value="{{ $otherPackageId }}" style="width:200px;float:left"  />
                            <input id="packagenames" class="form-control" type="text" name="package[other_package_names][]" data-id="{{$v}}" data-name="packagenames" value="{{ $package['other_package_names'][$k] }}" style="width:200px;float:left" readonly="true">
                            <span class="input-group-btn">
                            <button class="btn btn-default nav-link" type="button" data-id="" onclick="$('#modal-module-menus-package').modal();$(this).parent().parent().addClass('focuspackage')" >选择套餐</button>
                        </span>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                    <tbody>
                    <tr>
                        <td colspan="3">
                            <a href="javascript:;" id='add-param_package' onclick="addParam('package')"
                               style="margin-top:10px;" class="btn btn-primary"  title="添加套餐"><i class='fa fa-plus'></i> 添加套餐</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal-module-menus-package" class="modal fade" tabindex="-1"> {{--搜索套餐的弹窗--}}
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                    ×
                </button>
                <h3>选择套餐</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="search-kwd-package" placeholder="请输入套餐名称"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_package();">搜索</button>
                        </span>
                    </div>
                </div>
                <div id="module-menus-package" style="padding-top:5px;"></div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">状态</label>
    <div class="col-sm-9 col-xs-12">
        <div style="float: left">
            <label for="isshow3" class="radio-inline"><input type="radio" name="package[status]" @if($package['status']) checked="checked" @endif value="1" />开启</label>
            <label for="isshow4" class="radio-inline"><input type="radio" name="package[status]" @if(empty($package['status'])) checked="checked" @endif value="0" />关闭</label>
        </div>
    </div>
</div>

<script type="text/javascript">
    function addParam(type) {
        var url = "{!! yzWebUrl('plugin.goods-package.admin.package.add-param')!!}"+'&type='+type;
        $.ajax({
            "url": url,
            success: function(data) {
                $('#param-items'+type).append(data);
            }
        });
    }
    function deleteParam(o) {
        $(o).parent().parent().remove();
    }
    function search_goods() {
        if ($.trim($('#search-kwd-goods').val()) == '') {
            Tip.focus('#search-kwd-goods', '请输入关键词');
            return;
        }
        $("#module-menus-goods").html("正在搜索....");
        $.get('{!! yzWebUrl('plugin.goods-package.admin.package.get-search-goods') !!}', {
                keyword: $.trim($('#search-kwd-goods').val())
            }, function (dat) {
                $('#module-menus-goods').html(dat);
        });
    }
    function confirm_select() {
        var  goodsids = "";
        var  goodsnames = "";
        var tempGoodsIds = $(".focusgood:last input[data-name=goodsids]").val();
        var tempGoodsNames = $(".focusgood:last input[data-name=goodsnames]").val();

        //拿到所有checked的,將其字符串拼接，格式大概是是"1;5;7"  "商品1;商品2;商品3"
        $('.good_checked:checked').each(function () {
            goodsids += $(this).attr('good_id') + ";";
            goodsnames += "["+$(this).attr('good_id')+"]" + $(this).attr('good_name') + ";";
            //取消掉checked，下次打開不自動選擇
            $(this).prop('checked',false);
        });
        //拼接好后放入id和name,需要将之前的后面添加的进行拼接，不要全覆盖
        $(".focusgood:last input[data-name=goodsids]").val(tempGoodsIds + goodsids);
        $(".focusgood:last input[data-name=goodsnames]").val(tempGoodsNames + goodsnames);
        $(".focusgood").removeClass("focusgood");
        $("#modal-module-menus-goods .close").click();

    }

    function search_package() {
        if ($.trim($('#search-kwd-package').val()) == '') {
            Tip.focus('#search-kwd-package', '请输入关键词');
            return;
        }
        $("#module-menus-package").html("正在搜索....");
        $.get('<?php echo yzWebUrl('plugin.goods-package.admin.package.get-search-package'); ?>', {
                keyword: $.trim($('#search-kwd-package').val())
            }, function (dat) {
                $('#module-menus-package').html(dat);
            }
        );
    }
    function select_package(o) {
        $(".focuspackage:last input[data-name=packageids]").val(o.id);
        $(".focuspackage:last input[data-name=packagenames]").val(o.title);
        $(".focuspackage").removeClass("focuspackage");
        $("#modal-module-menus-package .close").click();
    }

    function showLimitStatus(){
        $('.limit_time_status').show();
    }
    function hideLimitStatus(){
        $('.limit_time_status').hide();
    }
    function showOtherPackage()
    {
        $('.other_package_status').show();
    }
    function hideOtherPackage()
    {
        $('.other_package_status').hide();
    }
</script>