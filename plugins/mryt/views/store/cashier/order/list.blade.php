@extends('layouts.base')
@section('title','收银台订单')

@section('content')

    <link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>

    <div class="w1200 m0a">
        <script type="text/javascript" src="{{static_url('js/dist/jquery.gcjs.js')}}"></script>
        <script type="text/javascript" src="{{static_url('js/dist/jquery.form.js')}}"></script>
        <script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>

        <div class="rightlist">
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">订单管理</a></li>
                </ul>
            </div>

            <div class="panel panel-info">
                <div class="panel-body">
                    @include('Yunshop\StoreCashier::admin.order.list_tpl.form')
                </div>
            </div>

            <div class="panel panel-default">
                @include('Yunshop\StoreCashier::admin.order.list_tpl.statistics')
                @include('Yunshop\StoreCashier::admin.order.list_tpl.foreach')
                @include('order.modals')
                <div id="pager">{!! $pager !!}</div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
    <script>
        function search_members() {
            if ($('#search-kwd-notice').val() == '') {
                Tip.focus('#search-kwd-notice', '请输入关键词');
                return;
            }
            $("#module-menus-notice").html("正在搜索....");
            $.get("{!! yzWebUrl('plugin.store-cashier.admin.store.query') !!}", {
                keyword: $.trim($('#search-kwd-notice').val())
            }, function (dat) {
                $('#module-menus-notice').html(dat);
            });
        }
        function select_member(o) {
            $("#noticeopenid").val(o.cashier_id);
            $("#saleravatar").show();
            $("#saleravatar").find('img').attr('src', o.thumb);
            $("#saler").val(o.store_name);
            $("#modal-module-menus-notice .close").click();
        }
        function remove_member(obj) {
            $(obj).parent().remove();
            refresh_members();
        }
        function refresh_members() {
            var nickname = "";
            $('.multi-item').each(function () {
                nickname += " " + $(this).find('.img-nickname').html() + "; ";
            });
            $('#salers').val(nickname);
        }
        $(function () {
            $("#ambiguous-field").on('change', function () {

                $(this).next('input').attr('placeholder', $(this).find(':selected').text().trim())
            });
        })
        $(function () {
            $('#search').click(function(){
                $('#route').val("plugin.store-cashier.admin.order.index");
            });
        });
        $(function () {
            $('#export').click(function(){
                $('#store_id').val({!! \Yunshop\StoreCashier\admin\OrderController::$export_param !!});
                $('#route').val("{{$export_url}}");
            });
        });
        $(function () {
            $('#search').click(function(){
                $('#route').val("{!! \Yunshop\StoreCashier\admin\OrderController::INDEX_URL !!}");
            });
        });
        function getStatistic() {
            $('#get_statistic').val("正在加载...")
            $.ajax({
                url: "{!! yzWebUrl('plugin.store-cashier.admin.order.get-statistics') !!}",
                type: "get",
                data: {},
                success: function (result) {
                    $('.has_settlement').html(result.data.has_settlement)
                    $('.no_settlement').html(result.data.no_settlement)
                    $('.deduct_point').html(result.data.deduct_point)
                    $('.deduct_love').html(result.data.deduct_love)
                    $('.deduct_coupon').html(result.data.deduct_coupon)
                    $('.remard_buyer_point').html(result.data.remard_buyer_point)
                    $('.remard_buyer_love').html(result.data.remard_buyer_love)
                    $('.remard_buyer_coupon').html(result.data.remard_buyer_coupon)
                    $('.remard_store_point').html(result.data.remard_store_point)
                    $('.remard_store_love').html(result.data.remard_store_love)
                    $("#statistics").show();
                    $('#get_statistic').val("加载成功...")
                }
            })
        }
    </script>
@endsection('content')