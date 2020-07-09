@extends('layouts.base')
@section('title', trans('分销商管理'))
@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">分销商管理</a></li>
        </ul>
    </div>
    <form action="" method="post" class="form-horizontal"  id="form1">
        <div class="panel panel-info">
            <div class="panel-body">

                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">成为分销商时间</label>
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
                    <div class="col-xs-12 col-sm-8 col-lg-9">
                        <input class="form-control" name="search[member]" id="" type="text"
                               value="{{$search['member']}}" placeholder="ID/昵称/姓名/手机/openId">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否关注</label>
                    <div class="col-sm-8 col-lg-9 col-xs-12">
                        <select name='search[follow]' class='form-control'>
                            <option value=''>全部</option>
                            <option value='2' @if($search['follow'] == '2') selected @endif>未关注</option>
                            <option value='1' @if($search['follow'] == '1') selected @endif>已关注</option>
                            <option value='0' @if($search['follow'] == '0') selected @endif>取消关注</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">推荐人</label>
                    <div class="col-sm-3">
                        <select name='search[parent_id]' class='form-control'>
                            <option value=''>全部</option>
                            <option value='0' @if($search['parent_id'] == '0') selected @endif>总店</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <input type="text"  class="form-control" name="search[parent_name]" value="{{$search['parent_name']}}" placeholder='推荐人昵称/姓名/手机号'/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">分销商等级</label>
                    <div class="col-sm-8 col-lg-9 col-xs-12">
                        <select name='search[level]' class='form-control'>
                            <option value=''>所有等级</option>
                            @foreach($agentlevels as $level)
                                <option value='{{$level['id']}}'
                                        @if($search['level'] == $level->id) selected @endif> {{$level->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">状态</label>
                    <div class="col-sm-8 col-lg-9 col-xs-12">
                        <select name='search[black]' class='form-control'>
                            <option value=''>全部</option>
                            <option value='0' @if($search['black'] == '0') selected @endif>否</option>
                            <option value='1' @if($search['black'] == '1') selected @endif>是</option>
                        </select>
                    </div>

                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"> </label>
                    <div class="col-xs-12 col-sm-2 col-lg-2">
                        {{--<input type="submit" class="btn btn-success" value="搜索">--}}

                        <input type="button" class="btn btn-success" onclick="exported({{ $total }});" id="export" value="导出">

                        <input type="button" class="btn btn-success pull-right" id="search" value="搜索">
                    </div>
                </div>

            </div>
        </div>
    </form>

    <div class='panel panel-default'>
        <div class='panel-heading'>
            管理 (数量: {{$total}} 条)

        </div>
        <div class='panel-body'>

            <table class="table table-hover" style="overflow:visible;">
                <thead>
                <tr>
                    <th style='width:8%;'>会员ID</th>
                    <th style='width:15%;'>推荐人</th>
                    <th style='width:15%;'>昵称</th>
                    <th style='width:15%;'>姓名</br>手机</th>
                    <th style='width:10%;'>分销商等级</br>下级分销商人数</th>
                    <th style='width:9%;'>累计佣金</br>已打款佣金</th>
                    <th style='width:6%;'>关注</th>
                    <th style='width:5%;'>黑名单</th>
                    <th style='width:20%;'>操作</th>

                </tr>
                </thead>
                <tbody>
                @foreach($list as $row)
                    <tr>
                        <td>{{$row['member_id']}}</td>
                        <td>
                            @if($row->toParent)
                                <img src="{{tomedia($row->toParent['avatar'])}}"
                                     style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                </br>
                                {{$row->toParent['nickname']}}
                            @else
                                <label class='label label-primary'>总店</label>
                            @endif
                        </td>
                        <td>
                            <img src="{{tomedia($row->Member['avatar'])}}"
                                 style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                            </br>
                            {{$row->Member['nickname']}}
                        </td>

                        <td>
                            {{$row->Member['realname']}}
                            </br>
                            {{$row->Member['mobile']}}
                        </td>

                        <td>
                            @if($row->agentLevel['name'] == '默认等级')
                                {{$defaultlevelname}}
                            @else
                                {{$row->agentLevel['name']}}
                            @endif
                            </br>{{$row['lowers']}}人
                        </td>

                        <td>
                            {{$row['commission_total']}}
                            </br>
                            {{$row['commission_pay']}}
                        </td>
                        <td>
                            @if($row['fans']['follow']) 已关注 @else 未关注 @endif
                        </td>
                        <td>
                            @if($row['is_black']) 是 @else 否 @endif
                        </td>
                        <td style="overflow:visible;">
                            <div class="btn-group btn-group-sm dropdown-box" data-type="1">
                                <a class="btn btn-default dropdown" data-expanded="1"
                                   href="javascript:;">操作 <span class="caret"></span></a>

                                <ul class="dropdown-menu" role="menu" style='z-index: 99999; top:93%!important'>
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
                                           href="{{yzWebUrl('plugin.commission.admin.commission-order.index', ['search[member_id]' => $row['member_id']])}}"
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
    <div style="width:100%;height:150px;"></div>
    <script type="text/javascript">
        function exported(total)
        {
            if (total == 0) {
                alert('分销商数据为空，不支持导出');return;
            }

            $('#form1').attr('action', '{!! yzWebUrl('plugin.commission.admin.agent.export') !!}');
            $('#form1').submit();
        }

        $(function () {
                {{--$('#export').click(function () {--}}
                    {{--$('#form1').attr('action', '{!! yzWebUrl('plugin.commission.admin.agent.export') !!}');--}}
                    {{--$('#form1').submit();--}}
                {{--});--}}

                $('#search').click(function () {
                    $('#form1').attr('action', '{!! yzWebUrl('plugin.commission.admin.agent.index') !!}');
                    $('#form1').submit();
                });


            $('.dropdown-box').hover(function () {
                var _this = $(this);
                $(".dropdown-menu").css('display','none');

                if (_this.attr('data-type') == 1) {
                    _this.children("a").siblings(".dropdown-menu").show();
                    $('.dropdown-box').attr('data-type', 1);
                    _this.attr('data-type', 2);
                } else {
                    _this.attr('data-type', 1);
                }
            });
        });

    </script>
@endsection