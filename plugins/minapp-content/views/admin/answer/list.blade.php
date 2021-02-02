@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.somato-type.index')}}">体质管理</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.label.index')}}">症状标签</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.question.index')}}">测评题库</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.answer.index')}}">用户测评</a></li>
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
                        <input type="text" placeholder="请输入标题进行搜索" value="{{$search['keywords']}}" name="search[keywords]" class="form-control">
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
                        <th>ID</th>
                        <th>用户UID</th>
                        <th>用户头像 / 昵称</th>
                        <th>用户性别</th>
                        <th>用户测评体质</th>
                        <th>测评体质转换分</th>
                        <th>测评时间</th>
                        <th class="text-right">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['user_id']}}</td>
                        <td>
                            <a href="{{ yzWebUrl('member.member.detail', ['id' => $value['user_id']]) }}" target="_blank">
                                <img src="{{tomedia($value['avatarurl'])}}" width="40" border="1"> <br/>
                                {{$value['nickname']}}
                            </a>
                        </td>
                        <td>
                            @if($value['gender'] == 1) 男
                            @elseif($value['gender'] == 2) 女
                            @else 未知
                            @endif
                        </td>
                        <td>{{$value['name']}}</td>
                        <td>{{$value['ture_somato_derived_score']}}</td>
                        <td>{{$value['create_time']}}</td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.answer.detail', ['id' => $value['id']]) }}" title="查看"><i class="fa fa-eye"></i></a> &nbsp; 
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.answer.delete', ['id' => $value['id']]) }}" onclick="return confirm('确定删除吗');return false;"  title="删除"><i class="fa fa-trash-o"></i></a>
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

