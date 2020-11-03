@extends('layouts.base')
@section('title', '弹窗列表')
@section('content')
    <div class="rightlist">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">弹窗列表</a></li>
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
                            <input class="form-control" name="search[id]" type="text" value="{{ $search['id'] or ''}}" placeholder="弹窗ID">
                        </div>
                        <div class="col-sm-2 col-lg-3 col-xs-12">
                            <input class="form-control" name="search[title]" type="text" value="{{ $search['title'] or ''}}" placeholder="弹窗名称">
                        </div>
                        <div class='col-sm-2 col-lg-3 col-xs-12'>
                            <select name="search[position_id]" class="form-control">
                                <option value="" selected>请选择弹窗位置</option>
                                @foreach($position as $item)
                                    <option value="{{ $item['id'] }}" @if($search['position_id'] == $item['id']) selected @endif>{{ $item['position_name'] }}</option>
                                @endforeach
                            </select>
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
            <div class="panel-heading">总数：{{ $popupList->total() }}&nbsp;&nbsp;&nbsp;&nbsp;<a class='btn btn-info' href="{{ yzWebUrl('plugin.min-app.Backend.Controllers.popup.edit') }}" style="margin-bottom: 2px">添加弹窗位置</a></div>
            <div class="panel-body ">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style='width:5%; text-align: center;'>ID</th>
                        <th style='width:5%; text-align: center;'>排序</th>
                        <th style='width:10%; text-align: center;'>弹窗位置名称</th>
                        <th style='width:10%; text-align: center;'>弹窗图片</th>
                        <th style='width:20%; text-align: center;'>小程序路径</th>
                        <th style='width:5%; text-align: center;'>展示时间</th>
                        <th style='width:10%; text-align: center;'>弹窗开始时间</th>
                        <th style='width:10%; text-align: center;'>弹窗结束时间</th>
                        <th style='width:10%; text-align: center;'>是否显示</th>
                        <th style='width:12%; text-align: center;'>添加时间</th>
                        <th style='width:10%; text-align: center;'>操作</th>
                    </tr>
                    </thead>
                    @foreach($popupList as $list)
                        <tr style="text-align: center;">
                            <td>{{ $list->id }}</td>
                            <td>{{ $list->sort }}</td>
                            <td>{{ $list->belongsToPosition->position_name }}</td>
                            <td>
                                <a href='{{yz_tomedia($list->picture)}}' target='_blank'><img src="{{yz_tomedia($list->picture)}}" style='width:100px;border:1px solid #ccc;padding:1px' /></a>
                            </td>
                            <td>{{ $list->pagepath }}</td>
                            <td>{{ $list->show_time }}</td>
                            <td>{{ $list->start_time }}</td>
                            <td>{{ $list->end_time }}</td>
                            <td>@if($list->is_show)<label class="label label-info">是</label> @else <label class="label label-warning">否</label>@endif</td>
                            <td><span class='label label-default'>{{ $list->created_at }}</span></td>
                            <td>
                                <a class='btn btn-default' href="{{ yzWebUrl('plugin.min-app.Backend.Controllers.popup.edit', array('id' => $list->id)) }}" style="margin-bottom: 2px">编辑</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                {!! $page !!}
            </div>
        </div>
    </div>


@endsection