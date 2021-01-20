@extends('layouts.base')

@section('content')
@section('title', trans('归还地址'))

<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">归还地址</a></li>
        <a class='btn btn-primary' href="{{yzWebUrl('plugin.lease-toy.admin.return-address.add')}}"
           style="margin-bottom:5px;"><i class='fa fa-plus'></i> 添加归还地址</a>
    </ul>
</div>


<div class='panel panel-default'>

    <div class='panel-body'>
        <table class="table table-hover" style="overflow:visible;">
            <thead>
            <tr>
                <th style='width:5%;'>ID</th>
                <th style='width:15%;'>归还地址</th>
                <th style='width:10%;'>联系人</th>
                <th style='width:10%;'>电话/手机</th>
                <th style='width:5%;'>默认地址</th>
                <th style='width:10%;'>操作</th>

            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
                <tr>
                    <td>{{$row->id}}</td>
                    <td>{{$row->province}}<br>{{$row->address}}</td>
                    <td>{{$row->contact_name}}</td>
                    <td>{{$row->mobile}}</td>
                    <td>
                        @if($row->is_default==1)
                            <label class='label label-success'>是</label>
                        @else
                            <label class='label label-default'>否</label>
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-default"
                           href="{!! yzWebUrl('plugin.lease-toy.admin.return-address.edit', ['id'=>$row->id]) !!}">编辑</a>
                        <a class="btn btn-default"
                           href="{!! yzWebUrl('plugin.lease-toy.admin.return-address.deleted', ['id'=>$row->id]) !!}"
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
    // $(function () {
    //     $('#export').click(function () {
    //         $('#form1').attr('action', '{!! yzWebUrl('plugin.lease-toy.admin.return-address.export') !!}');
    //         $('#form1').submit();
    //     });
    //     $('#search').click(function () {
    //         $('#form1').attr('action', '{!! yzWebUrl('plugin.lease-toy.admin.return-address.index') !!}');
    //         $('#form1').submit();
    //     });
    // });
</script>
@endsection