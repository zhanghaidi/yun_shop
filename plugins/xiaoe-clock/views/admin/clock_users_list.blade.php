@extends('layouts.base')
@section('title', trans('日记列表'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            @if($room_type=='1')
                <li class="active"><a href="#">日历打卡学员列表</a></li>
            @endif
            @if($room_type=='2')
                <li class="active"><a href="#">作业打卡学员列表</a></li>
            @endif
        </ul>
    </div>

    @if($room_type=='1')
        <div class="panel panel-info">
            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form2">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="{{ $request['do'] }}"/>
                    <input type="hidden" name="route" value="plugin.xiaoe-clock.admin.clock.clock_users_list"/>
                    <input type="hidden" name="rid" value="{{ $request['rid'] }}"/>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <input type="number" placeholder="ID" class="form-control" name="search[id]"
                                   value="{{$request['search']['id']}}"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <input type="text" class="form-control" name="search[user_id]"
                                   value="{{$request['search']['user_id']}}" placeholder="用户id"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <input type="text" class="form-control" name="search[nickname]"
                                   value="{{$request['search']['nickname']}}" placeholder="用户昵称"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class='panel panel-default'>
            <div class="clearfix panel-heading">
                <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:history.go(-1);">返回</a>

            </div>
            <div class='panel-body'>
                <table class="table table-hover" style="overflow:visible;">
                    <thead>
                    <tr>
                        <th style='width:5%;'>ID</th>
                        <th style='width:8%;'>id/头像/昵称</th>
                        <th style='width:11%;'>打卡次数</th>
                        <th style='width:15%;'>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($replay_list as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td style="overflow:visible;">
                                {{ $row['user_id'] }}
                                <div class="show-cover-img-big" style="position:relative;width:50px;overflow:visible">
                                    <img src="{!! $row['avatar'] !!}" alt=""
                                         style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                    <img class="img-big" src="{!! $row['avatar'] !!}" alt=""
                                         style="z-index:99999;position:absolute;top:0;left:0;border:1px solid #ccc;padding:1px;display: none">
                                </div>
                                {{ $row['nickname'] }}
                            </td>
                            <td>{{ $row['clock_num'] }}</td>
                            <td style="overflow:visible;">
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('member.member.detail', ['id' => $row['user_id']])}}"
                                   title='详情资料'><i class='fa fa-edit'></i>详情资料
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {!! $pager !!}
            </div>
        </div>
    @endif

    @if($room_type=='2')
        <div class="panel panel-info">
            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form2">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="{{ $request['do'] }}"/>
                    <input type="hidden" name="route" value="plugin.xiaoe-clock.admin.clock.clock_users_list"/>
                    <input type="hidden" name="rid" value="{{ $request['rid'] }}"/>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <input type="number" placeholder="ID" class="form-control" name="search[id]"
                                   value="{{$request['search']['id']}}"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <input type="text" class="form-control" name="search[user_id]"
                                   value="{{$request['search']['user_id']}}" placeholder="用户id"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <input type="text" class="form-control" name="search[nickname]"
                                   value="{{$request['search']['nickname']}}" placeholder="用户昵称"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class='panel panel-default'>
            <div class="clearfix panel-heading">
                <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:history.go(-1);">返回</a>

            </div>
            <div class='panel-body'>
                <table class="table table-hover" style="overflow:visible;">
                    <thead>
                    <tr>
                        <th style='width:5%;'>ID</th>
                        <th style='width:8%;'>id/头像/昵称</th>
                        <th style='width:11%;'>打卡次数</th>
                        <th style='width:15%;'>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($replay_list as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td>
                                {{ $row['user_id'] }}
                                <div class="show-cover-img-big" style="position:relative;width:50px;overflow:visible">
                                    <img src="{!! $row['avatar'] !!}" alt=""
                                         style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                    <img class="img-big" src="{!! $row['avatar'] !!}" alt=""
                                         style="z-index:99999;position:absolute;top:0;left:0;border:1px solid #ccc;padding:1px;display: none">
                                </div>
                                {{ $row['nickname'] }}
                            </td>
                            <td>{{ $row['clock_num'] }}</td>

                            <td style="overflow:visible;">
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('member.member.detail', ['id' => $row['user_id']])}}"
                                   title='详情资料'><i class='fa fa-edit'></i>详情资料
                                </a>
{{--                                <a class='btn btn-default'--}}
{{--                                   href="{{yzWebUrl('plugin.xiaoe-clock.admin.clock.clock_task_edit', ['id' => $row['id']])}}"--}}
{{--                                   title='编辑'><i class='fa fa-edit'></i>移入黑名单--}}
{{--                                </a>--}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {!! $pager !!}
            </div>
        </div>
    @endif

    <div style="width:100%;height:150px;"></div>
    <script type="text/javascript">
        // 查看商品封面大图
        $('.show-cover-img-big').on('mouseover', function () {
            $(this).find('.img-big').show();
        });
        $('.show-cover-img-big').on('mouseout', function () {
            $(this).find('.img-big').hide();
        });
    </script>
@endsection