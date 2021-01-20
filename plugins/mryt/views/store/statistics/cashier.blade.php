@extends('layouts.base')
@section('title', '统计')

@section('content')

    <link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>

    <div class="w1200 m0a">
        <script type="text/javascript" src="{{static_url('js/dist/jquery.gcjs.js')}}"></script>
        <script type="text/javascript" src="{{static_url('js/dist/jquery.form.js')}}"></script>
        <script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>

        <div class="rightlist">
            <div class="panel panel-info">
                <div class="panel-body">
                    @include('Yunshop\Mryt::store.statistics.cashier_tpl.form')
                </div>
            </div>

            <div class="panel panel-default">
                @include('Yunshop\Mryt::store.statistics.cashier_tpl.foreach')
                @include('order.modals')
                <div id="pager">{!! $pager !!}</div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
    <script>
        var province_id = $('#province_id').val();
        var city_id = $('#city_id').val();
        var district_id = $('#district_id').val();
        var street_id = $('#street_id').val();
        cascdeInit(province_id, city_id, district_id, street_id);
        $(function () {
            $('#export').click(function(){
                $('#route').val("{!! \Yunshop\Mryt\store\admin\CashierController::EXPORT_URL !!}");
            });
        });
        $(function () {
            $('#search').click(function(){
                $('#route').val("{!! \Yunshop\Mryt\store\admin\CashierController::INDEX_URL !!}");
            });
        });
    </script>
@endsection('content')