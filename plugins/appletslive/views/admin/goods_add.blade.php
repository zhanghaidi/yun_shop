@extends('layouts.base')
@section('title', trans('添加商品'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">添加商品</a></li>
        </ul>
    </div>

    <div class='panel panel-default'>
        <div class="clearfix panel-heading">
            <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
               href="javascript:history.go(-1);">返回</a>
        </div>
    </div>

    <div class="w1200 m0a">
        <div class="rightlist">
            <form action="" method="post" class="form-horizontal form" onsubmit="return false;">

                <div class="form-group">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">商品</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <select id="sltGoodsId" name='goods_id' class='form-control goods-select2'>
                            <option value="">请选择商品</option>
                            @foreach ($goods as $item)
                                <option value="{{ $item['id'] }}" data-title="{{ $item['title'] }}"
                                        data-price="{{ $item['price'] }}" data-thumb="{{ $item['thumb'] }}"
                                        data-imgurl="{!! tomedia($item['thumb']) !!}">{{ $item['title'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group fg-showhide" style="display:none;">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">商品名称</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input id="formName" name="name" type="text" class="form-control" value="" required />
                        <span class="help-block">商品名称不得超过14个汉字</span>
                    </div>
                </div>

                <div class="form-group fg-showhide" style="display:none;">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">预览图片</label>
                    <div class="col-md-9 col-sm-9 col-xs-12 thumb-img">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('cover_img_url', '') !!}
                        <span class="help-block">图片规则：图片尺寸最大300像素*300像素</span>
                    </div>
                </div>

                <div class="form-group fg-showhide" style="display:none;">
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

                <div class="form-group fg-showhide price1" style="display:none;">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">价格</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input id="formPrice" name="price" type="number" class="form-control" value="" required />
                    </div>
                </div>

                <div class="form-group fg-showhide price2" style="display:none;">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">价格(左边界)</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input id="formPrice1" name="price1" type="number" class="form-control" value="" required />
                    </div>
                </div>

                <div class="form-group fg-showhide price2" style="display:none;">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">价格(右边界)</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input id="formPrice2" name="price2" type="number" class="form-control" value="" required />
                    </div>
                </div>

                <div class="form-group fg-showhide price3" style="display:none;">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">原价</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input id="formPrice3" name="price3" type="number" class="form-control" value="" required />
                    </div>
                </div>

                <div class="form-group fg-showhide price3" style="display:none;">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">现价</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <input id="formPrice4" name="price4" type="number" class="form-control" value="" required />
                    </div>
                </div>

                <div class="form-group fg-showhide" style="display:none;">
                    <label class="col-md-2 col-sm-3 col-xs-12 control-label">小程序路径</label>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <span class="form-control wxapppagepath"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input id="submitGoodsForm" type="submit" name="submit" value="提交" class="btn btn-success"/>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script type="text/javascript">

        var Page = {
            data: {
                name: '',
                goodsId: 0,
                coverImgUrl: '',
                priceType: 1,
                price: 0,
                price2: 0,
                url: '',
            },
            init: function () {
                var that = this;

                $('.goods-select2').select2();

                // 商品下拉菜单onchange
                $(document).on('change', '#sltGoodsId', function () {
                    var goodId = $(this).val();
                    that.data.goodsId = goodId;
                    $('.fg-showhide').hide();

                    $('#priceType1').prop('checked', 'checked');
                    $('#priceType2').removeProp('checked');
                    $('#priceType3').removeProp('checked');
                    $('.price1 input').attr('required', true);
                    $('.price1 input').val('');
                    $('.price2 input').removeAttr('required');
                    $('.price2 input').val('');
                    $('.price3 input').removeAttr('required');
                    $('.price3 input').val('');

                    if (goodId !== '') {
                        $('input[name="name"]').val($(this).find('option:selected').data('title'));
                        $('input[name="price"]').val($(this).find('option:selected').data('price'));
                        $('input[name="cover_img_url"]').val($(this).find('option:selected').data('thumb'));
                        $('.thumb-img img').attr('src', $(this).find('option:selected').data('imgurl'));

                        var url = '/page/abc/def?id=' + goodId;
                        $('.wxapppagepath').text(url);
                        that.data.url = url;

                        $('.fg-showhide').show();
                        $('.price2').hide();
                        $('.price3').hide();
                    }
                });

                // 价格类型onchange
                $(document).on('change', 'input[name="price_type"]', function () {
                    var val = $(this).val();
                    that.data.priceType = val;
                    if (val == 1) {
                        $('.price1 input').attr('required', true);
                        $('.price1 input').val('');
                        $('.price1').show();
                        $('.price2 input').removeAttr('required');
                        $('.price2 input').val('');
                        $('.price2').hide();
                        $('.price3 input').removeAttr('required');
                        $('.price3 input').val('');
                        $('.price3').hide();
                    } else if (val == 2) {
                        $('.price1 input').removeAttr('required');
                        $('.price1 input').val('');
                        $('.price1').hide();
                        $('.price2 input').attr('required', true);
                        $('.price2 input').val('');
                        $('.price2').show();
                        $('.price3 input').removeAttr('required');
                        $('.price3 input').val('');
                        $('.price3').hide();
                    } else {
                        $('.price1 input').removeAttr('required');
                        $('.price1 input').val('');
                        $('.price1').hide();
                        $('.price2 input').removeAttr('required');
                        $('.price2 input').val('');
                        $('.price2').hide();
                        $('.price3 input').attr('required', true);
                        $('.price3 input').val('');
                        $('.price3').show();
                    }
                });

                // 点击提交表单
                $('#submitGoodsForm').on('click', function () {

                    $('input[name="cover_img_url"]').attr('required', true);
                    $('input[name="cover_img"]').attr('id', 'formCoverImgUrl');

                    var check = that.checkForm();
                    if (check) {

                        var submitBtn = $(this);
                        submitBtn.button('loading');

                        $.ajax({
                            url: "",
                            type: 'POST',
                            data: that.data,
                            success: function (res) {
                                submitBtn.button('reset');
                                var jump = "{!! yzWebUrl('plugin.appletslive.admin.controllers.goods.index', ['tag'=>'refresh']) !!}";
                                util.message(res.msg, res.result == 1 ? jump : '', res.result == 1 ? 'success' : 'info');
                            }
                        });
                    }
                });
            },
            checkForm: function () {
                var that = this;

                // 表单验证 - 商品名称长度
                var name = $('input[name="name"]').val().trim();
                if (name.length > 14) {
                    Tip.focus('#formName');
                    return false;
                }
                that.data.name = name;

                // 表单验证 - 价格
                if (that.data.priceType == 1) {
                    var price = $('input[name="price"]').val().trim();
                    if (price == '') {
                        Tip.focus('#formName');
                        return false;
                    }
                    that.data.price = price;
                    that.data.price2 = 0;
                } else if (that.data.priceType == 2) {
                    var price = $('input[name="price1"]').val().trim();
                    if (price == '') {
                        Tip.focus('#formPrice1');
                        return false;
                    }
                    that.data.price = price;
                    var price2 = $('input[name="price2"]').val().trim();
                    if (price2 == '') {
                        Tip.focus('#formPrice2');
                        return false;
                    }
                    that.data.price2 = price2;
                } else if (that.data.priceType == 3) {
                    var price = $('input[name="price3"]').val().trim();
                    if (price == '') {
                        Tip.focus('#formPrice3');
                        return false;
                    }
                    that.data.price = price;
                    var price2 = $('input[name="price4"]').val().trim();
                    if (price2 == '') {
                        Tip.focus('#formPrice4');
                        return false;
                    }
                    that.data.price2 = price2;
                }

                // 表单验证 - 商品图片
                var coverImgUrl = $('input[name="cover_img_url"]').val().trim();
                if (coverImgUrl.length == 0) {
                    Tip.focus('#formCoverImgUrl');
                    return false;
                }
                that.data.coverImgUrl = coverImgUrl;

                return true;
            }
        };

        Page.init();

    </script>

@endsection
