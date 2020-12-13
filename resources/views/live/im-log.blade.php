@extends('layouts.base')

@section('content')
@section('title', 'IM消息日志')
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">IM消息日志</a></li>
    </ul>
</div>

<div class='panel panel-default'>
    <form action="" method="get" class="form-horizontal" id="form1">
        <input type="hidden" name="c" value="site"/>
        <input type="hidden" name="a" value="entry"/>
        <input type="hidden" name="m" value="yun_shop"/>
        <input type="hidden" name="do" value="subs" id="form_do"/>
        <input type="hidden" name="route" value="live.IM-log.index" id="route" />
        <div class="panel panel-info">
            <div class="panel-body">

                <div class="form-group col-xs-12 col-sm-2">
                    <input class="form-control" name="search[group_id]" type="text" value="{{$search['group_id']}}" placeholder="主播群id">
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
                <th style='width:4%;text-align: center;'>群组ID</th>
                <th style='width:4%;text-align: center;'>回调类型</th>
                <th style='width:5%;text-align: center;'>发送者</th>
                <th style='width:10%;text-align: center;'>回调命令字</th>
                <th style='width:4%;text-align: center;'>消息类型</th>
                <th style='width:30%;text-align: center;'>消息内容</th>
                <th style='width:6%;text-align: center;'>消息发送时间</th>
                <th style='width:6%;text-align: center;'>创建时间</th>
                <th style='width:6%;text-align: center;'>客户端IP地址</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
                <tr>
                    <td style="text-align: center;">{{$row->id}}</td>
                    <td style="text-align: center;">{{$row->group_id}}</td>
                    <td style="text-align: center;">{{$row->type_parse}}</td>
                    <td style="text-align: center;">{{$row->from_account}}</td>
                    <td style="text-align: center;">{{$row->callback_command}}</td>
                    <td style="text-align: center;">{{$row->msg_type_parse}}</td>
                    <td style="text-align: left;white-space: normal;word-break: break-all;">{{$row->msg_content_parse}}</td>
                    <td style="text-align: center;">{{$row->msg_time}}</td>
                    <td style="text-align: center;">{{$row->created_at}}</td>
                    <td style="text-align: center;">{{$row->client_iP}}</td>
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

            $.get("{!! yzWebUrl('live.IM-log.del') !!}",{'start':start,'end':end}, function(json){
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