@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$data['id']}}">
            <input type="hidden" name="data[type]" value="2">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">题库分类</label>
                <div class="col-sm-9 col-xs-12">
                    <select name="data[sort_id]" class="form-control">
                    @foreach($sort_tree as $v1)
                        <option value="{{$v1['id']}}" @if($data['sort_id'] == $v1['id']) selected="selected" @endif>{{$v1['name']}}</option>
                        @if($v1['children'])
                        @foreach($v1['children'] as $v2)
                            <option value="{{$v2['id']}}" @if($data['sort_id'] == $v2['id']) selected="selected" @endif>{{$v2['name']}}</option>
                            @if($v2['children'])
                            @foreach($v2['children'] as $v3)
                                <option value="{{$v3['id']}}" @if($data['sort_id'] == $v3['id']) selected="selected" @endif>{{$v3['name']}}</option>
                            @endforeach
                            @endif
                        @endforeach
                        @endif
                    @endforeach
                    </select>
                </div>
            </div>

            <hr>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">题目(*)</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                {!! yz_tpl_ueditor('data[problem]', $data['problem']) !!}
                </div>
            </div>

@if($data['id'] > 0)
            @foreach($data['answer'] as $k => $v)
            <div class="form-group options">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">选项<span>{{$v['name']}}</span>(*)</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                {!! yz_tpl_ueditor("data[option$k]", $v['content']) !!}
                </div>
                <div class="col-xs-12 col-sm-1 col-md-1">
                    <button class="btn btn-danger" type="button" onclick="removeAnswerItem(this)"><i class="fa fa-trash"></i></button>
                </div>
                <div class="col-xs-12 col-sm-8 col-md-9 col-sm-offset-3 col-md-offset-2">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="data[answer][]" value="{{$v['name']}}" @if($v['option']) checked @endif /> 设为正确答案
                    </label>
                </div>
            </div>
            @endforeach
@else
            <div class="form-group options">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">选项<span>A</span>(*)</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                {!! yz_tpl_ueditor('data[optionA]') !!}
                </div>
                <div class="col-xs-12 col-sm-1 col-md-1">
                    <button class="btn btn-danger" type="button" onclick="removeAnswerItem(this)"><i class="fa fa-trash"></i></button>
                </div>
                <div class="col-xs-12 col-sm-8 col-md-9 col-sm-offset-3 col-md-offset-2">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="data[answer][]" value="A" /> 设为正确答案
                    </label>
                </div>
            </div>
            <div class="form-group options">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">选项<span>B</span>(*)</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                {!! yz_tpl_ueditor('data[optionB]') !!}
                </div>
                <div class="col-xs-12 col-sm-1 col-md-1">
                    <button class="btn btn-danger" type="button" onclick="removeAnswerItem(this)"><i class="fa fa-trash"></i></button>
                </div>
                <div class="col-xs-12 col-sm-8 col-md-9 col-sm-offset-3 col-md-offset-2">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="data[answer][]" value="B" /> 设为正确答案
                    </label>
                </div>
            </div>
            <div class="form-group options">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">选项<span>C</span>(*)</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                {!! yz_tpl_ueditor('data[optionC]') !!}
                </div>
                <div class="col-xs-12 col-sm-1 col-md-1">
                    <button class="btn btn-danger" type="button" onclick="removeAnswerItem(this)"><i class="fa fa-trash"></i></button>
                </div>
                <div class="col-xs-12 col-sm-8 col-md-9 col-sm-offset-3 col-md-offset-2">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="data[answer][]" value="C" /> 设为正确答案
                    </label>
                </div>
            </div>
            <div class="form-group options">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">选项<span>D</span>(*)</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                {!! yz_tpl_ueditor('data[optionD]', $data['optionD']) !!}
                </div>
                <div class="col-xs-12 col-sm-1 col-md-1">
                    <button class="btn btn-danger" type="button" onclick="removeAnswerItem(this)"><i class="fa fa-trash"></i></button>
                </div>
                <div class="col-xs-12 col-sm-8 col-md-9 col-sm-offset-3 col-md-offset-2">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="data[answer][]" value="D" /> 设为正确答案
                    </label>
                </div>
            </div>
@endif


            <div class="form-group addOptions">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <button type="button" class="btn btn-default" onclick="addAnswerItem()" style="margin-bottom: 5px">
                        <i class="fa fa-plus"></i> 新增选项
                    </button>
                </div>
            </div>

            <hr>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">解析</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                {!! yz_tpl_ueditor('data[explain]', $data['explain']) !!}
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-2">
                        <input type="submit" name="submit" value="提交" class="btn btn-success" onclick="return formcheck()"/>
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>

    <script language="JavaScript">
        function removeAnswerItem(obj) {
            $(obj).closest('.options').remove();
            rearrangeOptions();
        }

        function addAnswerItem() {
            _html = '<div class="form-group options">';
            _html += '<label class="col-xs-12 col-sm-3 col-md-2 control-label">选项<span>X</span>(*)</label>';
            _html += '<div class="col-xs-12 col-sm-8 col-md-9">';
            _html += '<textarea name="data[optionX]" class="form-control" rows="5"></textarea>';
            _html += '</div>';
            _html += '<div class="col-xs-12 col-sm-1 col-md-1">';
            _html += '<button class="btn btn-danger" type="button" onclick="removeAnswerItem(this)"><i class="fa fa-trash"></i></button>';
            _html += '</div>';
            _html += '<div class="col-xs-12 col-sm-8 col-md-9 col-sm-offset-3 col-md-offset-2">';
            _html += '<label class="checkbox-inline">';
            _html += '<input type="checkbox" name="data[answer][]" value="X" /> 设为正确答案';
            _html += '</label></div></div>';
            $('.addOptions').before(_html);

            rearrangeOptions();
        }

        function rearrangeOptions() {
            _name = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            _index = 0;
            $('div.options').each(function(){
                _optionName = _name.charAt(_index);
                $(this).find('label.control-label').find('span').html(_optionName);
                $(this).find('textarea').attr('name', 'data[option' + _optionName + ']');
                $(this).find('input[name="data[answer][]"]').attr('value', _optionName);
                _index += 1;
            });
        }
    </script>
@endsection

