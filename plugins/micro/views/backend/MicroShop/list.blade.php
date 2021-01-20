@extends('layouts.base')
@section('title', '微店管理')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>
    <script type="text/javascript" src="./resource/js/lib/jquery-ui-1.10.3.min.js"></script>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active">微店管理</li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info"><!--
                <div class="panel-heading">筛选</div>-->
                <div class="panel-body">
                    <form action="" method="post" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="route" value="plugin.micro.backend.controllers.MicroShop.list" id="route" />
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>-->
                            <div class="">
                                <input type="text" class="form-control"  name="search[member]" value="{{$request['member']}}" placeholder="可搜索昵称/姓名/手机号"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            {{--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微店名称</label>--}}
                            <div class="">
                                <input type="text" class="form-control"  name="search[shop_name]" value="{{$request['shop_name']}}" placeholder="可搜索微店名称"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            {{--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微店等级</label>--}}
                            <div class="">
                                <select name='search[level_id]' class='form-control'>
                                    <option value=''>等级不限</option>
                                    @foreach($levels as $level)
                                        <option value='{{$level->id}}'
                                                @if($request['level_id'] == $level->id)
                                                selected
                                                @endif
                                        >{{$level->level_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                            <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
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
                                <th style='width:14%;text-align: center;'>开店时间</th>
                                <th style='width:8%;text-align: center;'>微店名称</th>
                                <th style='width:10%;text-align: center;'>店主</th>
                                <th style='width:8%;'>店主等级</th>
                                <th style='width:6%;text-align: center;'>下级微店店主总数</th>
                                <th style='width:12%;text-align: center;'>微店消费总金额</th>
                                <th style='width:8%;text-align: center;'>已结算分红</th>
                                <th style='width:8%;text-align: center;'>累计分红</th>
                                <th style='width:24%;text-align: center;'>二维码/链接</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $row)
                                <tr>
                                    <td style="text-align: center;">   {{$row->created_at}}</td>
                                    <td style="text-align: center;">{{$row->shop_name}}</td>
                                    <td style="text-align: center;">
                                        <img src='{{$row->hasOneMember->avatar}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                                        <br/>
                                        <a href="{!! yzWebUrl($member_detail_url,['id' => $row->hasOneMember->uid])!!}">@if ($row->hasOneMember->nickname) {{$row->hasOneMember->nickname}} @else {{$row->hasOneMember->mobile}} @endif</a>
                                    </td>

                                    <td title="{{$row->hasOneMicroShopLevel->level_name}}" class='tdedit' width="26%">
                                        <span class=' fa-edit-item' style='cursor:pointer'>
                                            <span class="title">{{$row->hasOneMicroShopLevel->level_name}}</span>
                                            <i class='fa fa-pencil' style="display:none"></i>
                                        </span>

                                        <div class="input-group level" style="display:none">
                                            <select class="form-control tpl-agent-level" name="level_id" data-agencyid="{{$row['id']}}">
                                                @foreach($levels as $value)
                                                    <option value="{{$value->id}}"
                                                            @if($row['level_id']==$value->id)
                                                            selected
                                                            @endif
                                                    >{{$value->level_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>

                                    <td style="text-align: center;">{{$row->lower_total}}</td>
                                    <td style="text-align: center;">
                                        <label class="label label-info">{!! number_format($row->money_total, 2) !!}元</label>
                                    </td>
                                    <td style="text-align: center;">
                                        <label class="label label-info">{!! number_format($row->ok_total, 2) !!}元</label>
                                    <td style="text-align: center;">
                                        <label class="label label-info">{!! number_format($row->sum_total, 2) !!}元</label>
                                    </td>
                                    <td style="text-align: center;position: relative;overflow: visible;">
                                        <a class="btn btn-sm btn-default umphp" title="微店二维码"
                                           data-url="{{yzAppFullUrl('microShopShare/home/'.$row->id) . '&shop_id=' . $row->id}}"
                                           data-goodsid="{{$row->id}}">
                                            <div class="img">
                                                {!! QrCode::size(120)->generate(yzAppFullUrl('microShopShare/home/'.$row->id) . '&shop_id=' . $row->id) !!}
                                            </div>
                                            <i class="fa fa-qrcode"></i>
                                        </a>
                                        <a href="javascript:;"
                                           data-clipboard-text="{{yzAppFullUrl('microShopShare/home/'.$row->id) . '&shop_id=' . $row->id}}"
                                           data-url="{{yzAppFullUrl('microShopShare/home/'.$row->id) . '&shop_id=' . $row->id}}"
                                           title="复制连接" class="btn btn-default btn-sm js-clip"><i
                                                    class="fa fa-link"></i></a>
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
    <script language='javascript'>
        $('.tdedit').mouseover(function () {
            $(this).find('.fa-pencil').show();
        }).mouseout(function () {
            $(this).find('.fa-pencil').hide();
        });
        $('.fa-edit-item').click(function () {
            $(this).closest('span').hide();
            $(this).next('.level').show();

        });
        $('.tpl-agent-level').change(function () {
            var agencyId = $(this).data('agencyid');
            var levelId = $(this).val();
            fastChange(agencyId, levelId);
        });
        function fastChange(id, value) {
            $.ajax({
                url: "{!! yzWebUrl('plugin.micro.backend.controllers.MicroShop.list.change') !!}",
                type: "post",
                data: {id: id, value: value},
                cache: false,
                success: function ($data) {
                    console.log($data);
                    location.reload();
                }
            })
        }

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
                $('#route').val("plugin.micro.backend.controllers.MicroShop.list.export");
                $('#form1').submit();
                $('#route').val("plugin.micro.backend.controllers.MicroShop.list");
            });
        });
    </script>
@endsection