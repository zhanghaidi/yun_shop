@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">投诉单</div>
        <div class="panel-body">
            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <input type="hidden" name="id" value="{{$id}}" />
                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="input-group">
                        <div class="input-group-addon">投诉时间:</div>
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
                        <th>ID</th>
                        <th>投诉人(昵称 - 手机号)</th>
                        <th>投诉项目</th>
                        <th>投诉时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>
                            @if($value['member_id'])
                            <img src="{{$value['avatar']}}" style="width:30px;height:30px;padding:1px;border:1px solid #CCC;" alt="{{$value['member_id']}}"> &nbsp; 
                            {{$value['nickname']}} &nbsp; - &nbsp; 
                            {{$value['mobile']}}
                            @else
                            @endif
                        </td>
                        <td>{{$value['item_name']}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>
                            <a class='btn btn-danger' href="{{ yzWebUrl('plugin.wechat-complaint.admin.log.delete', ['id' => $value['id']]) }}" onclick="return confirm('确认删除该记录吗？');return false;" title="删除"><i class="fa fa-remove"></i></a>
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

