@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li @if($type=='banner') class="active" @endif><a
                                href="{{yzWebUrl('plugin.minapp-content.admin.sport-clock.step')}}">轮播图列表</a></li>
                    <li @if($type=='banner_position') class="active" @endif><a
                                href="{{yzWebUrl('plugin.minapp-content.admin.sport-clock.step-exchange-list')}}">轮播位</a>
                    </li>
                </ul>
            </div>

            <div class="panel-body">
                <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <a href="{{ yzWebUrl('plugin.minapp-content.admin.banner.add') }}" class="btn btn-info">添加轮播图</a>
                </div>
            </div>

            <div class="panel panel-defualt">
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>排序</th>
                            <th>轮播图位置</th>
                            <th>轮播图标题</th>
                            <th>轮播图</th>
                            <th>是否外链</th>
                            <th>跳转地址</th>
                            <th>跳转类型</th>
                            <th>显示状态</th>
                            <th>模板类型</th>
                            <th>添加时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($banner as $k => $value)
                            <tr>
                                <td>{{$value['list_order']}}</td>
                                <td>{{$value['name']}}</td>
                                <td>{{$value['title']}}</td>
                                <td>
                                    <a href="{{ tomedia($value['image']) }}" target="_blank">
                                        <img src="{{tomedia($value['image'])}}" width="60">
                                    </a>
                                </td>
                                <td>
                                    @if ($value['is_href'] == 0)
                                        否
                                    @elseif ($value['type'] == 1)
                                        是
                                    @else
                                        未知
                                    @endif
                                </td>
                                <td>{{ $value['jumpurl'] }}</td>
                                <td>
                                    @if ($value['jumptype'] == 1)
                                        普通页面
                                    @elseif ($value['jumptype'] == 2)
                                        底部导航
                                    @else
                                        未知
                                    @endif
                                </td>
                                <td>
                                    <a class="update-status"
                                       href="{{ yzWebUrl('plugin.minapp-content.admin.banner.display', ['id' => $value['id']]) }}"
                                       data-status="{{ $value['status'] }}">
                                        @if ($value['status'] == 1)
                                            <span class="label label-primary">显示</span>
                                        @else
                                            <span class="label label-default">隐藏</span>
                                        @endif
                                    </a>
                                </td>
                                <td>
                                    @if ($value['type'] == 1)
                                        banner轮播图
                                    @elseif ($value['type'] == 2)
                                        功能区导航
                                    @else
                                        未知
                                    @endif
                                </td>
                                <td>
                                    {{ date('Y-m-d H:i:s', $value['add_time']) }}
                                </td>
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
    //显示隐藏
    $('.update-status').click(function (e) {
        e.preventDefault();
        var _this = $(this);
        var url = _this.attr('href');
        var status = _this.attr('data-status')
        var label = _this.find('.label')
        var icon1 = '开启';
        var icon2 = '关闭';
        var span = _this.find('.btn');
        $.getJSON(url, {status: status}, function (data) {
            if (data.errno == 0) {
                if (label.hasClass('label-primary')) {
                    label
                        .removeClass('label-primary')
                        .addClass('label-default')
                        .text(icon2)
                    _this.attr('data-status', 0)
                    util.message(data.msg, '', 'error');
                } else {
                    label
                        .removeClass('label-default')
                        .addClass('label-primary')
                        .text(icon1)
                    _this.attr('data-status', 1)
                    util.message(data.msg, '', 'success');
                }
            } else {
                util.message(data.msg, '', 'error');
            }
        });
    });
</script>

@endsection
