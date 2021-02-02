@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.article.index')}}">文章列表</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.article-category.index')}}">文章分类</a></li>
                </ul>
            </div>

            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <input type="text" placeholder="请输入文章ID搜索" value="{{$search['article_id']}}" name="search[article_id]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <select name="search[cateid]" class="form-control">
                            <option value="">文章分类</option>
                            @foreach($category as $item)
                            <option value="{{$item['id']}}"@if($search['cateid'] == $item['id']) selected="selected" @endif>{{$item['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <select name="search[status]" class="form-control">
                            <option value="">显示状态</option>
                            <option value="1"@if($search['status'] === 1) selected="selected" @endif>显示</option>
                            <option value="0"@if($search['status'] === 0) selected="selected" @endif>隐藏</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <select name="search[is_hot]" class="form-control">
                            <option value="">是否推荐</option>
                            <option value="1"@if($search['is_hot'] === 1) selected="selected" @endif>是</option>
                            <option value="0"@if($search['is_hot'] === 0) selected="selected" @endif>否</option>
                        </select>
                    </div>
                </div>

                <!-- <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <select name="search[is_discuss]" class="form-control">
                            <option value="">辟谣状态</option>
                            <option value="1"@if($search['is_discuss'] === 1) selected="selected" @endif>开启</option>
                            <option value="0"@if($search['is_discuss'] === 0) selected="selected" @endif>关闭</option>
                        </select>
                    </div>
                </div> -->

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time_range]', [
                            'starttime'=>array_get($search['time_range'],'start',0),
                            'endtime'=>array_get($search['time_range'],'end',0),
                            'start'=>0,
                            'end'=>0,
                            ], true) !!}
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <input type="text" placeholder="请输入文章标题进行搜索" value="{{$search['keywords']}}" name="search[keywords]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.minapp-content.admin.article.edit') }}" class="btn btn-info">发布文章</a>
            </div>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 80px;">文章ID</th>
                        <th style="width: 80px;">文章排序
                            <div data-field="list_order" class="th-sorter none"><div></div><div></div></div>
                        </th>
                        <th>文章封面图</th>
                        <th>文章分享封面图</th>
                        <th style="width: 320px;">文章标题</th>
                        <th>所属分类</th>
                        <th>文章视频</th>
                        <th>文章作者</th>
                        <th>是否显示</th>
                        <th>是否推荐</th>
                        <th style="width: 66px;">浏览量
                            <div data-field="read_nums" class="th-sorter none"><div></div><div></div></div>
                        </th>
                        <th style="width: 66px;">点赞量
                            <div data-field="like_nums" class="th-sorter none"><div></div><div></div></div>
                        </th>
                        <!-- <th style="width: 66px;">评论量
                            <div data-field="comment_nums" class="th-sorter none"><div></div><div></div></div>
                        </th> -->
                        <!-- <th>是否开启辟谣</th> -->
                        <th>添加时间</th>
                        <th class="text-right">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td style="text-align: center;">{{$value['list_order']}}</td>
                        <td>
                            <a href="{{ tomedia($value['thumb']) }}" target="_blank">
                                <img src="{{tomedia($value['thumb'])}}" width="60" />
                            </a>
                        </td>
                        <td>
                            <a href="{{ tomedia($value['share_img']) }}" target="_blank">
                                <img src="{{tomedia($value['share_img'])}}" width="60" />
                            </a>
                        </td>
                        <td>{{$value['title']}}</td>
                        <td>
                        @foreach($category as $item) @if($value['cateid'] == $item['id']) {{$item['name']}} @endif @endforeach
                        </td>
                        <td>
                            <a href="{{ tomedia($value['video']) }}" target="_blank">
                                <video width="150" height="50">
                                    <source src="{{ tomedia($value['video']) }}">
                                </video>
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{ tomedia($value['avatar']) }}" target="_blank">
                                <img src="{{tomedia($value['avatar'])}}" width="50" />
                            </a><br>
                            {{$value['author']}}
                        </td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.article.status', ['id' => $value['id']]) }}">
                            @if($value['status'] == 1)
                                <span class="label label-primary">显示</span>
                            @else
                                <span class="label label-default">隐藏</span>
                            @endif
                            </a>
                        </td>
                        <td>
                            @if($value['is_hot'] == 1) 是
                            @else 否
                            @endif
                        </td>
                        <td style="text-align: center;">{{$value['read_nums']}}</td>
                        <td style="text-align: center;">{{$value['like_nums']}}</td>
                        <!-- <td style="text-align: center;">
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.article-replys.index', ['id' => $value['id']]) }}">
                                <i class="fa fa-comment-o"></i> {{$value['comment_nums']}}
                            </a>
                        </td> -->
                        <!-- <td>
                            @if($value['is_discuss'] == 1)
                            <span class="label label-primary" title="{{date('Y-m-d H:i', $value['discuss_start'])}} -- {{date('Y-m-d H:i', $value['end_time'])}}">开启</span>
                            @else
                            <span class="label label-default">关闭</span>
                            @endif
                        </td> -->
                        <td>{{$value['create_time']}}</td>
                        <td class="text-right">
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.article.edit', ['id' => $value['id']]) }}" title="编辑"><i class="fa fa-edit"></i></a> &nbsp; 
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.article.delete', ['id' => $value['id']]) }}" onclick="return confirm('确定删除吗?文章删除会删除相关评论点赞等信息');return false;"  title="删除"><i class="fa fa-trash-o"></i></a>
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

