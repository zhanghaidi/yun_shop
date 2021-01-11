@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.article.index')}}">文章列表</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.article-category.index')}}">文章分类</a></li>
                </ul>
            </div>

            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">栏目名称:</div>
                        <input type="text" placeholder="请输入栏目名搜索" value="{{$search['keyword']}}" name="search[keyword]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.minapp-content.admin.article-category.edit') }}" class="btn btn-info">添加栏目</a>
            </div>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>排序</th>
                        <th>栏目名称</th>
                        <th>栏目类型</th>
                        <th>是否显示</th>
                        <th>添加时间</th>
                        <th class="text-right">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['list_order']}}</td>
                        <td>
                        @if(isset($value['image'][0]))
                            <a href="{{ tomedia($value['image']) }}" target="_blank">
                                <img src="{{tomedia($value['image'])}}" width="50" border="1"/>
                            </a>&nbsp;&nbsp;
                        @endif
                            {{$value['name']}}
                        </td>
                        <td>
                            @if($value['type'] == 1) 辟谣类
                            @elseif($value['type'] == 2) 视频类
                            @elseif($value['type'] == 3) 文章类
                            @endif
                        </td>
                        <td>
                            @if($value['status'] == 1) 是
                            @else 否
                            @endif
                        </td>
                        <td>{{$value['create_time']}}</td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.article-category.edit', ['id' => $value['id']]) }}" title="编辑"><i class="fa fa-edit"></i></a> &nbsp; 
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.article-category.delete', ['id' => $value['id']]) }}" onclick="return confirm('确定删除吗');return false;"  title="删除"><i class="fa fa-trash-o"></i></a>
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

