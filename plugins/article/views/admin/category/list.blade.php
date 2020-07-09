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

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">文章分类</a></li>
        </ul>
    </div>

    <div class="panel panel-info">
        <div class="panel-body">
            <form action="" method="post" class="form-horizontal" role="form" id="form">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label">筛选</label>
                    <div class="col-sm-11 col-xs-12">
                            <input class="form-control" name="category[keyword]" id="" type="text" value="{{ $keyword }}" placeholder="请输入分类标题关键字进行检索">
                    </div>

                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-1 control-label"> </label>
                    <div class="col-xs-12 col-sm-2 col-lg-2">
                        <button class="btn btn-default" onclick="$('#form').submit();"><i class="fa fa-search"></i> 搜索</button>
                    </div>
               </div>
            </form>
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading"> 分类列表 (总数: <span id="categorynum">{{ $categorys['total'] }}</span>)</div>
    <div class="panel-body">
        <table class="table" style="">
            <thead>
                <tr>
                    <th style="width:50px; text-align:center;">ID</th>
                    <th style="width:500px;">分类名称</th>
                    <th style="width:100px; text-align:center;">操作</th>
                </tr>
            </thead>
            <tbody>
                @if (empty($categorys['data']))
                    <tr class="noarticle"> 
                        <td style="text-align: center; line-height: 100px;" colspan="8">亲~您还没有添加文章分类哦~您可以尝试 ↙ 左下角的 “<a class="nav-add" href="javascript:;">添加文章分类</a>”</td>
                    </tr>
                @else
                    @foreach ($categorys['data'] as $category)
                    <tr cid="{{ $category['id'] }}" cname="{{ $category['name'] }}">
                        <td class="cid" style="width:50px; text-align:center;">{{ $category['id'] }}</td>
                        <td class="cname">{{ $category['name'] }}</td>

                        <td style="text-align:center;">
                        {{--<span class="m_level hidden">{{ $category['member']['level_name'] }}</span>
                        <span class="d_level hidden">{$row['commission_level_limit']}</span>--}}
                                <a class='btn btn-default nav-edit' href="{{ yzWebUrl('plugin.article.admin.category.edit',['id' => $category['id']]) }}"><i class="fa fa-edit"></i></a>
                                <a class='btn btn-default nav-del' href="{{ yzWebUrl('plugin.article.admin.category.deleted',['id' => $category['id']]) }}" onclick="return confirm('确认删除此分类吗？');return false;"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        {!! $pager !!}
    </div>
    <div class='panel-footer'>
        <a class="btn btn-success" href="{{ yzWebUrl('plugin.article.admin.article.index') }}"><i class="fa fa-reply"></i> 返回文章列表</a>
            <a class="btn btn-info nav-add" href="{{ yzWebUrl('plugin.article.admin.category.add') }}"><i class="fa fa-plus"></i> 添加文章分类</a>
    </div>
</div>

</div>
@endsection

