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
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info"><!--
                <div class="panel-heading">筛选</div>-->
                <div class="panel-body">
                    <form action="" method="get" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="c" value="site"/>
                        <input type="hidden" name="a" value="entry"/>
                        <input type="hidden" name="m" value="yun_shop"/>
                        <input type="hidden" name="do" value="supplier_order" id="form_do"/>
                        <input type="hidden" name="route" value="plugin.supplier.supplier.controllers.insurance.insurance.index" id="form_p"/>
                        <input type="hidden" name="search[supplier_id]" value="{{Session::get('supplier')['id']}}" />
                        {{--<div class="form-group col-xs-12 col-sm-10 col-md-6 col-lg-4">--}}
                            {{--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">保险地址</label>--}}
                            {{--<div class="select">--}}
                                {{--{!! \Yunshop\Supplier\supplier\controllers\insurance\InsuranceController::tplLinkedAddress(['search[province_id]','search[city_id]','search[district_id]','search[street_id]'], [])!!}--}}

{{--                                {!! app\common\helpers\AddressHelper::tplLinkedAddress(['search[province_id]','search[city_id]','search[district_id]','search[street_id]'], [])!!}--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control"  name="search[serial_number]" value="{{$search['serial_number']?$search['serial_number']:''}}" placeholder="序号"/>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control"  name="search[insured_person]" value="{{$search['insured_person']?$search['insured_person']:''}}" placeholder="被保人姓名/电话"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control"  name="search[shop_name]" value="{{$search['shop_name']?$search['shop_name']:''}}" placeholder="门店名称"/>
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
                        <div class="form-group  col-xs-12 col-sm-7 col-lg-2">
                            <div class="">
                                <button  name="export"  id="export" onclick="exports()" class="btn btn-default excel back ">导出 Excel</button>
                                <button class="btn btn-success "><i class="fa fa-search" onclick="search()"></i> 搜索</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><div class="clearfix">
                <div class="panel panel-default" style="overflow-x:scroll;">
                    {{--<div class="panel-heading">总数：{{$list->total()}}   </div>--}}
                    <div class="panel-body" style="width:150%;">
                        <table class="table table-hover">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:30px;text-align: center;'>
                                    <input type="checkbox"  id="all-choose">
                                </th>
                                <th style='width:10%;text-align: center;'>ID</th>
                                {{--<th style='width:10%;text-align: center;'>序号</th>--}}
                                <th style='width:10%;text-align: center;'>供应商账号</th>
                                <th style='width:30%;text-align: center;'>店面名称</th>
                                <th style='width:15%;text-align: center;'>被保险人名字</th>
                                <th style='width:15%;text-align: center;'>被保险人联系方式</th>
                                <th style='width:20%;text-align: center;'>证件号码</th>
                                <th style='width:20%;text-align: center;'>保险地址</th>
                                <th style='width:8%;text-align: center;'>投保财产</th>
                                <th style='width:8%;text-align: center;'>用户类型</th>
                                <th style='width:10%;text-align: center;'>保额（万）</th>
                                <th style='width:10%;text-align: center;'>保险期限（年）</th>
                                <th style='width:8%;text-align: center;'>保费（元）</th>
                                <th style='width:8%;text-align: center;'>投保险种</th>
                                <th style='width:20%;text-align: center;'>附加玻璃险（35元保1万）份</th>
                                <th style='width:8%;text-align: center;'>投保人（安防公司）</th>
                                @if($is_company == 1)
                                <th style='width:8%;text-align: center;'>保险公司</th>
                                @endif
                                <th style='width:15%;text-align: center;'>创建时间</th>
                                <th style='width:8%;text-align: center;'>支付方式</th>
                                <th style='width:8%;text-align: center;'>备注</th>
                                <th style='width:15%;text-align: center;'>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $key=>$row)
                                <tr>
                                    <td style='width:30px;text-align: center;'>
                                        <input type="checkbox" data-id="{{$row['id']}}" key='{{$key}}' id="choose-one{{$row['id']}}" @if($row['is_pay']==1) disabled @endif onclick="changeOne({{$key}},{{$row['id']}},this)">
                                    </td>
                                    <th style='width:10%;text-align: center;'>{{$row['id']}}</th>
                                    {{--<td style='width:10%;text-align: center;'>{{$row['serial_number']}}</td>--}}
                                    <td style='width:10%;text-align: center;'>{{$row['supplier']['username']}}</td>
                                    <td style='width:30%;text-align: center;'>{{$row['shop_name']}}</td>
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
                                    <td style='width:8%;text-align: center;'>{{$row['has_one_company']['name']}}</td>
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
                                        <a class="btn btn-default" href="{!! yzWebUrl('plugin.supplier.supplier.controllers.insurance.insurance.insuranceEdit',['id'=>$row['id']])!!}">修改</a>
                                        @endif
                                        <a class="btn btn-default" href="{!! yzWebUrl('plugin.supplier.supplier.controllers.insurance.insurance.insuranceDel',['id'=>$row['id']])!!}">删除</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!!$pager!!}
                    </div>
                </div>
                <div class='panel-footer'>
                    <div class="form-group col-sm-6">
                        <a class='btn btn-info' href="{!! yzWebUrl('plugin.supplier.supplier.controllers.insurance.insurance.insuranceAdd')!!}"><i class='fa fa-plus'></i> 添加保单</a>
                        <a class='btn btn-success' href="{!! yzWebUrl('plugin.supplier.supplier.controllers.insurance.batchsend.index')!!}"><i class='fa fa-plus'></i> Excel 导入</a>
                        <a class='btn btn-success' onclick="pay()"> 支付</a>
                        <a class='btn btn-success' onclick="downPolicy()"> 下载保单</a>

                        {{--<a class='btn btn-success' href="{{yzWebUrl('plugin.supplier.supplier.controllers.insurance.insurance.getExample')}}"><i class='fa fa-download'></i> 下载Excel模板文件</a>--}}
                    </div>
                    {{--<form action="{{yzWebUrl('plugin.supplier.supplier.controllers.insurance.batchsend.index')}}" method="post" enctype="multipart/form-data">--}}
                        {{--<div class="col-sm-2 goodsname"  style="padding-right:0;" >--}}
                            {{--<input type="file" name="send[excelfile]" class="form-control" />--}}
                            {{--<span class="help-block">如果遇到数据重复则将进行数据更新</span>--}}
                        {{--</div>--}}
                        {{--<button type="submit" class="btn btn-primary" >确认导入</button>--}}
                    {{--</form>--}}
                </div>
            </div>
        </div>
    </div>
    <!-- 支付模态框（Modal） -->
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">支付</h4>
                </div>
                <div class="modal-body">
                    <div style="text-align:center">
                        <img id="er_img" src="#" alt="" style="width:250px;height:250px">
                    </div>
                    <div style="text-align:center;line-height: 60px;font-weight: 700;">
                        请扫描二维码支付
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <!-- <button type="button" class="btn btn-primary">提交更改</button> -->
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>

    <!-- 下载保单模态框（Modal） -->
    <div class="modal fade" id="downModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">下载保单</h4>
                    <!-- <input class="form-control" name="keyword" type="text" placeholder="请输入保单文件名称" style="width:80%;display:inline-block">
                    <a class="btn btn-success" onClick="searchPdf()">搜索</a> -->
                </div>
                <div class="modal-body">
                    <div style="height:400px;overflow-y:auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>文件</th>
                                    <th>时间</th>
                                    <th>下载</th>
                                </tr>
                            </thead>
                            <tbody id="datas">
                                
                            </tbody>
                        </table>
                    </div>
                    <div style="float: left;">
                        <nav id="image-list-pager">
                            <div>
                                <ul class="pagination pagination-centered" id="page">
                                    <li><a href="javascript:;" page="1" class="pager-nav">首页</a></li>
                                    <li><a href="javascript:;" page="last" class="pager-nav">«上一页</a></li>
                                    <li class="active"><a href="javascript:;" page="1">1</a></li>
                                    <li><a href="javascript:;" page="2">2</a></li>
                                    <li><a href="javascript:;" page="3">3</a></li>
                                    <li><a href="javascript:;" page="next" class="pager-nav">下一页»</a></li>
                                    <li><a href="javascript:;" page="9999" class="pager-nav">尾页</a></li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <!-- <button type="button" class="btn btn-primary">提交更改</button> -->
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
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

        function note(val) {
            alert(val);
        }

        function exports() {
            $("input[name^='route']").val('plugin.supplier.supplier.controllers.insurance.insurance.exportExample');
        }
        function search() {
            $("input[name^='route']").val('plugin.supplier.supplier.controllers.insurance.insurance.index');
        }


        let datas = {!! json_encode($data)?:'[]' !!};
        let choosed_arr = [];
        console.log(datas)
        $("#all-choose").change(function () {

            if ($('#all-choose').is(":checked") == false) {
                console.log('没选中')
                datas.forEach((item,index) => {
                    if(item.is_pay!=1) {
                        $('input[key='+index+']').prop('checked',false)
                    }
                    choosed_arr = [];
                })
            } else if ($('#all-choose').is(":checked") == true) {
                console.log('选中')
                datas.forEach((item,index) => {
                    if(item.is_pay!=1) {
                        $('input[key='+index+']').prop('checked',true)
                        // 判断是否已存在
                        if(choosed_arr.indexOf(item.id) == -1) {
                            choosed_arr.push(item.id);
                        }
                    }
                })
            }
            console.log(choosed_arr)
        })
        function changeOne(index,id,e) {
            console.log(index,id)
            console.log($(e).is(":checked"))
            if($(e).is(":checked")==true) {
                // 选中
                choosed_arr.push(id);
            }
            else{
                // 取消选中
                let index1 = choosed_arr.indexOf(id);
                console.log(index1)
                if(index1!=-1) {
                    choosed_arr.splice(index1,1);
                }
            }
            console.log(choosed_arr)

        }
        function pay() {
            if(choosed_arr.length<=0) {
                alert('请选择要支付的保单')
                return false;
            }
            console.log(choosed_arr)
            $.ajax({
                type: "post",
                url: "{!! yzWebUrl('plugin.supplier.supplier.controllers.insurance.insurance.createInsCode') !!}",
                data: {
                    ids: choosed_arr
                },
                success: function(response) {
                    if (response.result == 1) {
                        $('#payModal').modal();
                        $('#er_img').prop('src',response.data)
                    }
                    else {
                        alert(response.msg)
                    }
                }
            });
            
        }
        var page = 0;
        var total = 0;
        var current_page = 0;
        var last_page = 0;
        
        function downPolicy(page1) {
            console.log('下载保单')
            $('#downModal').modal();
            $.ajax({
                type: "post",
                url: "{!! yzWebUrl('plugin.supplier.supplier.controllers.insurance.insurance.pdfList') !!}",
                data: {
                    page:page1
                },
                success: function(response) {
                    if (response.result == 1) {

                        var html = '';
                        $('#datas').empty();
                        $('#page').empty();
                        for(let i=0;i<response.data.list.data.length;i++) {
                            for(let j=0;j<response.data.list.data[i].pdf.length;j++) {
                                html += `<tr>
                                    <td>`+response.data.list.data[i].file_name[j]+`</td>
                                    <td>`+response.data.list.data[i].created_at+`</td>
                                    <td>
                                        <a href="`+response.data.list.data[i].pdf[j]+`" download="`+response.data.list.data[i].file_name[j]+`" class="btn btn-success">下载</a>
                                        <a href="`+response.data.list.data[i].pdf[j]+`" class="btn btn-info">预览</a>
                                    </td>
                                </tr>`;
                            }
                            
                        }
                        $('#datas').append($(html));


                        total = response.data.list.total;
                        current_page = response.data.list.current_page;
                        page = response.data.list.current_page;
                        last_page = response.data.list.last_page;
                        var page_datas = '';
                        page_datas  += `
                            <li class="lis"><a href="javascript:;" page="1" class="pager-nav">首页</a></li>
                            <li class="lis"><a href="javascript:;" page="last" class="pager-nav">«上一页</a></li>
                        `
                            for(let i=0;i<last_page;i++) {
                                page_datas  += 
                                `
                                    <li class="lis li-page"><a href="javascript:;" page="1">1</a></li>
                                `
                            }
                        page_datas  += 
                        
                        `<li class="lis"><a href="javascript:;" page="next" class="pager-nav">下一页»</a></li>
                        <li class="lis"><a href="javascript:;" page="9999" class="pager-nav">尾页</a></li>`;
                        $('#page').append($(page_datas));
                        console.log($('.li-page'))
                        $('.li-page a').each(function() {
                            console.log($(this).attr('page'))
                            let page1 = $(this).attr('page');
                            if(page1 == page) {
                                $(this).parents('.li-page').addClass('active')
                            }
                            else {
                                (this).parents('.li-page').removeClass('active')
                            }
                        })
                    }
                    else {
                        alert(response.msg)
                    }
                }
            });
            
        }
        $("#image-list-pager").on('click','.lis',function() {
            console.log($(this).children().attr('page'))
            let page1 = $(this).children().attr('page')
            if(page1 == 'last') {
                page--;
                if(page<=0) {
                    page=1
                }
                downPolicy(page)
            }
            else if(page1 == 'next') {
                page++;
                if(page>=last_page) {
                    page=last_page
                }
                downPolicy(page)
            }
            else if(page1 == '9999') {
                page=last_page
                downPolicy(page)
            }
            else {
                page = page1;
                downPolicy(page)
            }
        })
        function showType($phone1, $phone2) {
            alert('保单已支付，若要修改请联系客服热线：' + $phone1 + ' 或者 ' + $phone2);
        }
    </script>
@endsection