@extends('layouts.base')
@section('title', trans('直播间管理'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">直播间管理</a></li>
        </ul>
    </div>

    <div class="panel panel-info">
        <div class="panel-body">
            <form action="" method="get" class="form-horizontal" role="form" id="form1">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="{{ $request['do'] }}"/>
                <input type="hidden" name="route" value="plugin.appletslive.admin.controllers.live.index"/>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <input type="number" placeholder="直播间ID" class="form-control" name="search[roomid]"
                               value="{{$request['search']['roomid']}}"/>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <input type="text" class="form-control" name="search[name]"
                               value="{{$request['search']['name']}}" placeholder="房间名称"/>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <select name="search[live_status]" class="form-control">
                            <option value="">请选择直播状态</option>
                            <option value="{{ APPLETSLIVE_ROOM_LIVESTATUS_101 }}"
                                    @if($request['search']['live_status']==APPLETSLIVE_ROOM_LIVESTATUS_101) selected @endif>
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_101_TEXT }}</option>
                            <option value='{{ APPLETSLIVE_ROOM_LIVESTATUS_102 }}'
                                    @if($request['search']['live_status']==APPLETSLIVE_ROOM_LIVESTATUS_102) selected @endif>
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_102_TEXT }}</option>
                            <option value='{{ APPLETSLIVE_ROOM_LIVESTATUS_103 }}'
                                    @if($request['search']['live_status']==APPLETSLIVE_ROOM_LIVESTATUS_103) selected @endif>
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_103_TEXT }}</option>
                            <option value='{{ APPLETSLIVE_ROOM_LIVESTATUS_104 }}'
                                    @if($request['search']['live_status']==APPLETSLIVE_ROOM_LIVESTATUS_104) selected @endif>
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_104_TEXT }}</option>
                            <option value='{{ APPLETSLIVE_ROOM_LIVESTATUS_105 }}'
                                    @if($request['search']['live_status']==APPLETSLIVE_ROOM_LIVESTATUS_105) selected @endif>
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_105_TEXT }}</option>
                            <option value='{{ APPLETSLIVE_ROOM_LIVESTATUS_106 }}'
                                    @if($request['search']['live_status']==APPLETSLIVE_ROOM_LIVESTATUS_106) selected @endif>
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_106_TEXT }}</option>
                            <option value='{{ APPLETSLIVE_ROOM_LIVESTATUS_107 }}'
                                    @if($request['search']['live_status']==APPLETSLIVE_ROOM_LIVESTATUS_107) selected @endif>
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_107_TEXT }}</option>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <select name="search[searchtime]" class="form-control">
                            <option value="" selected>请选择时间</option>
                            <option value="0" @if($request['search']['searchtime']=='0') selected @endif>直播开始时间</option>
                            <option value='1' @if($request['search']['searchtime']=='1') selected @endif>直播结束时间</option>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="search-select">
                            {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[date]', [
                            'starttime'=>$request['search']?$request['search']['date']['start']:date('Y-m-01',time()),
                            'endtime'=>$request['search']?$request['search']['date']['end']:date('Y-m-t',time()),
                            'start'=>0,'end'=>0
                            ], false) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <button class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class='panel panel-default'>
        <div class='panel-body'>

            <div class="clearfix panel-heading" id="liveRefreshClean">

                <a id="btn-room-refresh" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:;;" @click="refresh" v-if="allowRefresh==1">同步直播间列表</a>
                <a id="btn-room-refresh" class="btn btn-defaultt disabled" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:;;" @click="refresh" v-else disabled>同步直播间列表</a>

                <a id="btn-room-refresh" class="btn btn-defaultt" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:;;" @click="clean" v-if="allowClean==1">清除已失效直播间</a>
                <a id="btn-room-refresh" class="btn btn-defaultt disabled" style="height: 35px;margin-top: 5px;color: white;"
                   href="javascript:;;" @click="clean" v-else disabled>清除已失效直播间</a>

                <a id="" class="btn btn-primary" style="height: 35px;margin-top: 5px;color: white;"
                   href="{{ yzWebUrl('plugin.appletslive.admin.controllers.live.add') }}">新增直播间</a>
            </div>

            <table class="table table-hover" style="overflow:visible;">
                <thead>
                <tr>
                    <th style='width:5%;'>ID</th>
                    <th style='width:5%;'>房间号</th>
                    <th style='width:5%;'>封面</th>
                    <th style='width:15%;'>名称</th>
                    <th style='width:15%;'>开始时间</th>
                    <th style='width:15%;'>结束时间</th>
                    <th style='width:10%;'>直播状态</th>
                    <th style='width:20%;text-align:center;'>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $row)
                    <tr>
                        <td>{{ $row['id'] }}</td>
                        <td>{{ $row['roomid'] }}</td>
                        <td style="overflow:visible;">
                            <div class="show-cover-img-big" style="position:relative;width:50px;overflow:visible">
                                <img src="{!! tomedia($row['cover_img']) !!}" alt=""
                                     style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                <img class="img-big" src="{!! tomedia($row['cover_img']) !!}" alt=""
                                     style="z-index:99999;position:absolute;top:0;left:0;border:1px solid #ccc;padding:1px;display: none">
                            </div>
                        </td>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ date('Y-m-d H:i:s', $row['start_time']) }}</td>
                        <td>{{ date('Y-m-d H:i:s', $row['end_time']) }}</td>
                        <td>
                            @if ($row['live_status'] == APPLETSLIVE_ROOM_LIVESTATUS_101)
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_101_TEXT }}
                            @elseif ($row['live_status'] == APPLETSLIVE_ROOM_LIVESTATUS_102)
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_102_TEXT }}
                            @elseif ($row['live_status'] == APPLETSLIVE_ROOM_LIVESTATUS_103)
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_103_TEXT }}
                            @elseif ($row['live_status'] == APPLETSLIVE_ROOM_LIVESTATUS_104)
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_104_TEXT }}
                            @elseif ($row['live_status'] == APPLETSLIVE_ROOM_LIVESTATUS_105)
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_105_TEXT }}
                            @elseif ($row['live_status'] == APPLETSLIVE_ROOM_LIVESTATUS_106)
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_106_TEXT }}
                            @elseif ($row['live_status'] == APPLETSLIVE_ROOM_LIVESTATUS_107)
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_107_TEXT }}
                            @elseif ($row['live_status'] == APPLETSLIVE_ROOM_LIVESTATUS_108)
                                {{ APPLETSLIVE_ROOM_LIVESTATUS_108_TEXT }}
                            @else
                                未知
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if (in_array($row['live_status'], [APPLETSLIVE_ROOM_LIVESTATUS_101,APPLETSLIVE_ROOM_LIVESTATUS_102,APPLETSLIVE_ROOM_LIVESTATUS_105]))
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('plugin.appletslive.admin.controllers.live.import', ['id' => $row['id']])}}"
                                   title='设置'><i class='fa fa-edit'></i>商品管理
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

    <div id="test-vue" style="width:100%;height:150px;"></div>

    <script>

        // 查看直播间封面大图
        $('.show-cover-img-big').on('mouseover', function () {
            $(this).find('.img-big').show();
        });
        $('.show-cover-img-big').on('mouseout', function () {
            $(this).find('.img-big').hide();
        });

        var app = new Vue({
            el: '#liveRefreshClean',
            data: {
                allowRefresh: 1,
                allowClean: 1
            },
            mounted: function () {
            },
            methods: {
                refresh() {
                    var that = this;
                    that.allowRefresh = 0;
                    this.$http.get("{!! yzWebUrl('plugin.appletslive.admin.controllers.live.index', ['tag'=>'refresh']) !!}")
                        .then(res => {
                            that.allowRefresh = 1;
                            this.$message({
                                type: 'success',
                                duration: 1000,
                                message: res.data.msg,
                                onClose: function () {
                                    location.href = "{!! yzWebUrl('plugin.appletslive.admin.controllers.live.index') !!}";
                                }
                            });
                        });
                },
                clean() {
                    var that = this;
                    that.allowClean = 0;
                    this.$http.get("{!! yzWebUrl('plugin.appletslive.admin.controllers.live.index', ['tag'=>'clean']) !!}")
                        .then(res => {
                            that.allowClean = 1;
                            this.$message({
                                type: 'success',
                                duration: 1000,
                                message: res.data.msg,
                                onClose: function () {
                                    location.href = "{!! yzWebUrl('plugin.appletslive.admin.controllers.live.index') !!}";
                                }
                            });
                        });
                }
            }
        });
    </script>
@endsection