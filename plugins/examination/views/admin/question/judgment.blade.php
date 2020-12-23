@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$data['id']}}">
            <input type="hidden" name="data[type]" value="3">

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

            <div class="form-group options">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">答案(*)</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                    <label class="radio-inline">
                        <input type="radio" name="data[answer]" value="1" @if($data['answer'] == true) checked @endif /> 正确
                    </label>
                </div>
            </div>
            <div class="form-group options">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                    <label class="radio-inline">
                        <input type="radio" name="data[answer]" value="0" @if($data['answer'] == false) checked @endif /> 错误
                    </label>
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
    </script>
@endsection

