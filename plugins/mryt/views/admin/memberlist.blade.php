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
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">会员管理</a></li>
                    <a class="btn btn-primary" href="{!! yzWebUrl('plugin.mryt.admin.member.add') !!}"><i class="fa fa-plus"></i> 添加会员</a>
                </ul>
            </div>

            <div class="right-addbox"><!-- 此处是右侧内容新包一层div -->
                <div class="panel panel-info">
                    <div class="panel-body">
                        <form action="{!! yzWebUrl('plugin.mryt.admin.member') !!}" method="post" class="form-horizontal" role="form" id="form1">
                            <div class="form-group col-xs-12 col-sm-3">
                                <div class="">
                                    <input type="text" class="form-control"  name="realname" value="{{$request['realname']}}" placeholder="可搜索昵称/姓名/手机号"/>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                <select class="form-control tpl-category-parent" id="level" name="level_id">
                                    <option value="-1">会员等级</option>
                                    <option value="0">{{$set['default_level']}}</option>
                                    @foreach($level as $item)
                                        <option value="{{$item->id}}"
                                                @if($request['level_id']==$item->id)
                                                selected
                                                @endif
                                        >{{$item->level_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-6">
                                <div class="search-select">
                                    {!! app\common\helpers\DateRange::tplFormFieldDateRange('times', [
                                    'starttime'=>date('Y-m-d H:i', $starttime),
                                    'endtime'=>date('Y-m-d H:i',$endtime),
                                    'start'=>0,
                                    'end'=>0
                                    ], true) !!}
                                </div>
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
                    <div class="panel-heading">总数：{{$total}}</div>
                    <div class="panel-body table-responsive">
                        <table class="table table-hover">
                            <thead class="navbar-inner">
                            <tr>
                                <th width="6%">ID</th>
                                <th width="10%">会员信息</th>
                                <th width="10%">等级名称</th>
                                <th width="10%">累计{{ $set['referral_name'] }}</th>
                                <th width="10%">累计{{ $set['teammanage_name'] }}</th>
                                <th width="10%">累计{{ $set['team_name'] }}</th>
                                <th width="10%">累计{{ $set['thanksgiving_name'] }}</th>
                                <th width="10%">累计{{ $set['parenting_name'] }}</th>
                                <th width="10%">累计{{ $set['tier_name'] }}</th>
                                <th width="10%">累计总奖励</th>
                                <th width="12%">是否签劳动合同</th>
                                <th style='width:15%;text-align: left'>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list['data'] as $item)
                                <tr>
                                    <td>{{$item['id']}}</td>
                                    @if ($item['has_one_member'])
                                        <td>
                                            <a href="{!! yzWebUrl('member.member.detail', ['id'=>$item['has_one_member']['uid']]) !!}"><img src="{{$item['has_one_member']['avatar']}}" style="width:30px;height:30px;padding:1px;border:1px solid #ccc"><BR>{{$item['has_one_member']['nickname']}}</a>
                                        </td>
                                    @else
                                        <td style="height: 59px">
                                            未更新
                                        </td>
                                    @endif
                                    <td title="{{$item['has_one_level']['level_name'] ?: $set['default_level']}}" class='tdedit' width="26%">
                                        <span class=' fa-edit-item' style='cursor:pointer'>
                                            <span class="title">{{$item['has_one_level']['level_name'] ?: $set['default_level']}}</span>
                                            <i class='fa fa-pencil' style="display:none"></i>
                                        </span>

                                        <div class="input-group level" style="display:none">
                                            <select class="form-control tpl-agent-level" name="level_id" data-agencyid="{{$item['id']}}">
                                                <option value="0">{{$set['default_level']}}</option>
                                                @foreach($level as $value)
                                                    <option value="{{$value->id}}"
                                                            @if($item['level']==$value->id)
                                                            selected
                                                            @endif
                                                    >{{$value->level_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </td>
                                    <td>
                                        {{$item['referral_total']}}
                                    </td>
                                    <td>{{$item['order_team_total']}}</td>
                                    <td>
                                        {{$item['team_total']}}
                                    </td>
                                    <td>
                                        {{$item['thankful_total']}}
                                    </td>
                                    <td>{{$item['parenting_total']}}</td>
                                    <td>{{$item['tier_total']}}</td>
                                    <td>{{$item['referral_total'] + $item['order_team_total'] + $item['team_total'] + $item['thankful_total'] + $item['parenting_total'] + $item['tier_total']}}</td>
                                    <td>
                                        @if($item['status']=== 1)
                                            已签订
                                        @else
                                            未签订
                                        @endif
                                    </td>
                                    <td style="overflow:visible;text-align: left">
                                        <div class="btn-group btn-group-sm" >
                                            <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="javascript:;">操作 <span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-left" role="menu" style='z-index: 9999'>
                                                <li><a href="{!! yzWebUrl('member.member.detail', ['id'=>$item['has_one_member']['uid']]) !!}" title='会员详情'><i class='fa fa-pencil'></i> 会员详情</a></li>
                                                <li><a href="{{yzWebUrl('plugin.mryt.admin.member.edit-password', ['id'=>$item['has_one_member']['uid'],'nickname' => $item['has_one_member']['nickname']])}}" title='账号信息'><i class='fa fa-eraser'></i> 账号信息</a></li>
                                                <li><a href="{{yzWebUrl('plugin.mryt.admin.member.deletedAgency',['id'=>$item['id']])}}" onclick="return confirm('是否确认删除?');
                                                   return false;" title="删除"><i class="fa fa-trash"></i>删除</a></li>
                                            </ul>
                                        </div>

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


        $('.tdedit').mouseover(function () {
            $(this).find('.fa-pencil').show();
        }).mouseout(function () {
            $(this).find('.fa-pencil').hide();
        });
        $('.fa-edit-item').click(function () {
            $(this).closest('span').hide();
            $(this).next('.level').show();

        });
        $('.tpl-agent-level').change(function () {
            var agencyId = $(this).data('agencyid');
            var levelId = $(this).val();
            fastChange(agencyId, levelId);
        });
        function fastChange(id, value) {
            $.ajax({
                url: "{!! yzWebUrl('plugin.mryt.admin.member.change') !!}",
                type: "post",
                data: {id: id, value: value},
                cache: false,
                success: function ($data) {
                    console.log($data);
                    location.reload();
                }
            })
        }
        $(function () {
            $('#export').click(function(){
                $('#form1').get(0).action="{!! yzWebUrl('plugin.mryt.admin.member.export') !!}";
                $('#form1').submit();
                $('#form1').get(0).action="{!! yzWebUrl('plugin.mryt.admin.member.index') !!}";
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