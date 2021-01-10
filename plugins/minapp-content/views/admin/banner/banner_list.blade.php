@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li @if($type=='step') class="active" @endif><a
                                href="{{yzWebUrl('plugin.minapp-content.admin.sport-clock.step')}}">轮播图列表</a></li>
                    <li @if($type=='step_exchange_list') class="active" @endif><a
                                href="{{yzWebUrl('plugin.minapp-content.admin.sport-clock.step-exchange-list')}}">轮播位</a>
                    </li>
                </ul>
            </div>

            <div class="panel-body">
                <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                            <select name='search[status]' class='form-control'>
                                <option value='0' @if($request['search']['status']==0) selected @endif>选择快评类型
                                </option>
                                <option value='1' @if($request['search']['status']==1) selected @endif>穴位快评
                                </option>
                                <option value='3' @if($request['search']['status']==3) selected @endif>文章快评
                                </option>
                                <option value='4' @if($request['search']['status']==4) selected @endif>帖子快评
                                </option>
                                <option value='5' @if($request['search']['status']==5) selected @endif>课程快评
                                </option>
                                <option value='6' @if($request['search']['status']==6) selected @endif>灸师快评
                                </option>
                                <option value='7' @if($request['search']['status']==7) selected @endif>直播快捷消息
                                </option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                            <input type="text" class="form-control" name="search[content]"
                                   value="{{$request['search']['content']}}" placeholder="请输入快评内容"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>
                </form>
                <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <a href="{{ yzWebUrl('plugin.minapp-content.admin.quick-comment.edit') }}" class="btn btn-info">添加快捷评语</a>
                </div>
            </div>

            <div class="panel panel-defualt">
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>快评类型</th>
                            <th>快评内容</th>
                            <th>是否开启</th>
                            <th>添加时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($comments as $k => $value)
                            <tr>
                                <td>{{$value['id']}}</td>
                                <td>
                                    @if ($value['type'] == 1)
                                        <span class="btn btn-white"> 穴位快评</span>
                                    @elseif ($value['type'] == 2)
                                        <span class="btn btn-white"> -- </span>
                                    @elseif ($value['type'] == 3)
                                        <span class="btn btn-white"> 文章快评</span>
                                    @elseif ($value['type'] == 4)
                                        <span class="btn btn-white"> 帖子快评</span>
                                    @elseif ($value['type'] == 5)
                                        <span class="btn btn-white"> 课程快评</span>
                                    @elseif ($value['type'] == 6)
                                        <span class="btn btn-white"> 灸师快评</span>
                                    @elseif ($value['type'] == 7)
                                        <span class="btn btn-white"> 直播快捷消息</span>
                                    @else
                                        未知
                                    @endif
                                </td>
                                <td>
                                    {{ $value['content'] }}
                                </td>
                                <td>
                                    <a class="update-status"
                                       href="{{ yzWebUrl('plugin.minapp-content.admin.quick-comment.display', ['id' => $value['id']]) }}"
                                       data-status="{{ $value['status'] }}">
                                        @if ($value['status'] == 1)
                                            <span class="label label-primary">开启</span>
                                        @else
                                            <span class="label label-default">关闭</span>
                                        @endif
                                    </a>
                                </td>
                                <td>
                                    {{ date('Y-m-d H:i:s', $value['create_time']) }}
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
