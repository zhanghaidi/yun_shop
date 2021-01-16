@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.banner.index')}}">轮播图列表</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.banner-position.index')}}">轮播位</a>
                    </li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.system-category.index')}}">首页功能区分类</a>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.system-image.index')}}">系统图片</a>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.system-notice.index')}}">系统通知</a>
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
                        <input type="text" placeholder="请输入栏目名搜索" value="{{$search['keywords']}}" name="search[keywords]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.minapp-content.admin.system-category.edit') }}" class="btn btn-info">添加栏目</a>
            </div>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th width="100">排序</th>
                        <th width="300">栏目名称</th>
                        <th width="100">是否显示</th>
                        <th>小程序跳转页</th>
                        <th>跳转类型</th>
                        <th>添加时间</th>
                        <th class="text-right">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['list_order']}}</td>
                        <td>
                            @if(isset($value['image'][0]))
                            <a href="{{tomedia($value['image'])}}" target="_blank"><img src="{{tomedia($value['image'])}}" width="50" border="1"></a> &nbsp; &nbsp;
                            @endif
                            {{$value['name']}}
                        </td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.system-category.status', ['id' => $value['id']]) }}">
                            @if($value['status'] == 1)
                                <span class="label label-primary">显示</span>
                            @else
                                <span class="label label-default">隐藏</span>
                            @endif
                            </a>
                        </td>
                        <td>{{$value['jumpurl']}}</td>
                        <td>
                        @if($value['jumptype'] == 1)
                        普通页跳转
                        @else
                        导航跳转
                        @endif
                        </td>
                        <td>{{$value['create_time']}}</td>
                        <td class="text-right">
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.system-category.edit', ['id' => $value['id']]) }}" title="编辑"><i class="fa fa-edit"></i></a> &nbsp; 
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.system-category.delete', ['id' => $value['id']]) }}" onclick="return confirm('确定删除吗');return false;"  title="删除"><i class="fa fa-trash-o"></i></a>
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

