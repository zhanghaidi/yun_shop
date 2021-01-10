@extends('layouts.base')

@section('content')
    <div id="member-blade" class="rightlist">
        <div class="right-titpos">
            @include('layouts.tabs')
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-heading">活码列表</div>

            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="plugin" id="form_do"/>
                    <input type="hidden" name="route" value="plugin.activity-qrcode.admin.activity.index" id="route"/>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <input type="text" class="form-control" name="search[name]"
                                   value="{{$search['name']}}" placeholder="活码名称关键字"/>
                        </div>
                    </div>

                    {{--<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">ID</label>-->
                        <div class="">
                            <input type="text" placeholder="搜索类型" class="form-control" name="search[type]"
                                   value="{{$search['type']}}"/>
                        </div>
                    </div>--}}

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


                        </div>
                    </div>

                </form>

            </div>


        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <a class='btn btn-info' href="{{ yzWebUrl('plugin.activity-qrcode.admin.activity.add') }}" style="margin-bottom: 2px">添加活码</a>&nbsp;&nbsp;&nbsp;&nbsp;

                <div class="panel-heading">记录总数：{{ $pageList->total() }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:6%; text-align: center;'>创建时间</th>
                            <th style='width:6%; text-align: center;'>活码名称</th>
                            <th style='width:6%; text-align: center;'>活码标题</th>
                            <th style='width:6%; text-align: center;'>活码类型</th>
                            <th style='width:12%; text-align: center;'>今日扫码人数</th>
                            <th style='width:12%; text-align: center;'>累计扫码人数</th>
                            <th style='width:12%; text-align: center;'>二维码状态</th>
                            <th style='width:12%; text-align: center;'>活码</th>
                            <th style='width:12%; text-align: center;'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr>
                                <td style="text-align: center;">{{ $list->created_at }}</td>

                                <td style="text-align: center;">
                                    {{ $list->activity_name }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $list->title }}
                                </td>

                                <td style="text-align: center;">
                                    @if($list->switch_type == 1) 平均切换
                                    @else 群满切换
                                    @endif
                                </td>

                                <td style="text-align: center;">


                                </td>
                                <td style="text-align: center;">
                                    {{$list->has_many_qrcode_count}}<br>
                                    {{$list->timeout}}

                                </td>
                                <td style="text-align: center;">
                                    总数量：<span style="color: green">{{$list->has_many_qrcode_count}}</span><br>
                                    已满：<span style="color:orange">{{$list->timeout}}</span><br>
                                    到期：<span style="color: red">{{$list->timeout}}</span>
                                </td>
                                <td style="text-align: center;">
                                    {{$list->qrcode}}
                                </td>

                                <td style="text-align: center;">
                                    <a class='btn btn-default' href="{{ yzWebUrl('plugin.activity-qrcode.admin.qrcode.index', array('id' => $list->id)) }}" style="margin-bottom: 2px">二维码管理</a>
                                    <a class='btn btn-default nav-edit' href="{{ yzWebUrl('plugin.activity-qrcode.admin.activity.edit', array('id' => $list->id)) }}"><i class="fa fa-edit"></i></a>
                                    <a class='btn btn-default nav-del' href="{{ yzWebUrl('plugin.activity-qrcode.admin.activity.deleted', array('id' => $list->id)) }}" onclick="return confirm('确认删除此活码？');return false;"><i class="fa fa-trash-o"></i></a>
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
