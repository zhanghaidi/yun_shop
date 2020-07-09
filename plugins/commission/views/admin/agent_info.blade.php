@extends('layouts.base')

@section('content')
@section('title', trans('分销商详细信息'))
    <div class="w1200 m0a">
        <section class="content">
            <form action="" method='post' class='form-horizontal'>

                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        分销商详细信息
                    </div>
                    <div class='panel-body'>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝</label>
                            <div class="col-sm-9 col-xs-12">
                                <img src='{{$agentModel->member['avatar']}}'
                                     style='width:100px;height:100px;padding:1px;border:1px solid #ccc'/>
                                {{$agentModel->member['nickname']}}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">真实姓名</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    {{$agentModel->member['realname']}}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">联系电话</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    {{$agentModel->member['mobile']}}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销商等级</label>
                            <div class="col-sm-9 col-xs-12">

                                <select name='agent[agent_level_id]' class='form-control'>
                                    <option value='0'>
                                        @if(empty($agentModel->agent_level['name']))
                                            普通等级
                                        @else
                                            <!-- 默认等级 -->
                                            {{$defaultlevelname}}
                                            {{--{{$agentModel->agent_level['name']--}}
                                        @endif
                                    </option>
                                    @foreach($agentLevel as $level)
                                        <option value='{{$level['id']}}'
                                                @if($level['id'] == $agentModel->agent_level['id'])
                                                selected @endif >{{$level['name']}}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">累计佣金</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'> {{$agentModel->commission_total}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">已打款佣金</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'> {{$agentModel->commission_pay}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">注册时间</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{date('Y-m-d H:i:s',
                                    $agentModel->member['createtime'])}}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">成为代理时间</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{$agentModel->created_at}}
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">强制不自动升级</label>
                            <div class="col-sm-9 col-xs-12">

                                <label class="radio-inline">
                                    <input type="radio" name="agent[agent_not_upgrade]" value="0"
                                           @if($agentModel->agent_not_upgrade == 0) checked @endif >允许自动升级</label>
                                <label class="radio-inline">
                                    <input type="radio" name="agent[agent_not_upgrade]" value="1"
                                           @if($agentModel->agent_not_upgrade == 1) checked @endif >强制不自动升级</label>
                                <span class="help-block">如果强制不自动升级，满足任何条件，此分销商的级别也不会改变</span>

                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">黑名单</label>
                            <div class="col-sm-9 col-xs-12">

                                <label class="radio-inline">
                                    <input type="radio" name="agent[is_black]" value="1"
                                           @if($agentModel->is_black == 1) checked @endif >是</label>
                                <label class="radio-inline">
                                    <input type="radio" name="agent[is_black]" value="0"
                                           @if($agentModel->is_black == 0) checked @endif >否</label>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">备注</label>
                            <div class="col-sm-9 col-xs-12">
                                <textarea name="agent[content]" class='form-control'>{{$agentModel->content}}</textarea>
                            </div>
                        </div>

                    </div>


                    <div class='panel-body'>
                        <div class="form-group"></div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"/>

                                <input type="button" name="back" onclick='history.back()'
                                       value="返回列表" class="btn btn-default"/>
                            </div>
                        </div>
                    </div>


                </div>

            </form>
        </section><!-- /.content -->
    </div>
@endsection

