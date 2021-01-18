@extends('layouts.base')
@section('title','购物车挂件')

@section('content')
    <div id="member-blade" class="rightlist">
        <div class="right-titpos">
            @include('layouts.tabs')
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            {{-- <div class="panel-heading">聊天记录筛选</div>

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
                             --}}{{--<button type="button" name="export" value="1" id="export" class="btn btn-default">导出
                                 Excel
                             </button>--}}{{--

                         </div>
                     </div>

                 </form>

             </div>
 --}}

        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading"><a class='btn btn-info' href="{{ yzWebUrl('live.live-room.cart-add') }}" style="margin-bottom: 2px">添加购物车挂件</a> &nbsp;&nbsp;&nbsp;&nbsp; 记录总数：{{ $pageList->total() }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='text-align: center;'>ID</th>
                            <th style='text-align: center;'>排序</th>
                            <th style='text-align: center;'>标题</th>
                            <th style='text-align: center;'>简介</th>
                            <th style='text-align: center;'>封面</th>
                            <th style='text-align: center;'>路径</th>
                            <th style='text-align: center;'>创建时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr>
                                <td style="text-align: center;">{{ $list->id }}</td>
                                <td style="text-align: center;"><input type='text' value="{{ $list->sort }}" name="sort[{$item['id']}]" class="form-control " style="width: 80px"/></td>
                                <td style="text-align: center;">{{$list->title}}</td>
                                <td style="text-align: center;">{{$list->description}}</td>
                                <td style="text-align: center;">
                                    <a href="yz_tomedia({{$list->thumb}})" target="_blank">
                                        <img src='yz_tomedia({{$list->thumb}})' style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/>
                                    </a>
                                </td>
                                <td style="text-align: center;white-space: normal;word-break: break-all;">{{$list->page_path}}</td>
                                <td style="text-align: center;">{{$list->created_at}}</td>
                                <td>
                                    <a class='btn btn-default' href="{{ yzWebUrl('live.live-room.cart-edit', array('id' => $list->id, 'room_id' => $list->room_id)) }}" style="margin-bottom: 2px" title="编辑"><i class="fa fa-edit"></i></a>
                                    <a class='btn btn-danger'
                                       href="{{yzWebUrl('live.live-room.cart-del', ['id' => $list->id, 'room_id' => $list->room_id])}}"
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
