@extends('layouts.base')
@section('title', '推广下线')
@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">

            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">会员信息</a></li>
                </ul>
            </div>

            <div class="panel panel-default">
                <div class='panel-body'>
                    <div style='height:100px;width:110px;float:left;'>
                        <img src='{{$member->avatar}}' style='width:100px;height:100px;border:1px solid #ccc;padding:1px' />
                    </div>
                    <div style='float:left;height:100px;overflow: hidden'>
                        昵称: {{$member->nickname}}<br/>
                        姓名: {{$member->realname}} <br/>
                        手机号: {{$member->mobile}}<br/>
                        余额: {{$member->credit2}} / 积分：{{$member->credit1}}<br/>
                    </div>
                </div>
            </div>

                <div class="panel panel-info">

                    <div class="panel-body">
                        <form action="" method="post" class="form-horizontal" role="form" id="form1">
                            <input type="hidden" name="route" value="member.member.agent" id="route" />
                            <input type="hidden" name="id" value="{{$request->id}}" />

                            <div class="form-group col-xs-12 col-sm-6 col-md-2">
                                <div class="">
                                    <input type="text" class="form-control"  name="aid" value="{{$request->aid}}" placeholder="请输入您的ID"/>
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2">
                                <div class="">
                                    <input type="text" class="form-control"  name="keyword" value="{{$request->keyword}}" placeholder='可搜索昵称/名称/手机号'/>
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2">
                                <div class="">
                                    <select name='followed' class='form-control'>
                                        <option value=''>是否关注</option>
                                        <option value='1'
                                                @if($request->followed=='1')
                                                selected
                                                @endif
                                        >已关注
                                        </option>
                                        <option value='0'
                                                @if($request->followed=='0')
                                                selected
                                                @endif
                                        >取消关注
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                                <label class=" control-label"></label>
                                <div class="col-sm-3 pull-right" style="margin-right:30px;"><button class="btn btn-success">
                                        <i class="fa fa-search"></i> 搜索</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


            <div class="panel panel-default"  style="margin-bottom:200px!important">
                <div class="panel-heading">总数：{{$total}}</div>
                <div class="panel-body">
                    <table class="table table-hover"   style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:5%;'>会员ID</th>
                            <th style='width:13%;text-align: center;'>推荐人</th>
                            <th style='width:10%;text-align: center;'>粉丝</th>
                            <th style='width:12%;'>姓名</th>
                            <th style='width:12%;'>手机号码</th>
                            <th style='width:12%;'>状态</th>
                            <th style='width:12%;'>下线状态</th>
                            <th style='width:14%;'>注册时间</th>
                            <th style='width:10%;text-align: center;'>关注</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['data'] as $row)
                        <tr>
                            <td>{{$row['uid']}}</td>
                            <td  style="text-align: center;" @if(!empty($row['yz_member']['parent_id']))title='ID: {{$row['yz_member']['parent_id']}}'@endif>
                            @if(empty($row['yz_member']['parent_id']))
                                @if($row['yz_member']['is_agent']==1)
                            <label class='label label-primary'>总店</label>
                                @else
                            <label class='label label-default'>暂无</label>
                                @endif
                            @else
                            <img src='{{$row['yz_member']['agent']['avatar']}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/> {{$row['yz_member']['agent']['nickname']}}
                            @endif
                            </td>
                            <td  style="text-align: center;">
                                @if(!empty($row['yz_member']['agent']['avatar']))
                                    @if(!empty($row['avatar']))
                                        <img src='{{$row['avatar']}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    @endif
                                    @if(empty($row['nickname']))
                                        未更新
                                    @else
                                        {{$row['nickname']}}
                                    @endif
                                @endif

                            </td>

                            <td>{{$row['realname']}}</td>
                            <td>{{$row['mobile']}}</td>

                            <td>
                                @if($row['yz_member']['is_agent']==1)
                                    @if($row['yz_member']['status']==0)
                                        <span class="label label-default">未审核</span>
                                    @elseif ($row['yz_member']['status']==1)
                                        <span class="label label-default">审核中</span>
                                    @else
                                    <span class="label label-success">已审核</span>
                                    @endif
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                @if($row['yz_member']['inviter'] == 0)
                                    <span class="label label-default">暂定下线</span>
                                @elseif($row['yz_member']['inviter'] == 1)
                                    <span class="label label-success">锁定关系下线</span>
                                @endif
                            </td>
                            <td>{{date('Y-m-d H:i',$row['createtime'])}}</td>
                            <td class="text-center">
                                @if(empty($row['has_one_fans']['followed']))
                                    @if(empty($row['has_one_fans']['uid']))
                                        <label class='label label-default'>未关注</label>
                                    @else
                                        <label class='label label-warning'>取消关注</label>
                                    @endif
                                @else
                                    <label class='label label-success'>已关注</label>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {!! $pager !!}
                </div>
            </div>
        </div>
    </div>

@endsection