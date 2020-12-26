@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">考试人员 - 考试:{{$info['name']}}</div>
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th width="5%">会员ID</th>
                        <th width="10%">考试人(昵称 - 手机号)</th>
                        <th width="10%">交卷 / 答卷 数量</th>
                        <th width="10%">最低 / 最高 分数</th>
                        <th width="10%">最低 / 最高 正确题数</th>
                        <th width="10%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['member_id']}}</td>
                        <td>
                            @if($value['member']['uid'])
                            <img src="{{$value['member']['avatar']}}" style="width:30px;height:30px;padding:1px;border:1px solid #CCC;" alt="{{$value['member']['uid']}}"> &nbsp; 
                            {{$value['member']['nickname']}} &nbsp; - &nbsp; 
                            {{$value['member']['mobile']}}
                            @else
                            @endif
                        </td>
                        <td>{{$value['paper_complete']}} / {{$value['paper_count']}}</td>
                        <td>{{$value['bad_score']}} / {{$value['good_score']}}</td>
                        <td>{{$value['bad_question']}} / {{$value['good_question']}}</td>
                        <td>
                            <a class='btn btn-success' href="{{ yzWebUrl('plugin.examination.admin.examination.answer', ['id' => $info['id'],'search[member_id]' => $value['member_id']]) }}" title="批阅答卷"><i class="fa fa-tasks"></i></a>
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

