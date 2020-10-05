@extends('layouts.base')
@section('title', trans('课程管理'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">课程管理</a></li>
        </ul>
    </div>

    <div class="panel panel-info">
        <ul class="add-shopnav">
            <li @if($type=='1') class="active" @endif>
                <a href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.index', ['type' => 1])}}">录播</a>
            </li>
            <li @if($type=='2') class="active" @endif>
                <a href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.index', ['type' => 2])}}">品牌特卖</a>
            </li>
        </ul>
    </div>

    @if($type=='1')
        <div class="panel panel-info">
            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form2">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="{{ $request['do'] }}"/>
                    <input type="hidden" name="route" value="plugin.appletslive.admin.controllers.room.index"/>
                    <input type="hidden" name="type" value="1"/>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <input type="number" placeholder="课程ID" class="form-control" name="search[id]"
                                   value="{{$request['search']['id']}}"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <input type="text" class="form-control" name="search[name]"
                                   value="{{$request['search']['name']}}" placeholder="课程标题"/>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <select name="search[status]" class="form-control">
                                <option value="">请选择显示/隐藏</option>
                                <option value="1" @if($request['search']['status']=='1') selected @endif>显示</option>
                                <option value='0' @if($request['search']['status']=='0') selected @endif>隐藏</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class='panel panel-default'>
            <div class='panel-body'>
                <div class="clearfix panel-heading">
                    <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.add', ['type'=>1])}}">添加录播课程</a>
                </div>

                <table class="table table-hover" style="overflow:visible;">
                    <thead>
                    <tr>
                        <th style='width:10%;'>ID</th>
                        <th style='width:15%;'>排序</th>
                        <th style='width:15%;'>封面</th>
                        <th style='width:25%;'>名称</th>
                        <th style='width:25%;'>状态</th>
                        <th style='width:15%;'>订阅人数</th>
                        <th style='width:15%;'>评论量</th>
                        <th style='width:30%;'>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($room_list as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td>{{ $row['sort'] }}</td>
                            <td style="overflow:visible;">
                                <div class="show-cover-img-big" style="position:relative;width:50px;overflow:visible">
                                    <img src="{!! tomedia($row['cover_img']) !!}" alt=""
                                         style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                    <img class="img-big" src="{!! tomedia($row['cover_img']) !!}" alt=""
                                         style="z-index:99999;position:absolute;top:0;left:0;border:1px solid #ccc;padding:1px;display: none">
                                </div>
                            </td>
                            <td>{{ $row['name'] }}</td>
                            <td>
                                @if ($row['live_status'] == 1)
                                    更新中
                                @elseif ($row['live_status'] == 2)
                                    已完结
                                @else
                                    筹备中
                                @endif
                            </td>
                            <td>{{ $row['subscription_num'] }}</td>
                            <td>
                                <a class='btn btn-default'
                                    href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.commentlist', ['rid' => $row['id']])}}"
                                    title='评论列表'><i class='fa fa-list'></i>评论量（{{ $row['comment_num'] }}）
                                </a>
                            </td>
                            <td style="overflow:visible;">
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.edit', ['id' => $row['id']])}}"
                                   title='课程设置'><i class='fa fa-edit'></i>课程设置
                                </a>
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $row['id']])}}"
                                   title='录播列表'><i class='fa fa-list'></i>录播列表
                                </a>
                                @if ($row['delete_time'] > 0)
                                    <a class='btn btn-default btn-success'
                                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.showhide', ['id' => $row['id']])}}"
                                       title='显示'>显示
                                    </a>
                                @else
                                    <a class='btn btn-default btn-danger'
                                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.showhide', ['id' => $row['id']])}}"
                                       title='隐藏'>隐藏
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {!! $pager !!}
            </div>
        </div>
    @endif

    @if($type=='2')
        <div class="panel panel-info">
            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form2">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="{{ $request['do'] }}"/>
                    <input type="hidden" name="route" value="plugin.appletslive.admin.controllers.room.index"/>
                    <input type="hidden" name="type" value="2"/>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <input type="number" placeholder="特卖专辑ID" class="form-control" name="search[id]"
                                   value="{{$request['search']['id']}}"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <input type="text" class="form-control" name="search[name]"
                                   value="{{$request['search']['name']}}" placeholder="专辑名称"/>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <select name="search[status]" class="form-control">
                                <option value="">请选择显示/隐藏</option>
                                <option value="1" @if($request['search']['status']=='1') selected @endif>显示</option>
                                <option value='0' @if($request['search']['status']=='0') selected @endif>隐藏</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class='panel panel-default'>
            <div class='panel-body'>
                <div class="clearfix panel-heading">
                    <a id="" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.add', ['type'=>2])}}">添加特卖专辑</a>
                </div>

                <table class="table table-hover" style="overflow:visible;">
                    <thead>
                    <tr>
                        <th style='width:10%;'>ID</th>
                        <th style='width:15%;'>排序</th>
                        <th style='width:15%;'>封面</th>
                        <th style='width:25%;'>名称</th>
                        <th style='width:15%;'>订阅人数</th>
                        <th style='width:15%;'>评论量</th>
                        <th style='width:30%;'>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($room_list as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td>{{ $row['sort'] }}</td>
                            <td style="overflow:visible;">
                                <div class="show-cover-img-big" style="position:relative;width:50px;overflow:visible">
                                    <img src="{!! tomedia($row['cover_img']) !!}" alt=""
                                         style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                    <img class="img-big" src="{!! tomedia($row['cover_img']) !!}" alt=""
                                         style="z-index:99999;position:absolute;top:0;left:0;border:1px solid #ccc;padding:1px;display: none">
                                </div>
                            </td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['subscription_num'] }}</td>
                            <td>
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.commentlist', ['rid' => $row['id']])}}"
                                   title='评论列表'><i class='fa fa-list'></i>评论量（{{ $row['comment_num'] }}）
                                </a>
                            </td>
                            <td style="overflow:visible;">
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.edit', ['id' => $row['id']])}}"
                                   title='课程设置'><i class='fa fa-edit'></i>专辑设置
                                </a>
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.replaylist', ['rid' => $row['id']])}}"
                                   title='录播列表'><i class='fa fa-list'></i>直播列表
                                </a>
                                @if ($row['delete_time'] > 0)
                                    <a class='btn btn-default btn-success'
                                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.showhide', ['id' => $row['id']])}}"
                                       title='显示'>显示
                                    </a>
                                @else
                                    <a class='btn btn-default btn-danger'
                                       href="{{yzWebUrl('plugin.appletslive.admin.controllers.room.showhide', ['id' => $row['id']])}}"
                                       title='隐藏'>隐藏
                                    </a>
                                @endif
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
        // 查看课程封面大图
        $('.show-cover-img-big').on('mouseover', function () {
            $(this).find('.img-big').show();
        });
        $('.show-cover-img-big').on('mouseout', function () {
            $(this).find('.img-big').hide();
        });
    </script>
@endsection