@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<style>
.question {
    border : 1px solid #EEE;
}
.question:hover {
    border : 1px solid #0AC0D2;
}
</style>
<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">试卷编辑</div>
        <div class="panel-body">
        
            <form id="form" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="data[id]" value="{{$data['id']}}">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">试卷名称</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" class="form-control" name="data[name]" value="{{$data['name']}}" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">题目乱序</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                    <label class="radio-inline">
                        <input type="radio" name="data[random_question]" value="1" @if($data['random_question'] == 1) checked @endif /> 启用
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[random_question]" value="0" @if($data['random_question'] == 0) checked @endif /> 关闭
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">随机试卷</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                    <label class="radio-inline">
                        <input type="radio" name="data[random_topic]" value="1" @if($data['random_topic'] == '') checked @endif /> 启用
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="data[random_topic]" value="0" @if($data['random_topic'] == '') checked @endif /> 关闭
                    </label>
                </div>
            </div>

            <div class="form-group random_topic">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">题目乱序</label>
                <div class="col-xs-12 col-sm-8 col-md-9">
                    <div class="input-group">
                        <div class="input-group">
                            <div class="input-group-addon">单选题</div>
                            <input type="text" name="data[random_topic_single]" class="form-control" value="">
                            <div class="input-group-addon">/ <span>2</span></div>
                        </div>
                        <div class="input-group">
                            <div class="input-group-addon">多选题</div>
                            <input type="text" name="data[random_topic_single]" class="form-control" value="">
                            <div class="input-group-addon">/ <span>2</span></div>
                        </div>
                        <div class="input-group">
                            <div class="input-group-addon">判断题</div>
                            <input type="text" name="data[random_topic_single]" class="form-control" value="">
                            <div class="input-group-addon">/ <span>2</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group addQuestion">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <button type="button" class="btn btn-primary" id="manualQuestion" style="margin-bottom: 5px">
                        <i class="fa fa-plus"></i> 手动选题
                    </button>
                    <button type="button" class="btn btn-warning" style="margin-bottom: 5px;margin-left: 20px">
                        <i class="fa fa-plus"></i> 系统抽题
                    </button>
                </div>
            </div>

            <hr>

            @foreach($data['question'] as $value)
            <div class="form-group question">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                    <input type="hidden" name="data[question_id][]" value="{{$value['question_id']}}">
                    <input type="hidden" name="data[order][]" value="{{$value['order']}}">
                </label>
                <div class="col-xs-12 col-sm-7 col-md-8">
                    <div class="input-group">
                        <div class="input-group">
                            <div class="input-group-addon type">
                            @if($value['type'] == 1)
                            单选题
                            @elseif($value['type'] == 2)
                            多选题
                            @elseif($value['type'] == 3)
                            判断题
                            @else
                            未知
                            @endif
                            :</div>
                            <div class="input-group-addon name">{{$value['problem']}}</div>
                        </div>
                        <div class="input-group">
                            <div class="input-group-addon">分值:</div>
                            <input type="number" name="data[score][]" class="form-control" value="{{$value['score']}}">
                            <div class="input-group-addon">分</div>
                        </div>
                        @if($value['type'] == 2)
                        <div class="input-group">
                            <div class="input-group-addon">选项共计:</div>
                            <input type="number" name="question_number" class="form-control" value="{{$value['question_number']}}" disabled="disabled">
                            <div class="input-group-addon">个，正确选项：</div>
                            <input type="number" name="answer_number" class="form-control" value="{{$value['answer_number']}}" disabled="disabled">
                            <div class="input-group-addon">个</div>
                        </div>
                        <div class="input-group">
                            <div class="input-group-addon">漏选分设置:</div>
                            <label class="radio-inline">
                                <input type="radio" name="data[omission_option][o{{$value['question_id']}}]" value="1"@if($value['omission_option'] == 1) checked @endif>
                                漏选则扣X分
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="data[omission_option][o{{$value['question_id']}}]" value="2"@if($value['omission_option'] == 2) checked @endif>
                                漏选时每个选项扣X分
                            </label>
                        </div>
                        <div class="input-group">
                            <div class="input-group-addon">漏选分分值:</div>
                            <input type="number" name="data[omission_score][]" class="form-control" value="{{$value['omission_score']}}">
                            <div class="input-group-addon">分</div>
                        </div>
                        @else
                        <input type="hidden" name="data[omission_option][o{{$value['question_id']}}]" class="form-control" value="0">
                        <input type="hidden" name="data[omission_score][]" class="form-control" value="{{$value['omission_score']}}">
                        @endif
                    </div>
                </div>
                <div class="col-xs-12 col-sm-2 col-md-2 question_operate hide">
                    <button type="button" class="btn btn-xs btn-success" name="op-up" style="margin-bottom: 5px;margin-top: -5px"><i class="fa fa-chevron-up"></i></button> <br />
                    <button type="button" class="btn btn-xs btn-primary" name="op-down" style="margin-bottom: 5px"><i class="fa fa-chevron-down"></i></button> 
                    <a href="{{ yzWebUrl('plugin.examination.admin.question.edit', ['id' => $value['question_id']]) }}" target="_blank" class="btn btn-xs btn-warning" style="margin-bottom:5px"><i class="fa fa-edit"></i></a> <br />
                    <button type="button" class="btn btn-xs btn-danger" name="op-del"><i class="fa fa-trash"></i></button>
                </div>
            </div>
            @endforeach

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

<div class="modal fade" id="manual-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 1280px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    添加题目
                </h4>
            </div>
            <div class="modal-body embed-responsive embed-responsive-16by9" style="padding-bottom:66%;">
                <iframe class="embed-responsive-item" src="about:blank"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<script language="JavaScript">
$(function () {
    $('.random_topic').hide('slow');
    $('input[name="data[random_topic]"]').parents('.form-group').hide('slow');
    $('input[name="data[random_topic]"]').change(function(){
        if ($(this).val() == 1) {
            $('.random_topic').show();
        } else {
            $('.random_topic').hide();
        }
    });

    _manual_url = "{{ yzWebUrl('plugin.examination.admin.question.index', ['source' => 'manual']) }}";
    _manual_url = _manual_url.replace(/&amp;/g, '&');
    $('#manualQuestion').click(function() {
        $('#manual-modal iframe').attr('src', _manual_url);
        $('#manual-modal').modal()
    });

    $('#form').on('mouseover', 'div.question', function(){
        $(this).find('.question_operate').removeClass('hide').addClass('show');
    });
    $('#form').on('mouseout', 'div.question', function(){
        $(this).find('.question_operate').removeClass('show').addClass('hide');
    });

    $('#form').on('click', 'button[name="op-up"]', function() {
        _parent = $(this).parents('.question');
        _prev = _parent.prev();
        _prev_class = _prev.attr('class');
        if (_prev_class == undefined) {
            return ;
        }
        if (_prev_class.indexOf('question') <= 0) {
            return ;
        }

        _prev.fadeOut('fast', function() {
            $(this).before(_parent);
        }).fadeIn();

        setTimeout(() => {
            rearrangeOrder();
        }, 1000);
    });

    $('#form').on('click', 'button[name="op-down"]', function() {
        _parent = $(this).parents('.question');
        _next = _parent.next();
        _next_class = _next.attr('class');
        if (_next_class == undefined) {
            return ;
        }
        if (_next_class.indexOf('question') <= 0) {
            return ;
        }

        _next.fadeOut('fast', function() {
            $(this).after(_parent);
        }).fadeIn();

        setTimeout(() => {
            rearrangeOrder();
        }, 1000);
    });

    $('#form').on('click', 'button[name="op-del"]', function() {
        _parent = $(this).parents('.question');
        _parent.remove();

        rearrangeOrder();
    });
});

function addQuestion(question_ids) {
    $('#manual-modal iframe').attr('src', "about:blank");
    $('#manual-modal').modal('hide');

    _question_ids = question_ids.join(',');
    
    _question_add_url = "{{ yzWebUrl('plugin.examination.admin.paper.add-question') }}";
    _question_add_url = _question_add_url.replace(/&amp;/g, '&');
    _question_add_url += '&id=' + _question_ids;
    $.get(_question_add_url, function(res){
        _question_edit_url = "{{ yzWebUrl('plugin.examination.admin.question.edit') }}";
        _question_edit_url = _question_edit_url.replace(/&amp;/g, '&');
        _question_str = '';
        for (i in res.data) {
            _question_str += '<div class="form-group question">';
            _question_str += '<label class="col-xs-12 col-sm-3 col-md-2 control-label">';
            _question_str += '<input type="hidden" name="data[question_id][]" value="' + res.data[i].id + '">';
            _question_str += '<input type="hidden" name="data[order][]" value="0">';
            _question_str += '</label>';
            _question_str += '<div class="col-xs-12 col-sm-7 col-md-8">';
            _question_str += '<div class="input-group">';
            _question_str += '<div class="input-group">';
            if (res.data[i].type == 1) {
                _question_str += '<div class="input-group-addon type">单选题:</div>';
            } else if (res.data[i].type == 2) {
                _question_str += '<div class="input-group-addon type">多选题:</div>';
            } else if (res.data[i].type == 3) {
                _question_str += '<div class="input-group-addon type">判断题:</div>';
            } else {
                _question_str += '<div class="input-group-addon type">未知题型，请删除</div>';
            }
            _question_str += '<div class="input-group-addon name">' + res.data[i].problem + '</div>';
            _question_str += '</div>';
            _question_str += '<div class="input-group">';
            _question_str += '<div class="input-group-addon">分值:</div>';
            _question_str += '<input type="text" name="data[score][]" class="form-control" value="">';
            _question_str += '<div class="input-group-addon">分</div>';
            _question_str += '</div>';
            if (res.data[i].type == 2) {
                _question_str += '<div class="input-group">';
                _question_str += '<div class="input-group-addon">选项共计:</div>';
                _question_str += '<input type="number" name="question_number" class="form-control" value="' + res.data[i].question_number + '" disabled="disabled">';
                _question_str += '<div class="input-group-addon">个，正确选项：</div>';
                _question_str += '<input type="number" name="answer_number" class="form-control" value="' + res.data[i].answer_number + '" disabled="disabled">';
                _question_str += '<div class="input-group-addon">个</div>';
                _question_str += '</div>';
                _question_str += '<div class="input-group">';
                _question_str += '<div class="input-group-addon">漏选分设置:</div>';
                _question_str += '<label class="radio-inline">';
                _question_str += '<input type="radio" name="data[omission_option][o' + res.data[i].id + ']" value="1"> 漏选则扣X分';
                _question_str += '</label>';
                _question_str += '<label class="radio-inline">';
                _question_str += '<input type="radio" name="data[omission_option][o' + res.data[i].id + ']" value="2"> 漏选时每个选项扣X分';
                _question_str += '</label>';
                _question_str += '</div>';
                _question_str += '<div class="input-group">';
                _question_str += '<div class="input-group-addon">漏选分分值:</div>';
                _question_str += '<input type="number" name="data[omission_score][]" class="form-control" value="0">';
                _question_str += '<div class="input-group-addon">分</div>';
                _question_str += '</div>';
            } else {
                _question_str += '<input type="hidden" name="data[omission_option][o' + res.data[i].id + ']" class="form-control" value="0">';
                _question_str += '<input type="hidden" name="data[omission_score][]" class="form-control" value="0">';
            }
            _question_str += '</div>';
            _question_str += '</div>';
            _question_str += '<div class="col-xs-12 col-sm-2 col-md-2 question_operate hide">';
            _question_str += '<button type="button" class="btn btn-xs btn-success" name="op-up" style="margin-bottom: 5px;margin-top: -5px"><i class="fa fa-chevron-up"></i></button> <br />';
            _question_str += '<button type="button" class="btn btn-xs btn-primary" name="op-down" style="margin-bottom: 5px"><i class="fa fa-chevron-down"></i></button> ';
            _question_str += '<a href="' + _question_edit_url + '&id=' +  res.data[i].id + '" target="_blank" class="btn btn-xs btn-warning" style="margin-bottom:5px"><i class="fa fa-edit"></i></a> <br />';
            _question_str += '<button type="button" class="btn btn-xs btn-danger" name="op-del"><i class="fa fa-trash"></i></button>';
            _question_str += '</div>';
            _question_str += '</div>';
        }

        _submit = $('input[name="submit"]').parent().parent();
        _submit.before(_question_str);

        rearrangeOrder();
    });
}

function rearrangeOrder() {
    _order = 1;
    $('div.question').each(function(){
        $(this).find('input[name="data[order][]"]').val(_order);
        _order += 1;
    });
}
</script>
@endsection

