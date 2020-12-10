@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li @if(\YunShop::request()->route == 'plugin.face-analysis.admin.face-analysis-set.index') class="active" @endif><a href="{{yzWebUrl('plugin.face-analysis.admin.face-analysis-set.index')}}">基础设置</a></li>
                    <li @if(\YunShop::request()->route == 'plugin.face-analysis.admin.face-analysis-set.share') class="active" @endif><a href="{{yzWebUrl('plugin.face-analysis.admin.face-analysis-set.share')}}">分享设置</a></li>
                </ul>
            </div>

            <form id="setform" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div><b>积分花费设置:</b></div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分花费是否开启</label>
                <div class="col-sm-9 col-xs-12">
                    <label class="radio-inline">
                        <input type="radio" name="setdata[consume_status]" value="1" @if($set['consume_status'] == 1) checked="checked" @endif /> 开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="setdata[consume_status]" value="0" @if($set['consume_status'] == 0) checked="checked" @endif /> 关闭
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分花费控制</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="input-group">
                        <div class="input-group">
                            <div class="input-group-addon" style="width: 100px">每用户，使用本服务的 前</div>
                            <input type="text" name="setdata[consume_frequency]" class="form-control" value="{{$set['consume_frequency']}}" placeholder="请输入大于等于0的整数">
                            <div class="input-group-addon">次，花费</div>
                            <input type="text" name="setdata[consume_number]" class="form-control" value="{{$set['consume_number']}}" placeholder="请输入大于等于0的整数">
                            <div class="input-group-addon">积分</div>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-group">
                            <div class="input-group-addon" style="width: 100px">以后使用，花费</div>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[consume_type]" value="1" @if($set['consume_type'] == 1) checked="checked" @endif /> 与颜值相等的
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[consume_type]" value="2" @if($set['consume_type'] == 2) checked="checked" @endif /> 固定的
                            </label>
                            <div class="input-group-addon">积分</div>
                        </div>
                    </div>
                    <div class="input-group consume-surplus">
                        <div class="input-group">
                            <div class="input-group-addon" style="width: 100px">以后使用，花费</div>
                            <input type="text" name="setdata[consume_surplus]" class="form-control" value="{{$set['consume_surplus']}}" placeholder="请输入大于等于0的整数">
                            <div class="input-group-addon">积分</div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            <div><b>积分赠送设置:</b></div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分赠送是否开启</label>
                <div class="col-sm-9 col-xs-12">
                    <label class="radio-inline">
                        <input type="radio" name="setdata[gain_status]" value="1" @if($set['gain_status'] == 1) checked="checked" @endif /> 开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="setdata[gain_status]" value="0" @if($set['gain_status'] == 0) checked="checked" @endif /> 关闭
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">赠送积分控制</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="input-group">
                        <div class="input-group">
                            <div class="input-group-addon" style="width: 100px">每用户，使用本服务的 前</div>
                            <input type="text" name="setdata[gain_frequency]" class="form-control" value="{{$set['gain_frequency']}}" placeholder="请输入大于等于0的整数">
                            <div class="input-group-addon">次，赠送积分</div>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-group">
                            <div class="input-group-addon" style="width: 100px">使用本服务的前X次，赠送</div>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[gain_type]" value="1" @if($set['gain_type'] == 1) checked="checked" @endif /> 与颜值相等的
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[gain_type]" value="2" @if($set['gain_type'] == 2) checked="checked" @endif /> 固定的
                            </label>
                            <div class="input-group-addon">积分</div>
                        </div>
                    </div>
                    <div class="input-group gain-number">
                        <div class="input-group">
                            <div class="input-group-addon" style="width: 100px">使用本服务的前X次，赠送</div>
                            <input type="text" name="setdata[gain_number]" class="form-control" value="{{$set['gain_number']}}" placeholder="请输入大于等于0的整数">
                            <div class="input-group-addon">积分</div>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-group">
                            <div class="input-group-addon" style="width: 100px">以后使用，赠送</div>
                            <input type="text" name="setdata[gain_surplus]" class="form-control" value="{{$set['gain_surplus']}}" placeholder="请输入大于等于0的整数">
                            <div class="input-group-addon">积分</div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            <div><b>用户控制:</b></div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">手机号码</label>
                <div class="col-sm-9 col-xs-12">
                    <label class="radio-inline">
                        <input type="radio" name="setdata[need_phone]" value="1" @if($set['need_phone'] == 1) checked="checked" @endif /> 必须提供手机号
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="setdata[need_phone]" value="0" @if($set['need_phone'] == 0) checked="checked" @endif /> 无需手机号
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">检测频率</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="input-group">
                        <div class="input-group">
                            <div class="input-group-addon">每用户，在</div>
                            <input type="text" name="setdata[frequency][time]" class="form-control" value="{{$set['frequency']['time']}}" placeholder="请输入大于等于0的整数">
                            <div class="input-group-addon">分钟内，可检测</div>
                            <input type="text" name="setdata[frequency][number]" class="form-control" value="{{$set['frequency']['number']}}" placeholder="请输入大于等于0的整数">
                            <div class="input-group-addon">次</div>
                        </div>
                        <span class="help-block">任一数值设置为0，则代表不限制频率</span>
                    </div>
                </div>
            </div>

            <hr>
            <div><b>排行榜控制:</b></div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启</label>
                <div class="col-sm-9 col-xs-12">
                    <label class="radio-inline">
                        <input type="radio" name="setdata[ranking_status]" value="1" @if($set['ranking_status'] >= 1) checked="checked" @endif /> 开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="setdata[ranking_status]" value="0" @if($set['ranking_status'] == 0) checked="checked" @endif /> 关闭
                    </label>
                        <span class="help-block">变更排行榜是否开启状态，将重新开始计算排行数据！！！</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">性别排行榜</label>
                <div class="col-sm-9 col-xs-12">
                    <label class="radio-inline">
                        <input type="radio" name="setdata[sex_ranking]" value="1" @if($set['sex_ranking'] == 1) checked="checked" @endif /> 开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="setdata[sex_ranking]" value="0" @if($set['sex_ranking'] == 0) checked="checked" @endif /> 关闭
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">年龄排行榜</label>
                <div class="col-sm-9 col-xs-12">
                    <label class="radio-inline">
                        <input type="radio" name="setdata[age_ranking][sex]" value="1" @if($set['age_ranking']['sex'] == 1) checked="checked" @endif /> 区分性别
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="setdata[age_ranking][sex]" value="0" @if($set['age_ranking']['sex'] == 0) checked="checked" @endif /> 不区分性别
                    </label>

                    <div class="ranking-items">
                        @foreach($set['age_ranking']['start'] as $k => $v)
                        <div class="input-group ranking-item" style="margin-top: 10px;">
                            <span class="input-group-addon">开始年龄</span>
                            <input type="text" class="form-control" name="setdata[age_ranking][start][]" value="{{$v}}" />
                            <span class="input-group-addon">岁，结束年龄</span>
                            <input type="text" class="form-control" name="setdata[age_ranking][end][]" value="{{$set['age_ranking']['end'][$k]}}" />
                            <span class="input-group-addon">岁</span>
                            <div class="input-group-btn">
                                <button class="btn btn-danger" type="button" onclick="removeRankingItem(this)"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <h4>
                        <button type="button" class="btn btn-default" onclick="addRankingItem()" style="margin-bottom: 5px">
                            <i class="fa fa-plus"></i> 添加年龄排行规则
                        </button>
                        <span class="help-block">活动开始后，变更排行榜年龄设置，可能对排行榜数据造成影响，请慎重</span>
                    </h4>
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
        $(function () {
            _consume_type = $('input[name="setdata[consume_type]"]:checked').val();
            if (_consume_type != 2) {
                $('.consume-surplus').hide();
            }
            $('input[name="setdata[consume_type]"]').change(function(){
                if ($(this).val() == 2) {
                    $('.consume-surplus').show();
                } else {
                    $('.consume-surplus').hide();
                }
            });

            _gain_type = $('input[name="setdata[gain_type]"]:checked').val();
            if (_gain_type != 2) {
                $('.gain-number').hide();
            }
            $('input[name="setdata[gain_type]"]').change(function(){
                if ($(this).val() == 2) {
                    $('.gain-number').show();
                } else {
                    $('.gain-number').hide();
                }
            });
        });

        function removeRankingItem(obj) {
            $(obj).closest('.ranking-item').remove();
        }

        function addRankingItem() {
            _html = '<div class="input-group ranking-item" style="margin-top: 10px;">';
            _html += '<span class="input-group-addon">开始年龄</span>';
            _html += '<input type="text" class="form-control" name="setdata[age_ranking][start][]" value="" />';
            _html += '<span class="input-group-addon">岁，结束年龄</span>';
            _html += '<input type="text" class="form-control" name="setdata[age_ranking][end][]" value="" />';
            _html += '<span class="input-group-addon">岁</span>';
            _html += '<div class="input-group-btn">';
            _html += '<button class="btn btn-danger" type="button" onclick="removeRankingItem(this)"><i class="fa fa-trash"></i></button>';
            _html += '</div></div>';
            $('.ranking-items').append(_html);
        }
    </script>
@endsection

