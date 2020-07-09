@extends('layouts.base')
@section('title', '定时充值')
@section('content')

    <div class="rightlist">
        <form action="" method="post"
              class="form-horizontal form" enctype="multipart/form-data" id="form1">
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">定时充值</a></li>
                </ul>
            </div>
            <div class="panel panel-default">


                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ $rechargeMenu['name'] }}</label>
                        <div class="col-sm-9 col-xs-12">
                            <img src='{{ $memberInfo['avatar'] }}'
                                 style='width:100px;height:100px;padding:1px;border:1px solid #ccc'/>
                            {{ $memberInfo['nickname'] }}
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ $rechargeMenu['profile'] }}</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static">
                                姓名: {{ $memberInfo['realname'] or $memberInfo['nickname'] }} /
                                手机号: {{ $memberInfo['mobile'] }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ $rechargeMenu['old_value'] }}</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static">{{ $memberInfo->love->usable or '0.00'}}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ $rechargeMenu['change_value'] }}</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="change_value" class="form-control" value=""/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">定时充规则</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="timing-rule">
                                <div class="input-group">
                                    <div class="input-group-addon">天数</div>
                                    <input type="text" name="timing[{{$rule_num}}][timing_days]"
                                           class="form-control" value=""/>
                                    <div class="input-group-addon">天 充值比例</div>

                                    <input type="text" name="timing[{{$rule_num}}][timing_rate]"
                                           class="form-control" value=""/>
                                    <div class="input-group-addon">%</div>

                                    <div class="input-group-addon del-rule" title="删除">
                                        <i class="fa fa-trash"></i>
                                    </div>
                                </div>
                            </div>
                            <span class='help-block'></span>
                            <span class='help-block'>
                                <input type="button" value="添加定时充规则" class="btn btn-success add-rule"/>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="hidden" name="member_id" value="{{ $memberInfo['uid'] }}"/>
                            <input name="submit" type="submit" value="充 值" class="btn btn-success span2"
                                   onclick="return confirm('确认充值？');return false;">
                        </div>
                    </div>

                </div>
            </div>

        </form>
    </div>
    <script type="text/javascript">
        var i = "{{$rule_num + 1}}";
        $('.add-rule').click(function () {
            var html = '';

            html += '<div class="input-group">';
            html += '<div class="input-group-addon">天数</div>';
            html += '<input type="text" name="timing[' + i + '][timing_days]" class="form-control" value=""/>';
            html += '<div class="input-group-addon">天 充值比例</div>';

            html += '<input type="text" name="timing[' + i + '][timing_rate]" class="form-control" value=""/>';
            html += '<div class="input-group-addon">%</div>';

            html += '<div class="input-group-addon del-rule" title="删除">';
            html += '<i class="fa fa-trash"></i>';
            html += '</div>';
            html += '</div>';

            $('.timing-rule').append(html);
            i = parseInt(i) + parseInt(1);
        });

        $(document).on('click', '.del-rule', function () {
            var _this = $(this);
            _this.parent('.input-group').remove();
        });
    </script>
@endsection