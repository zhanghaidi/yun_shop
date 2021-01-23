@extends('layouts.base')

@section('content')
    <div id="member-blade" class="rightlist">
        <div class="right-titpos">
            @include('layouts.tabs')
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-heading">识别记录</div>

           {{-- <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="plugin" id="form_do"/>
                    <input type="hidden" name="route" value="plugin.activity-qrcode.admin.activity.userList" id="route"/>


                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <select name='search[status]' class='form-control'>
                                <option value=''>操作动作不限</option>
                                <option value='1' @if($search['status']=='1')selected @endif>正常</option>
                                <option value='2' @if($search['status']=='2')selected @endif>过期</option>
                                <option value='3' @if($search['status']=='3')selected @endif>已满</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group  col-xs-12 col-md-12 col-lg-6">

                        <div class="">
                            <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>

                        </div>
                    </div>

                </form>

            </div>--}}

        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                {{--<a class='btn btn-info' href="{{ yzWebUrl('plugin.activity-qrcode.admin.qrcode.add', array('id'=> $activityId)) }}" style="margin-bottom: 2px"> 上传二维码 </a>&nbsp;--}}&nbsp;&nbsp;&nbsp;

                <div class="panel-heading">记录总数：{{ $pageList->total() }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:6%; text-align: center;'>ID</th>
                            <th style='width:6%; text-align: center;'>时间</th>
                            <th style='width:6%; text-align: center;'>IP</th>
                            <th style='width:6%; text-align: center;'>OS</th>
                            <th style='width:6%; text-align: center;'>container</th>
                            <th style='width:6%; text-align: center;'>入群名称</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr>
                                <td style="text-align: center;">{{ $list->id }}</td>

                                <td style="text-align: center;">
                                    {{ $list->created_at }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $list->ip }}
                                </td>

                                <td style="text-align: center;">
                                    {{ $list->os }}
                                </td>

                                <td style="text-align: center;">
                                    {{ $list->container }}
                                </td>

                                <td style="text-align: center;">
                                    {{$list->belongsToQrcode->name}}
                                </td>

                                {{--<td style="text-align: center;">
                                    <a class='btn btn-default nav-edit' href="{{ yzWebUrl('plugin.activity-qrcode.admin.qrcode.edit', array('id' => $activityId,'qrcode_id' => $list->id)) }}"><i class="fa fa-edit"></i></a>
                                    <a class='btn btn-default nav-del' href="{{ yzWebUrl('plugin.activity-qrcode.admin.qrcode.deleted', array('id' => $activityId,'qrcode_id' => $list->id)) }}" onclick="return confirm('确认删除此二维码？');return false;"><i class="fa fa-trash-o"></i></a>
                                </td>--}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $page !!}

                </div>
            </div>
        </div>


@endsection('content')
