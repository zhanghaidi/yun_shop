@extends('layouts.base')
@section('title', trans('等级详情'))
@section('content')
    <div class="w1200 ">
        <div class=" rightlist ">
            <div class="right-addbox">
                <form action="{!! yzWebUrl('plugin.nominate.admin.level.sub') !!}" method="post" class="form-horizontal form">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group"></div>
                            <div class="form-group">
                                <input type='hidden' name='levelData[level_id]' value="{{$level->id}}"/>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                    等级权重
                                </label>
                                <div class="col-sm-6 col-xs-6">
                                    <input type='text' disabled class="form-control" value="{{$level->level?:0}}"/>
                                    {{--<span class="help-block">等级权重一定要设置且不能重复。</span>--}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                    等级名称
                                </label>
                                <div class="col-sm-6 col-xs-6">
                                    <input type='text' disabled class="form-control" value="{{$level->level_name}}"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                    {{$set['nominate_prize_name']?:'直推奖'}}
                                </label>
                                <div class="col-sm-6 col-xs-6">
                                    <div class='input-group'>
                                        <input type='text' onkeyup="this.value= this.value.match(/\d+(\.\d{0,2})?/) ? this.value.match(/\d+(\.\d{0,2})?/)[0] : ''" name='levelData[nominate_prize]' class="form-control"
                                               value="{!! $nominateLevel->nominate_prize?:0 !!}"/>
                                        <div class='input-group-addon waytxt'>元</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                    {{$set['nominate_prize_name']?:'团队奖'}}
                                </label>
                                <div class="col-sm-6 col-xs-6">
                                    <div class='input-group'>
                                        <input type='text' onkeyup="this.value= this.value.match(/\d+(\.\d{0,2})?/) ? this.value.match(/\d+(\.\d{0,2})?/)[0] : ''" name='levelData[team_prize]' class="form-control"
                                               value="{!! $nominateLevel->team_prize?:0 !!}"/>
                                        <div class='input-group-addon waytxt'>元</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                    {{$set['nominate_prize_name']?:'团队业绩奖'}}
                                </label>
                                <div class="col-sm-6 col-xs-6">
                                    <div class='input-group'>
                                        <input type='text' onkeyup="this.value= this.value.match(/\d+(\.\d{0,2})?/) ? this.value.match(/\d+(\.\d{0,2})?/)[0] : ''" name='levelData[team_manage_prize]' class="form-control"
                                               value="{!! $nominateLevel->team_manage_prize?:0 !!}"/>
                                        <div class='input-group-addon waytxt'>%</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                    推荐任务
                                </label>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="upgrade-group">

                                        @foreach($task as $key => $item)
                                            <div class='input-group'>
                                                <select name='levelData[task][{{$key}}][level_id]' class="form-control" style="width:25%;">
                                                    @foreach($levelList as $level)
                                                        <option value='{{$level->id}}'
                                                            @if($level->id == $item['level_id'])
                                                            selected @endif >
                                                            <span class="input-group-addon">
                                                                {{$level->level_name}}
                                                            </span>
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <input type="text" name="levelData[task][{{$key}}][member_num]" class="form-control" style="width:25%;"
                                                       value="{{$item['member_num']}}">

                                                <select name='levelData[task][{{$key}}][code]' class="form-control" style="width:25%;">
                                                    <option value='1'
                                                            @if(1 == $item['code'])
                                                            selected @endif >
                                                    <span class="input-group-addon">
                                                        奖励天数
                                                    </span>
                                                    </option>
                                                    <option value='2'
                                                            @if(2 == $item['code'])
                                                            selected @endif >
                                                    <span class="input-group-addon">
                                                        奖励余额
                                                    </span>
                                                    </option>
                                                </select>

                                                <input type="text" name="levelData[task][{{$key}}][amount]"  class="form-control" style="width:25%;"
                                                       value="{{$item['amount']}}">

                                                <div class="input-group-addon del-task" title="删除"><i class="fa fa-trash"></i></div>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-xs-6">
                                    <span class='help-block'><input type="button" value="添加任务" class="btn btn-success add-task"/></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="submit" name="submit" value="提交" class="btn btn-success">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script  language='javascript'>
        var i = "{{ count($task) }}";
        $('.add-task').click(function () {
            var html = '';

            html += '<div class="input-group">';
            html += '<select name="levelData[task]['+ i +'][level_id]" class="form-control" style="width:25%;">';
            html += '@foreach($levelList as $level)';
            html += '<option value="{{$level->id}}"';
            html += '<span class="input-group-addon">';
            html += '{{$level->level_name}}';
            html += '</span>';
            html += '</option>';
            html += '@endforeach';
            html += '</select>';

            html += '<input type="text" name="levelData[task]['+ i +'][member_num]" class="form-control" style="width:25%;"';
            html += 'class="chk_income"';
            html += 'value=""/>';

            html += '<select name="levelData[task]['+ i +'][code]" class="form-control" style="width:25%;">';
            html += '<option value="1"';
            html += '<span class="input-group-addon">';
            html += '奖励天数';
            html += '</span>';
            html += '</option>';
            html += '<option value="2"';
            html += '<span class="input-group-addon">';
            html += '奖励余额';
            html += '</span>';
            html += '</option>';
            html += '</select>';

            html += '<input type="text" name="levelData[task]['+ i +'][amount]" class="form-control" style="width:25%;"';
            html += 'class="chk_income"';
            html += 'value=""/>';

            html += '<div class="input-group-addon del-task" title="删除" ><i class="fa fa-trash"></i></div>';
            html += '</div>';

            $('.upgrade-group').append(html);
            i = parseInt(i) + parseInt(1);
        });

        $(document).on('click', '.del-task', function () {
            var _this = $(this);
            _this.parent('.input-group').remove();
        });
        $(document).on('keyup', '.chk_income', function () {
            var _this = $(this);
            var _val = _this.val();
            _this.val(_val.replace(/\D/g, ''));
        });
    </script>
@endsection