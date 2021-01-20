@extends('layouts.base')
@section('title', trans('会员管理'))
@section('content')
    <style>
        .time {
            width: 30%;
            float: left;
        }
        table a {
            color: #333;
        }
    </style>
    <div class="w1200 ">
        <div class=" rightlist ">

            <div class="right-addbox"><!-- 此处是右侧内容新包一层div -->
                <div class="panel panel-info">
                    <div class="panel-body">
                        <form action="{!! yzWebUrl('plugin.nominate.admin.member.index') !!}" method="post" class="form-horizontal" role="form" id="form1">

                            <div class="form-group col-xs-12 col-sm-3">
                                <div class="">
                                    <input type="text" class="form-control"  name="search[uid]" value="{{$search['uid']}}" placeholder="会员ID"/>
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-sm-3">
                                <div class="">
                                    <input type="text" class="form-control"  name="search[member]" value="{{$search['member']}}" placeholder="可搜索昵称/姓名/手机号"/>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                <select class="form-control tpl-category-parent" id="level" name="search[level_id]">
                                    <option value="-1">会员等级</option>
                                    @foreach($levels as $item)
                                        <option value="{{$item->id}}"
                                                @if($search['level_id']==$item->id)
                                                selected
                                                @endif
                                        >{{$item->level_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                                <div class="">
                                    <button type="button" name="export" value="1" id="export" class="btn btn-default excel back ">导出 Excel</button>
                                    <input type="hidden" name="token" value="{{$var['token']}}" />
                                    <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>

                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">总数：{{$list->total()}}</div>
                    <div class="panel-body table-responsive">
                        <table class="table table-hover">
                            <thead class="navbar-inner">
                            <tr>
                                <th width="12%">会员ID</th>
                                <th width="16%">推荐人</th>
                                <th width="16%">会员信息</th>
                                <th width="12%">等级名称</th>
                                <th width="14%">累计{{ $set['nominate_prize_name']?:'直推奖' }}</th>
                                <th width="14%">累计{{ $set['nominate_poor_prize_name']?:'直推极差奖' }}</th>
                                <th width="14%">累计{{ $set['team_prize_name']?:'团队奖' }}</th>
                                <th width="14%">累计{{ $set['team_manage_prize_name']?:'团队业绩奖' }}</th>
                                <th width="12%">累计总奖励</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$item->member_id}}</td>
                                    @if ($item->parent)
                                        <td>
                                            <a href="{!! yzWebUrl('member.member.detail', ['id'=>$item->parent_id]) !!}"><img src="{{$item->parent->avatar}}" style="width:30px;height:30px;padding:1px;border:1px solid #ccc"><BR>{{$item->parent->nickname}}</a>
                                        </td>
                                    @else
                                        <td style="height: 59px">
                                            <label class='label label-primary'>
                                                总店
                                            </label>
                                        </td>
                                    @endif
                                    @if ($item->hasOneMember)
                                        <td>
                                            <a href="{!! yzWebUrl('member.member.detail', ['id'=>$item->member_id]) !!}"><img src="{{$item->hasOneMember->avatar}}" style="width:30px;height:30px;padding:1px;border:1px solid #ccc"><BR>{{$item->hasOneMember->nickname}}</a>
                                        </td>
                                    @else
                                        <td style="height: 59px">
                                            <label class='label label-default'>
                                                未更新
                                            </label>
                                        </td>
                                    @endif
                                    <td>
                                        {{$item->shopMemberLevel->level_name}}
                                    </td>
                                    <td>
                                        {{$item['nominate_prize_amount']}}
                                    </td>
                                    <td>{{$item['nominate_poor_prize_amount']}}</td>
                                    <td>
                                        {{$item['team_prize_amount']}}
                                    </td>
                                    <td>
                                        {{$item['team_manage_prize_amount']}}
                                    </td>
                                    <td>
                                        {{$item['team_manage_prize_amount'] + $item['team_prize_amount'] + $item['nominate_poor_prize_amount'] + $item['nominate_prize_amount']}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    {!!$pager!!}
                    <!--分页-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script language='javascript'>
        $(function () {
            $('#export').click(function(){
                $('#form1').get(0).action="{!! yzWebUrl('plugin.nominate.admin.member.export') !!}";
                $('#form1').submit();
                $('#form1').get(0).action="{!! yzWebUrl('plugin.nominate.admin.member.index') !!}";
            });
        });
    </script>
    <style>
        .table-hover tr th, .table-hover tr td{text-align: center;}
        .input-group{
            margin: 4px auto;height: 30px; line-height: 30px;
        }
    </style>
@endsection