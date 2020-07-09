<style>.btn-disabled, .btn-disabled:hover {background: #ddd; border: 1px solid #bbb; color: #999; cursor: not-allowed;}</style>
<style>.trbody td {border-left: 1px solid #ccc;}.tac { text-align: center;}</style>
<style>
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('https://img-blog.csdn.net/20161205162919763') 50% 50% no-repeat rgb(249,249,249);
    }
</style>
<script language='javascript' src="{{resource_get('plugins/exhelper/src/common/static/js/LodopFuncs.js', 1)}}"></script>
<script language='javascript' src="{{resource_get('plugins/exhelper/src/common/static/js/jquery.jqprint-0.3.js')}}"></script>
<script language='javascript' src="{{resource_get('plugins/exhelper/src/common/static/js/jquery-migrate-1.2.1.min.js')}}"></script>
{{--<script src="http://www.jq22.com/jquery/jquery-migrate-1.2.1.min.js";></script>--}}
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width="0"
    height="0">
    <embed id="LODOP_EM" type="application/x-print-lodop" width="0" height="0" pluginspage="{{resource_get('plugins/exhelper/src/common/lodop/install_lodop32.exe', 1)}}"></embed>
</object>


<div class="panel-heading">订单信息</div>
<div class="panel-body">
    <form class="form-horizontal">
        <div class="form-group">
            <div class="col-sm-7">
                <div class="input-group">
                    <span class="input-group-addon">姓名</span>
                    <input style="width: 200px" type="hidden" name="nickname" value="{{$address->realname}}"/>
                    <input style="width: 200px" type="text" class="form-control" name="realname" value="{{$address->realname}}" style="border-right: none;" />
                    <span class="input-group-addon" style="border-right: none;">电话</span>
                    <input style="width: 200px" type="text" class="form-control" name="mobile" value="{{$address->mobile}}" />
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12 col-xs-12">
                <div class="input-group">
                    <span class="input-group-addon">地址</span>
                    <input type="text" class="form-control" name="province" style="width:100px; border-right: none;" value="{{$province}}" />
                    <input type="text" class="form-control" name="city" style="width:100px; border-right: none;" value="{{$city}}" />
                    <input type="text" class="form-control" name="area" style="width:100px; border-right: none;" value="{{$district}}" />
                    <input type="text" class="form-control" name="address" value="{{$address->address}}" style="width:700px; border-right: none;" />
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12 col-xs-12">
                <div class="input-group">
                    <span class="input-group-addon">邮编</span>
                    <input type="text" class="form-control" name="zip_code" id="zip_code" value="" style="width:500px; border-right: none;" />
                    <i style="color: red">注：邮政快递必须填写收件人邮编</i>
                </div>
            </div>
        </div>
        <table class='table' style='float:left;border:1px solid #ccc;margin-bottom:5px;table-layout: fixed'>
            <tr class='trhead'>
                <td style='width:30px;'><input type="checkbox" checked="" class="cBoxOrderAll"></td>
                <td colspan='2' style='text-align:left;'>订单总数: {{$list->count()}} <br/>订单金额: {{$list->sum('price')}}</td>
                <td class="tac" style="border: 1px solid #ccc;">规格及编码</td>
                <td class="tac" style="border: 1px solid #ccc;">单价(元)</td>
                <td class="tac" style="border: 1px solid #ccc;">数量</td>
                <td class="tac" style="border: 1px solid #ccc;">支付方式</td>
                <td class="tac" style="border: 1px solid #ccc;">配送方式</td>
                <td class="tac" style="border: 1px solid #ccc;">价格</td>
                <td class="tac" style="border: 1px solid #ccc;">订单状态</td>
                <td class="tac" style="border: 1px solid #ccc;">打印状态<br/>次数</td>
            </tr>
        </table>
        <div class="allorder">
            @foreach($list as $item)
                <table class='table' style='float:left;border:1px solid #ccc;margin-top:5px;margin-bottom:5px;table-layout: fixed' data-ordersn="{{$item->order_sn}}">
                    <tr>
                        <td style='border-bottom:1px solid #ccc;background:#efefef;width:30px;'>
                            <input type="checkbox" class="cBoxOrderOne" checked="">
                        </td>
                        <td class colspan='9' style='border-bottom:1px solid #ccc;background:#efefef;'>
                            <b>订单编号: </b><span class="ordersn">{{$item->order_sn}}</span>
                            <b>下单时间: </b>
                            {{$item->create_time}}
                            @if(!empty($item->refund_id))
                                <label class='label label-danger'>退款申请</label>
                            @endif
                            <b>打印状态: </b>
                            <span class="orderprintstate">
                            @if(!isset($item->hasOnePrint) || $item->hasOnePrint->express_print_status == 0)
                                    快递单未打印
                                @elseif($item->hasOnePrint->express_print_status > 0)
                                    快递单已打印
                                @endif
                        </span>
                            <span class="orderprintstate2">
                            @if(!isset($item->hasOnePrint) || $item->hasOnePrint->send_print_status == 0)
                                    发货单未打印
                                @elseif($item->hasOnePrint->send_print_status > 0)
                                    发货单已打印
                                @endif
                        </span>
                            <span class="orderprintstate3">
                            @if(!isset($item->hasOnePrint) || $item->hasOnePrint->panel_print_status == 0)
                                    电子面单未打印
                                @elseif($item->hasOnePrint->panel_print_status > 0)
                                    电子面单已打印
                                @endif
                        </span>
                        </td>
                        <td style='border-bottom:1px solid #ccc;background:#efefef;text-align: center'></td>
                    </tr>
                    @foreach($item->hasManyOrderGoods as $k => $g)
                        <tr class='trbody' id="ordergoodstr">
                            <td style='width:80px;'>
                                <input type="checkbox" class="cBoxGood" checked="">
                            </td>

                            <td id="orderinfo" valign='top' colspan='2' style='text-align: left; width: 400px; position: relative; '
                                data-ordersn="{{$item->order_sn}}" data-orderid="{{$item->id}}" data-ordergoodid="{{$g->id}}" data-goodid="{{$g->goods_id}}" data-goodtitle="{{$g->title}}"
                                data-shorttitle = "@if(!$g->goods->hasOneShort)-@else{{$g->goods->hasOneShort->short_title}}@endif" data-goodnum="{{$g->total}}" data-goodssn="{{$g->goods->goods_sn}}" data-productsn="{{$g->goods->product_sn}}" data-marketprice="{!! $g->goods_price/$g->total !!}"
                                data-productprice="{!! $g->price/$g->total !!}" data-total="{{$g->total}}" data-goodoption="@if(!$g->goods_option_title)-@else{{$g->goods_option_title}}@endif" data-goodunit="@if(!$g->goods->sku)-@else{{$g->goods->sku}}@endif"
                                data-realprice="{!! $g->price/$g->total !!}" data-goodweight="@if(!$g->goods->weight)-@else{{$g->goods->weight}}@endif" data-goodsprice="{{$item->goods_price}}" data-dispatchprice="{{$item->dispatch_price}}" data-discountprice="{{$item->discount_price}}"

                                data-deductprice="{{$item->deduction_price}}"
                                data-vipdiscount="{!! $item->goods_price - $item->order_goods_price !!}"
                                {{--data-deductcredit2="{$item['deductcredit2']}" data-deductenough="{$item['deductenough']}" data-changeprice="{$item['changeprice']}" data-changedispatchprice="{$item['changedispatchprice']}" data-couponprice="{$item['couponprice']}" --}}

                                data-price="{{$item->price}}" >
                                <img src="{!! tomedia($g->thumb) !!}" style="width: 50px; height: 50px;border:1px solid #ccc;padding:1px;" >
                                <span class='goodtitle' >@if(!$g->goods->hasOneShort){{$g->title}}@else{{$g->goods->hasOneShort->short_title}}@endif</span>
                                <span class="editShort" style="position: absolute; bottom: 0; right: 0; background: rgba(0,0,0,0.5); font-size: 12px; color: #FFF; padding-left: 11px; border-radius: 20px 0px 0px 0px; width: 40px; cursor: pointer;" data-do="e">编辑</span>
                            </td>

                            <td class="tac">
                                @if($g->goods_option_title)<span class="label label-primary">{{$g->goods_option_title}}</span>@endif
                                <br/>{{$g->goods->goods_sn}}
                            </td>
                            <td class="tac">原价: {!! $g->goods_price/$g->total !!}<br/>折后: {!! $g->price/$g->total !!}</td>
                            <td class="tac">x{{$g->total}}</td>
                            @if($k == 0)
                                <td rowspan="{!! count($item->hasManyOrderGoods) !!}"  class="tac"><label class='label label-success'>{{$item->pay_type_name}}</label></td>
                                <td  rowspan="{!! count($item->hasManyOrderGoods) !!}" class="tac">{{$item->express->express_company_name}}</td>
                                <td style='text-align:center;'  rowspan="{!! count($item->hasManyOrderGoods) !!}">{{$item->price}} 元
                                    <br/>含运费 {{$item->dispatch_price}} 元
                                </td>
                                <td   rowspan="{!! count($item->hasManyOrderGoods) !!}"  class="tac">
                                    <label class='label label-success label-oss'>{{$item->status_name}}</label><br />
                                    <a href="{!! yzWebUrl('order.detail.index', ['id' => $item->id]) !!}" >查看详情</a>
                                </td>
                            @endif
                            <td class="tac" style="border: 1px solid #ccc;padding: 2px 4px">
                                <label class="icon-ps1 label label-@if($item->hasOnePrint->express_print_status > 0){!! $css = 'primary' !!}@else{!! $css = 'default' !!}@endif" data-printstate="{{$item->hasOnePrint->express_print_status}}">@if($item->hasOnePrint->express_print_status > 0)快递单 x {{$item->hasOnePrint->express_print_status}}@else{{快递单未打印}}@endif</label><br>
                                <label class="icon-ps2 label label-@if($item->hasOnePrint->send_print_status > 0){!! $css = 'success' !!}@else{!! $css = 'default' !!}@endif" data-printstate="{{$item->hasOnePrint->send_print_status}}">@if($item->hasOnePrint->send_print_status > 0)发货单 x {{$item->hasOnePrint->send_print_status}}@else{{发货单未打印}}@endif</label><br>
                                <label class="test label label-@if($item->hasOnePrint->panel_print_status > 0){!! $css = 'danger' !!}@else{!! $css = 'default' !!}@endif" data-printstate="{{$item->hasOnePrint->panel_print_status}}">@if($item->hasOnePrint->panel_print_status > 0)电子面单 x {{$item->hasOnePrint->panel_print_status}}@else{{电子面单未打印}}@endif</label>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endforeach
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <div class="col-sm-12">
                <div class='form-control-static'>发货信息</div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <textarea class='form-control sendinfo' style="min-height: 100px; height: auto;">{{$address->sendinfo}}</textarea>
            </div>
        </div>
    </form>
</div>

<div class="panel-footer" style="height: auto; overflow: hidden;">
    <div class="form-group">
        <div class="col-sm-12">
            <p style="color:red;">注：电子面单打印建议使用谷歌浏览器，其他浏览器不支持打印预览效果，由于快递单模板不同，请自行调整打印大小，方法：点击打印电子面单，点击"更多设置"，"缩放"选择自定义调整大小</p>
            <span class="btn btn-primary doprint" id="doprint1" data-state="0" data-cate="1">打印快递单</span>
            <span class="btn btn-warning doprint" id="doprint2" data-state="0" data-cate="2">打印发货单</span>
            <span class="btn btn-default doprint" id="doprint3" data-state="0" data-cate="3">打印电子面单</span>
            <!-- <span class="btn btn-success" id="dosend" data-state="0">一键发货</span> -->
        </div>

    </div>
</div>
<!-- 一键发货浮层 -->
<div id="modal-send" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width:700px;margin:0px auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <div style="height: 50px;">
                    <div style="height: 50px; width: 90px; font-size: 22px; line-height: 60px; float: left;">一键发货</div>
                    <div style="height: 50px; width: 150px; float: right; font-size: 14px; margin-right: 10px; line-height: 70px;">快递类型: [<span id="expresscom" data-expresscom="" data-express="">loading</span>]</div>
                    <div style="height: 50px; 300px; float: right; font-size: 14px; margin-right: 10px; line-height: 70px; overflow: hidden; text-align: right;">快递模版: <span id="printtempname">loading</span></div>
                </div>
            </div>
            <div class="modal-body" style=" text-align: center; max-height: 700px; overflow-y: auto;">
                <table class="table sendtable" style="margin-bottom: 0;"></table>
                <div id="module-menus"></div>
            </div>
            <div class="modal-footer">
                <span style="float: left; line-height: 30px; color: #999;">提示: 快递类型请在快递单默认模版更改。</span>
                <a class="btn btn-primary" onclick="cleardata()">清除数据</a>
                <a class="btn btn-success dosend" onclick="dosend()" data-state="0">执行发货</a>
                <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">取消</a>
            </div>
        </div>
    </div>
</div>


<script>
    $(function(){
        var printDatas;
        var printUser;
        var printTemp;
        $(".panel-body").find("input[type=checkbox]").change(function(){
            changeSendInfo();
        });
     
        $(".doprint").click(function(){
            // 执行保存收件人信息
            savesender();
            var thisState = $(this).data("state");
            var thisCate = $(this).data("cate");
            if(thisState>0){
                return;
            }
            if (thisCate == 3) {
                //检测订单数量 == 1
                console.log($('input[class="cBoxOrderOne"]:checked').length);
                
                if ($('input[class="cBoxOrderOne"]:checked').length > 1) {
                    alert('暂不支持多订单打印面单'); return ;
                    // var order_sn = new Array();
                    // $("input[class='cBoxGood']").each(function(index) {
                        // console.log('this.html = '+$(this).html(), 'ordersn_value = '+ $(this).parent().next().attr('data-ordersn'));
                    //     if ($(this).is(':checked')) {
                    //         order_sn.push($(this).parent().next().attr('data-ordersn') );
                    //     } 
                    // });
                // } else if( $("input[class='cBoxGood']:checked").length > 5) {
                //     // alert('请选择等于或少于5条订单操作'); return false;
                //     // alert('暂不支持多订单打印面单'); return ;
                } 
                 
                var order_sn = $('input[class="cBoxOrderOne"]:checked').parent().next().find('.ordersn').text();
                var zip_code = $('#zip_code').val();
                console.log(order_sn);
                // $('#loading').fadeOut('slow');
                //获取电子面单信息
                $.ajax({
                    type: 'POST',
                    url: "{!! yzWebUrl('plugin.exhelper.admin.panel.test') !!}",
                    data: {ordersn: order_sn,zip_code:zip_code},
                    dataType: 'json',
                    success: function(msg) {
                        // if ($("input[class='cBoxGood']:checked").length > 1) {
                        //     $.each(msg, function(i, temp){
                        //         if (msg[i].result == 'success') {
                        //             //判断模板宽高
                        //             switch (msg[i].resp.data.exhelper_style) {
                        //                 case 'PJ': 
                        //                     var panel_width = 80;
                        //                     break;
                        //                 case 'CNEX':
                        //                     var panel_width = 90;
                        //                     break;
                        //                 default :
                        //                     var panel_width = 100;
                        //             }
                        //             //执行打印
                        //             printpanel(msg[i].resp.html, panel_width, msg[i].resp.data.panel_style, order_sn[i]);
                        //         } else {
                        //             //获取失败时
                        //             alert(msg[i].resp); 
                        //         }
                        //     });
                        // }
                        if ($("input[class='cBoxOrderOne']:checked").length == 1 && msg.result == 'success') {
                            // alert('电子面单下单成功，订单状态为已发货');  
                            //判断模板宽高
                                switch (msg.resp.data.exhelper_style) {
                                    case 'PJ': 
                                        var panel_width = 80;
                                        break;
                                    case 'CNEX':
                                        var panel_width = 90;
                                        break;
                                    default :
                                        var panel_width = 100;
                                }
                            var html = $("<div id='div1' style='width:100%;height:100%'>").append(msg.resp.html);
                            //jqprint
                            // $(html[0].outerHTML).jqprint();
                            $(html[0].outerHTML).jqprint({
                                importCSS:false 
                            });
                            changeOrderPrintInfo('print3', order_sn);
                            // $("#div1").remove();
                            //执行打印
                            // printpanel(msg.resp.html, panel_width, msg.resp.data.panel_style, order_sn);
                        } else {
                            alert(msg.resp);
                        }
                    }
                });

                return ;
            }
            //打印快递单或发货单
            var url = "{!! yzWebUrl('plugin.exhelper.admin.print-once.getPrintTemp') !!}";
            var data = {type:thisCate};
           
            // 执行ajax获取打印模版
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                dataType:'json',
                async:false,
                success: function(d){
                    if(d.result=="success"){
                        printDatas = d.respDatas;
                        printUser = d.respUser;
                        printTemp = d.respTemp;
                        if (thisCate == 3) {
                            printTemp = d.resp;
                            console.log(printTemp);
                        }
                    }else{
                        alert(d.resp);
                        return;
                    }
                }
            });
            console.log(printDatas);
            var rep_sendInfo = $(".sendinfo").val();
            var rep_realName =$(":input[name=realname]").val();
            var rep_nickName =$(":input[name=nickname]").val();
            var rep_mobile =$(":input[name=mobile]").val();
            var rep_province =$(":input[name=province]").val();
            var rep_city =$(":input[name=city]").val();
            var rep_area =$(":input[name=area]").val();
            var rep_address =$(":input[name=address]").val();
            var rep_ordersn = '';
            $(".ordersn").each(function(){
                if(rep_ordersn){
                    rep_ordersn += ", ";
                }
                rep_ordersn += $(this).text();
            });
            $.each(printDatas,function(){
                this.items = this.items.replace("sendinfo",rep_sendInfo);
                this.items = this.items.replace("realname",rep_realName);
                this.items = this.items.replace("nickname",rep_nickName);
                this.items = this.items.replace("mobile",rep_mobile);
                this.items = this.items.replace("province",rep_province);
                this.items = this.items.replace("city",rep_city);
                this.items = this.items.replace("area",rep_area);
                this.items = this.items.replace("address",rep_address);
                this.items = this.items.replace("ordersn",rep_ordersn);
                this.items = this.items.replace("shopname",printTemp.shopname);
            });
            // 定义打印模版信息
            LODOP.PRINT_INITA(0,0,printTemp.width+"mm",printTemp.height+"mm","单据打印");
            LODOP.NewPageA();
            LODOP.SET_PRINT_PAGESIZE(1,printTemp.width+"mm",printTemp.height+"mm", "");
            if(printTemp.bg){
                LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='"+printTemp.bg+"'>");
                LODOP.SET_SHOW_MODE("BKIMG_IN_PREVIEW",1);
            }
            LODOP.SET_PRINT_MODE("AUTO_CLOSE_PREWINDOW",true);

            console.log('printDatas', printDatas);
            $.each(printDatas,function(i,d){
                console.log(i, d);
                if(d.cate==1){
                    LODOP.ADD_PRINT_TEXTA('"t_'+i+'"',d.top+"px",d.left+"px",d.width+"px",d.height+"px",d.pre+d.items+d.last);
                    if(d.color){
                        LODOP.SET_PRINT_STYLEA('"t_'+i+'"',"FontColor",d.color);    // 文字颜色
                    }
                    if(d.bold){
                        LODOP.SET_PRINT_STYLEA('"t_'+i+'"',"Bold",1);   // 文字加粗
                    }
                    if(d.align){
                        LODOP.SET_PRINT_STYLEA('"t_'+i+'"',"Alignment",d.align);    // 对齐方式
                    }
                    var FontSize = !d.size?"12":d.size;
                    LODOP.SET_PRINT_STYLEA('"t_'+i+'"',"FontSize",FontSize);    //文字大小
                    var FontName = !d.font?"微软雅黑":d.font;
                    LODOP.SET_PRINT_STYLEA('"t_'+i+'"',"FontName",FontName);    // 文字字体
                }
                if(d.cate==2){
                    var strings = d.string.split(',');
                    var values = d.items.split(',');
                    var _html = '<table style="width: '+d.width+'; display:table-fixed; border-collapse:collapse;border-spacing:0;border-left:1px solid '+d.color+';border-top:1px solid '+d.color+'; corlor:'+d.color+'; ';
                    if(d.align==1){
                        _html += "text-align:left;"
                    }
                    if(d.align==2){
                        _html += "text-align:center;"
                    }
                    if(d.align==3){
                        _html += "text-align:right;"
                    }
                    _html += '">';

                    _html += '<tr>';

                    $.each(strings, function(ii,s) {
                        console.log('ii: ',ii);
                        console.log('s: ',s);
                        _html += '<td style="border-right:1px solid '+d.color+'; border-bottom:1px solid '+d.color+'; font-size:'+d.size+'pt; font-family:'+d.font+'; color:'+d.color+';">'+s;
                        _html += '</td>';
                    });
                    _html += '</tr>';
                    console.log('strings: ',strings);
                    console.log('values: ',values);
                    var info = [];
                    $(".allorder").find("input:checkbox:checked").not(".cBoxOrderOne").each(function(index){
                        var _this = $(this).parent().next()
                        console.log('index',index);
                        console.log('_this',_this);
                        var _ordersn = _this.data("ordersn")    // 订单编号
                        var _goodname = _this.data("goodtitle");    // 商品名称
                        var _goodshortname = _this.data("shorttitle");  // 商品简称
                        var _goodssn = _this.data("goodssn");   // 商品编码
                        var _productsn = _this.data("productsn");   //商品条码
                        var _marketprice = _this.data("marketprice");   //商品原价
                        var _productprice = _this.data("productprice"); //商品现价
                        var _allprice = _this.data("productprice") * _this.data("total");   // 商品总价
                        _allprice = _allprice.toFixed(2);
                        var _total = _this.data("total");   // 商品数量
                        var _note = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';   //备注栏
                        var _goodoption = _this.data("goodoption"); // 商品规格
                        var _goodunit = _this.data("goodunit"); // 商品单位
                        var _goodweight = _this.data("goodweight"); // 商品重量
                        var _realprice = _this.data("realprice");   // 商品折后价格
                        _realprice = _realprice.toFixed(2);
                        //订单信息
                        var _goodsprice = _this.data("goodsprice"); // 商品小计
                        var _dispatchprice = _this.data("dispatchprice");   // 运费
                        var _deductprice = _this.data("deductprice");   // 抵扣金额
                        var _discountprice = _this.data("discountprice");   // 优惠券金额
                        var _vipdiscount = _this.data("vipdiscount");   // 会员折扣
                        var _deductcredit2 = _this.data("deductcredit2");   // 余额抵扣
                        var _deductenough = _this.data("deductenough"); // 满额立减
                        var _changeprice = _this.data("changeprice");   // 改价优惠
                        var _changedispatchprice = _this.data("changedispatchprice");   // 运费改价
                        var _couponprice = _this.data("couponprice");   // 优惠券优惠
                        var _price = _this.data("price");   // 实付费

                        info.push({
                            'odsn':_ordersn,'goodname':_goodname,'goodshortname':_goodshortname,'goodssn':_goodssn,'productsn':_productsn,
                            'marketprice':_marketprice,
                            'productprice':_productprice,
                            'allprice':_allprice,
                            'total':_total,
                            'note':_note,
                            'goodoption':_goodoption,
                            'goodunit':_goodunit,
                            'goodweight':_goodweight,
                            'realprice':_realprice,
                            'goodsprice':_goodsprice,
                            'dispatchprice':_dispatchprice,
                            'discountprice':_discountprice,
                            'deductprice':_deductprice,
                            'vipdiscount':_vipdiscount,
                            'deductenough':_changeprice,'changedispatchprice':_changedispatchprice,'couponprice':_couponprice,
                            'price':_price
                        });
                        console.log('----------');
                        console.log('info: ',info);
                        _html += '<tr>';
                        // row += 1;
                        $.each(values, function(iii,val) {
                            _html += '<td style="border-right:1px solid '+d.color+'; border-bottom:1px solid '+d.color+'; font-size:'+d.size+'pt; font-family:'+d.font+'; color:'+d.color+';">';
                            if(val=="printsn"){
                                _html += index+1;
                            }else{
                                _html += info[index][val];
                            }
                            _html += '</td>';
                        });
                        _html += '</tr>';
                    });
                    _html += '</table>';
                    LODOP.ADD_PRINT_HTM(d.top+"px", d.left+"px", d.width+"px", d.height+"px", _html);
                }
            });
            
            // 获取打印状态
            if (LODOP.CVERSION) {
                LODOP.On_Return=function(TaskID,Value) {
                    if (!Value) {
                        alert("打印已取消");
                    } else {
                        alert("已提交至打印机");
                        // 执行修改订单表数据
                        if(thisCate==1){
                            changeOrderPrintInfo("print1", rep_ordersn);
                        }
                        if(thisCate==2){
                            changeOrderPrintInfo("print2", rep_ordersn);
                        }
                    }
                };
                LODOP.PREVIEW();
                return;
            };

        });

        $("#dosend").click(function(){
            var thisState = $(this).data("state");
            if(thisState>0){
                return;
            }
            // 遍历已选中订单
            var ordersns = [];
            $(".allorder").find("input:checkbox:checked").not(".cBoxOrderOne").each(function() {
                var _this = $(this).parent().next();
                var ordersn = _this.data("ordersn");
                var inarray = $.inArray(ordersn,ordersns);
                if(inarray<0){
                    ordersns.push(ordersn);
                }
            });
            // 初始化信息
            $(".dosend").text("执行发货").data("state",0);
            // 执行ajax
            $.ajax({
                type: 'POST',
                url: "{!! yzWebUrl('plugin.exhelper.admin.print-once.getOrderState') !!}",
                data: {ordersns:ordersns},
                dataType:'json',
                async:false,
                success: function(result){
                    result.printTemp.expresscom = !result.printTemp.expresscom?'其他快递':result.printTemp.expresscom;
                    $("#modal-send").find("#expresscom").text(result.printTemp.expresscom).data({'expresscom':result.printTemp.expresscom,'express':result.printTemp.express});
                    $("#modal-send").find("#printtempname").text(result.printTemp.expressname);
                    $(".sendtable").html("");
                    var _table = '<tr style="font-weight: bold;">';
                    _table+='<td style="width:60px;">序号</td>';
                    _table+='<td style="width: 200px;">订单号</td>';
                    _table+='<td style="width: 80px;">订单状态</td>';
                    _table+='<td style="width: 100px;">快递公司</td>';
                    _table+='<td>';
                    _table+='<span style="float: left; margin-left: 30px;">快递单号</span>';
                    _table+='<a href="javascript:;" style="float: right;" onclick="autonum()"><i class="fa fa-angle-double-down"></i> 自动填充</a>';
                    _table+='</td></tr>';
                    $(".sendtable").html(_table);
                    $.each(result.datas, function(i,arr) {
                        var sn = i+1;
                        var _html = '<tr data-state="'+arr.status+'" data-ordersn="'+arr.ordersn+'">';
                        _html+='<td>'+sn+'</td>';
                        _html+='<td>'+arr.ordersn+'</td>';
                        _html+='<td>';
                        if(arr.status==0){_html+='<label class="label label-danger">待付款</label>';}
                        if(arr.status==1){_html+='<label class="label label-info">待发货</label>';}
                        if(arr.status==2){_html+='<label class="label label-warning">待收货</label>';}
                        if(arr.status==3){_html+='<label class="label label-success">已完成</label>';}
                        _html+='</td>';
                        _html+='<td>'
                        //if(arr.status==0){_html+=' - ';}
                        //if(arr.status==1){_html+=result.printTemp.expresscom;}
                        //if(arr.status>1){_html+=arr.expresscom;}
                        _html+='</td>';
                        _html+='<td>';
                        if(arr.status==0){_html+='<input class="form-control" type="tel" data-state="'+arr.status+'" value="" placeholder="订单状态为“待付款”无法发货" disabled="">';}
                        if(arr.status==1){_html+='<input class="form-control" type="tel" data-state="'+arr.status+'" value="" placeholder="请输入单号">';}
                        if(arr.status>1){_html+='<input class="form-control" type="tel" data-state="'+arr.status+'" value="'+arr.expresssn+'" disabled="">';}
                        _html+='</td>';
                        _html+='</tr>';
                        // 获取订单状态 并且 赋给 #modal-send
                        $(".sendtable").append(_html);
                        // 显示 #modal-send
                        $("#modal-send").modal();
                    });
                }
            });
        });
    });
</script>


<script>
    //执行打印电子面单
    function printpanel(html_text, width, height, order_sn) {
        LODOP = getLodop($('#LODOP_OB'), $('#LODOP_EM'));
        console.log(LODOP);
        LODOP.PRINT_INITA("");
        // LODOP.PRINT_INIT(0, 0, width+'mm', height+'mm', '打印电子面单');
        LODOP.SET_PRINT_MODE("AUTO_CLOSE_PREWINDOW", 1);        //自动关闭窗口
        LODOP.SET_PRINT_MODE("RESELECT_PRINTER", 1); 
        LODOP.SET_PRINT_MODE("CATCH_PRINT_STATUS", 1);                //设置打印模式
        // LODOP.SET_PRINT_MODE("POS_BASEON_PAPER",true);          //“可打区域”边缘为基点
        LODOP.SET_PRINT_PAGESIZE(1, width+"mm", height, "");             //设定纸张大小及纵向打印
        // LODOP.SET_PRINT_PAGESIZE(0, width+"mm", height, "");             //设定纸张大小及纵向打印(test)
        
        LODOP.ADD_PRINT_HTML("0mm", "0mm", width+"mm", height, html_text);
        // LODOP.SET_PREVIEW_WINDOW(1, 2, 0, , , "打印电子面单.开始打印");                 //设置预览窗口
        // LODOP.SELECT_PRINTER();                 //选择打印设备
        // LODOP.PRINT();
        console.log('CVERSION:'+LODOP.CVERSION);
        if (LODOP.CVERSION) {
            LODOP.On_Return = function (TaskID, Value) {
            // TaskID1=LODOP.GET_VALUE("PRINT_STATUS_OK",P_ID);
            console.log('Value:'+Value+':TaskID'+TaskID);
            // if (LODOP.GET_VALUE('PRINT_STATUS_ID', 'PRINT_JOBID') == 128 || LODOP.GET_VALUE('PRINT_STATUS_ID','PRINT_JOBID') == 20) {
                // if (Value==1) {
                //     alert('准备打印');
                //     // return true;
                //     // 修改订单打印状态
                //     changeOrderPrintInfo('print3', order_sn);
                // } 
                if(!Value) {
                    // alert('打印已取消:value'+Value+',taskid:'+TaskID);
                    alert('打印已取消');
                } else {
                    alert('准备打印');
                    // 修改订单打印状态
                    changeOrderPrintInfo('print3', order_sn);
                }
            };   
            LODOP.PREVIEW();
            return ;
        }
    }

    // 执行保存收件人信息
    function savesender(){
        var realname = $(":input[name=realname]").val();
        var mobile = $(":input[name=mobile]").val();
        var province = $(":input[name=province]").val();
        var city = $(":input[name=city]").val();
        var area = $(":input[name=area]").val();
        var address = $(":input[name=address]").val();
        // 遍历获取 订单号
        var ordersns = [];
        $(".allorder").find("input:checkbox:checked").not(".cBoxOrderOne").each(function() {
            var _this = $(this).parent().next();
            var ordersn = _this.data("ordersn");
            var inarray = $.inArray(ordersn,ordersns);
            if(inarray<0){
                ordersns.push(ordersn);
            }
        });
        // ajax 执行写入数据库
        $.ajax({
            type: 'POST',
            url: "{!! yzWebUrl('plugin.exhelper.admin.print-once.saveAddress') !!}",
            data: {
                realname:realname,
                mobile:mobile,
                province:province,
                city:city,
                area:area,
                address:address,
                ordersns:ordersns
            },
            dataType:'json',
            success: function(result){
                if(result.result=='error'){
                    alert(result.resp);
                }
            }
        });
    }
    // 清除数据
    function cleardata(){
        $(".sendtable").find("input").each(function(){
            var _state = $(this).data("state");
            if(_state!=1){
                $(this).parent().parent().remove();
            }
        });
    }
    // 执行一键发货
    function dosend(){
        var _thisState = $(".dosend").data("state");
        if(_thisState==1){
            alert("正在执行...请稍候...");
            return;
        }
        if(_thisState==2){
            return;
        }
        var state = 0;
        $(".sendtable").find("input[data-state=1]").each(function(){
            if($(this).val()==''){
                alert("您还有未填写快递单号的订单哦~");
                Tip.focus($(this),'不能为空!');
                state = 1;
                return false;
            }
        });
        // 遍历输入框内容
        if(state==0){
            if(confirm('确定执行发货？')){
                $(".dosend").data("state",1);
                var total =$(".sendtable").find("input[data-state=1]").length;
                if(total<1){
                    alert("当前可发货订单为空");
                    return;
                }
                var expresscom = $("#modal-send").find("#expresscom").data("expresscom");
                var express = $("#modal-send").find("#expresscom").data("express");

                $(".sendtable").find("input[data-state=1]").each(function(i){
                    var expresssn = $(this).val();
                    var ordersn = $(this).parent().parent().data("ordersn");
                    $.ajax({
                        type: 'POST',
                        url: "{!! yzWebUrl('plugin.exhelper.admin.doprint',array('op'=>'dosend','type'=>$type)) !!}",
                        data: {ordersn:ordersn,expresssn:expresssn,expresscom:expresscom,express:express},
                        dataType:'json',
                        //async:false,
                        success: function(result){
                            if(result.result=='error'){
                                alert(result.resp);
                            }
                            if(result.result=='success'){
                                $(".dosend").text("正在执行...("+i+"/"+total+")");
                                $(".sendtable").find("tr[data-ordersn="+ordersn+"]").find("input").attr("disabled","disabled").data("state",2);
                                $(".sendtable").find("tr[data-ordersn="+ordersn+"]").find("label").removeClass("label-info").addClass("label-warning").text("待收货");
                                $(".sendtable").find("tr[data-ordersn="+ordersn+"]").data("state",2);
                                $(".allorder").find("table[data-ordersn="+ordersn+"]").find(".label-oss").removeClass("label-info").addClass("label-warning").text("待收货");
                            }
                        }
                    });
                    if(i+1==total){
                        alert("发货完成");
                        $(".dosend").text("发货完成");
                        $(".dosend").data("state",2);
                    }
                });
            }
        }
    }
    // 自动填充
    function autonum(){
        var indexval = $(".sendtable").find("input:first");
        console.log('indexval: '+indexval);
        var val = $.trim(indexval.val());
        console.log('val: '+val);

        if(val==''){
            Tip.focus(indexval,'不能为空!');
            return;
        }
        $(".sendtable").find("input[data-state=1]").each(function(){
            $(this).val(val);
        });
    }
    // 执行遍历发货信息并重组
    function changeSendInfo(){
        var arr = [];
        var sendInfo = '';
        $(".allorder").find("input:checkbox:checked").not(".cBoxOrderOne").each(function(){
            var goodId = $(this).parent().next().data("goodid")
            var goodNum = $(this).parent().next().data("goodnum");
            var shortTitle = $(this).parent().next().data("shorttitle");
            var goodTitle = $(this).parent().next().data("goodtitle");
            var gTitle = !shortTitle?goodTitle:shortTitle;
            var state = -1;
            $.each(arr,function(i,d){
                if(d.id===goodId){
                    state = i;
                    return false;
                }
            });
            if(state>-1){
                arr[state].num += goodNum;
            }else{
                arr.push({'id':goodId,'title':gTitle,'num':goodNum});
            }
        });
        $.each(arr, function(i,v) {
            sendInfo += v.title + " x "+ v.num + "; ";
        });
        $(".sendinfo").val(sendInfo);
    }
</script>
<script>
    // 公共JS脚本
    $(function(){

        // 全选当前页面所有订单
        $(".cBoxOrderAll").click(function(){
            //$("#gset").attr("checked","checked");
            var _thisState = $(this).is(":checked");
            var ordersn = $('#orderinfo').attr('data-ordersn');

            if(_thisState){
                $(".allorder").find("input[type=checkbox]").each(function(){
                    this.checked = true;
                });
                setPrintBtnState(0, ordersn);
            }else{
                $(".allorder").find("input[type=checkbox]").each(function(){
                    this.checked = false;
                });
                setPrintBtnState(1, ordersn);
            }
        });
        $(".cBoxOrderOne").click(function(){
            var _thisState = $(this).is(":checked");
            if(_thisState){
                $(this).parent().parent().parent().parent().find("input[type=checkbox]").each(function(){
                    this.checked = true;
                });
            }else{
                $(this).parent().parent().parent().parent().find("input[type=checkbox]").each(function(){
                    this.checked = false;
                });
            }
        });

        $(".cBoxGood").click(function(){
            var _thisState = $(this).is(":checked");
            var _table = $(this).closest(".table");
            if(_thisState){
                _table.find(".cBoxGood").each(function(){
                    _tS = $(this).is(":checked");
                    if(!_tS){
                        return;
                    }
                    _table.find(".cBoxOrderOne").each(function(){
                        this.checked = true;
                    });
                });
            }else{
                _table.find(".cBoxOrderOne").each(function(){
                    this.checked = false;
                });
            }
        });

        $(".allorder").find("input[type=checkbox]").change(function(){
            // 设置 打印按钮状态
            var gBtn = $(".allorder").find("input[type=checkbox]:checked").not(".cBoxOrderOne").length;
            console.log('设置打印按钮状态-gBtn: ' + gBtn);
            var ordersn = $('#orderinfo').attr('data-ordersn');
            console.log('设置打印按钮状态-ordersn: '+ ordersn);
            if(gBtn<=0){
                setPrintBtnState(1, ordersn);
            
            }else{
                setPrintBtnState(0, ordersn);
            }
            // 遍历选择框
            $(".allorder").find(".cBoxGood").each(function(){
                var _thisState = $(this).is(":checked");
                if(!_thisState){
                    $(".cBoxOrderAll").each(function(){
                        this.checked = false;
                    });
                    return false;
                }
                $(".cBoxOrderAll").each(function(){
                    this.checked = true;
                });
            });
        });

        // 修改商品短标题
        $(".editShort").click(function(){
            var _this = $(this);
            var _td = _this.parent();
            var clickDo = _this.data("do");
            var goodId = _td.data("goodid");
            var goodTitle = _td.data("goodtitle");
            var shortTitle = _td.data("shorttitle");
            if(clickDo=='e'){
                var gTitle = !shortTitle?goodTitle:shortTitle;
                _this.text("保存");
                _td.find(".goodtitle").html("<input type='text' value='"+gTitle+"' style='width:220px; padding-left:0; margin-left:0;'>");
                _this.data("do","s");
            }
            else if(clickDo=='s'){
                var gTitle = _td.find(".goodtitle").find("input").val();
                if(!goodId){
                    return;
                }
                // 执行ajax
                $.ajax({
                    type: 'POST',
                    url: "{!! yzWebUrl('plugin.exhelper.admin.print-once.shortTitle') !!}",
                    data: {goodid:goodId,shorttitle:gTitle},
                    dataType:'json',
                    success: function(d){
                        // 成功 后执行
                        var pTitle = !gTitle?goodTitle:gTitle;
                        _td.data("shorttitle",pTitle).find(".goodtitle").html(pTitle);
                        _this.text("编辑").data("do","e");
                        $("td[data-goodid="+goodId+"]").each(function(){
                            $(this).data("shorttitle",pTitle);
                            $(this).find(".goodtitle").text(pTitle);
                            $(this).find(".editShort").text("编辑").data("do","e");
                            // 执行遍历重组发货信息
                            changeSendInfo();
                        });
                    }
                });
            }
        });

    });

    function setPrintBtnState(d, ordersn){
        $.ajax({
            dataType: 'json',
            data: {ordersn: ordersn},
            url: "{!! yzWebUrl('plugin.exhelper.admin.print-once.getOrderPrintStatus') !!}",
            success: function(res){
                console.log(res);
                if (res.result != 1) {
                    alert(res.msg);
                
                } else {
                    // editBtnClass(res.msg);
                }
            }
        });
        if(d==0){
            // 正常
            $("#doprint1").data("state",0).removeClass("btn-disabled").addClass("btn-primary");
            $("#doprint2").data("state",0).removeClass("btn-disabled").addClass("btn-warning");
            $("#doprint3").data("state",0).removeClass("btn-disabled").addClass("btn-default");
            $("#dosend").data("state",0).removeClass("btn-disabled").addClass("btn-success");
        }else{

            $("#doprint1").data("state",1).removeClass("btn-primary").addClass("btn-disabled");
            $("#doprint2").data("state",1).removeClass("btn-warning").addClass("btn-disabled");
            $("#doprint3").data("state",1).removeClass("btn-default").addClass("btn-disabled");
            $("#dosend").data("state",1).removeClass("btn-success").addClass("btn-disabled");
        }
    }

    // function editBtnClass(msg) {
    //     if (msg.express_print_status == 1) {
    //         //部分打印
    //     } else if(msg.express_print_status == 2) {
    //         //完全打印
    //         $("#doprint1").data("state",0).removeClass("btn-default").addClass("btn-primary");
    //     } else {
    //         //未打印
    //         $("#doprint1").data("state",1).removeClass("btn-primary").addClass("btn-default");
    //     }
    //     switch(msg.send_print_status) {
    //         case '0': 
    //             //未打印快递单                
    //             $("#doprint2").data("state",1).removeClass("btn-warning").addClass("btn-default");
    //             break;
    //         case '1':
    //             break;
    //         default :
    //             $("#doprint2").data("state",0).removeClass("btn-default").addClass("btn-warning");
    //             break;
    //     }

    //     switch(msg.panel_print_status) {
    //         case '0':
    //             //未打印电子面单
    //             $("#doprint3").data("state",1).removeClass("btn-success").addClass("btn-default");
    //             break;
    //         case '1':
    //             break;
    //         default :
    //             $("#doprint3").data("state",0).removeClass("btn-default").addClass("btn-danger");
    //             break;                
    //     }
    // }

    // 执行修改订单打印数据，修改样式
    function changeOrderPrintInfo(pt, rep_ordersn) {
        // alert('1.1');
        // 遍历已选中的商品
        var arr = [];
        $(".allorder").find("input:checkbox:checked").not(".cBoxOrderOne").each(function() {
            var _this = $(this).parent().next();
            // console.log('__this1 = '+_this.html());
            var orderid = _this.data("orderid");
            console.log('orderid: '+ orderid);
            var ordergoodid = _this.data("ordergoodid");
            console.log('ordergoodid: ' + ordergoodid);
            arr.push({
                orderid: orderid,
                ordergoodid: ordergoodid
            });
        });
        
        var column = '';
        switch (pt) {
            case 'print1' : column = 'express_print_status' ; break;
            case 'print2' : column = 'send_print_status' ; break;
            default : column = 'panel_print_status' ; break;
        }
        // console.log('get_order_id'+ orderid);
        // 修改订单打印状态信息
        $.ajax({
            type: 'POST',
            // url: "{php echo $this->createPluginWebUrl('exhelper/doprint',array('op'=>'pushdata','type'=>$type))}",
                // url: "{!! yzWebUrl('plugin.exhelper.admin.print-once.shortTitle') !!}",
            url: "{!! yzWebUrl('plugin.exhelper.admin.print-once.editOrderPrintStatus') !!}",
            data: {
                 arr: arr,
                // pt:pt
                column: column
            },
            dataType: 'json',
            success: function(d) {
                console.log('d: '+JSON.stringify(d));
                // if (d.result == 'success') {
                if (d.result == 1) {
                    $(".allorder").find("input:checkbox:checked").not(".cBoxOrderOne").each(function() {
                        var _this = $(this).parent().parent();
                        // console.log('__this2 = '+_this.html());

                        if(pt=="print1"){
                            var _order = $(this).closest("tbody").find(".orderprintstate");
                            console.log('_order:' + _order);
                            var iconps1 = _this.find(".icon-ps1").data("printstate");
                            // 判断如果是未打印 则更改样式，如果是已经打印则更新打印次数
                            if (iconps1 < 1) {
                                _this.find(".icon-ps1").removeClass("label-default").addClass("label-primary").text("快递单 x 1").data("printstate", 1);
                            } else {
                                var num = iconps1 + 1;
                                _this.find(".icon-ps1").text("快递单 x " + num).data("printstate", num);
                            }
                            // 判断订单打印状态
                            _order.text("快递单已打印");
                        }
                        else if(pt=="print2"){
                            var _order = $(this).closest("tbody").find(".orderprintstate2");
                            var iconps2 = _this.find(".icon-ps2").data("printstate");
                            // 判断如果是未打印 则更改样式，如果是已经打印则更新打印次数
                            if (iconps2 < 1) {
                                // alert('<1');
                                _this.find(".icon-ps2").removeClass("label-default").addClass("label-success").text("发货单 x 1").data("printstate", 1);
                                console.log('num: ' + _this.find(".icon-ps2").removeClass("label-default").addClass("label-success").text("发货单 x 1").data("printstate"));
                            } else {
                                // alert('>1');
                                var num = iconps2 + 1;
                                if (num + 1 > 2) {return ;}
                                console.log('num: '+num);
                                _this.find(".icon-ps2").text("发货单 x " + num).data("printstate", num);
                                // _this.find(".icon-ps2").text("发货单已打印");
                                changeBtnNum(num, 'print2');
                            }

                            // 判断订单打印状态
                            _order.text("发货单已打印");
                        }
                        else {
                            //样式查找
                            var _order = $(this).closest("tbody").find(".orderprintstate3");
                            var iconps3 = _this.find(".test").data("printstate");
                            console.log('iconps3='+iconps3);
                            // 判断如果是未打印 则更改样式，如果是已经打印则更新打印次数
                            if (iconps3 < 1) {
                                // alert('<1');
                                _this.find(".test").removeClass("label-default").addClass("label-danger").text("电子面单 x 1").data("printstate", 1);
                            } else {
                                // alert('>1');
                                var num = iconps3 + 1;
                                console.log('num='+num);
                                _this.find(".test").text("电子面单 x " + num).data("printstate", num);
                            }
                            // 判断订单打印状态
                            _order.text("电子面单已打印");
                        }
                    });
                }
            }
        });
        // 写入打印记录
        function changeBtnNum(num,btn) {
            $.ajax({
                type: 'POST',
                url: "{!! yzWebUrl('plugin.exhelper.admin.print-once.editOrderPrintStatus') !!}",
                dataType: 'json',
                data: {num: num, column: btn},
            })
            .done(function() {
                console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
            
        }
    }
</script>