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
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-heading">{{$info['discription']}} - 穴位列表</div>
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>排序</th>
                        <th>首字母</th>
                        <th>穴位图</th>
                        <th>穴位名</th>
                        <th>编辑</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>
                            <input type='text' value="{{$value['sort']}}" name="sort[{{$value['id']}}]" class="form-control " style="width: 80px"/>
                        </td>
                        <td>{{$value['chart']}}</td>
                        <td>
                            <a href="{{ tomedia($value['image']) }}" target="_blank">
                                <img src="{{tomedia($value['image'])}}" width="50">
                            </a>
                        </td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.acupoint.edit', ['id' => $value['acupoint_id']]) }}">{{$value['name']}}</a>
                        </td>
                        <td>
                            <a href="{{ yzWebUrl('plugin.minapp-content.admin.acupoint.edit', ['id' => $value['acupoint_id']]) }}" title="编辑"><i class="fa fa-pencil-square-o"></i></a>
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

