@extends('layouts.base')

@section('content')
    <link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div id="member-blade" class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->

        <div class="right-titpos">
            @include('layouts.tabs')
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="" method="post" class="form-horizontal" role="form" id="form1">
                    <div class="form-group col-sm-11 col-lg-11 col-xs-12">
                        <div class="">
                            <div class='input-group'>
                                <input class="form-control" name="search[jiushi_wechat]" type="text"
                                       value="{{ $request['search']['jiushi_wechat'] or ''}}" placeholder="灸师微信号">
                                <input class="form-control" name="search[jiushi_id]" type="text"
                                       value="{{ $request['search']['jiushi_id'] or ''}}" placeholder="灸师ID">
                                <input class="form-control" name="search[jiushi_name]" type="text"
                                       value="{{ $request['search']['jiushi_name'] or ''}}" placeholder="灸师真实姓名">
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-1 col-lg-1 col-xs-12">
                        <div class="">
                            <input type="submit" class="btn btn-block btn-success" value="搜索">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">总数：{{ $count }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:15%; text-align: center;'>灸师ID</th>
                            <th style='width:10%; text-align: center;'>灸师账号</th>
                            <th style='width:10%; text-align: center;'>灸师真实姓名</th>
                            <th style='width:10%; text-align: center;'>灸师微信号</th>
                            <th style='width:10%; text-align: center;'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr style="text-align: center;">
                                <td>{{ $list['id'] }}</td>
                                <td>{{ $list['username'] }}</td>
                                <td>{{ $list['jiushi_name'] }}</td>
                                <td>{{ $list['jiushi_wechat'] }}</td>
                                <td style="overflow:visible;">
                                    <a class='btn btn-default'
                                       href="{{yzWebUrl('jiushisms.jiushisms.jiushiedit', ['id' => $list['id']])}}"
                                       title='编辑'><i class='fa fa-list'></i>编辑
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $pager !!}

                </div>
            </div>
        </div>

@endsection