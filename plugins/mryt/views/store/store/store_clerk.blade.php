@extends('layouts.base')

@section('content')
@section('title', trans('门店核销员'))
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">门店核销员</a></li>
    </ul>
</div>

<div class='panel panel-default'>
    <form action="" method="post" class="form-horizontal" id="form1">
        <div class="panel panel-info">
            <div class="panel-body">


                <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                    <input class="form-control" name="search[member]" type="text"
                           value="{{$search['member']}}" placeholder="昵称/姓名/手机">
                </div>

                <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                    <input class="form-control" name="search[store]" type="text"
                           value="{{$search['store']}}" placeholder="所属门店关键字">
                </div>

                <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                    <select class="form-control tpl-category-parent" id="status" name="search[status]">
                        <option value="">状态</option>
                        <option value="1">启用</option>
                        <option value="0">禁用</option>
                    </select>
                </div>

                <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                    <div class="">
                        <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class='panel panel-default'>

    <div class='panel-body'>
        <table class="table table-hover" style="overflow:visible;">
            <thead>
            <tr>
                <th style='width:5%;'>ID</th>
                <th style='width:10%;'>核销员</th>
                <th style='width:8%;'>姓名</br>手机号码</th>
                <th style='width:8%;'>所属门店</th>
                <th style='width:8%;'>状态</th>
                <!-- <th style='width:10%;'>操作</th> -->
            </tr>
            </thead>
            <tbody>
            @foreach($list['data'] as $row)
                <tr>
                    <td>{{$row['id']}}</td>
                    <td>
                        <a target="_blank"
                           href="{{yzWebUrl('member.member.detail',['id'=>$row['has_one_member']['uid']])}}">
                            <img src="{{tomedia($row['has_one_member']['avatar'])}}"
                                 style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                            </br>
                            {{$row['has_one_member']['nickname']}}
                        </a>
                    </td>
                    <td>{{$row['realname']}}</br>{{$row['mobile']}}</td>
                    <td>{{$row['has_one_store']['store_name']}}</td>
                    <td>{{$row['status_name']}}</td>
                    <!-- <td>
                        <a class='btn btn-info' href="{{yzWebUrl('plugin.store-cashier.admin.stores.store-order.index',['clerk_id'=>$row['id']])}}">核销订单</a>
                    </td> -->
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
            $('#form1').attr('action', '{!! yzWebUrl('plugin.store-cashier.admin.stores.clerk.export') !!}');
            $('#form1').submit();
        });
        $('#search').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('plugin.store-cashier.admin.stores.clerk') !!}');
            $('#form1').submit();
        });
    });
</script>
@endsection