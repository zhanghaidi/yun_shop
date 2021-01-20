@extends('layouts.base')

@section('content')

@section('title', trans('代客下单'))

@section('css')
    <style>
        .fixian{
            background-color: white;
            border-radius: 5px;
            border:1px solid #d3d3d3;
            width:20px;
            height:20px;
            display: inline-block;
            text-align: center;
            vertical-align: middle;
            line-height: 20px;
        }
        .fixian:active{
            background: #EEEEEE;
        }
        /*滚动条样式*/
        .nui-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        /*正常情况下滑块的样式*/
        .nui-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,.05);
            border-radius: 10px;
            -webkit-box-shadow: inset 1px 1px 0 rgba(0,0,0,.1);
        }
        /*鼠标悬浮在该类指向的控件上时滑块的样式*/
        .nui-scroll:hover::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,.2);
            border-radius: 10px;
            -webkit-box-shadow: inset 1px 1px 0 rgba(0,0,0,.1);
        }
        /*鼠标悬浮在滑块上时滑块的样式*/
        .nui-scroll::-webkit-scrollbar-thumb:hover {
            background-color: rgba(0,0,0,.4);
            -webkit-box-shadow: inset 1px 1px 0 rgba(0,0,0,.1);
        }
        /*正常时候的主干部分*/
        .nui-scroll::-webkit-scrollbar-track {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0);
            background-color: white;
        }
        /*鼠标悬浮在滚动条上的主干部分*/
        .nui-scroll::-webkit-scrollbar-track:hover {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.4);
            background-color: rgba(0,0,0,.01);
        }
    </style>
@endsection

<div  id="buying" class="w1200 ">
    <div class="right-titpos">
        <ul class="add-snav">
            {{--<a class="btn btn-info" style="color: white" href="#" onclick="aaa(this)">支付测试</a>--}}
            <li class="active"><a href="#" onclick="window.location.href='{!! yzWebUrl('plugin.help-user-buying.admin.home.select') !!}'"><i class="fa fa-mail-reply"></i>返回选择</a></li>
        </ul>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" style="background-color: rgb(245, 245, 245);border: 1px solid #ddd;margin: 0px">@if(empty($store)) 平台自营 @else 门店:{{$store['store_name']}} @endif</div>
        <div class="panel-body" style="border: 1px solid #ddd">
            <form action="" method="post" class="form-horizontal" role="form" id="form1" onkeydown="if(event.keyCode==13){return false;}">
                <input type="hidden" name="store_id" value="{{$store['id']}}"/>
                <input type="hidden" name="realname" value="{{$store['store_name']}}"/>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="">
                        <input type="text" class="form-control" name="search[keyword]"
                               value="" id="search-keyword" placeholder="输入商品名称"/>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-8 col-lg-5">
                    <div class="col-sm-12 col-xs-12">
                        {!!$catetory_menus!!}
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                    <div class="">
                        <button class="btn btn-success" id="search" type="button" onclick="searchSelect()"><i class="fa fa-search" ></i> 搜索</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <br>
    <div class="clearfix row">
        <div class="col-sm-6" style="border: 1px solid #ddd;border-radius:5px;margin-bottom:50px;">
            <div class="panel-default">
                <div class="panel-body" style="margin-bottom:20px">
                    <table class="table" style="overflow:visible;">
                        <thead class="navbar-inner" style="display:table;width:100%;table-layout:fixed;">
                        <tr>
                            <th style='width:5%;text-align: center;'>选择</th>
                            <th style='width:10%;text-align: center;'>ID</th>
                            <th style='width:20%;'>商品</th>
                            <th style='width:10%;text-align: center;'>价格</th>
                            <th style='width:10%;text-align: center;'>库存</th>
                        </tr>
                        </thead>
                        <tbody  id="goods" class="nui-scroll" style="height: 300px;display:block;overflow-y:scroll;">
                        @foreach($goodsList as $row)
                            <tr style="display:table;width:100%;table-layout:fixed;">
                                <td style="width:5%;text-align: center;">
                                    <label class="fixian">
                                        <span></span>
                                        <input style="display: none" type="checkbox" name="goods_ids[]" value="{{$row['id']}}" onclick="checkShopping(this)">
                                    </label>
                                </td>
                                <td style="width:10%;text-align: center;">{{$row['id']}}</td>
                                <td style="width:20%;">
                                    <img src="{{tomedia($row['thumb'])}}" style="width: 40px; height: 40px;border:1px solid #ccc;padding:1px;">
                                    {{$row['title']}}
                                </td>
                                <td style="width:10%;text-align: center;">{{$row['price']}}</td>
                                <td style="width:10%;text-align: center;">{{$row['stock']}}</td>
                                <input type="hidden" class="category" value="{{$row['category_ids']}}">
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6" style="padding-right: 0">
            <div style="border: 1px solid #ddd;margin-bottom:20px;border-radius:5px">
                <div class="panel-default">
                    <div class="panel-heading">
                        订单信息&nbsp;&nbsp;&nbsp;
                        <button type="button" class="btn btn-info" id="clean_shopping" onclick="cleanShopping()">清空购物车</button>
                    </div>
                    <div class="panel-body" style="margin-bottom:100px">
                        <table class="table" style="overflow:visible">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:30%'>商品名称</th>
                                <th style='width:15%;text-align: center'>价格</th>
                                <th style='width:15%;text-align: center'>数量</th>
                                <th style='width:15%;text-align: center'>金额</th>
                                <th style='width:12%;text-align: center'>删除</th>
                            </tr>
                            </thead>
                            <tbody id="shopping">
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer" style="background-color: #f5f5f5;" >
                        (不包含运费) 合计：<span style="color: red" class="total">0</span>元
                        <button class="btn btn-info" style="margin-left: 100px" onclick="goodsBuy(this)">预下单</button>
                    </div>
                </div>
            </div>
            <div style="border: 1px solid #ddd;border-radius:5px">
                <div class="panel-default">
                    <div class="panel-heading">
                        会员信息
                    </div>
                    <div class="panel-body form-horizontal">
                        <div class="form-group">
                            <label class="col-xs-2 col-sm-3 col-md-2 control-label">客户</label>
                            <div class="col-sm-9 col-md-10">
                                <input type='hidden' id='uid' name='member_id' value="{{$member['uid']}}"/>
                                <div class='input-group'>
                                    <input type="text" name="saler" maxlength="30" value="{{$member['nickname']}}" id="saler" class="form-control" readonly/>
                                </div>
                                <span id="saleravatar" class='help-block'>
                                    <img style="width: 100px" src="{{$member['avatar']}}"/>
                                </span>
                            </div>
                        </div>

                        {{--<div class="form-group">--}}
                            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">付款方式</label>--}}
                            {{--<div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">--}}
                                {{--<select id="pay-type" class="form-control">--}}
                                    {{--<option value="17"  selected="selected">货到付款</option>--}}
                                    {{--<option value="5">后台付款</option>--}}
                                    {{--<option value="3">余额支付</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">收件人</label>
                            <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                                <input type="text" name="username" class="form-control"
                                       value="{{$member_address['username']}}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">联系电话</label>
                            <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                                <input type="text" name="mobile" class="form-control"
                                       value="{{$member_address['mobile']}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">所在区域</label>
                            <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                                <input type="hidden" id="province_id" value="{{ $member_address['province_id']?:0 }}"/>
                                <input type="hidden" id="city_id" value="{{ $member_address['city_id']?:0 }}"/>
                                <input type="hidden" id="district_id" value="{{ $member_address['district_id']?:0 }}"/>
                                @if(\Setting::get('shop.trade.is_street'))
                                    <input type="hidden" id="street_id" value="{{ $member_address['street_id']?:0 }}"/>
                                    {!! app\common\helpers\AddressHelper::tplLinkedAddress(['address[province_id]','address[city_id]','address[district_id]','address[street_id]'], [])!!}
                                @else
                                    {!! app\common\helpers\AddressHelper::tplLinkedAddress(['address[province_id]','address[city_id]','address[district_id]'], []) !!}
                                @endif

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">详细地址</label>
                            <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                                <input type="text" name="address" class="form-control"
                                       value="{{$member_address['address']}}"/>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #f5f5f5;">
                        合计：<span style="color: red" class="order-price">0</span>元
                    </div>
                </div>
            </div>
            <div style="margin-top: 20px; ">
                <button class="btn btn-info" id="create_order_btn" style="width: 100%" onclick="createOrder(this)">提交订单</button>
            </div>
        </div>
    </div>
</div>

@include('Yunshop\HelpUserBuying::admin.payform')

<script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
<script language='javascript'>
    
    function aaa(btn) {
        $(btn).removeAttr("onclick");
        $(btn).attr('disabled', true);
    }

    var province_id = $('#province_id').val();
    var city_id = $('#city_id').val();
    var district_id = $('#district_id').val();
    var street_id = $('#street_id').val();
    cascdeInit(province_id, city_id, district_id, street_id);

    $("#goods").on("change",".fixian",function(){
        var bool = $(this).find('input[type="checkbox"]').is(":checked");
        var goods_id = $(this).find('input[type="checkbox"]').val();
        if (bool) {
            var tds= $(this).parent().siblings();//获取当前元素的父节点的全部兄弟节点，就是当前这行的所有td
            console.log(goods_id);
            //选中进行ajax请求加入购物车
            $(this).css('background-color', '#0076FF');
            $(this).find('span').css('color','white').html('&radic;');
        } else {
            //取消进行ajax请求移除购物车
            $(this).css('background-color', 'white');
            $(this).find('span').html('');
        }
    });

    function cleanShopping() {
        $("#shopping").find("tr").remove();
        $("#goods").find(':input[type="checkbox"]').attr("checked", false);
        $("#goods").find(".fixian").css('background-color', 'white');
        $("#goods").find('span').html('');
        $("#total").text(0);
    }

    function checkShopping(btn) {
        if ($(btn).is(':checked')) {
            //开
            addShopping(btn);
        } else {
            //关
            var id = $(btn).val();
            $("#goods_" + id).remove();
            total();
        }
    }
    function addShopping(btn){
        var tds=$(btn).parent().parent().siblings();//获取当前元素的父节点的全部兄弟节点，就是当前这行的所有td
        var id=$(tds).eq(0).text();//获取商品名称的td的文本值
        var name=$(tds).eq(1).text();//获取商品名称的td的文本值
        var price=$(tds).eq(2).text();//获取商品价格的td的文本值
        var html = $("<tr id='goods_"+ id + "'>"    //开始拼接HTML元素，将取到的东西展示到对用的input中
            +"<td>" + name + "</td>"
            +"<td align='center'>" + price + "</td>"
            +"<td align='center'>"
            +"<input style='border: 0; background-color: #FFFFFF; font-size:20px' type='button' value='-' onclick='decrease(this, "+id+")' />"
            +"<input style='background-color: #f2f2f2; border: 0;' type='text' name='total' onBlur='manual(this, "+id+")' size='1' value='1'/>"
            +"<input style='border: 0; background-color: #FFFFFF; font-size:20px' type='button' value='+' onclick='increase(this, "+id+")' />"
            +"</td>"
            +"<td align='center'>"+price+"</td>"
            +"<td align='center'>"
            +"<input type='button' class='btn btn-danger' value='删除' onclick='deleteShopping(this)'/>"
            +"<input type='hidden' name='goods_id' value='"+ id +"'/>"
            +"</td></tr>");
        $("#shopping").append(html);
        total();
    }

    function deleteShopping(btn){
        //给上一步你拼接的删除按钮上绑定一个这样的方法
        var id = $(btn).parent().parent().attr("id");
        $("#goods tr").each(function () {
            var goods_id = 'goods_'+$(this).find('td').eq(1).text();

            if (goods_id == id) {
                $(this).find(':input[type="checkbox"]').attr("checked", false);
                $(this).find(".fixian").css('background-color', 'white');
                $(this).find('span').html('');
            }
        });
        $(btn).parent().parent().remove();
        total();
    }

    function manual(btn, goodsid) {
        var num = $(btn).val();
        var shuzi = /^\d+$/;
        if (!shuzi.test(num)) {
            alert('数量必须为正整数');
            $(btn).val(1);
            var tds = $(btn).parent().siblings();
            var price = parseFloat($(tds).eq(1).text());
            //获取总价
            $(tds).eq(2).text(toDecimal2(price*1));
            total();
            return false;
        }
        var text = $(btn);
        $.get("{!! yzWebUrl('plugin.help-user-buying.admin.home.goods-increase') !!}", {'id':goodsid, 'num':num, 'type': 1}, function(json){
            console.log(json.result);
            if (json.result == 1) {
                var num = json.data.data;
                $(text).val(num);
                //获取单价
                var tds = $(text).parent().siblings();
                var price = parseFloat($(tds).eq(1).text());
                //获取总价
                var sum = price*num;
                $(tds).eq(2).text(toDecimal2(sum));
                total();

            } else {
                alert(json.msg);
                deleteShopping(btn);
            }

        });
    }

    //商品加
    function increase(btn, goodsid){

        var text=$(btn).prev();
        var count = parseFloat($(text).val());

        $.get("{!! yzWebUrl('plugin.help-user-buying.admin.home.goods-increase') !!}", {'id':goodsid, 'num':count, 'type': 0}, function(json){
            console.log(json.result);
            if (json.result == 1) {
                var num = json.data.data;
                $(text).val(num);
                //获取单价
                var tds = $(text).parent().siblings();
                var price = parseFloat($(tds).eq(1).text());
                //获取总价
                var sum = price*num;
                $(tds).eq(2).text(toDecimal2(sum));
                total();

            } else {
                alert(json.msg);
                deleteShopping(btn);
            }

        });

    }
    //商品减
    function decrease(btn, goodsid){
        var text=$(btn).next();
        var count = parseFloat($(text).val());
        if (--count <= 0) {
            deleteShopping(btn);
        }
        $(text).val(count);

        //获取单价
        var tds = $(text).parent().siblings();
        var price = parseFloat($(tds).eq(1).text());

        //获取总价
        var sum = price*count;
        $(tds).eq(2).text(toDecimal2(sum));
        total();
    }

    function total(){
        var trs = $("#shopping tr");
        var sum = 0;
        for(var i=0;i<trs.length;i++){
            var td = trs.eq(i).children().eq(3);
            var price = parseFloat($(td).text());
            //alert(price);
            sum = sum + price;

        }
        $(".total").text(toDecimal2(sum));
    }


    //商品搜索
    $("body").undelegate("#search-keyword","keyup").delegate("#search-keyword","keyup",function(){
        searchInp();
    });

    function searchInp(){
        var tbody = document.getElementById("goods");
        var trArr = tbody.getElementsByTagName("tr");

        var keyword = $('#search-keyword').val();
        var reg = new RegExp(keyword);

        // var items = [];
        if (keyword) {
            $("#goods tr").hide();
            for(var i = 0;i<trArr.length;i++){
                var temp = trArr[i].innerHTML;
                if (temp.match(reg)) {
                    // items.push(temp);
                    $("#goods tr").eq(i).show();
                }
            }
            // $("#goods tr").hide();
            // //遍历出items的内容并将其显示出来
            // for(var i = 0; i< items.length;i++){
            //     $("#goods").append("<tr style='display:table;width:100%;table-layout:fixed;'>"+ items[i] +"</tr>");
            // }
        }else{
            $("#goods tr").show();
        }
    }

    //四舍五入 强制保留两位小数
    function toDecimal2(x) {
        var f = parseFloat(x);
        if (isNaN(f)) {
            return false;
        }
        var f = Math.round(x*100)/100;
        var s = f.toString();
        var rs = s.indexOf('.');
        if (rs < 0) {
            rs = s.length;
            s += '.';
        }
        while (s.length <= rs + 2) {
            s += '0';
        }
        return s;
    }

    function searchSelect() {

        var cat_p = $('#category_parent').val();
        var cat_c = $('#category_child').val();
        var cat_t = $('#category_third').val();

        //console.log(cat_p, cat_c, cat_t);

        if (cat_p != 0 || cat_c != 0 || cat_t != 0) {
            $("#goods tr").hide();
            if (cat_t != 0 && cat_t != undefined) {
                var reg = new RegExp(cat_t);
            }else if (cat_c != 0) {
                var reg = new RegExp(cat_c);
            } else {
                var reg = new RegExp(cat_p);
            }
            $("#goods tr").each(function (i) {
                if ($(this).find('.category').val().match(reg)) {
                    $("#goods tr").eq(i).show();
                }
            });
        } else {
            $("#goods tr").each(function (i) {
                $("#goods tr").eq(i).show();
            });
        }


    }

    //预下单
    function goodsBuy(wo) {

        if ($("#shopping").children("tr").length < 1) {
            alert('购物车为空');
            return false;
        }

        //下单商品
        var goods = [];
        $("#shopping").children("tr").each(function (index, domEle) {
            var goods_id = $(domEle).find('input[name="goods_id"]').val();
            var total = $(domEle).find('input[name="total"]').val();
            goods[index] = {"goods_id":goods_id,"total":total,"option_id":0};
        });

        if ($(':input[name="member_id"]').val() == '') {
            alert('用户ID不能为空');
            return false;
        }


        var request_data = {
            //"member_id": $(':input[name="member_id"]').val(),
            "dispatch_type_id": 1,
            "goods" : JSON.stringify(goods),
            "address": {},
            "member_coupon_ids": "[]",
            "orders" : "[]",
            "store_id": $(':input[name="store_id"]').val(),
        }

        var ziji = $(wo);
        ziji.text('计算中...');
        $.get("{!! $order_url['pre_url'] !!}", request_data, function(json){
            ziji.text('预下单');
            if (json.result == 1) {
                $(".order-price").text(json.data.total_price);
            } else {
                alert(json.msg);
            }

        });


    }


    //下单
    function createOrder(btn)
    {
        var myreg = /^1\d{10}$/;

        //下单商品
        var goods = [];
        //订单收货地址
        var address;

        //var pay_type = $('#pay-type option:selected').val();

        if ($("#shopping").children("tr").length < 1) {
            alert('购物车为空');
            return false;
        }



        $("#shopping").children("tr").each(function (index, domEle) {
            var goods_id = $(domEle).find('input[name="goods_id"]').val();
            var total = $(domEle).find('input[name="total"]').val();
            goods[index] = {"goods_id":goods_id,"total":total,"option_id":0};
        });


        if ($(':input[name="member_id"]').val() == '') {
            alert('用户ID不能为空');
            return false;
        }
        if ($(':input[name="username"]').val() == '') {
            alert('收件人不能为空');
            return false;
        }
        if (!myreg.test($(':input[name="mobile"]').val())) {
            alert('联系电话格式不正确');
            return false;
        }
        if ($(':input[name="address"]').val() == '') {
            alert('详细地址不能为空');
            return false;
        }



        var province = $('#sel-provance option:selected');
        var city = $('#sel-city option:selected');
        var area = $('#sel-area option:selected');
        var street = $('#sel-street option:selected');


        if (province.val() == 0 || city.val() == 0 || area.val() == 0) {
            alert('请选择省市区');
            return false;
        }


        address = {
            "uid": $(':input[name="member_id"]').val(),
            "username": $(':input[name="username"]').val(),
            "mobile": $(':input[name="mobile"]').val(),
            "province": province.text(),
            "city": city.text(),
            "district": area.text(),
            "zipcode" : "",
            "address": $(':input[name="address"]').val(),
        };

        if(street.val() !== undefined && street.val() != 0) {
            address.street = street.text();
        }

        //下单请求数据
        let request_data = {
            //"member_id": $(':input[name="member_id"]').val(),
            "dispatch_type_id": 1,
            "goods" : JSON.stringify(goods),
            "address": JSON.stringify(address),
            "member_coupon_ids": "[]",
            "orders" : "[]",
            "cart_ids": "[]",
            "store_id": $(':input[name="store_id"]').val(),
            "realname": $(':input[name="realname"]').val(),
        };

        $(btn).removeAttr("onclick");
        $(btn).attr('disabled', true);
        $.get("{!! $order_url['create_url'] !!}", request_data, function(json){

            if (json.result == 1) {
                    pay(json.data.order_ids);
            } else {
                alert('下单失败:'+json.msg);
            }
            $(btn).attr("onclick","createOrder;");
            $(btn).attr('disabled', false);
        });
    }


    //支付
    function pay(order_ids)
    {
        $.get("{!! yzWebUrl('plugin.help-user-buying.admin.user-merge-pay.index') !!}",{order_ids:order_ids}, function(json){
                if (json.result == 1) {

                    let order_pay = json.data.order_pay; //订单支付类
                    let member = json.data.member; //下单用户

                    $('#thawing-funds').modal();

                    $(':input[name="order_pay_id"]').val(order_pay.id);
                    $(':input[name="order_ids"]').val(order_pay.order_ids);

                    $('#member_credit2').html(member.credit2);
                    $('#pay_sn').html(order_pay.pay_sn);
                    $('#amount').html(order_pay.amount);

                } else {
                    console.log(json);
                    alert(json.msg);

                }

            });
    }

</script>

@endsection