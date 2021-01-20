@extends('layouts.base')
@section('title', '门店申请列表')
@section('content')
<div class="w1200 m0a">

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">门店申请列表</a></li>
        </ul>
    </div>

    <div class="rightlist">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form">

                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="store" id="form_do"/>
                    <input type="hidden" name="route" value="{{\Yunshop\Mryt\store\admin\ApplyController::INDEX_URL}}" id="route" />

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">会员ID</label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <input type="text" class="form-control"  name="search[id]" value="{{$search['id']}}" placeholder="可搜索昵称/姓名/手机号"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">会员信息</label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <input type="text" class="form-control"  name="search[member]" value="{{$search['member']}}" placeholder="可搜索昵称/姓名/手机号"/>
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
                                <th style='width:4%;text-align: center;'>ID</th>
                                <th style='width:14%;text-align: center;'>会员</th>
                                <th style='width:16%;text-align: center;'>账号</th>
                                <th style='width:10%;text-align: center;'>姓名</th>
                                <th style='width:10%;text-align: center;'>手机</th>
                                <th style='width:20%;text-align: center;'>申请时间</th>
                                <th style='width:8%;text-align: center;'>状态</th>
                                <th style='width:8%;text-align: center;'>详情</th>
                                <th style='width:15%;text-align: center;'>操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($list as $row)
                                <tr>
                                    <td style="text-align: center;">{{$row->id}}</td>
                                    <td style="text-align: center;">
                                        <img src='{{$row->hasOneMember->avatar}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                                        <br/>
                                        <a href="{!! yzWebUrl('member.member.detail',['id' => $row->uid])!!}">@if ($row->hasOneMember->nickname) {{$row->hasOneMember->nickname}} @else {{$row->hasOneMember->mobile}} @endif</a>
                                    </td>
                                    <td style="text-align: center;">{{$row->username}}</td>
                                    <td style="text-align: center;">{{$row->realname}}</td>
                                    <td style="text-align: center;">{{$row->mobile}}</td>
                                    <td style="text-align: center;">{{$row->created_at}}</td>
                                    <td style="text-align: center;">
                                        @if ($row->status == -1)
                                            <label type="button" class="label label-primary">
                                        @elseif($row->status == 1)
                                            <label type="button" class="label label-success">
                                        @elseif($row->status == 0)
                                            <label type="button" class="label label-warning">
                                        @endif
                                            {{$row->status_name}}
                                        </label>
                                    </td>
                                    <td style="text-align: center;">
                                            <a class="label label-info label-info" href="{{yzWebUrl('plugin.mryt.store.admin.apply.detail', ['id' => $row['id']])}}">查看详情</a>
                                    </td>
                                    <td style="text-align: center;">
                                        @if (!$row->status)
                                            <a class="label label-default " href="{{yzWebUrl('plugin.mryt.store.admin.apply.examine', ['apply_id' => $row['id'], 'type' => -1])}}">驳回审核</a>
                                            <a class="label label-default label-info" href="{{yzWebUrl('plugin.mryt.store.admin.apply.examine', ['apply_id' => $row['id'], 'type' => 1])}}">审核通过</a>
                                        @endif
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