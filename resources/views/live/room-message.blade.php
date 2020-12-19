@extends('layouts.base')
@section('title','直播间聊天记录')

@section('content')
    <div id="member-blade" class="rightlist">
        <div class="right-titpos">
            @include('layouts.tabs')
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-heading">聊天记录筛选</div>

            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form1">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="live" id="form_do"/>
                    <input type="hidden" name="route" value="live.live-room.room-message" id="route"/>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>-->
                        <div class="">
                            <input type="text" class="form-control" name="search[keywords]"
                                   value="{{$search['keywords']}}" placeholder="可搜索会员ID/房间名称"/>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">

                        <div class="time">
                            <select name='search[search_time]' class='form-control'>
                                <option value='0' @if($search['search_time']=='0') selected @endif>不搜索时间</option>
                                <option value='1' @if($search['search_time']=='1') selected @endif>搜索时间</option>
                            </select>
                            <div class="search-select">
                                {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                                'starttime'=>date('Y-m-d H:i', strtotime($search['time']['start']) ?: strtotime('-1 month')),
                                'endtime'=>date('Y-m-d H:i',strtotime($search['time']['end']) ?: time()),
                                'start'=>0,
                                'end'=>0
                                ], true) !!}
                            </div>
                        </div>

                    </div>
                    <div class="form-group  col-xs-12 col-md-12 col-lg-6">
                        <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
                        <div class="">
                            <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>
                            {{--<button type="button" name="export" value="1" id="export" class="btn btn-default">导出
                                Excel
                            </button>--}}

                        </div>
                    </div>

                </form>

            </div>


        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">记录总数：{{ $pageList->total() }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:6%; text-align: center;'>消息ID</th>
                            <th style='width:6%; text-align: center;'>群组ID</th>
                            <th style='width:6%; text-align: center;'>发送用户</th>
                            <th style='width:6%; text-align: center;'>消息内容</th>
                            <th style='width:12%; text-align: center;'>发送时间</th>
                            <th style='width:12%; text-align: center;'>客户端IP</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr>
                                <td style="text-align: center;">{{ $list->id }}</td>
                                <td style="text-align: center;">{{$list->group_id}}</td>
                                <td style="text-align: center;">
                                    <a href="{{yzWebUrl('live.live-room.room-message',array('search[keywords]' => $list->user_id ))}}">
                                        <img src='{{$list->user->avatarurl}}'
                                             style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/><br/>
                                        {{ $list->user->nickname }}
                                    </a>
                                </td>
                                <td style="text-align: center;white-space: normal;word-break: break-all;">{{$list->msg_content->text}}</td>
                                <td style="text-align: center;">{{$list->created_at}}</td>
                                <td style="text-align: center;">{{$list->client_iP}}</td>
                                <td>
                                    <a class='btn btn-danger'
                                       href="{{yzWebUrl('live.live-room.room-message-del', ['id' => $list->id])}}"
                                       onclick="return confirm('确认删除此记录吗？');return false;"><i class="fa fa-remove"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $page !!}

                </div>
            </div>
        </div>


@endsection('content')
