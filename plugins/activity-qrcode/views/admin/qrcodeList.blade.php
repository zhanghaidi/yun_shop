@extends('layouts.base')

@section('content')
    <div id="member-blade" class="rightlist">
        <div class="right-titpos">
            @include('layouts.tabs')
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-heading">二维码列表</div>

            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="plugin" id="form_do"/>
                    <input type="hidden" name="route" value="plugin.activity-qrcode.admin.qrcode.index" id="route"/>


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

            </div>


        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <a class='btn btn-info' href="{{ yzWebUrl('plugin.activity-qrcode.admin.qrcode.add') }}" style="margin-bottom: 2px"> 上传二维码 </a>&nbsp;&nbsp;&nbsp;&nbsp;

                <div class="panel-heading">记录总数：{{ $pageList->total() }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:6%; text-align: center;'>排序</th>
                            <th style='width:6%; text-align: center;'>二维码名称</th>
                            <th style='width:6%; text-align: center;'>缩略图</th>
                            <th style='width:6%; text-align: center;'>自动切换上限</th>
                            <th style='width:12%; text-align: center;'>二维码失效时间</th>
                            <th style='width:12%; text-align: center;'>二维码状态</th>
                            <th style='width:12%; text-align: center;'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr>
                                <td style="text-align: center;">{{ $list->sort }}</td>

                                <td style="text-align: center;">
                                    {{ $list->name }}
                                </td>
                                <td style="text-align: center;">
                                    <a href='{{yz_tomedia($list->qr_img)}}' target='_blank'><img src="{{yz_tomedia($list->qr_img)}}" style='width:100px;border:1px solid #ccc;padding:1px' /></a>
                                </td>

                                <td style="text-align: center;">
                                    <span style="color: green"> 0</span>/{{$list->switch_limit}}
                                </td>

                                <td style="text-align: center;">
                                    {{date('Y-m-d', $list->end_time)}}

                                </td>

                                <td style="text-align: center;">
                                    @if($list->status == 1)<span class="label label-success">{{$list->status}}</span>
                                        @elseif($list->status == 2)<span class="label label-warning">{{$list->status}}</span>
                                        @else <span class="label label-danger">{{$list->status}}</span>
                                    @endif
                                </td>

                                <td style="text-align: center;">
                                    <a class='btn btn-default nav-edit' href="{{ yzWebUrl('plugin.activity-qrcode.admin.qrcode.edit', array('id' => $list->id)) }}"><i class="fa fa-edit"></i></a>
                                    <a class='btn btn-default nav-del' href="{{ yzWebUrl('plugin.activity-qrcode.admin.qrcode.deleted', array('id' => $list->id)) }}" onclick="return confirm('确认删除此二维码？');return false;"><i class="fa fa-trash-o"></i></a>
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
