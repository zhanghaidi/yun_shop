@extends('layouts.base')
@section('title', '供货商保单管理')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>
    <script type="text/javascript" src="./resource/js/lib/jquery-ui-1.10.3.min.js"></script>
    <style>
        .select select{height:34px;border:#ccc 1px solid}
    </style>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active">保单列表</li>
                    <li>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <a class="btn btn-success" style="width: 100px" href="{!! yzWebUrl('plugin.supplier.admin.controllers.insurance.insurance.upload')!!}">上传保单</a>
                    </li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info">
                <div class="panel-body">
                    <form action="" method="get" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="c" value="site"/>
                        <input type="hidden" name="a" value="entry"/>
                        <input type="hidden" name="m" value="yun_shop"/>
                        <input type="hidden" name="do" value="supplier_order" id="form_do"/>
                        <input type="hidden" name="route" value="plugin.supplier.admin.controllers.insurance.insurance.index" id="form_p"/>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control"  name="search[supplier_id]" value="{{$search['supplier_id']?$search['supplier_id']:''}}" placeholder="供货商ID"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control"  name="search[supplier_number]" value="{{$search['supplier_number']?$search['supplier_number']:''}}" placeholder="供货商账号"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control"  name="search[member_id]" value="{{$search['member_id']?$search['member_id']:''}}" placeholder="会员ID"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control"  name="search[shop_name]" value="{{$search['shop_name']?$search['shop_name']:''}}" placeholder="店面名称"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control"  name="search[insured_person]" value="{{$search['insured_person']?$search['insured_person']:''}}" placeholder="被保人姓名/电话"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control"  name="search[member_name]" value="{{$search['member_name']?$search['member_name']:''}}" placeholder="会员昵称/姓名/手机号码"/>
                            </div>
                        </div>
                        <div class="form-group col-sm-8 col-lg-5 col-xs-12">
                            <div class="form-group col-sm-8 col-lg-5 col-xs-12">
                                <select name="search[time_range][field]" class="form-control form-time">
                                    <option value="" @if( array_get($search,'time_range.field',''))selected="selected"@endif >
                                        创建时间
                                    </option>
                                    <option value="1" @if( array_get($search,'time_range.field','')=='1')  selected="selected"@endif >
                                        是
                                    </option>
                                    <option value="0" @if( array_get($search,'time_range.field','')=='0')  selected="selected"@endif >
                                        否
                                    </option>
                                </select>
                            </div>
                            <div class="form-group col-sm-8 col-lg-5 col-xs-12">

                                {!!
                                    app\common\helpers\DateRange::tplFormFieldDateRange('search[time_range]', [
                            'starttime'=>array_get($search,'time_range.start',0),
                            'endtime'=>array_get($search,'time_range.end',0),
                            'start'=>0,
                            'end'=>0
                            ], true)!!}
                            </div>

                        </div>


                        <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                            <div class="">
                                <button  name="export"  id="export" onclick="exports()" class="btn btn-default excel back ">导出 Excel</button>
                                <button class="btn btn-success "><i class="fa fa-search" onclick="search()"></i> 搜索</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="clearfix">
                <div class="panel panel-default" style="overflow-x:scroll;">
                    <div class="panel-body" style="margin-bottom:200px ;width:150%;">
                        <table class="table table-hover" style="overflow:visible">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:4%;text-align: center;'>序号</th>
                                <th style='width:10%;text-align: center;'>供应商账号</th>
                                <th style='width:10%;text-align: center;'>店面名称</th>
                                <th style='width:10%;text-align: center;'>被保险人名字</th>
                                <th style='width:10%;text-align: center;'>被保险人联系方式</th>
                                <th style='width:10%;text-align: center;'>证件号码</th>
                                <th style='width:20%;text-align: center;'>保险地址</th>
                                <th style='width:8%;text-align: center;'>投保财产</th>
                                <th style='width:8%;text-align: center;'>用户类型</th>
                                <th style='width:10%;text-align: center;'>保额（万）</th>
                                <th style='width:10%;text-align: center;'>保险期限（年）</th>
                                <th style='width:8%;text-align: center;'>保费（元）</th>
                                <th style='width:8%;text-align: center;'>投保险种</th>
                                <th style='width:8%;text-align: center;'>附加玻璃险（35元保1万）份</th>
                                <th style='width:8%;text-align: center;'>投保人（安防公司）</th>
                                @if($is_company == 1)
                                    <th style='width:10%;text-align: center;'>保险公司</th>
                                @endif
                                <th style='width:15%;text-align: center;'>创建时间</th>
                                <th style='width:8%;text-align: center;'>支付方式</th>
                                <th style='width:8%;text-align: center;'>备注</th>
                                <th style='width:15%;text-align: center;'>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $row)
                                <tr>
                                    <td style='width:4%;text-align: center;'>{{$row['serial_number']}}</td>
                                    <td style='width:10%;text-align: center;'>{{$row['supplier']['username']}}</td>
                                    <td style='width:20%;text-align: center;'>{{$row['shop_name']}}</td>
                                    <td style='width:10%;text-align: center;'>{{$row['insured']}}</td>
                                    <td style='width:10%;text-align: center;'>{{$row['phone']}}</td>
                                    <td style='width:10%;text-align: center;'>{{$row['identification_number']}}</td>
                                    <td style='width:8%;text-align: center;'>{{$row['address']}}</td>
                                    <td style='width:8%;text-align: center;'>{{$row['insured_property']}}</td>
                                    <td style='width:8%;text-align: center;'>{{$row['customer_type']}}</td>
                                    <td style='width:10%;text-align: center;'>{{$row['insured_amount']}}</td>
                                    <td style='width:10%;text-align: center;'>{{$row['guarantee_period']}}</td>
                                    <td style='width:8%;text-align: center;'>{{$row['premium']}}</td>
                                    <td style='width:8%;text-align: center;'>{{$row['insurance_coverage']}}</td>
                                    <td style='width:8%;text-align: center;'>{{$row['additional_glass_risk']}}</td>
                                    <td style='width:8%;text-align: center;'>{{$row['insurance_company']}}</td>
                                    @if($is_company == 1)
                                        <td style='width:5%;text-align: center;'>{{$row['has_one_company']['name']}}</td>
                                    @endif
                                    <td style='width:8%;text-align: center;'>{{$row['created_at']}}</td>
                                    @if($row['is_pay'] == 1)
                                        <td style='width:8%;text-align: center;' class="infoBeti"><a class="label label-info ">{{$row['pay_type'] ?: '余额'}}</a></td>
                                    @else
                                        <td style='width:8%;text-align: center;' class="infoBeti"><a class="label label-danger ">未支付</a></td>
                                    @endif
                                    <td style='width:8%;text-align: center;' class="infoBeti"><a class="label label-info " onclick="InfoClick('{{$row['note']}}')" >备注信息</a></td>
                                    <td style='width:8%;text-align: center;'>
                                        @if($row['is_pay'] == 1)
                                            <a class="btn btn-default" onclick="showType('{{ $phone1 }}', '{{ $phone2 }}')" href="#">修改</a>
                                        @else
                                            <a class="btn btn-default" href="{!! yzWebUrl('plugin.supplier.admin.controllers.insurance.insurance.insuranceEdit',['id'=>$row['id']])!!}">修改</a>
                                        @endif
                                        <a class="btn btn-default" href="{!! yzWebUrl('plugin.supplier.admin.controllers.insurance.insurance.insuranceDel',['id'=>$row['id']])!!}">删除</a>
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

        function InfoClick(Val){
            alert(Val)
        }
        function exports() {
            $("input[name^='route']").val('plugin.supplier.admin.controllers.insurance.insurance.export');
        }
        function search() {
            $("input[name^='route']").val('plugin.supplier.admin.controllers.insurance.insurance.index');
        }
        function showType($phone1, $phone2) {
            alert('保单已支付，若要修改请联系客服热线：' + $phone1 + ' 或者 ' + $phone2);
        }
    </script>
@endsection