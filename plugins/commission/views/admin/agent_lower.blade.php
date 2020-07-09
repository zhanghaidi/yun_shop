@extends('layouts.base')

@section('content')
@section('title', trans('推广粉丝'))
<section class="content">
    <div class="panel panel-default">
        <div class='panel-body'>
            <div style='height:100px;width:110px;float:left;'>
                <img src='{{$member->avatar}}' style='width:100px;height:100px;border:1px solid
                        #ccc;padding:1px'/>
            </div>
            <div style='float:left;height:100px;overflow: hidden'>
                昵称: {{$member->nickname}}<br/>
                姓名: {{$member->realname}} <br/>
                手机号: {{$member->mobile}} <br/>

                下级分销商: 总共 <span style='color:red'>{{$lower['agent']}}</span> 人
                @if($set['level']>=1) 一级: <span style='color:red'>{{$lower['first']}} </span> 人 @endif
                @if($set['level']>=2) 二级: <span style='color:red'>{{$lower['second']}}</span> 人 @endif
                @if($set['level']>=3) 三级: <span style='color:red'>{{$lower['third']}}</span> 人 @endif
            </div>
        </div>
    </div>
    <form action="" method="post" class="form-horizontal">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">

                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">成为分销时间</label>
                    <div class="col-sm-7 col-lg-9 col-xs-12">
                        <div class="col-sm-3">
                            <label class='radio-inline'>
                                <input type='radio' value='0' name='search[is_time]'
                                       @if($search['is_time'] == '0') checked @endif>不搜索
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' value='1' name='search[is_time]'
                                       @if($search['is_time'] == '1') checked @endif>搜索
                            </label>
                        </div>
{{--                        {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', ['starttime'=>date('Y-m-d H:i', $search['starttime']), 'endtime'=>date('Y-m-d H:i', $search['endtime'])], true) !!}--}}
                        {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                                                                                'starttime'=>$search['time']['start'],
                                                                                'endtime'=>$search['time']['end'],
                                                                                'start'=>$search['time']['start'],
                                                                                'end'=>$search['time']['end']
                                                                                ], true) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>
                    <div class="col-sm-8 col-lg-9 col-xs-12">
                        <input type="text" class="form-control" name="search[member]"
                               value="{{$search['member']}}"
                               placeholder='可搜索ID/昵称/名称/手机号'/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否关注</label>
                    <div class="col-sm-8 col-lg-9 col-xs-12">
                        <select name='search[follow]' class='form-control'>
                            <option value=''></option>
                            <option value='2' @if($search['follow'] == '2') selected @endif>未关注</option>
                            <option value='1' @if($search['follow'] == '1') selected @endif>已关注</option>
                            <option value='0' @if($search['follow'] == '0') selected @endif>取消关注</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">推荐人</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="search[parent_name]"
                               value="{{$search['parent_name']}}" placeholder='推荐人昵称/姓名/手机号'/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">分销商等级</label>
                    <div class="col-sm-8 col-lg-9 col-xs-12">
                        <select name='search[level]' class='form-control'>
                            <option value=''></option>
                            @foreach($agentlevels as $level)
                                <option value='{{$level['id']}}'
                                        @if($search['level'] == $level->id) selected @endif> {{$level->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">下级层级</label>
                    <div class="col-sm-8 col-lg-9 col-xs-12">
                        <select name='search[lower]' class='form-control'>
                            <option value=''>所有粉丝</option>
                            @if($set['level']>=1)
                                <option value='1' @if($search['lower'] == '1') selected @endif>一级粉丝</option>
                            @endif
                            @if($set['level']>=2)
                                <option value='2' @if($search['lower'] == '2') selected @endif>二级粉丝</option>
                            @endif
                            @if($set['level']>=3)
                                <option value='3' @if($search['lower'] == '3') selected @endif>三级粉丝</option>
                            @endif
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">状态</label>


                    <div class="col-sm-8 col-lg-9 col-xs-12">
                        <select name='search[black]' class='form-control'>
                            <option value=''>黑名单状态</option>
                            <option value='0' @if($search['black'] == '0') selected @endif>否</option>
                            <option value='1' @if($search['black'] == '1') selected @endif>是</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
                    <div class="col-sm-3">
                        <input type="submit" class="btn" value="搜索">
                    </div>
                </div>

            </div>
        </div>
    </form>

    <div class="panel panel-default">
        <div class="panel-heading">总数：{{$total}}</div>
        <div class="panel-body">
            <table class="table table-hover" style="overflow:visible;">
                <thead class="navbar-inner">
                <tr>
                    <th style='width:5%;'>会员ID</th>
                    <th style='width:10%;text-align: center;'>推荐人</th>
                    <th style='width:10%;text-align: center;'>粉丝</th>
                    <th style='width:10%;'>姓名<br/>手机号码</th>
                    <th style='width:10%;'>分销等级<br/>下级分销商</th>
                    <th style='width:10%;'>累计佣金<br/>打款佣金</th>
                    <th style='width:15%;'>时间</th>
                    <th style='width:10%;text-align: center;'>关注</th>
                    <th style='width:8%'>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $row)
                    <tr>
                        <td>{{$row['member_id']}}</td>
                        <td style="text-align: center;" title='ID: {{$row['agent_id']}}'>
                            @if(!$row->toParent)
                                <label class='label label-primary'>总店</label>
                            @else
                                <a href="{!! yzWebUrl('member.member.detail', ['id'=>$row->toParent['uid']]) !!}">
                                    <img src='{{tomedia($row->toParent['avatar'])}}'
                                         style='width:30px;height:30px;padding:1px;border:1px solid #ccc'
                                    /><br/> {{$row->toParent['nickname']}}
                                </a>
                            @endif

                        </td>

                        <td style="text-align: center;">
                            <a href="{!! yzWebUrl('member.member.detail', ['id'=>$row->member['uid']]) !!}">
                                <img src='{{tomedia($row->member['avatar'])}}'
                                     style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/><br/>
                                {{$row->member['nickname']}}
                            </a>
                        </td>

                        <td>{{$row->member['realname']}} <br/> {{$row->member['mobile']}}</td>
                        <td>
                            @if($row->agentlevel)
                                    {{$row->agentLevel['name']}}         
                           @else
                                {{$defaultlevelname}}
                            @endif
                            <br/>
                            总计：{{\Yunshop\Commission\models\Agents::getAgentCount($row->member_id)}} 人
                            <br/>一级：{{\Yunshop\Commission\models\Agents::getAgentCount($row->member_id,1)}} 人
                            <br/> 二级：{{\Yunshop\Commission\models\Agents::getAgentCount($row->member_id,2)}} 人
                            {{--<br/>三级：{{\Yunshop\Commission\models\Agents::getAgentCount($row->member_id,3)}} 人--}}
                        </td>

                        <td>
                            <label class="label label-primary">{{$row->commission_total}}</label>
                            <br/><label class="label label-danger">{{$row->commission_pay}}</label>
                        </td>

                        <td>注册时间：{{date('Y-m-d H:i:s', $row->member['createtime'])}}<br/>
                            分销商时间：{{$row->created_at}}
                        </td>
                        <td style="text-align: center;">
                            @if(!$row->fans['follow'])
                                @if(!$row->fans['uid'])
                                    <label class='label label-default'>未关注</label>
                                @else
                                    <label class='label label-warning'>取消关注</label>
                                @endif
                            @else
                                <label class='label label-success'>已关注</label>
                            @endif
                        </td>
                        <td style="overflow:visible;">

                            <div class="btn-group btn-group-sm">
                                <a class="btn btn-default dropdown" data-type="1"
                                   href="javascript:;">操作 </a>

                                <ul class="dropdown-menu " style='z-index: 99999;display:none;'>
                                    <li>
                                        <a class='btn btn-default'
                                           href="{{yzWebUrl('member.member.detail', ['id' => $row['member_id']])}}"
                                           title='会员信息'><i class='fa fa-user'></i> 会员信息
                                        </a>
                                    </li>
                                    <li>
                                        <a class='btn btn-default'
                                           href="{{yzWebUrl('plugin.commission.admin.agent.detail', ['id' => $row['id']])}}"
                                           title='详细信息'><i class='fa fa-edit'></i> 详细信息
                                        </a>
                                    </li>
                                    <li>
                                        <a class='btn btn-default'
                                           href="{{yzWebUrl('plugin.commission.admin.commission-order.index')}}"
                                           title='推广订单'><i class='fa fa-list'></i> 推广订单
                                        </a>
                                    </li>
                                    <li>
                                        <a class='btn btn-default'
                                           href="{{yzWebUrl('plugin.commission.admin.agent.lower', ['id' => $row['member_id']])}}"
                                           title='推广粉丝'><i class='fa fa-users'></i> 推广粉丝
                                        </a>
                                    </li>
                                    <li>
                                    @if($row['is_black'])
                                        <li>
                                            <a class='btn btn-default'
                                               href="{{yzWebUrl('plugin.commission.admin.agent.black', ['id' => $row['id'],'is_black'=>'0'])}}"
                                               title='取消黑名单'><i class='fa fa-minus-square'></i> 取消黑名单
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a class='btn btn-default'
                                               href="{{yzWebUrl('plugin.commission.admin.agent.black', ['id' => $row['id'],'is_black'=>'1'])}}"
                                               title='加入黑名单'><i class='fa fa-minus-circle'></i> 设置黑名单
                                            </a>
                                        </li>
                                    @endif
                                    {{--暂时不要分销商删除--}}
                                    {{--<li>--}}
                                    {{--<a class='btn btn-default'--}}
                                    {{--href="{{yzWebUrl('plugin.commission.admin.agent.deleted', ['id' => $row['id']])}}"--}}
                                    {{--onclick="return confirm('确认删除此分销商吗？');return false;"><i--}}
                                    {{--class='fa fa-remove'></i>--}}
                                    {{--&nbsp;删除分销商--}}
                                    {{--</a>--}}
                                    {{--</li>--}}
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $pager !!}
        </div>
    </div>
</section>
<script type="text/javascript">
    $(function () {
        $('.dropdown').click(function () {
            var _this = $(this);
            $(".dropdown-menu").css('display', 'none');

            if (_this.attr('data-type') == 1) {
                _this.siblings(".dropdown-menu").css('display', 'block');
                $('.dropdown').attr('data-type', 1);
                _this.attr('data-type', 2);
            } else {
                _this.attr('data-type', 1);
            }
        });
    });
</script>
@endsection

