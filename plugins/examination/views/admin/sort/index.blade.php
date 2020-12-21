@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">题库分类管理</div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="20%">名称</th>
                        <th width="10%">排序</th>
                        <th width="10%">题目数量</th>
                        <th width="20%">创建时间</th>
                        <th width="10%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td><a href="javascript:void(0);" title="查看子分类">{{$value['name']}}</a></td>
                        <td>{{$value['order']}}</td>
                        <td>{{$value['number']}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>
                            <a class='btn btn-default' href="{{ yzWebUrl('plugin.examination.admin.question.edit', ['id' => $value['id']]) }}"><i class="fa fa-edit"></i></a>

                            <a class='btn btn-default' href="{{ yzWebUrl('plugin.face-analysis.admin.face-analysis-log-manage.del', ['id' => $value['id']]) }}" onclick="return confirm('确认删除该记录吗？');return false;"><i class="fa fa-remove"></i></a>
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
        $('.table tbody tr td a').on('click', function(){
            _id = $(this).parent().prev().html();
            if (_id <= 0) {
                return ;
            }
        });
    });
</script>
@endsection

