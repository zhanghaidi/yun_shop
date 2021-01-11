@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li @if($type=='banner') class="active" @endif><a
                                href="{{yzWebUrl('plugin.minapp-content.admin.banner.index')}}">轮播图列表</a></li>
                    <li @if($type=='banner_position') class="active" @endif><a
                                href="{{yzWebUrl('plugin.minapp-content.admin.banner-position.index')}}">轮播位</a>
                    </li>
                </ul>
            </div>

            <div class="panel-body">
                <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <a href="{{ yzWebUrl('plugin.minapp-content.admin.banner-position.add') }}" class="btn btn-info">添加轮播位</a>
                </div>
            </div>

            <div class="panel panel-defualt">
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>轮播位名称</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bannerPosition as $k => $value)
                            <tr>
                                <td>{{$value['id']}}</td>
                                <td>{{$value['name']}}</td>
                                <td>
                                    <a href="{{ yzWebUrl('plugin.minapp-content.admin.quick-comment.edit', ['id' => $value['id']]) }}"
                                       title="编辑"><i class="fa fa-edit"></i></a> &nbsp;
                                    <a href="{{ yzWebUrl('plugin.minapp-content.admin.quick-comment.delete', ['id' => $value['id']]) }}"
                                       onclick="return confirm('确认删除该记录吗？');return false;" title="删除"><i
                                                class="fa fa-trash-o"></i></a>
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
<script language="JavaScript">
    require(["{{yz_tomedia('/images/ajy/js/layer/laydate/laydate.js')}}"], function (laydate) {
        laydate.render({
            elem: '#start_time'
            , type: 'time'
            , format: 'HH:mm'
        });
        laydate.render({
            elem: '#end_time'
            , type: 'time'
            , format: 'HH:mm'
        });
    })
</script>

@endsection
