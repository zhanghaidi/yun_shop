@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">批阅答卷 - 考试:{{$info['name']}}</div>
        <div class="panel-body">
            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <input type="hidden" name="id" value="{{$info['id']}}" />
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-1">
                    <div class="input-group">
                        <div class="input-group-addon">状态:</div>
                        <select class="form-control" name="search[status]">
                            <option value="">全部</option>
                            <option value="1" @if($search['status'] == 1) selected="selected" @endif>未交卷</option>
                            <option value="2" @if($search['status'] == 2) selected="selected" @endif>已交卷</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">得分:</div>
                        <input type="number" class="form-control" name="search[score][min]" value="{{$search['score']['min']}}">
                        <div class="input-group-addon">-</div>
                        <input type="number" class="form-control" name="search[score][max]" value="{{$search['score']['max']}}">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="input-group">
                        <div class="input-group-addon">开考时间:</div>
                        {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time_range]', [
                            'starttime'=>array_get($search['time_range'],'start',0),
                            'endtime'=>array_get($search['time_range'],'end',0),
                            'start'=>0,
                            'end'=>0
                            ], true) !!}
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
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
                        <th width="5%">ID</th>
                        <th width="10%">考试人(昵称 - 手机号)</th>
                        <th width="10%">得分 / 总分</th>
                        <th width="10%">正确 / 总题目数量</th>
                        <th width="5%">状态</th>
                        <th width="10%">开考时间</th>
                        <th width="10%">交卷时间</th>
                        <th width="10%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>
                            @if($value['member']['uid'])
                            <img src="{{$value['member']['avatar']}}" style="width:30px;height:30px;padding:1px;border:1px solid #CCC;" alt="{{$value['member']['uid']}}"> &nbsp; 
                            {{$value['member']['nickname']}} &nbsp; - &nbsp; 
                            {{$value['member']['mobile']}}
                            @else
                            @endif
                        </td>
                        <td>{{$value['score_obtain']}} / {{$value['score_total']}}</td>
                        <td>{{$value['question_correct']}} / {{$value['question_total']}}</td>
                        <td>@if($value['status'] ==2) 交卷 @endif</td>
                        <td>{{$value['created_at']}}</td>
                        <td>@if($value['status'] ==2) {{$value['updated_at']}} @endif</td>
                        <td>
                            <a class='btn btn-info' href="{{ yzWebUrl('plugin.examination.admin.examination.paper', ['id' => $value['id']]) }}" title="查看答卷"><i class="fa fa-leanpub"></i></a>
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

