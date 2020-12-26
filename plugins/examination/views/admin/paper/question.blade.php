@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
            <form id="form1" role="form" class="form-horizontal form" method="post" action="">
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">分类:</div>
                        <select class="form-control" name="search[sort_id]">
                            <option value="">全部</option>
                            @foreach($sort as $v1)
                                <option value="{{$v1['id']}}" @if($v1['id'] == $search['sort_id']) selected="selected" @endif>{{$v1['name']}}</option>
                                @if($v1['children'])
                                @foreach($v1['children'] as $v2)
                                    <option value="{{$v2['id']}}" @if($v2['id'] == $search['sort_id']) selected="selected" @endif>{{$v2['name']}}</option>
                                    @if($v2['children'])
                                    @foreach($v2['children'] as $v3)
                                        <option value="{{$v3['id']}}" @if($v3['id'] == $search['sort_id']) selected="selected" @endif>{{$v3['name']}}</option>
                                    @endforeach
                                    @endif
                                @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">题型:</div>
                        <select class="form-control" name="search[type]">
                            <option value="">全部</option>
                            <option value="1" @if($search['type'] == 1) selected="selected" @endif>单选题</option>
                            <option value="2" @if($search['type'] == 2) selected="selected" @endif>多选题</option>
                            <option value="3" @if($search['type'] == 3) selected="selected" @endif>判断题</option>
                            <!-- <option value="4" @if($search['type'] == 4) selected="selected" @endif>填空题</option>
                            <option value="5" @if($search['type'] == 5) selected="selected" @endif>问答题</option> -->
                        </select>
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <div class="input-group-addon">题目:</div>
                        <input type="text" placeholder="请输入题目进行模糊搜索" value="{{$search['problem']}}" name="search[problem]" class="form-control">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="input-group">
                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="panel panel-defualt">
        <div class="panel-body">
            <input type="hidden" name="source[paper_id]" value="{{$paper_id}}">
            <table class="table">
                <thead>
                    <tr>
                        <th width="5%"></th>
                        <th width="20%">题目</th>
                        <th width="10%">题型</th>
                        <th width="10%">被引用次数</th>
                        <th width="10%">分类</th>
                        <th width="10%">最后更新时间</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr class="q-{{$value['id']}}">
                        <td>
                            <input type="checkbox" name="manual_id" value="{{$value['id']}}">
                        </td>
                        <td>{{$value['problem']}}</td>
                        <td>
                            @if($value['type'] == 1) 单选 
                            @elseif($value['type'] == 2) 多选 
                            @elseif($value['type'] == 3) 判断
                            @elseif($value['type'] == 4) 填空
                            @elseif($value['type'] == 5) 问答
                            @else 未知
                            @endif
                        </td>
                        <td>{{$value['use_number']}}</td>
                        <td>{{$value['sort_name']}}</td>
                        <td>{{$value['updated_at']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {!! $pager !!}
        <div class="panel-footer">
            <button type="submit" class="btn btn-warning" id="manual_btn">确认选择</button>
        </div>
    </div>
</div>

<script language="JavaScript">
$(function () {
    // 父页面已添加题库问题的处理
    $('.question', parent.document).each(function(){
        _question_id = $(this).find('input[name="data[question_id][]"]').val();
        $('.table tbody tr').each(function(){
            _id = $(this).find('input[name="manual_id"]').val();
            if (_id == undefined) {
                return ;
            }
            if (_id == _question_id) {
                $(this).find('input[name="manual_id"]').attr('disabled','disabled');
            }
        });
    });

    // 确认选择 按钮
    $('#manual_btn').on('click', function(){
        _question_ids = [];
        $('.table tbody tr').each(function(){
            _is_check = $(this).find('input[name="manual_id"]:checked').val();
            if (_is_check == undefined) {
                return ;
            }
            _question_ids.push(_is_check);
            $(this).find('input[name="manual_id"]').attr('disabled','disabled');
        });
        if (_question_ids.length == 0) {
            return ;
        }

        window.parent.window.addQuestion(_question_ids);
    });
});
</script>
@endsection

