@extends('layouts.base')

@section('content')
@section('title', '模板消息日志')
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">模板消息日志</a></li>
    </ul>
</div>

<div class='panel panel-default'>
    <form action="" method="get" class="form-horizontal" id="form1">
        <input type="hidden" name="c" value="site"/>
        <input type="hidden" name="a" value="entry"/>
        <input type="hidden" name="m" value="yun_shop"/>
        <input type="hidden" name="do" value="subs" id="form_do"/>
        <input type="hidden" name="route" value="setting.message-log.index" id="route" />
        <div class="panel panel-info">
            <div class="panel-body">

                <div class="form-group col-xs-12 col-sm-2">
                    <input class="form-control" name="search[member_id]" type="text"
                           value="{{$search['member_id']}}" placeholder="会员id">
                </div>
                <div class="form-group col-xs-12 col-sm-2">
                    <input class="form-control" name="search[tpl_id]" type="text"
                           value="{{$search['tpl_id']}}" placeholder="模板id">
                </div>

                {{--<div class="form-group col-xs-12 col-sm-3">--}}
                    {{--<input class="form-control" name="search[member]"  type="text"--}}
                           {{--value="{{$search['member']}}" placeholder="会员昵称/姓名/手机">--}}
                {{--</div>--}}

                <div class="form-group col-xs-12 col-sm-4">
                    <div class="col-sm-6">
                        <label class='radio-inline'>
                            <input type='radio' value='0' name='search[is_time]' @if(!$search['is_time']) checked @endif>不搜索
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' value='1' name='search[is_time]' @if($search['is_time'] == '1') checked @endif>搜索
                        </label>
                    </div>
                    {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', ['starttime'=>array_get($search,'time.start',0),
                    'endtime'=>array_get($search,'time.end',0),
                    'start'=>0,
                    'end'=>0
                    ], true) !!}
                </div>

                <div class="form-group  col-xs-12 col-sm-2">
                    <div class="">
                        {{--<button type="submit" name="export" value="1" id="export" class="btn btn-info">导出--}}
                        {{--Excel--}}
                        {{--</button>--}}
                        <button class="btn btn-success ">
                            <i class="fa fa-search"></i>
                            搜索
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<div class='panel panel-default'>
    <div class='panel-heading'>
        总数：{{$list->total()}}个
    </div>
    <div class='panel-body table-responsive'>
        <table class="table table-" style="table-layout:fixed;">
            <thead>
            <tr>
                <th style='width:2%;text-align: center;'>日志ID</th>
                <th style='width:4%;text-align: center;'>会员ID</th>
                <th style='width:4%;text-align: center;'>用户昵称</th>
                <th style='width:15%;text-align: center;'>模板ID</th>
                <th style='width:15%;text-align: center;'>小程序路径/网页链接</th>
                <th style='width:15%;text-align: center;'>消息内容</th>
                <th style='width:2%;text-align: center;'>错误码</th>
                <th style='width:15%;text-align: center;'>微信响应数据</th>
                <th style='width:6%;text-align: center;'>发送时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
                <tr>
                    <td style="text-align: center;">{{$row->id}}</td>
                    <td style="text-align: center;">{{$row->member_id}}</td>
                    <td style="text-align: center;">{{$row->member->nickname}}</td>
                    <td style="text-align: center;">{{$row->template_id}}</td>
                    <td style="text-align: center;">@if($row->weapp_pagepath){{$row->weapp_pagepath}}@else{{$row->news_link}}@endif</td>
                    <td style="text-align: center;">{{$row->message}}</td>
                    <td style="text-align: center;">@if($row->respon_code == 0) {{'正常'}}@else{{$row->respon_code}}@endif</td>
                    <td style="text-align: center;">{{$row->respon_data}}</td>
                    <td style="text-align: center;">{{$row->created_at}}</td>
                </tr>

            @endforeach
            </tbody>
        </table>

        {!! $pager !!}
    </div>
    <div style="margin-left:13px;margin-top:8px">
        <button class='btn btn-success' onclick="del()"><i class='fa fa-delicious'></i> 删除</button>
        {!! app\common\helpers\DateRange::tplFormFieldDateRange('del[time]', ['starttime'=> 0,
               'endtime'=> 0,
               'start'=>0,
               'end'=>0
               ], true) !!}
    </div>
</div>
<div style="width:100%;height:150px;"></div>
<script>
    function del() {
        if (confirm('是否确认删除?')) {
            var start = $(':input[name="del[time][start]"]').val();
            var end = $(':input[name="del[time][end]"]').val();

            $.get("{!! yzWebUrl('setting.message-log.del') !!}",{'start':start,'end':end}, function(json){
                if (json.result == 1) {
                    alert('删除成功');
                    location.href = location.href;
                } else {
                    console.log(json.msg, json);
                    alert(json.msg);
                }

            },'json');
        }
    }

</script>
@endsection