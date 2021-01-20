@extends('layouts.base')

@section('content')
@section('title', trans('幻灯片管理'))

<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">幻灯片管理</a></li>
        <a class='btn btn-primary' href="{{yzWebUrl('plugin.video-demand.admin.video-slide.add')}}"
           style="margin-bottom:5px;"><i class='fa fa-plus'></i> 添加幻灯片</a>
    </ul>
</div>


<div class='panel panel-default'>

    <div class='panel-body'>
        <table class="table table-hover" style="overflow:visible;">
            <thead>
            <tr>
                <th style='width:5%;'>ID</th>
                <th style='width:10%;'>标题</th>
                <th style='width:15%;'>链接</th>
                <th style='width:10%;'>状态</th>
                <th style='width:10%;'>操作</th>

            </tr>
            </thead>
            <tbody>
            @foreach($slide as $row)
                <tr>
                    <td>{{$row->id}}</td>
                    <td>{{$row->slide_name}}</td>
                    <td>{{$row->link}}</td>
                    <td>{{$row->status_name}}</td>
                    <td>
                        <a class="btn btn-default"
                           href="{!! yzWebUrl('plugin.video-demand.admin.video-slide.edit', ['id'=>$row->id]) !!}">编辑</a>
                        <a class="btn btn-default"
                           href="{!! yzWebUrl('plugin.video-demand.admin.video-slide.deleted', ['id'=>$row->id]) !!}"
                           onclick="return confirm('是否确认删除？');return false;">删除</a>
                    </td>

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