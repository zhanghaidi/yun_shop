@extends('layouts.base')

@section('content')
@section('title', trans('素材管理'))

<div class="top">
    <ul class="add-shopnav" id="myTab">
        <!-- <li class="active"><a href="#shop">商家素材</a></li> -->
        <!-- <li class="" ><a href="#tab_share">推客素材</a></li> -->
    </ul>
</div>
<div class="panel panel-info">
    <div class="panel-heading">筛选</div>
    <div class="panel-body">
        <form action="" method="post" class="form-horizontal" role="form">
            <input type="hidden" name="c" value="site"/>
            <input type="hidden" name="a" value="entry"/>
            <input type="hidden" name="m" value="yun_shop"/>
            <input type="hidden" name="do" value="material" id="form_do"/>
            <input type="hidden" name="route" value="plugin.material-center.admin.material.index" id="route" />
            <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                <div class="col-sm-8 col-xs-12">
                    <select class="form-control" name="search[is_show]" id="is_show">
                        <option value="">请选择显示状态</option>
                        <option value="1" @if($search['is_show'] == 1) selected="selected" @endif>显示</option>
                        <option value="2" @if($search['is_show'] == 2) selected="selected" @endif>不显示</option>
                    </select>
                </div>
            </div>

            <div class="form-group col-xs-12 col-sm-8 col-lg-2">

                <div class="">
                    <input class="form-control" placeholder="输入关键字" name="search[keyword]" id="keyword"
                           type="text" @if($search['keyword']) value="{{$search['keyword']}}" @endif ／>
                </div>
            </div>
            <div class='form-group col-xs-12 col-sm-4 col-md-4 col-lg-4'>
                <div class="input-group">
                    <span class="input-group-addon">
                        <input type="checkbox" name="search[is_time]" value="1" @if($search['is_time'] == '1')checked="checked"@endif>
                        &nbsp;&nbsp;生成时间
                    </span>
                        {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time_range]', [
                            'starttime'=>$search['time']['start'] ? $search['time']['start'] : date('Y-m-d H:i:s',strtotime('-7 day')),
                            'endtime'=>$search['time']['end'] ? $search['time']['end'] : date('Y-m-d H:i:s'),
                            'start'=>0,
                            'end'=>0
                        ], true)!!}
                    </div>
                </div>
            

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
                <div class="col-sm-8 col-lg-9 col-xs-12">
                    <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">素材列表</div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th style="width:120px;">ID</th>
                    <th style="width: 30%;">标题</th>
                    <th style="width: 40%;">商品名称</th>
                    <!-- <th style="width: 15%;">下载人数</th> -->
                    <th style="width: 15%;">图片分享数</th>
                    <!-- <th style="width: 15%;">收藏人数</th> -->
                    <th style="width: 17%;">创建时间</th>
                    <th style="width: 15%;">显示状态</th>
                    <th style="width: 17%;">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $row)
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{$row->title}}</td>
                        <td>{{$row->goods->title}}</td>
                        <!-- <td>{{$row->download}}</td> -->
                        <td>{{$row->share}}</td>
                        <!-- <td>{{$row->collect}}</td> -->
                        <td>{{$row->created_at}}</td>
                        <td>
                            @if($row->is_show == 1)
                                <a href="{{yzWebUrl('plugin.material-center.admin.material.changeStatus', ['id' => $row->id])}}" class='label label-success'>显示</a>
                            @else
                                <a href="{{yzWebUrl('plugin.material-center.admin.material.changeStatus', ['id' => $row->id])}}" class='label label-default'>不显示</a>
                            @endif
                        </td>
                        <td style="text-align:left;">
                            <a href="{{yzWebUrl('plugin.material-center.admin.material.edit', ['id' => $row->id])}}" class="btn btn-default btn-sm" title="编辑"><i class="fa fa-edit"></i></a>

                            <a href="{{yzWebUrl('plugin.material-center.admin.material.delete', ['id' => $row->id])}}" class="btn btn-default btn-sm" onclick="return confirm('确认删除此素材?')" title="删除"><i class="fa fa-times"></i></a>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $pager !!}
            <script>
                require(['bootstrap'], function($) {
                    $('.btn').hover(function() {
                        $(this).tooltip('show');
                    }, function() {
                        $(this).tooltip('hide');
                    });
                });
            </script>
        </div>
        <div class="panel-footer">
            <a class='btn btn-default' href="{{yzWebUrl('plugin.material-center.admin.material.add', [])}}"><i class='fa fa-plus'></i>添加素材</a>
        </div>
    </div>
</div>
@endsection