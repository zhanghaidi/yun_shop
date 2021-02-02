@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="panel-body">
                <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                            <input type="text" class="form-control" name="search[name]"
                                   value="{{$request['search']['name']}}" placeholder="请输入分享名称关键词进行搜索"/>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>
                </form>
                <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <a href="{{ yzWebUrl('plugin.minapp-content.admin.custom-share.edit') }}" class="btn btn-info">添加自定义分享</a>
                </div>
            </div>

            <div class="panel panel-defualt">
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>分享名称</th>
                            <th>分享标识</th>
                            <th>分享标题</th>
                            <th>分享图片</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($shares as $k => $value)
                            <tr>
                                <td>{{$value['id']}}</td>
                                <td>
                                    {{ $value['name'] }}
                                </td>
                                <td>
                                    {{ $value['key'] }}
                                </td>
                                <td>
                                    {{ $value['title'] }}
                                </td>
                                <td>
                                    <a href="{{ tomedia($value['image']) }}" target="_blank">
                                        <img src="{{tomedia($value['image'])}}" width="60">
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ yzWebUrl('plugin.minapp-content.admin.custom-share.edit', ['id' => $value['id']]) }}"
                                       title="编辑"><i class="fa fa-edit"></i></a> &nbsp;
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

</script>

@endsection
