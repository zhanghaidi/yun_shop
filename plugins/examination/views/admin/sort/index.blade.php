@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">
            题库分类管理 <small class="text-success">点击下面列表中分类的名称，可以查看其子分类</small>
            <a href="{{ yzWebUrl('plugin.examination.admin.question-sort.edit') }}" class="pull-right btn btn-sm btn-success">添加分类</a>
        </div>
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
                    <tr class="sort{{$value['id']}} one">
                        <td>{{$value['id']}}</td>
                        <td><a href="javascript:void(0);" title="查看子分类" class="name">{{$value['name']}}</a></td>
                        <td>{{$value['order']}}</td>
                        <td>{{$value['number']}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>
                            <a class='btn btn-success' href="{{ yzWebUrl('plugin.examination.admin.question-sort.edit', ['id' => $value['id']]) }}"><i class="fa fa-edit"></i></a>

                            <a class='btn btn-danger' href="{{ yzWebUrl('plugin.examination.admin.question-sort.del', ['id' => $value['id']]) }}" onclick="return confirm('确认删除该记录吗？');return false;"><i class="fa fa-remove"></i></a>
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
        $('.table').on('click', '.name', function(){
            _id = $(this).parent().prev().html();
            if (_id <= 0) {
                return ;
            }
            _class = $(this).parent().parent().attr('class');
            if (_class.indexOf('click') >= 0) {
                return ;
            }

            if (_class.indexOf('one') >= 0) {
                _class = 'two';
            } else if (_class.indexOf('two') >= 0) {
                _class = 'three';
            }

            _url = '{{ yzWebUrl('plugin.examination.admin.question-sort.index') }}';
            _url = _url.replace(/&amp;/g, '&');
            _url += '&id=' + _id + '&is_ajax=1';

            _editUrl = '{{ yzWebUrl('plugin.examination.admin.question-sort.edit') }}';
            _editUrl = _editUrl.replace(/&amp;/g, '&');

            _delUrl = '{{ yzWebUrl('plugin.examination.admin.question-sort.del') }}';
            _delUrl = _delUrl.replace(/&amp;/g, '&');

            $.get(_url,function(res){
                _childStr = '';
                for (i in res.data) {
                    _childStr += '<tr class="sort' + res.data[i].id + ' ' + _class + '">';
                    _childStr += '<td>' + res.data[i].id + '</td>';
                    _childStr += '<td><a href="javascript:void(0);" title="查看子分类" class="name">';
                    if (_class == 'two') {
                        _childStr += ' &nbsp; |__';
                    } else {
                        _childStr += ' &nbsp; &nbsp; |____ &nbsp; ';
                    }
                    _childStr += res.data[i].name + '</a></td>';
                    _childStr += '<td>' + res.data[i].order + '</td>';
                    _childStr += '<td>' + res.data[i].number + '</td>';
                    _childStr += '<td>' + res.data[i].created_at + '</td>';
                    _childStr += '<td><a class="btn btn-success" href="' + _editUrl +'&id=' + res.data[i].id + '"><i class="fa fa-edit"></i></a><a class="btn btn-danger" href="' + _delUrl +'&id=' + res.data[i].id + '"  onclick="return confirm(\'确认删除该记录吗？\');return false;"><i class="fa fa-remove"></i></a></td>';
                    _childStr += '</tr>';
                }
                $('.sort' + _id).after(_childStr);
                $('.sort' + _id).addClass('click');
            });
        });
    });
</script>
@endsection

