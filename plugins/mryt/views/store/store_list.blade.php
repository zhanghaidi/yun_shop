@extends('layouts.base')
@section('title', '门店列表')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>
    <script type="text/javascript" src="./resource/js/lib/jquery-ui-1.10.3.min.js"></script>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active">门店列表</li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info"><!--
                <div class="panel-heading">筛选</div>-->
                <div class="panel-body">
                    <form action="" method="get" class="form-horizontal" role="form" id="form1">
                        @include('Yunshop\Mryt::store.store.form')
                        <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                            <div class="">
                                <button type="button" name="export" value="1" id="export" class="btn btn-default excel back ">导出 Excel</button>
                                <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">总数：{{$list->total()}}   </div>
                    <div class="panel-body" style="margin-bottom:200px">
                        <table class="table table-hover" style="overflow:visible">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:4%;text-align: center;'>ID</th>
                                <th style='width:10%;text-align: center;'>门店名称</th>
                                <th style='width:20%;text-align: center;'>门店地址</th>
                                <th style='width:10%;text-align: center;'>门店电话</th>
                                <th style='width:10%;text-align: center;'>门店店长</th>
                                <th style='width:8%;text-align: center;'>分类</th>
                                <th style='width:8%;text-align: center;'>二维码</th>
                                <th style='width:8%;text-align: center;'>下载</th>
                                <th style='width:16%;'>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $row)
                                <tr>
                                    <td style="text-align: center;">{{$row->id}}</td>
                                    <td style="text-align: center;">
                                        <img src='{{tomedia($row->thumb)}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                                        <br/>
                                        {{$row->store_name}}
                                    </td>
                                    <td style="text-align: center;">{{$row->province}}<br>{{$row->address}}</td>
                                    <td style="text-align: center;">{{$row->mobile}}</td>
                                    <td style="text-align: center;">
                                        <img src='{{$row->hasOneMember->avatar}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                                        <br/>
                                        <a href="{!! yzWebUrl('member.member.detail',['id' => $row->hasOneMember->uid])!!}">@if ($row->hasOneMember->nickname) {{$row->hasOneMember->nickname}} @else {{$row->hasOneMember->mobile}} @endif</a>
                                    </td>
                                    <td style="text-align: center;">{{$row->hasOneCategory->name}}</td>
                                    <td style="text-align: center;position: relative;overflow: visible;">
                                        <a class="btn btn-sm btn-default umphp" title="门店二维码"
                                           data-url="
                                           @if($row->boss_uid)
                                           {{yzAppFullUrl('cashier_pay/' . $row->id,['mid'=>$row->boss_uid])}}
                                           @else
                                           {{yzAppFullUrl('cashier_pay/' . $row->id,['mid'=>$row->uid])}}
                                           @endif"
                                           data-goodsid="{{$row->id}}">
                                            <div class="img">
                                                <img style="width: 120px;high:120px;" src="{{$row->download_url}}">
                                            </div>
                                            <i class="fa fa-qrcode"></i>
                                        </a>
                                    </td>
                                    <td style="text-align: center;position: relative;overflow: visible;">
                                        <a download="{{$row->download_url}}" href="{{$row->download_url}}" title="下载二维码" class="btn btn-default btn-sm js-clip"><i class="fa  fa-file-image-o"></i></a>
                                    </td>
                                    <td style="overflow:visible;">
                                        <a class="btn btn-primary" href="{{yzWebUrl('plugin.mryt.store.admin.store.edit', ['store_id' => $row->id])}}">编辑</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!!$pager!!}
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
    <script language='javascript'>
        cascdeInit();
        $('.umphp').hover(function () {
                    var url = $(this).attr('data-url');
                    $(this).addClass("selected");
                },
                function () {
                    $(this).removeClass("selected");
                })
        $('.js-clip').each(function () {
            util.clip(this, $(this).attr('data-url'));
        });
        $(function () {
            $('#export').click(function(){
                $('#route').val("{!! \Yunshop\Mryt\store\admin\StoreController::EXPORT_URL !!}");
                $('#form1').submit();
                $('#route').val("{!! \Yunshop\Mryt\store\admin\StoreController::INDEX_URL !!}");
            });
        });
    </script>
@endsection