@extends('layouts.base')
@section('title', '激活记录')
@section('content')
    <link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div id="member-blade" class="rightlist">

        <div class="panel panel-info">
            <div class="panel-heading">激活记录</div>
            <div class="panel-body">
                <form action="{{ yzWebUrl('plugin.love.Backend.Modules.Love.Controllers.activation-records.index') }}" method="get" class="form-horizontal"  role="form" id="form1">

                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="sign" id="form_do"/>
                    <input type="hidden" name="route" value="plugin.love.Backend.Modules.Love.Controllers.activation-records.index" id="route"/>

                    <div class="form-group col-sm-11 col-lg-11 col-xs-12">
                        <div class="">
                            <div class='input-group'>

                                <input class="form-control" name="search[id]" type="text"
                                       value="{{ $search['id'] or ''}}" placeholder="激活ID">
                                <input class="form-control" name="search[realname]" type="text"
                                       value="{{ $search['realname'] or ''}}" placeholder="{{ trans('Yunshop\Love::member_love.search_member') }}">

                                <div class='form-input'>
                                    <p class="input-group-addon price">激活值区间</p>
                                    <input class="form-control price" name="search[min_love]" type="text" value="{{ $search['min_love'] or ''}}" placeholder="{{ trans('Yunshop\Love::member_love.search_section_min') }}">
                                    <p class="line">—</p>
                                    <input class="form-control price" name="search[max_love]" type="text" value="{{ $search['max_love'] or ''}}" placeholder="{{ trans('Yunshop\Love::member_love.search_section_max') }}">
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-8">
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
                            'starttime'=>$search['time']['start'] ? $search['time']['start'] : date('Y-m-d H:i:s',strtotime('-7 day')),
                            'endtime'=>$search['time']['end'] ? $search['time']['end'] : date('Y-m-d H:i:s'),
                            'start'=>0,
                            'end'=>0
                        ], true)!!}
                    </div>

                    <div class="form-group col-sm-1 col-lg-1 col-xs-12">
                        <div class="">
                            <input type="submit" class="btn btn-block btn-success" value="{{ trans('Yunshop\Love::member_love.button.search') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class='input-group-btn'>
                        <a class="btn btn-primary" href="{{ yzWebUrl('plugin.love.Backend.Controllers.activation-set.activation') }}" onclick="return confirm('添加激活队列，将在三～五分钟完成激活！');return false;">手动激活</a>
                    </div>
                </div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:6%;text-align: center;'>激活ID</th>
                            <th style='width:12%;text-align: center;'>激活时间</th>
                            <th style='width:8%;text-align: center;'>会员</th>
                            <th style='width:8%;text-align: center;'>固定激活值</th>
                            <th style='width:8%;text-align: center;'>一级激活值</th>
                            <th style='width:8%;text-align: center;'>二、三级激活值</th>
                            <th style='width:8%;text-align: center;'>团队激活值</th>
                            <th style='width:8%;text-align: center;'>周期利润激活</th>
                            <th style='width:8%;text-align: center;'>本次应激活</th>
                            <th style='width:8%;text-align: center;'>实际激活值</th>
                            <th style='width:8%;text-align: center;'>剩余冻结</th>
                            <th style='width:8%;text-align: center;'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr style="text-align: center;">
                                <td>{{ $list->id }}</td>
                                <td>{{ $list->created_at }}</td>
                                <td>
                                    <a href="{{ yzWebUrl('member.member.detail', ['id'=>$list->member_id]) }}" >
                                        @if($list->member->avatar || $shopSet['headimg'])
                                            <img src='{{ $list->member->avatar ? tomedia($list->member->avatar) : tomedia($shopSet['headimg']) }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                        @endif

                                        {{ $list->member->nickname ?: '未更新'}}
                                    </a>
                                </td>
                                <td>{{ $list->fixed_activation_love }}</td>
                                <td>{{ $list->first_activation_love }}</td>
                                <td>{{ $list->second_three_activation_love }}</td>
                                <td>{{ $list->team_activation_love }}</td>
                                <td>{{ $list->profit_activation_love }}</td>
                                <td>{{ $list->sum_activation_love }}</td>
                                <td>{{ $list->actual_activation_love }}</td>
                                <td>{{ $list->surplus_froze_love }}</td>
                                <td  style="overflow:visible;">
                                    <a class='btn btn-default' href="{{ yzWebUrl('plugin.love.Backend.Modules.Love.Controllers.activation-record-detail.index', array('record_id' => $list->id)) }}" style="margin-bottom: 2px">查看详情</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $page !!}

                </div>
            </div>
        </div>
    </div>

@endsection