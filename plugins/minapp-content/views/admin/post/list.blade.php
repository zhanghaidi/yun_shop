@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.post.index')}}">话题管理</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-board.index')}}">话题版块</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-filter.post')}}">敏感词库</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-upload-filter.index')}}">上传敏感图用户</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.cos-images.index')}}">敏感图片</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.cos-video.index')}}">敏感视频管理</a></li>
                </ul>
            </div>

            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <select name="search[board_id]" class="form-control">
                            <option value="">版块分类</option>
                            @foreach($board as $item)
                            <option value="{{$item['id']}}"@if($search['board_id'] == $item['id']) selected="selected" @endif>{{$item['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <select name="search[is_recommend]" class="form-control">
                            <option value="">是否推荐</option>
                            <option value="1"@if($search['is_recommend'] === 1) selected="selected" @endif>是</option>
                            <option value="0"@if($search['is_recommend'] === 0) selected="selected" @endif>否</option>
                        </select>
                    </div>
                </div>

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
                        <select name="search[is_hot]" class="form-control">
                            <option value="">是否首页精选</option>
                            <option value="1"@if($search['is_hot'] === 1) selected="selected" @endif>是</option>
                            <option value="0"@if($search['is_hot'] === 0) selected="selected" @endif>否</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <input type="text" placeholder="请输入标题/会员ID/帖子ID进行搜索" value="{{$search['keywords']}}" name="search[keywords]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.minapp-content.admin.post.edit') }}" class="btn btn-info">发布帖子</a>
            </div>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>发帖用户</th>
                        <th>话题标题</th>
                        <th style="width: 320px;">话题内容</th>
                        <th style="width: 320px;">话题图片</th>
                        <th>所属版块</th>
                        <th>话题视频</th>
                        <th>视频封面</th>
                        <th>是否推荐</th>
                        <th>是否首页精选</th>
                        <th>显示状态</th>
                        <th style="width: 66px;">浏览量
                            <div data-field="view_nums" class="th-sorter none"><div></div><div></div></div>
                        </th>
                        <th style="width: 66px;">点赞量
                            <div data-field="like_nums" class="th-sorter none"><div></div><div></div></div>
                        </th>
                        <!-- <th style="width: 66px;">评论量
                            <div data-field="comment_nums" class="th-sorter none"><div></div><div></div></div>
                        </th> -->
                        <th>发布时间</th>
                        <th class="text-right">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td style="text-align: center;">
                            <a href="{{ yzWebUrl('member.member.detail', ['id' => $value['user_id']]) }}" target="_blank">
                                <img src="{{tomedia($value['avatarurl'])}}" width="30" border="1"> <br/>
                                {{$value['nickname']}}
                            </a>
                        </td>
                        <td>{{$value['title']}}</td>
                        <td>{{$value['content']}}</td>
                        <td>
                        @foreach($value['images'] as $image)
                        <a href="{{tomedia($image)}}" target="_blank"><img src="{{tomedia($image)}}" width="75" height="75"></a> 
                        @endforeach
                        </td>
                        <td>{{$value['name']}}</td>
                        <td>
                        @if($value['video'])
                            <a href="{{ tomedia($value['video']) }}" target="_blank">
                                <video width="150" height="95">
                                    <source src="{{ tomedia($value['video']) }}">
                                </video>
                            </a>
                        @endif
                        </td>
                        <td>
                        @if($value['video_thumb'])
                            <a href="{{ tomedia($value['video_thumb']) }}" target="_blank">
                                <img src="{{ tomedia($value['video_thumb']) }}" width="75" height="75" >
                            </a>
                        @endif
                        </td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.post.status', ['id' => $value['id'], 'is_recommend' => 1]) }}">
                            @if($value['is_recommend'] == 1)
                                <span class="btn btn-success btn-circle icon-recommend"><i class="fa fa-thumbs-up"></i></span>
                            @else
                                <span class="btn btn-danger btn-circle icon-recommend"><i class="fa fa-thumbs-down"></i></span>
                            @endif
                            </a>
                        </td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.post.status', ['id' => $value['id'], 'is_hot' => 1]) }}">
                            @if($value['is_hot'] == 1)
                                <span class="btn btn-success btn-circle icon-recommend"><i class="fa fa-thumbs-up"></i></span>
                            @else
                                <span class="btn btn-danger btn-circle icon-recommend"><i class="fa fa-thumbs-down"></i></span>
                            @endif
                            </a>
                        </td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.post.status', ['id' => $value['id'], 'check' => 1]) }}">
                            @if($value['status'] == 1)
                                <span class="btn btn-success btn-circle icon-check"><i class="fa fa-check"></i></span>
                            @else
                                <span class="btn btn-danger btn-circle icon-check"><i class="fa fa-times"></i></span>
                            @endif
                            </a>
                        </td>
                        <td style="text-align: center;">{{$value['view_nums']}}</td>
                        <td style="text-align: center;">{{$value['like_nums']}}</td>
                        <!-- <td style="text-align: center;">
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.sns-replys.index', ['id' => $value['id']]) }}">
                                <i class="fa fa-comment-o"></i> {{$value['comment_nums']}}
                            </a>
                        </td> -->
                        <td>{{$value['create_time']}}</td>
                        <td class="text-right">
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.post.edit', ['id' => $value['id']]) }}" title="编辑"><i class="fa fa-edit"></i></a> &nbsp; 
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.post.delete', ['id' => $value['id']]) }}" onclick="return confirm('确定删除吗');return false;"  title="删除"><i class="fa fa-trash-o"></i></a>
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

