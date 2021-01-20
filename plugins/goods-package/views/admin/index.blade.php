@extends('layouts.base')
@section('title', '套餐列表')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>
    <script type="text/javascript" src="./resource/js/lib/jquery-ui-1.10.3.min.js"></script>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active">套餐列表</li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info"><!--
                <div class="panel-heading">筛选</div>-->
                <div class="panel-body">
                    <form action="" method="get" class="form-horizontal" role="form" id="form1">
                        @include('Yunshop\GoodsPackage::admin.form')
                        <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                            <div class="">
                                <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">总数:	&nbsp;{{count($packages)}}&nbsp;个</div>
                    <div class="panel-body" style="margin-bottom:20px">
                        <table class="table table-hover" style="overflow:visible">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:30%;text-align: center;'>套餐标题</th>
                                <th style='width:30%;text-align: center;'>套餐限时</th>
                                <th style='width:10%;text-align: center;'>套餐价格</th>
                                <th style='width:10%;text-align: center;'>状态</th>
                                <th style='width:20%;text-align: center;'>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($packages as $package)
                                <tr>
                                    <td style="text-align: center;">{{$package['title']}}</td>
                                    <td style="text-align: center;"> @if($package['limit_time_status']) {{date("Y-m-d H:i",$package['start_time'])}}至{{date("Y-m-d H:i",$package['end_time'])}} @else 未限时 @endif </td>
                                    <td style="text-align: center;">{{$package['price_sum']}}元</td>
                                    <td style="text-align: center;">
                                        @if($package['status'] == 1)
                                        <label class="label label-success">开启</label>
                                        @else
                                        <label class="label label-default">关闭</label>
                                        @endif
                                    </td>
                                    <td style="text-align: center;position:relative; overflow:visible;" width="20%">
                                        <a class="btn btn-sm btn-default umphp" title="套餐二维码"
                                           data-url="{{yzAppFullUrl('packageGoods/'.$package['id'])}}"
                                           data-goodsid="{{$package['id']}}">
                                            <div class="img">
                                                {!! QrCode::size(120)->generate(yzAppFullUrl('packageGoods/'.$package['id'])) !!}
                                            </div>
                                            <i class="fa fa-qrcode"></i>
                                        </a>
                                        <a href="javascript:;"
                                           data-clipboard-text="{{yzAppFullUrl('packageGoods/'.$package['id'])}}"
                                           data-url="{{yzAppFullUrl('packageGoods/'.$package['id'])}}"
                                           title="复制链接" class="btn btn-default btn-sm js-clip"><i class="fa fa-link"></i>
                                        </a>
                                        <a href="{{yzWebUrl('plugin.goods-package.admin.package.edit', ['id' => $package['id']])}}" class="btn btn-sm btn-default" title='编辑'><i class='fa fa-edit'></i></a>
                                        <a href="{{yzWebUrl('plugin.goods-package.admin.package.delete', ['id' => $package['id']])}}" class="btn btn-sm btn-default" title='删除'><i class='fa fa-remove'></i></a>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!!$pager!!}
                    </div>
                </div>
                <div class='panel-footer'>
                    <a class='btn btn-info' href="{{yzWebUrl('plugin.goods-package.admin.package.create')}}"><i class='fa fa-plus'></i> 添加套餐</a>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
    <script language='javascript'>
        if (Clipboard.isSupported()) {
            var clipboard =  new Clipboard('.js-clip');
            clipboard.on('success', function(e) {
                //alert('复制成功');
                swal({
                    title: "复制成功",
                    buttonsStyling: false,
                    confirmButtonClass: "btn btn-success"
                });
                //swal('Any fool can use a computer')
                e.clearSelection();
            });
        } else {
            $('.js-clip').each(function () {
                util.clip(this, $(this).attr('data-url'));
            });
        }
        cascdeInit();
        $('.umphp').hover(function () {
                var url = $(this).attr('data-url');
                $(this).addClass("selected");
            },
            function () {
                $(this).removeClass("selected");
            });
        $(function () {
            $('#export').click(function(){
                $('#route').val("{!! 'plugin.goods-package.admin.package.index' !!}");
                $('#form1').submit();
            });
        });
    </script>
@endsection