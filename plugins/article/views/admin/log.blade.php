@extends('layouts.base')
@section('content')

    <div class="rightlist" style="margin: 0;">
        <div class="panel panel-default">
            <div class="panel-heading"> 数据统计 (id: {{ $id }})</div>
            <div class="panel-body">
                <div class="alert alert-info">
                    <p>文章标题：{{ $article['title'] }}</p>
                    <p>文章分类：{{ $article['belongs_to_category']['name'] }}</p>
                    <p>触发关键字：{{ $article['keyword'] }}</p>
                    <p>创建时间：{{ $article['created_at'] }}</p>
                    <p>阅读量(真实+虚拟=总数)：{{ $article['read_num'] }} + {{ $article['virtual_read_num'] }} = {{ $article['read_num'] + $article['virtual_read_num'] }}</p>
                    <p>点赞数(真实+虚拟=总数)：{{ $article['like_num'] }} + {{ $article['virtual_like_num'] }} = {{ $article['like_num'] + $article['virtual_like_num'] }}</p>
                    <p>积分累计发放数量：{{ $point_sum }} 积分</p>
                    <p>余额累计发放数量：{{ $bonus_sum }} 元</p>
                </div>
                <table class="table">
                    <thead>
                    <tr>
                        <th style="width:80px;">记录ID</th>
                        <th style="width:300px;">会员昵称</th>
                        <th style="width:100px; text-align:center;">阅读次数</th>
                        <th style="width:100px; text-align:center;">点赞状态</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (empty($logs['data']))
                    <tr>
                        <td  colspan="7" style="line-height:30px;">没有查询到记录！</td>
                    </tr>
                    @else
                    @foreach ($logs['data'] as $log)
                    <tr style="border-bottom:0px;">
                        <td>{{ $log['id'] }}</td>
                        <td> @if(!empty($log['belongs_to_member']['nickname'])) {{ $log['belongs_to_member']['nickname'] }} @else * 用户未更新信息 @endif</td>
                        <td style="text-align:center;">{{ $log['read_num'] }}</td>
                        <td style="text-align:center;">
                            @if ($log['liked'] == 1)
                            <label class="label label-success">已点赞</label>
                            @else
                            <label class="label label-default">未点赞</label>
                            @endif
                        </td>
                        <td></td>
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
                {!! $pager !!}
            </div>
            <div class="panel-footer">
                <a class="btn btn-default" href="{{ yzWebUrl('plugin.article.admin.article.index') }}"><i class="fa fa-reply"></i> 返回文章列表</a>
                <a class="btn btn-default" href="{{ yzWebUrl('plugin.article.admin.article.share', ['id' => $id]) }}" style="margin-left:10px;"><i class="fa fa-list"></i> 查看分享记录</a>
            </div>
        </div>
    </div>

@endsection
