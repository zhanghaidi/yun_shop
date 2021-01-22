@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.post.index')}}">话题管理</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-board.index')}}">话题版块</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-filter.post')}}">敏感词库</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.sns-upload-filter.index')}}">上传敏感图用户</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.cos-images.index')}}">敏感图片</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.cos-video.index')}}">敏感视频管理</a></li>
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
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th width="60">图片</th>
                        <th width="800">路径URL</th>
                        <th>识别结果</th>
                        <th>冻结状态</th>
                        <th>涉黄信息</th>
                        <th>涉暴恐信息</th>
                        <th>涉政信息</th>
                        <th>广告引导信息</th>
                        <th>上传时间</th>
                        <th class="text-right">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>
                            <a href="{{$value['url']}}" target="_blank"><img src="{{$value['url']}}" width="50" border="1"></a>
                        </td>
                        <td>{{$value['url']}}</td>
                        <td>
                            @if($value['result'] == 0) 确认正常
                            @elseif($value['result'] == 1) 确认敏感
                            @elseif($value['result'] == 2) 疑似敏感
                            @endif
                        </td>
                        <td>
                            @if($value['forbidden_status'] == 0) 正常
                            @elseif($value['forbidden_status'] == 1) 已冻结
                            @endif
                        </td>
                        <td>
                            @if($value['porn_info']['hit_flag'] == 1) 
                            涉黄 <br>
                            分值：{{$value['porn_info']['score']}}
                            @endif
                        </td>
                        <td>
                            @if($value['terrorist_info']['hit_flag'] == 1) 
                            涉暴恐 <br>
                            分值：{{$value['terrorist_info']['score']}}
                            @endif
                        </td>
                        <td>
                            @if($value['politics_info']['hit_flag'] == 1) 
                            涉政 <br>
                            分值：{{$value['politics_info']['score']}}
                            @endif
                        </td>
                        <td>
                            @if($value['ads_info']['hit_flag'] == 1) 
                            广告引导 <br>
                            分值：{{$value['ads_info']['score']}}
                            @endif
                        </td>
                        <td>{{$value['create_time']}}</td>
                        <td class="text-right">
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.cos-images.delete', ['id' => $value['id']]) }}" onclick="return confirm('确定删除吗');return false;"  title="删除"><i class="fa fa-trash-o"></i></a>
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

