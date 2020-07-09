@extends('layouts.base')

@section('content')
@section('title', trans('分销商管理'))
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">分销商管理</a></li>
    </ul>
</div>
<form action="" method="post" class="form-horizontal"  id="form1">
    <div class="panel panel-info">
        <div class="panel-body">

            <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <div class="">
                    <input type="text" class="form-control"  name="search[order_sn]" value="{{$search['order_sn']?$search['order_sn']:''}}" placeholder="订单编号"/>
                </div>
            </div>

            <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                <div class="">
                    <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>
                </div>
            </div>

        </div>
    </div>
</form>

<div class='panel panel-default'>
    <div class='panel-heading'>
        数量: {{$list->total()}} 条
    </div>
    <div class='panel-body'>

        <table class="table table-hover" style="overflow:visible;">
            <thead>
            <tr>
                <th style='width:6%;'>ID</th>
                <th style='width:16%;'>订单号</th>
                <th style='width:12%;'>购买人</th>
                <th style='width:12%;'>上级</th>
                <th style='width:8%;'>比例</th>
                <th style='width:52%;'>详情</th>

            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
                <tr>
                    <td>
                        {{$row->id}}
                    </td>
                    <td>
                        {{$row->hasOneOrder->order_sn}}
                    </td>
                    <td>
                        <img src="{{tomedia($row->hasOneBuyMember->avatar)}}"
                             style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        </br>
                        {{$row->hasOneBuyMember->nickname}}
                    </td>
                    <td>
                        <img src="{{tomedia($row->hasOneMember->avatar)}}"
                             style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        </br>
                        {{$row->hasOneMember->nickname}}
                    </td>

                    <td>
                        {{$row->ratio}}%
                    </td>
                    <td>
                        {{$row->content}}
                    </td>
                </tr>

            @endforeach
            </tbody>
        </table>

        {!! $pager !!}
    </div>
</div>
<div style="width:100%;height:150px;"></div>
<script type="text/javascript">

    $(function () {

        $('#search').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('plugin.commission.admin.agent.index') !!}');
            $('#form1').submit();
        });
    });

</script>
@endsection