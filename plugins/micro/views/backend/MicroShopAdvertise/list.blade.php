@extends('layouts.base')
@section('title', '广告位')
@section('content')

    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">广告位</a></li>
                </ul>
            </div>
            <form action="" method="post">
                <div class="panel panel-default">
                    <div class="panel-body table-responsive">
                        <table class="table table-hover">
                            <thead class="navbar-inner">
                            <tr>
                                <th style="width:80px;">ID</th>
                                <th>标题</th>
                                <th>连接</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($slide as $row)
                                <tr>
                                    <td>{{$row['id']}}</td>

                                    <td>{{$row['slide_name']}}</td>
                                    <td>{{$row['link']}}</td>
                                    <td>
                                        @if($row['enabled']==1)
                                            <span class='label label-success'>显示</span>
                                        @else
                                            <span class='label label-danger'>隐藏</span>
                                        @endif
                                    </td>
                                    <td style="text-align:left;">
                                        <a href="{{yzWebUrl("plugin.micro.backend.controllers.MicroShopAdvertise.list.edit",['id'=>$row['id']])}}"
                                           class="btn btn-default btn-sm"
                                           title="{修改"><i
                                                    class="fa fa-edit"></i></a>
                                        <a href="{{yzWebUrl("plugin.micro.backend.controllers.MicroShopAdvertise.list.delete",['id'=>$row['id']])}}"
                                           class="btn btn-default btn-sm"
                                           onclick="return confirm('确认删除此广告位?');"
                                           title="删除"><i
                                                    class="fa fa-times"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan='6'>
                                    <a class='btn btn-primary'
                                       href="{{yzWebUrl("plugin.micro.backend.controllers.MicroShopAdvertise.list.add")}}"><i
                                                class='fa fa-plus'></i>
                                        添加广告位</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {{$pager}}
                    </div>
                </div>
            </form>

        </div>
    </div>
    <!--   @include('public.admin.mylink') -->
@endsection()


