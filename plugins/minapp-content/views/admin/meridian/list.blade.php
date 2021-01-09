@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li @if(strpos(\YunShop::request()->route, '.acupoint.') !== false) class="active" @endif><a href="{{yzWebUrl('plugin.minapp-content.admin.acupoint.index')}}">穴位列表</a></li>
                    <li @if(strpos(\YunShop::request()->route, '.meridian.') !== false) class="active" @endif><a href="{{yzWebUrl('plugin.minapp-content.admin.meridian.index')}}">经络列表</a></li>
                </ul>
            </div>

            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                <a href="{{ yzWebUrl('plugin.minapp-content.admin.meridian.edit') }}" class="btn btn-info">添加经络</a>
            </div>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>排序</th>
                        <th>经络全称</th>
                        <th>经络图片</th>
                        <th>经络简称</th>
                        <th>经络类型</th>
                        <th>养生开始时间</th>
                        <th>养生结束时间</th>
                        <th>养生提示</th>
                        <th>详细介绍</th>
                        <th>语音讲解</th>
                        <th>热推</th>
                        <th>穴位表</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['list_order']}}</td>
                        <td>{{$value['discription']}}</td>
                        <td>
                            <a href="{{ tomedia($value['image']) }}" target="_blank">
                                <img src="{{tomedia($value['image'])}}" width="60">
                            </a>
                        </td>
                        <td>{{$value['name']}}</td>
                        <td>
                            @if($value['type_id'] == 1) 十二经脉
                            @elseif($value['type_id'] == 2) 奇经八脉
                            @endif
                        </td>
                        <td>
                            @if($value['start_time']== "00:00:00") N/A
                            @else {{substr($value['start_time'],0,5)}}
                            @endif
                        </td>
                        <td>
                            @if($value['end_time']== "00:00:00") N/A
                            @else {{substr($value['end_time'],0,5)}}
                            @endif
                        </td>
                        <td>{{$value['notice']}}</td>
                        <td>{{$value['content']}}</td>
                        <td>
                        @if($value['audio'])
                            <audio style="width: 100px;height: 50px" controls><source src="{{tomedia($value['audio'])}}"></audio>
                        @endif
                        </td>
                        <td>
                            @if($value['is_hot'] == 0) 否
                            @elseif($value['is_hot'] == 1) 是
                            @endif
                        </td>
                        <td>
                            <a class='btn btn-info' href="{{ yzWebUrl('plugin.minapp-content.admin.meridian.acupoints', ['id' => $value['id']]) }}" title="穴位"><i class="fa fa-table"></i> 所属穴位</a>
                        </td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.meridian.edit', ['id' => $value['id']]) }}" title="编辑"><i class="fa fa-edit"></i></a> &nbsp; 
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.meridian.delete', ['id' => $value['id']]) }}" onclick="return confirm('确认删除该记录吗？');return false;"  title="删除"><i class="fa fa-trash-o"></i></a>
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

