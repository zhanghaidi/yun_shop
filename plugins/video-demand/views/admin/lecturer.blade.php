@extends('layouts.base')

@section('content')
@section('title', trans('讲师管理'))
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">讲师管理</a></li>
        <a class='btn btn-primary' href="{{yzWebUrl('plugin.video-demand.admin.lecturer.add')}}"
           style="margin-bottom:5px;"><i class='fa fa-plus'></i> 添加讲师</a>
    </ul>
</div>

<div class='panel panel-default'>
    <form action="" method="post" class="form-horizontal" id="form1">
        <div class="panel panel-info">
            <div class="panel-body">

                <div class="form-group col-xs-12 col-sm-3">
                    <input class="form-control" name="search[lecturer_id]" type="text"
                           value="{{$search['lecturer_id']}}" placeholder="ID">
                </div>

                <div class="form-group col-xs-12 col-sm-3">
                    <input class="form-control" name="search[member]" type="text"
                           value="{{$search['member']}}" placeholder="会员ID/昵称/姓名/手机">
                </div>

                <div class="form-group col-xs-12 col-sm-4">

                    <input type="button" class="btn btn-success" id="export" value="导出">

                    <input type="button" class="btn btn-success pull-right" id="search" value="搜索">

                </div>

            </div>
        </div>
    </form>
</div>

<div class='panel panel-default'>
    <div class='panel-heading'>
        总数 ：{{$total}}
    </div>
    <div class='panel-body'>
        <table class="table table-hover" style="overflow:visible;">
            <thead>
            <tr>
                <th style='width:5%;'>ID</th>
                <th style='width:10%;'>会员</th>
                <th style='width:10%;'>姓名</br>手机号</th>
                <th style='width:10%;'>成为时间</th>
                <th style='width:10%;'>课程商品数</th>
                <th style='width:10%;'>累计结算分红金额</th>
                <th style='width:8%;'>累计未结算分红金额</th>

            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
                <tr>
                    <td>{{$row->id}}</td>

                    <td>
                        <a target="_blank"
                           href="{{yzWebUrl('member.member.detail',['id'=>$row->hasOneMember->uid])}}">
                            <img src="{{tomedia($row->hasOneMember->avatar)}}"
                                 style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                            </br>
                            {{$row->hasOneMember->nickname}}
                        </a>
                    </td>
                    <td>{{$row->real_name}}</br>{{$row->mobile}}</td>
                    <td>{{$row->created_at}}</td>

                    <td>{{count($row->hasManyCourseGoods)}}</td>
                    <td>{{$row->statement}}</td>
                    <td>{{$row->not_statement}}</td>
                </tr>

            @endforeach
            </tbody>
        </table>

        {!! $pager !!}
    </div>
</div>
<div style="width:100%;height:150px;"></div>
<script language='javascript'>
    $(function () {
        $('#export').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('plugin.video-demand.admin.lecturer.export') !!}');
            $('#form1').submit();
        });
        $('#search').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('plugin.video-demand.admin.lecturer.index') !!}');
            $('#form1').submit();
        });
    });
</script>
@endsection