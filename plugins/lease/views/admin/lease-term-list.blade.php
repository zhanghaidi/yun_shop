@extends('layouts.base')

@section('content')
@section('title', trans('租期设置'))

<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">租期设置</a></li>
        <a class='btn btn-primary' href="{{yzWebUrl('plugin.lease-toy.admin.lease-term.add')}}"
           style="margin-bottom:5px;"><i class='fa fa-plus'></i> 添加租期</a>
    </ul>
</div>


<div class='panel panel-default'>

    <div class='panel-body'>
        <table class="table table-hover" style="overflow:visible;">
            <thead>
            <tr style="height: 20%;text-align: center;">
                <th style='width:5%;'>排序</th>
                <th style='width:10%;'>名称</th>
                <th style='width:10%;'>天数</th>
                <th style='width:10%;'>优惠比例</th>
                <th style='width:10%;'>操作</th>

            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
                <tr>
                    <td>{{$row->sequence}}</td>
                    <td>{{$row->term_name}}</td>
                    <td>{{$row->term_days}}</td>
                    <td>
                        {{$row->term_discount}}
                        @if (!empty($row->term_discount))
                        %
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-default"
                           href="{!! yzWebUrl('plugin.lease-toy.admin.lease-term.edit', ['id'=>$row->id]) !!}">编辑</a>
                        <a class="btn btn-default"
                           href="{!! yzWebUrl('plugin.lease-toy.admin.lease-term.deleted', ['id'=>$row->id]) !!}"
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
            $('#form1').attr('action', '{!! yzWebUrl('plugin.lease-toy.admin.lease-term.export') !!}');
            $('#form1').submit();
        });
        $('#search').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('plugin.lease-toy.admin.lease-term.index') !!}');
            $('#form1').submit();
        });
    });
</script>
@endsection