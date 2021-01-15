@extends('layouts.base')
@section('title', '直播间列表')
@section('content')
    <div class="rightlist">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">直播间列表</a></li>
            </ul>
        </div>

        <div class="panel panel-info">
            <div class="panel-body">
                <form action=" " method="post" class="form-horizontal" role="form" >
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>

                    <div class="form-group">
                        <div class="col-sm-2 col-lg-3 col-xs-12">
                            <input class="form-control" name="search[id]" type="text" value="{{ $search['id'] or ''}}" placeholder="直播间ID">
                        </div>
                        <div class="col-sm-2 col-lg-3 col-xs-12">
                            <input class="form-control" name="search[name]" type="text" value="{{ $search['name'] or ''}}" placeholder="直播间名称">
                        </div>
                        <div class="col-sm-2 col-lg-3 col-xs-12">
                            <input class="form-control" name="search[anchor_name]" type="text" value="{{ $search['anchor_name'] or ''}}" placeholder="主播名称">
                        </div>
                        <div class="col-xs-2 col-sm-2 col-lg-3 search-btn">
                            <div class="btn-input">
                                <input type="submit" class="btn btn-block btn-success" value="搜索">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">总数：{{ $roomList->total() }}&nbsp;&nbsp;&nbsp;&nbsp;<a class='btn btn-info' href="{{ yzWebUrl('live.live-room.edit') }}" style="margin-bottom: 2px">添加直播间</a>&nbsp;&nbsp;&nbsp;&nbsp;</div>
            <div class="panel-body ">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style='width:3%; text-align: center;'>ID</th>
                        <th style='width:3%; text-align: center;'>排序</th>
                        <th style='width:5%; text-align: center;'>主播信息</th>
                        <th style='width:5%; text-align: center;'>直播间名称</th>
                        <th style='width:8%; text-align: center;'>直播间封面图</th>
                        <th style='width:18%; text-align: center;'>推流URL</th>
                        <th style='width:15%; text-align: center;'>拉流URL</th>
                        <th style='width:8%; text-align: center;'>直播开始-结束时间</th>
                        <th style='width:3%; text-align: center;'>直播状态</th>
                        <th style='width:3%; text-align: center;'>虚拟人数</th>
                        <th style='width:3%; text-align: center;'>虚拟倍数</th>
                        <th style='width:8%; text-align: center;'>添加时间</th>
                        <th style='width:10%; text-align: center;'>操作</th>
                    </tr>
                    </thead>
                    @foreach($roomList as $list)
                        <tr style="text-align: center;">
                            <td>{{ $list->id }}</td>
                            <td>{{ $list->sort }}</td>
                            <td>
                                <a href='{{yz_tomedia($list->header_img)}}' target='_blank'><img src="{{yz_tomedia($list->header_img)}}" style='width:50px; height:50px; border:1px solid #ccc;padding:1px' /></a><br>
                                {{ $list->anchor_name }}
                            </td>
                            <td>{{ $list->name }}</td>
                            <td>
                                <a href='{{yz_tomedia($list->picture)}}' target='_blank'><img src="{{yz_tomedia($list->cover_img)}}" style='width:100px; height:100px; border:1px solid #ccc;padding:1px' /></a>
                            </td>
                            {{--<td>
                                <a href='{{yz_tomedia($list->picture)}}' target='_blank'><img src="{{yz_tomedia($list->share_img)}}" style='width:100px;border:1px solid #ccc;padding:1px' /></a>
                            </td>--}}
                            <td title="{{ $list->push_url }}">
                                {{ $list->push_url }}<br>
                               {{-- <a style="margin-bottom: 2px" href="javascript:;" data-url="{{ $list->push_url }}" class="btn btn-default copy_push">复制推流</a>--}}
                                <h6><a href="javascript:;" data-clipboard-text="{!! $list->push_url !!}" data-url="{!! $list->push_url !!}" class="js-clip" title="复制推流地址">复制推流地址</a></h6>
                            </td>
                            <td title="{{ $list->pull_url }}">
                                {{ $list->pull_url }}
                                <h6><a href="javascript:;" data-clipboard-text="{!! $list->pull_url !!}" data-url="{!! $list->pull_url !!}" class="js-clip" title="复制拉流地址">复制拉流地址</a></h6>
                            </td>
                            <td title="开始时间：{{ $list->start_time }} .<br>.结束时间：{{ $list->end_time }}">
                                <span class="label label-info">{{ $list->start_time }}</span><br>
                                <span>↓</span><br>
                                <span class="label label-info">{{ $list->end_time }}</span>
                            </td>
                            <td>
                                @if( $list->live_status == 101 ) <label class="label label-success">{{ $list->status_parse }}</label>
                                @elseif($list->live_status == 0)<label class="label label-default">{{ $list->status_parse }}</label>
                                @else <label class="label label-warning">{{ $list->status_parse }}</label>
                                @endif</td>
                            <td>{{ $list->virtual_people }}</td>
                            <td>{{ $list->virtual_num }}</td>
                            <td>{{ $list->created_at }}</td>
                            <td>
                                @if( $list->live_status == 101 ) <a class='btn btn-default' href="{{ yzWebUrl('live.live-room.stop', array('id' => $list->id)) }}" style="margin-bottom: 2px">结束直播</a>                                          @elseif($list->end_time > date('Y-m-d H:i:s')) <a class='btn btn-default' href="{{ yzWebUrl('live.live-room.start', array('id' => $list->id)) }}" style="margin-bottom: 2px">开始直播</a>
                                @endif
                                <a class='btn btn-default' href="{{ yzWebUrl('live.live-room.edit', array('id' => $list->id)) }}" style="margin-bottom: 2px" title="编辑"><i class="fa fa-edit"></i></a>

                            </td>
                        </tr>
                    @endforeach
                </table>
                {!! $page !!}
            </div>
        </div>
    </div>

@endsection