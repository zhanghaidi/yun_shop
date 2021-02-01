@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li ><a href="{{yzWebUrl('plugin.minapp-content.admin.banner.index')}}">轮播图列表</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.banner-position.index')}}">轮播位</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.system-category.index')}}">首页功能区分类</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.system-image.index')}}">系统图片</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.hot-spot.index')}}">首页热区</a></li>
                </ul>
            </div>

            <form id="form1" role="form" class="form-horizontal form" method="post" action="">

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[datelimit]', [
                            'starttime'=>array_get($search['datelimit'],'start',0),
                            'endtime'=>array_get($search['datelimit'],'end',0),
                            'start'=>0,
                            'end'=>0,
                            ], true) !!}
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <input type="text" placeholder="请输入名称搜索" value="{{$search['keywords']}}" name="search[keywords]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.minapp-content.admin.hot-spot.edit') }}" class="btn btn-info">添加热区</a>
            </div>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th width="100">ID</th>
                        <th width="100">排序</th>
                        {{--<th>热区标题</th>
                        <th>显示样式</th>--}}
                        <th>是否显示</th>
                        <th>添加时间</th>
                        <th>图片数量(显示状态)</th>
                        <th>图片</th>
                        <th class="text-right">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['list_order']}}</td>
                        {{-- <td>{{$value['title']}}</td>
                       <td>

                                @if($value['type'] == 1)
                                    <span class="label label-primary">横版</span>
                                @else
                                    <span class="label label-success">竖版</span>
                                @endif

                        </td>--}}
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.hot-spot.status', ['id' => $value['id']]) }}">
                            @if($value['status'] == 1)
                                <span class="label label-primary">显示</span>
                            @else
                                <span class="label label-default">隐藏</span>
                            @endif
                            </a>
                        </td>
                        <td>{{$value['create_time']}}</td>
                        <td><a href="{{ yzWebUrl('plugin.minapp-content.admin.hot-spot-image.index', ['hotSpotId' => $value['id']]) }}">{{$value['image_count']}}</a></td>
                        <td>
                            @foreach($value['image'] as $image)
                                <a href="{{tomedia($image['image'])}}" target="_blank"><img src="{{tomedia($image['image'])}}" width="{{120/$value['image_count']}}"></a>
                            @endforeach

                        </td>
                        <td class="text-right">
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.hot-spot-image.index', ['hotSpotId' => $value['id']]) }}" title="添加热区图片"><i class="fa fa-image"></i></a> &nbsp;
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.hot-spot.edit', ['id' => $value['id']]) }}" title="编辑"><i class="fa fa-edit"></i></a> &nbsp;
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.hot-spot.delete', ['id' => $value['id']]) }}" onclick="return confirm('确定删除吗');return false;"  title="删除"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {!! $pager !!}
    </div>
</div>

@endsection

