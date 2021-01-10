@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li @if($type=='step') class="active" @endif><a
                                href="{{yzWebUrl('plugin.minapp-content.admin.sport-clock.step')}}">运动打卡设置</a></li>
                    <li @if($type=='step_exchange_list') class="active" @endif><a
                                href="{{yzWebUrl('plugin.minapp-content.admin.sport-clock.step-exchange-list')}}">步数兑换记录</a>
                    </li>
                </ul>
            </div>
            <div class="panel-body">
                <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                            <input type="text" class="form-control" name="search[nickname]"
                                   value="{{$request['search']['nickname']}}" placeholder="用户昵称"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                            <input type="text" class="form-control" name="search[user_id]"
                                   value="{{$request['search']['user_id']}}" placeholder="用户id"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                            <div class="time">
                                <select name='search[search_time]' class='form-control'>
                                    <option value='0' @if($request['search']['search_time']=='0') selected @endif>选择时间</option>
                                    <option value='1' @if($request['search']['search_time']=='1') selected @endif>兑换时间</option>
                                </select>
                                <div class="search-select">
                                    {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                                    'starttime'=>date('Y-m-d H:i', strtotime($request['search']['time']['start']) ?: strtotime('-1 month')),
                                    'endtime'=>date('Y-m-d H:i',strtotime($request['search']['time']['end']) ?: time()),
                                    'start'=>0,
                                    'end'=>0
                                    ], true) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="panel panel-defualt">
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>UID</th>
                            <th>用户头像</th>
                            <th>用户昵称</th>
                            <th>消耗步数</th>
                            <th>兑换健康金</th>
                            <th>兑换时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($exchanges as $k => $value)
                            <tr>
                                <td>{{$value['user_id']}}</td>
                                <td>
                                    <div class="show-cover-img-big"
                                         style="position:relative;width:50px;overflow:visible">
                                        <img src="{{ $value['avatar'] }}" alt=""
                                             style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                        <img class="img-big" src="{{ $value['avatar'] }}" alt=""
                                             style="z-index:99999;position:absolute;top:0;left:0;border:1px solid #ccc;padding:1px;display: none">
                                    </div>
                                </td>
                                <td>
                                    {{ $value['nickname'] }}
                                </td>
                                <td>{{$value['steps']}}</td>
                                <td>{{$value['point']}}</td>
                                <td>
                                    {{ date('Y-m-d H:i:s', $value['create_time']) }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {!! $pager !!}
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    // 查看商品封面大图
    $('.show-cover-img-big').on('mouseover', function () {
        $(this).find('.img-big').show();
    });
    $('.show-cover-img-big').on('mouseout', function () {
        $(this).find('.img-big').hide();
    });
</script>
@endsection
