@extends('layouts.base')

@section('content')
<div class="w1200 m0a">

    <div class="rightlist">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="" method="post" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">会员信息</label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <input type="text" class="form-control"  name="search[member]" value="{{array_get($params,'member','')}}" placeholder="可搜索会员ID/昵称/姓名/手机号"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label"></label>
                        <div class="col-sm-7 col-lg-9 col-xs-12">
                            <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">总数：{{$total}}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                            <tr>
                                <th style='width:6%;text-align: center;'>ID</th>
                                <th style='width:16%;text-align: center;'>账户</th>
                                <th style='width:16%;text-align: center;'>详情</th>
                                <th style='width:16%;text-align: center;'>状态</th>
                                <th style='width:16%;text-align: center;'>操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($list['data'] as $row)
                                <tr>
                                    <td style="text-align: center;">{{$row['id']}}</td>
                                    <td style="text-align: center;">{{$row['username']}}</td>
                                    <td style="text-align: center;">
                                        @if ($row['diyform_data_id'] == 0)
                                            <a class="label label-default label-info" href="{{yzWebUrl('plugin.supplier.admin.controllers.apply.supplier-apply.detail', ['id' => $row['id']])}}">查看申请信息</a>
                                        @endif
                                        @if ($exist_diyform && $row['diyform_data_id'] != 0)
                                            <a class="label label-default label-info" href="{{yzWebUrl('plugin.supplier.admin.controllers.apply.information.get-info-by-form-id', ['member_id' => $row['member_id'],'form_data_id'=>$row['diyform_data_id'],'supplier_id' => $row['id']])}}">查看申请信息</a>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if($row->status == 1)
                                            <label type="button" class="label label-success">
                                                审核通过
                                            </label>
                                        @else
                                            <label type="button" class="label label-warning">
                                                等待审核
                                            </label>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <a class="label label-default " href="{{yzWebUrl('plugin.supplier.admin.controllers.apply.apply-operation.apply-operation', ['apply_id' => $row['id'], 'type' => -1])}}">驳回审核</a>
                                        <a class="label label-default label-info" href="{{yzWebUrl('plugin.supplier.admin.controllers.apply.apply-operation.apply-operation', ['apply_id' => $row['id'], 'type' => 1])}}">审核通过</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! $pager !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection