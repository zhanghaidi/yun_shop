@extends('layouts.base')

@section('content')
@section('title', trans('发货人信息管理'))
    <div class="rightlist">
        <form action="" method="post">
            <div class="panel panel-default">
                <div class="panel-body table-responsive">
                    <table class="table table-hover">
                        <thead class="navbar-inner">
                        <tr>
                            <th style="width:30px;">ID</th>
                            <th>发件人</th>
                            <th>发件人电话</th>
                            <th>发件人签名</th>
                            <th>发件地邮编</th>
                            <th>发件地址</th>
                            <th>发件城市</th>
                            <th>是否默认</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->sender_name}}</td>
                            <td>{{$item->sender_tel}}</td>
                            <td>{{$item->sender_sign}}</td>
                            <td>{{$item->sender_code}}</td>
                            <td>{{$item->sender_address}}</td>
                            <td>{{$item->sender_city}}</td>
                            <td>
                                @if($item->isdefault == 1)
                                    <span class='label label-success'><i class='fa fa-check'></i></span>
                                @endif
                            </td>
                            <td style="text-align:left;">

                                <a href="{{yzWebUrl('plugin.exhelper.admin.send-user.edit', ['id' => $item->id])}}" class="btn btn-default btn-sm" title="修改"><i class="fa fa-edit"></i></a>
                                <a href="{{yzWebUrl('plugin.exhelper.admin.send-user.delete', ['id' => $item->id])}}" class="btn btn-default btn-sm" onclick="return confirm('确认删除此模板?')"title="删除"><i class="fa fa-times"></i></a>
                                @if(empty($item->is_default))
                                <a href="{{yzWebUrl('plugin.exhelper.admin.send-user.isDefault', ['id' => $item->id])}}" class="btn btn-default btn-sm" onclick="return confirm('确认设置默认?')" title="设置默认"><i class="fa fa-check"></i></a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan='8'>
                                <a class='btn btn-default' href="{{yzWebUrl('plugin.exhelper.admin.send-user.add', ['id' => $item->id])}}"><i class='fa fa-plus'></i> 添加快递单信息模板</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    {!! $pager !!}
                </div>
            </div>
        </form>
    </div>
    <script>
        require(['bootstrap'], function ($) {
            $('.btn').hover(function () {
                $(this).tooltip('show');
            }, function () {
                $(this).tooltip('hide');
            });
        });
    </script>
@endsection