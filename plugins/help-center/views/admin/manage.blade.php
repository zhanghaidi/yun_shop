@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

    {{--<script type="text/javascript">--}}
        {{--window.optionchanged = false;--}}
        {{--require(['bootstrap'], function () {--}}
            {{--$('#myTab a').click(function (e) {--}}
                {{--e.preventDefault();--}}
                {{--$(this).tab('show');--}}
            {{--})--}}
        {{--});--}}
    {{--</script>--}}

        <form id="setform" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

                    <div><b>帮助管理:</b></div><hr>

                    <div>
                        <a class="btn btn-default" href="{{ $addurl }}" role="button">添加帮助</a>
                    </div>
                    <br>
                    <div class="col-sm-2"><span>帮助列表</span></div>
                    <br><br>
        </form>

        <div class='panel panel-default'>
            <div class='panel-body'>
                <table class="table">
                    <thead>
                        <tr >
                            <th width="10%">ID</th>
                            <th width="10%">排序</th>
                            <th width="40%">标题</th>
                            <th width="30%">发布时间</th>
                            <th width="10%">编辑</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $value)
                        <tr style="height: 45px">
                            <td width="10%">{{ $value['id'] }}</td>
                            <td width="10%">{{ $value['sort'] }}</td>
                            <td width="40%">{{ $value['title'] }}</td>
                            <td width="30%">{{ $value['created_at'] }}</td>
                            <td width="10%">
                                <a class='btn btn-default' href="{{ yzWebUrl('plugin.help-center.admin.help-center-add.edit', ['id' => $value['id']]) }}"><i class="fa fa-edit"></i></a>
                                <a class='btn btn-default' href="{{ yzWebUrl('plugin.help-center.admin.help-center-add.del', ['id' => $value['id']]) }}" onclick="return confirm('确认删除该角色吗？');return false;"><i class="fa fa-remove"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {!! $pager !!}
        </div>

@endsection

