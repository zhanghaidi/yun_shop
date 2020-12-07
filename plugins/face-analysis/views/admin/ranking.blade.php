@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top">
                <ul class="add-shopnav" id="myTab">
                    @foreach($rank as $value)
                    <li @if($search['type'] == $value['type']) class="active" @endif><a href="{{yzWebUrl('plugin.face-analysis.admin.face-beauty-ranking.index')}}&search[type]={{$value['type']}}">{{$value['name']}} ({{$value['count']}})</a></li>
                    @endforeach
                </ul>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">排行</th>
                        <th width="10%">上传图片</th>
                        <th width="10%">用户昵称 - 手机号</th>
                        <th width="10%">性别</th>
                        <th width="10%">年龄</th>
                        <th width="10%">魅力</th>
                        <th width="10%">检测时间</th>
                        <th width="10%">点赞量</th>
                        <th width="10%">状态</th>
                        <th width="10%">编辑</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr style="height: 90px;">
                        <td>{{$value['ranking']}}</td>
                        <td><a href="{{$value['url']}}" target="_blank"><img src="{{$value['url']}}" height="90"></a></td>
                        <td>{{$value['nickname']}} @if($value['mobile']) - {{$value['mobile']}}@endif</td>
                        <td>@if($value['gender'] == 1) 女 @elseif($value['gender'] == 2) 男 @else 未知 @endif</td>
                        <td>{{$value['age']}}</td>
                        <td>{{$value['beauty']}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>{{$value['like']}}</td>
                        <td>@if($value['status'] == 1) 有效 @elseif($value['status'] == 2) 无效 @else 未知 @endif</td>
                        <td>
                        @if($value['status'] == 1)
                            <a class="btn btn-default" href="javascript:void(0)" data-id="{{$value['id']}}" data-switch="hide"><i class="fa fa-eye-slash"></i> 隐藏</a>
                        @elseif($value['status'] == 2) 
                            <a class="btn btn-default" href="javascript:void(0)" data-id="{{$value['id']}}" data-switch="show"><i class="fa fa-eye"></i> 显示</a>
                        @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {!! $pager !!}
    </div>
</div>

<script language="JavaScript">
    $(function () {
        $('.table a.btn-default').on('click', function(){
            _url = "{!! yzWebUrl('plugin.face-analysis.admin.face-beauty-ranking.status') !!}";
            _id = $(this).data('id');
            _switch = $(this).data('switch');
            if (_id <= 0) {
                return;
            }

            _url += '&id=' + _id;
            $.get(_url, function(res){
                console.log(res.data);
            });

            _switch = _switch == 'hide' ? 'show' : 'hide';
            $(this).data('switch', _switch);
            if (_switch == 'hide') {
                $(this).html('<i class="fa fa-eye-slash"></i> 隐藏');
                $(this).parent().prev('td').html('有效');
            } else {
                $(this).html('<i class="fa fa-eye"></i> 显示');
                $(this).parent().prev('td').html('无效');
            }
        });
    });
</script>
@endsection

