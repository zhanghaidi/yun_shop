@extends('layouts.base')
@section('title', trans('日记列表'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            @if($room_type=='1')
                <li class="active"><a href="#">日历打卡日记列表</a></li>
            @endif
            @if($room_type=='2')
                <li class="active"><a href="#">作业打卡日记列表</a></li>
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
                    <input type="hidden" name="route" value="plugin.xiaoe-clock.admin.clock.users_clock_list"/>
                    <input type="hidden" name="rid" value="{{ $request['rid'] }}"/>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-1">
                            <input type="number" placeholder="日历打卡ID" class="form-control" name="search[id]"
                                   value="{{$request['search']['id']}}"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                            <input type="text" class="form-control" name="search[text_desc]"
                                   value="{{$request['search']['text_desc']}}" placeholder="日历打卡内容"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                            <input type="text" class="form-control" name="search[user_id]"
                                   value="{{$request['search']['user_id']}}" placeholder="日历打卡用户id"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                            <input type="text" class="form-control" name="search[nickname]"
                                   value="{{$request['search']['nickname']}}" placeholder="日历打卡用户昵称"/>
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
                        <th style='width:11%;'>打卡时间</th>
                        <th style='width:11%;'>打卡日记</th>
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
                                    <img src="{!! tomedia($row['avatar']) !!}" alt=""
                                         style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                    <img class="img-big" src="{!! tomedia($row['avatar']) !!}" alt=""
                                         style="z-index:99999;position:absolute;top:0;left:0;border:1px solid #ccc;padding:1px;display: none">
                                </div>
                                {{ $row['nickname'] }}
                            </td>
                            <td>{{ date('Y-m-d H:i:s', $row['created_at']) }}</td>
                            <td>
                                {{ $row['text_desc'] }}
                            </td>
                            <td style="overflow:visible;">
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.xiaoe-clock.admin.clock.users_clock_detail', ['id' => $row['id']])}}"
                                   title='详情'><i class='fa fa-edit'></i>详情
                                </a>
                                <a class='btn btn-default btn-delete'
                                   href="{{yzWebUrl('plugin.xiaoe-clock.admin.clock.users_clock_del', ['id' => $row['id']])}}"
                                   title='删除'>删除
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
                    <input type="hidden" name="route" value="plugin.xiaoe-clock.admin.clock.users_clock_list"/>
                    <input type="hidden" name="rid" value="{{ $request['rid'] }}"/>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-1">
                            <input type="number" placeholder="作业打卡ID" class="form-control" name="search[id]"
                                   value="{{$request['search']['id']}}"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                            <input type="text" class="form-control" name="search[text_desc]"
                                   value="{{$request['search']['text_desc']}}" placeholder="作业打卡内容"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                            <input type="text" class="form-control" name="search[user_id]"
                                   value="{{$request['search']['user_id']}}" placeholder="作业打卡用户id"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                            <input type="text" class="form-control" name="search[nickname]"
                                   value="{{$request['search']['nickname']}}" placeholder="作业打卡用户昵称"/>
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
                        <th style='width:11%;'>打卡时间</th>
                        <th style='width:11%;'>打卡日记</th>
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
                                    <img src="{!! tomedia($row['avatar']) !!}" alt=""
                                         style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                    <img class="img-big" src="{!! tomedia($row['avatar']) !!}" alt=""
                                         style="z-index:99999;position:absolute;top:0;left:0;border:1px solid #ccc;padding:1px;display: none">
                                </div>
                                {{ $row['nickname'] }}
                            </td>
                            <td>{{ date('Y-m-d H:i:s', $row['created_at']) }}</td>
                            <td>
                                {{ $row['text_desc'] }}
                            </td>
                            <td style="overflow:visible;">
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.xiaoe-clock.admin.clock.users_clock_detail', ['id' => $row['id']])}}"
                                   title='详情'><i class='fa fa-edit'></i>详情
                                </a>
                                <a class='btn btn-default btn-delete'
                                   href="{{yzWebUrl('plugin.xiaoe-clock.admin.clock.users_clock_del', ['id' => $row['id']])}}"
                                   title='删除'>删除
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