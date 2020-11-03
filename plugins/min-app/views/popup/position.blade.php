@extends('layouts.base')
@section('title', '弹窗位置列表')
@section('content')
    <div class="rightlist">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">弹窗位置列表</a></li>
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
                            <input class="form-control" name="search[id]" type="text" value="{{ $search['id'] or ''}}" placeholder="弹窗位置ID">
                        </div>
                        <div class="col-sm-2 col-lg-3 col-xs-12">
                            <input class="form-control" name="search[name]" type="text" value="{{ $search['name'] or ''}}" placeholder="弹窗名称">
                        </div>
                        <div class='col-sm-2 col-lg-3 col-xs-12'>
                            <select name="search[account_id]" class="form-control">
                                <option value="" selected>请选择小程序</option>
                                @foreach($weappAccount as $key => $value)
                                    <option value="{{ $key }}" @if($search['account_id'] == $key) selected @endif>{{ $value }}</option>
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
            <div class="panel-heading">总数：{{ $positionList->total() }}&nbsp;&nbsp;&nbsp;&nbsp;<a class='btn btn-info' href="{{ yzWebUrl('plugin.min-app.Backend.Controllers.popup.position-edit') }}" style="margin-bottom: 2px">添加弹窗位置</a></div>
            <div class="panel-body ">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style='width:15%; text-align: center;'>ID</th>
                        <th style='width:10%; text-align: center;'>小程序名称</th>
                        <th style='width:10%; text-align: center;'>位置名称</th>
                        <th style='width:10%; text-align: center;'>位置类型</th>
                        <th style='width:10%; text-align: center;'>是否显示</th>
                        <th style='width:12%; text-align: center;'>添加时间</th>
                        <th style='width:10%; text-align: center;'>操作</th>
                    </tr>
                    </thead>
                    @foreach($positionList as $list)
                        <tr style="text-align: center;">
                            <td>{{ $list->id }}</td>
                            <td>{{ $weappAccount[$list->weapp_account_id] }}</td>
                            <td>{{ $list->position_name }}</td>
                            <td>{{ $typeList[$list->type] }}</td>
                            <td>@if($list->is_show)<label class="label label-info">是</label> @else <label class="label label-warning">否</label>@endif</td>
                            <td><span class='label label-default'>{{ $list->created_at }}</span></td>
                            <td>
                                <a class='btn btn-default' href="{{ yzWebUrl('plugin.min-app.Backend.Controllers.popup.position-edit', array('id' => $list->id)) }}" style="margin-bottom: 2px">编辑</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                {!! $page !!}
            </div>
        </div>
    </div>


@endsection