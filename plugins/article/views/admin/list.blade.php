@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        {{--<div class="ulleft-nav">--}}
            {{--<ul class="nav nav-tabs">--}}
                {{--<li><a href="{{ yzWebUrl('plugin.article.article.index') }}" style="cursor: pointer;">文章管理</a></li>--}}
                {{--<li><a href="" style="cursor: pointer;">添加文章</a></li>--}}
                {{--<li><a href="{{ yzWebUrl('plugin.article.category.index') }}" style="cursor: pointer;">分类管理</a></li>--}}
                {{--<li><a href="" style="cursor: pointer;">其他设置</a></li>--}}
                {{--<li><a href="" style="cursor: pointer;">举报记录</a></li>--}}

            {{--</ul>--}}
        {{--</div>--}}
        <div class="rightlist">
            {{--@include('Yunshop\Article::admin.tabs')--}}
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">文章管理</a></li>
                </ul>
            </div>

            <div class="panel panel-info">
                <div class="panel-body">
                    <form action="" method="post" class="form-horizontal" role="form" id="form">
                        <div class="form-group">
                            <!--<label class="col-xs-12 col-sm-3 col-md-1 control-label">筛选</label>-->
                            <div class="col-sm-11 col-xs-12">
                                <div class="row row-fix tpl-category-container">
                                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                                        <select class="form-control tpl-category-parent" name="search[category_id]">
                                            <option value="">全部分类</option>
                                            @foreach ($categorys as $category)
                                            <option value="{{ $category['id'] }}" @if ($search['category_id'] == $category['id']) selected="selected" @endif>{{ $category['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xs-12 col-sm-8 col-lg-9">
                                        <input class="form-control" name="search[keyword]" id="" type="text" value="{{ $search['keyword'] }}" placeholder="请输入文章标题关键字进行检索（选择文章分类减小检索范围）">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <!--<label class="col-xs-12 col-sm-3 col-md-1 control-label"> </label>-->
                            <div class="col-xs-12 col-sm-2 col-lg-2">
                                <button class="btn btn-success" onclick="$('#form').onsubmit();"><i class="fa fa-search"></i> 搜索</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"> 文章列表 (总数: <span id="$articlenum">{{ $articles['total'] }}</span>)</div>
            <div class="panel-body">
                <table class="table" style="">
                    <thead>
                    <tr>
                        <th style="width:30px; text-align:center;">ID</th>
                        <th style="width:140px;">文章标题</th>
                        <th style="width:80px;">文章分类</th>
                        <th style="width:50px;">文章关键字</th>
                        <th style="width:80px;">文章创建时间</th>
                        <th style="width:50px;">真实阅读量</th>
                        <th style="width:50px;">真实点赞量</th>
                        <th style="width:80px;">数据统计</th>
                        <th style="width:50px;">状态</th>
                        <th style="width:100px; text-align:center;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (empty($categorys))
                        <tr class="noarticle">
                            <td style="text-align: center; line-height: 100px;" colspan="8">亲~您还没有添加文章分类哦~您可以点击 “<a class="nav-add" href="{{ yzWebUrl('plugin.article.admin.category.add') }}">添加文章分类</a>”</td>
                        </tr>
                    @elseif (empty($articles['data']))
                        <tr class="noarticle">
                            <td style="text-align: center; line-height: 100px;" colspan="8">亲~您还没有添加文章哦~您可以尝试 ↙ 左下角的 “<a class="nav-add" href="{{ yzWebUrl('plugin.article.admin.article.add') }}">添加文章</a>”</td>
                        </tr>
                    @else
                        @foreach ($articles['data'] as $article)
                            <tr cid="{{ $article['id'] }}" cname="{{ $article['title'] }}">
                                <td>{{ $article['id'] }}</td>
                                <td>{{ $article['title'] }}</td>
                                <td>{{ $article['belongs_to_category']['name'] }}</td>
                                <td>{{ $article['keyword'] }}</td>
                                <td>{{ $article['created_at'] }}</td>
                                <td>{{ $article['read_num'] }}</td>
                                <td>{{ $article['like_num'] }}</td>
                                <td><a href="{{ yzWebUrl('plugin.article.admin.article.log', ['id' => $article['id']]) }}">查看记录</a></td>
                                <td>@if ($article['state'] == 1) <span style="color: green;">开启</span> @else 关闭 @endif</td>


                                <td style="text-align:center;">
                                    <a href="javascript:;" data-clipboard-text="{{yzAppFullUrl('articleContent/'.$article['id'])}}" data-url="{{yzAppFullUrl('articleContent/'.$article['id'])}}" title="复制连接" class="js-clip">复制链接</a>
                                    <a class='btn btn-default nav-edit' href="{{ yzWebUrl('plugin.article.admin.article.edit',['id' => $article['id']]) }}"><i class="fa fa-edit"></i></a>
                                    <a class='btn btn-default nav-del' href="{{ yzWebUrl('plugin.article.admin.article.deleted',['id' => $article['id']]) }}" onclick="return confirm('确认删除此分类吗？');return false;"><i class="fa fa-trash-o"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                {!! $pager !!}
            </div>
            <div class="panel-footer">
                <a class='btn btn-primary' href="{{ yzWebUrl('plugin.article.admin.article.add') }}"><i class="fa fa-plus"></i> 添加一篇文章</a>
                {{--<a class='btn btn-primary' href="{{ yzWebUrl('plugin.article.admin.article.collect') }}"><i class="fa fa-plus"></i> 文章采集</a>--}}
            </div>
        </div>

    </div>
@endsection

