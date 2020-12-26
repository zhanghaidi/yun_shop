@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">试卷批阅</div>
        <div class="panel-body">

            <div class="row" style="line-height:50px">
                <div class="col-xs-12 col-sm-4 col-md-3">
                    答题人:
                    <img src="{{$member['avatar']}}" style="width:50px;height:50px;padding:1px;border:1px solid #CCC;" alt="{{$member['uid']}}">
                    {{$member['nickname']}}  &nbsp; - &nbsp; 
                    {{$member['mobile']}}
                </div>
                <div class="col-xs-12 col-sm-4 col-md-3">
                    @if($paper['status'] == 2)
                        交卷时间：{{$paper['updated_at']}}
                    @else
                        未交卷
                    @endif
                </div>
                <div class="col-xs-12 col-sm-4 col-md-3">
                    @if($paper['status'] == 2)
                        用时：{{$paper['use_time']}}
                    @else
                    @endif
                </div>
                <div class="col-xs-12 col-sm-4 col-md-3">
                    题目数量：{{$paper['question_total']}}
                </div>
            </div>

            @if($paper['status'] == 2)
            <div class="row bg-info" style="margin:50px 0;height:100px">
                <div class="col-xs-12 col-sm-4 col-md-3" style="padding:30px;text-align:center;">
                    <span class="text-success" style="font-size:50px;">{{$paper['score_obtain']}}<i style="font-size:11px;">分</i></span><br />
                    得分
                </div>
                <div class="col-xs-12 col-sm-4 col-md-3" style="padding:30px;text-align:center;">
                    <span style="font-size:50px;">{{$paper['score_total']}}<i style="font-size:11px;">分</i></span><br />
                    试卷总分
                </div>
                <div class="col-xs-12 col-sm-4 col-md-3" style="padding:30px;text-align:center;">
                    <span class="text-info" style="font-size:50px;">{{$paper['question_correct']}}<i style="font-size:11px;">道</i></span><br />
                    答对
                </div>
                <div class="col-xs-12 col-sm-4 col-md-3" style="padding:30px;text-align:center;">
                    <span class="text-danger" style="font-size:50px;">{{$paper['question_total'] - $paper['question_correct']}}<i style="font-size:11px;">道</i></span><br />
                    答错
                </div>
            </div>
            @endif

            @foreach($content as $key => $value)
            @if($value['correct'])
            <div class="row bg-success" style="margin-bottom:20px;">
            @else
            <div class="row bg-danger" style="margin-bottom:20px;">
            @endif
                <div class="col-xs-12">
                    <div style="float:left">{{$key + 1}}、</div>
                    <div> {{ strip_tags($value['problem']) }}</div>
                </div>

                @if($value['type'] == 1 || $value['type'] == 2)
                <div class="col-xs-1">选项：</div>
                <div class="col-xs-11">
                    @foreach($value['answer'] as $k2 => $v2)
                    <div style="margin-bottom:10px">
                        <div style="float:left;margin-right:10px">序号{{$k2}}: </div>
                        <div>{!! strip_tags($v2) !!}</div>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($value['type'] == 1)
                <div class="col-xs-12">
                    答案: {{$value['solution']}}
                </div>
                @elseif($value['type'] == 2)
                <div class="col-xs-12">
                    答案: @foreach($value['solution'] as $v2) {{$v2}}、 @endforeach
                </div>
                @elseif($value['type'] == 3)
                <div class="col-xs-12">
                    答案: @if($value['solution']) 正确 @else 错误@endif
                </div>
                @endif

                @if($value['type'] == 1)
                <div class="col-xs-12 bg-primary">
                    作答: {{$value['reply']}}
                </div>
                @elseif($value['type'] == 2)
                <div class="col-xs-12 bg-primary">
                    作答: @foreach($value['reply'] as $v2) {{$v2}}、 @endforeach
                </div>
                @elseif($value['type'] == 3)
                <div class="col-xs-12 bg-primary">
                    作答: @if($value['reply']) 正确 @else 错误@endif
                </div>
                @endif

            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

