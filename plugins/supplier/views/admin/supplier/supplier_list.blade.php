@extends('layouts.base')

@section('content')
<div class="w1200 m0a">
    <script type="text/javascript" src="resource/js/lib/jquery-ui-1.10.3.min.js"></script>
    <style type='text/css'>
        .trhead td {  background:#efefef;text-align: center}
        .trbody td {  text-align: center; vertical-align:top;border-left:1px solid #ccc;overflow: hidden;}
    </style>
    <div class="rightlist">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">供应商管理</a></li>
            </ul>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="" method="post" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员ID</label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <input type="text" class="form-control"  name="search[member_id]" value="{{array_get($requestSearch,'member_id','')}}" placeholder='搜索会员ID'/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">供货商ID</label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <input type="text" class="form-control"  name="search[supplier_id]" value="{{array_get($requestSearch,'supplier_id','')}}" placeholder='搜索供货商ID'/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">供货商账号</label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <input type="text" class="form-control"  name="search[supplier]" value="{{array_get($requestSearch,'supplier','')}}" placeholder='搜索供货商账号'/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel panel-default">
            <a class='btn btn-info' href="{{yzWebUrl('plugin.supplier.admin.controllers.supplier.supplier-detail.add')}}"><i class="fa fa-plus"></i>添加新供应商</a>
            {{--<a href="javascript:;" data-url="{{yzPluginFullUrl('supplier.admin.login')}}" class="btn btn-default js-clip" title="复制链接">复制链接(登录)</a>--}}
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">总数：{{$total}}</div>
            <div class="panel-body" style="margin-bottom:100px;">
                <table class="table table-hover table-responsive">

                    <thead class="navbar-inner" >
                        <tr>
                            <th style='width:auto;'>ID</th>
                            <th style='width:auto;'>用户名</th>
                            <th style='width:auto;'>粉丝</th>
                            <th style='width:auto;'>姓名</th>
                            <th style='width:auto;'>电话</th>
                            @if($set['insurance_policy'])
                            <th style='width:auto;'>保单状态</th>
                            @endif
                            <th>操作</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($list['data'] as $row)
                            <tr>
                                <td>
                                    {{$row['id']}}
                                </td>
                                <td>
                                    {{$row['has_one_wq_user']['username']}}
                                </td>
                                <td>
                                    @if(!empty( $row['has_one_member']['avatar'] ))
                                        <a href="{!! yzWebUrl('member.member.detail', ['id'=>$row['has_one_member']['uid']]) !!}"><img src="{{$row['has_one_member']['avatar']}}" style="width:30px;height:30px;padding:1px;border:1px solid #ccc"></a>
                                        {{--<img src="{{ $row['has_one_member']['avatar'] }}" style='width:30px;height:30px;padding:1px;border:1px solid #ccc' alt="">--}}
                                    @endif
                                    <br>
                                    @if(!empty($row['has_one_member']['nickname']))
                                            <a href="{!! yzWebUrl('member.member.detail', ['id'=>$row['has_one_member']['uid']]) !!}"> {{ $row['has_one_member']['nickname'] }}</a>
                                        {{--{{ $row['has_one_member']['nickname'] }}--}}
                                    @endif
                                </td>
                                <td>
                                    {{$row['realname']}}
                                </td>
                                <td>
                                    {{$row['mobile']}}
                                </td>
                                @if($set['insurance_policy'])
                                <td>
                                    <div class="col-sm-2 col-xs-6">
                                        <input class="mui-switch mui-switch-animbg" id="insurance_status_{!! $row['id'] !!}" type="checkbox"
                                               @if($row['insurance_status'])
                                               checked
                                               @endif
                                               onclick="message_default(this.id, {{$row['id']}})"/>
                                    </div>
                                </td>
                                @endif
                                <td  style="overflow:visible;">
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="javascript:;">
                                            操作 <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-left" role="menu" style='z-index: 99999'>
                                            <li>
                                                <a href="{{yzWebUrl('plugin.supplier.admin.controllers.supplier.supplier-detail.index', ['supplier_id' => $row['id']])}}" title='供应商信息'>
                                                    <i class='fa fa-user'></i> 供应商信息
                                                </a>
                                            </li>
                                            {{--@if($row['diyform_data_id'] != 0 && $exist_diyform)
                                                <li>
                                                    <a href="{{yzWebUrl('plugin.diyform.admin.diyform-data.get-member-form-data', ['member_id' => $row['uid'],'form_type'=>'register'])}}"
                                                       title="会员详情"><i class='fa fa-edit'></i> 自定义表单信息</a>
                                                </li>
                                            @endif--}}
                                            <li>
                                                <a href="{{yzWebUrl('plugin.supplier.admin.controllers.supplier.supplier-edit-pwd.index', ['uid' => $row['uid']])}}" title='修改密码'><i class='fa fa-edit'></i> 修改密码</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $pager !!}
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('.js-clip').each(function () {
            util.clip(this, $(this).attr('data-url'));
        });
        function message_default(name, s_id) {
            var id = "#" + name;
            var url_open = "{!! yzWebUrl('plugin.supplier.admin.controllers.supplier.supplier-list.change-open') !!}"
            var url_close = "{!! yzWebUrl('plugin.supplier.admin.controllers.supplier.supplier-list.change-close') !!}"
            var postdata = {
                id: s_id,
            };
            if ($(id).is(':checked')) {
                //开
                $.post(url_open, postdata, function(data){
                    if (data.result == 1) {
                        alert(data.msg)
                    } else {
                        alert(data.msg)
                    }
                }, "json");
            } else {
                //关
                $.post(url_close, postdata, function(data){
                    alert(data.msg)
                }, "json");
            }
        }
    </script>
@endsection