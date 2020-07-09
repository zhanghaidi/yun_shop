@extends('layouts.base')
@section('content')

    {{--<div class="main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="./index.php" method="get" class="form-horizontal" role="form">
                    <input type="hidden" name="c" value="site">
                    <input type="hidden" name="a" value="entry">
                    <input type="hidden" name="m" value="sz_yi">
                    <input type="hidden" name="do" value="plugin">
                    <input type="hidden" name="p" value="article">
                    <input type="hidden" name="op" value="report">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label">筛选</label>
                        <div class="col-sm-11 col-xs-12">
                            <div class="row row-fix tpl-category-container">
                                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                                    <select class="form-control tpl-category-parent" name="aid">
                                        <option value="">全部文章</option>
                                        {loop $articles $article}
                                        <option value="{$article['id']}" {if $_GPC['aid']==$article['id']}selected="selected"{/if}>{$article['article_title']}</option>
                                        {/loop}
                                    </select>
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2" style='padding:0px 10px;'>
                                    <select class="form-control tpl-category-parent" name="cid">
                                        {loop $categorys $ccid $cname}
                                        <option value="{$ccid}" {if $_GPC['cid']==$ccid}selected="selected"{/if}>{$cname}</option>
                                        {/loop}
                                    </select>
                                </div>
                                <div class="col-xs-12 col-sm-8 col-lg-9" style="width:400px;">
                                    <input class="form-control" name="keyword" id="" type="text" value="{$_GPC['keyword']}" placeholder="请输入举报内容关键字进行检索（选择分类减小检索范围）">
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-1 control-label"></label>
                        <div class="col-xs-12 col-sm-2 col-lg-2">
                            <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>--}}

    <!-- 举报列表 -->
    <div class='panel panel-default'>
        <div class='panel-heading'> 举报记录 (总数: {{ $reports['total'] }})</div>
        <div class='panel-body'>
            <table class="table">
                <thead>
                <tr>
                    <th style="width:6%; text-align: center;">ID</th>
                    <th style="width:10%; text-align: center;">会员ID</th>
                    <th style="width:15%;">会员名字</th>
                    <th style="width:10%; text-align: center;">文章ID</th>
                    <th style="width:25%;">文章标题</th>
                    {{--<th style="width:14%; text-align: center;">违规分类</th>--}}
                    <th style="width:20%;">举报描述</th>
                </tr>
                </thead>
                <tbody>
                @if (!empty($reports['data']))
                @foreach ($reports['data'] as $report)
                <tr>
                    <td style="text-align: center;">{{ $report['id'] }}</td>
                    <td style="text-align: center;">{{ $report['uid'] }}</td>
                    <td>{{ $report['belongs_to_member']['nickname'] }}</td>
                    <td style="text-align: center;">{{ $report['article_id'] }}</td>
                    <td>{{ $report['belongs_to_article']['title'] }}</td>
                    {{--<td<td style="text-align: center;">{{ $report['type'] }}</td>--}}
                    <td style="word-break:break-all">{{ $report['desc'] }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td style="text-align: center; line-height: 100px;" colspan="7">啊哦~没有相关举报记录哦</td>
                </tr>
                @endif
                <tr><td colspan="7" style="padding:0px; margin: 0px;">{{ $pager }}</td></tr>
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <a class='btn btn-default' href="{{ yzWebUrl('plugin.article.admin.article.index') }}"><i class="fa fa-reply"></i> 返回文章列表</a>
        </div>
    </div>

@endsection
