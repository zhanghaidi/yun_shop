@extends('layouts.base')
@section('title', trans('Yunshop\Love::member_love.title'))
@section('content')
    <link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div id="member-blade" class="rightlist">
        <div class="panel panel-info">
            <div class="panel-heading">{{ trans('Yunshop\Love::member_love.subtitle') }}</div>
            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form1">

                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>
                    <input type="hidden" name="do" value="sign" id="form_do"/>
                    <input type="hidden" name="route" value="plugin.love.Backend.Modules.Member.Controllers.member-love.index" id="route"/>
                    <div class="form-group col-sm-11 col-lg-11 col-xs-12">
                        <div class="">
                            <div class='input-group'>

                                <input class="form-control" name="search[realname]" type="text"
                                       value="{{ $search['realname'] or ''}}" placeholder="{{ trans('Yunshop\Love::member_love.search_member') }}">

                                <div class='form-input'>
                                    <p class="input-group-addon" >{{ trans('Yunshop\Love::member_love.search_member_level') }}</p>
                                    <select name="search[member_level]" class="form-control">
                                        <option value="" selected>{{ trans('Yunshop\Love::member_love.search_select') }}</option>
                                        @foreach($memberLevels as $level)
                                            <option value="{{ $level['id'] }}" @if($search['member_level'] == $level['id']) selected @endif>{{ $level['level_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class='form-input'>
                                    <p class="input-group-addon" >{{ trans('Yunshop\Love::member_love.search_member_group') }}</p>
                                    <select name="search[member_group]" class="form-control">
                                        <option value="" selected >{{ trans('Yunshop\Love::member_love.search_select') }}</option>
                                        @foreach($memberGroups as $group)
                                            <option value="{{ $group['id'] }}" @if($search['member_group'] == $group['id']) selected @endif>{{ $group['group_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class='form-input'>
                                    <p class="input-group-addon price">{{ trans('Yunshop\Love::member_love.search_section') }}</p>
                                    <input class="form-control price" name="search[min_love]" type="text" value="{{ $search['min_love'] or ''}}" placeholder="{{ trans('Yunshop\Love::member_love.search_section_min') }}">
                                    <p class="line">—</p>
                                    <input class="form-control price" name="search[max_love]" type="text" value="{{ $search['max_love'] or ''}}" placeholder="{{ trans('Yunshop\Love::member_love.search_section_max') }}">
                                </div>

                            </div>
                        </div>
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
                    {{ trans('Yunshop\Love::member_love.total') }}：{{ $pageList->total() }}
                    &nbsp&nbsp&nbsp&nbsp
                    {{ trans('Yunshop\Love::member_love.usable_total') }}：{{ $usable or '0' }}
                    &nbsp&nbsp&nbsp&nbsp
                    {{ trans('Yunshop\Love::member_love.froze_total') }}：{{ $froze or '0' }}
                </div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:8%;text-align: center;'>{{ trans('Yunshop\Love::member_love.menu.menu_one') }}</th>
                            <th style='width:10%;text-align: center;'>{{ trans('Yunshop\Love::member_love.menu.menu_two') }}</th>
                            <th style='width:12%;text-align: center;'>{{ trans('Yunshop\Love::member_love.menu.menu_three') }}</th>
                            <th style='width:10%;text-align: center;'>{{ trans('Yunshop\Love::member_love.menu.menu_four') }}</th>
                            <th style='width:10%;text-align: center;'>{{ trans('Yunshop\Love::member_love.menu.menu_five') }}</th>
                            <th style='width:15%;text-align: center;'>{{ trans('Yunshop\Love::member_love.menu.menu_six') }}</th>
                            <th style='width:12%;text-align: center;'>{{ trans('Yunshop\Love::member_love.menu.menu_seven') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr style="text-align: center;">
                                <td>{{ $list->uid }}</td>
                                <td>
                                    <a href="{{ yzWebUrl('member.member.detail', ['id'=>$list->uid]) }}" >
                                        @if($list->avatar || $shopSet['headimg'])
                                            <img src='{{ $list->avatar ? tomedia($list->avatar) : tomedia($shopSet['headimg']) }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                        @endif

                                        {{ $list->nickname ?: '未更新'}}
                                    </a>
                                </td>
                                <td>{{ $list->realname }}<br/>{{ $list->mobile }}</td>
                                <td>
                                    {{ isset($list->yzMember->level) ? $list->yzMember->level->level_name : $shopSet['level_name'] }}
                                </td>
                                <td>{{ $list->yzMember->group->group_name or '无分组' }}</td>
                                <td>
                                    <label class="label label-danger">可用：{{ $list->love->usable or '0' }}</label><br/>
                                    <label class="label label-info">冻结：{{ $list->love->froze or '0' }}</label>
                                </td>
                                <td  style="overflow:visible;">
                                    <a class='btn btn-default' href="{{ yzWebUrl('plugin.love.Backend.Modules.Love.Controllers.recharge.index', array('member_id' => $list->uid)) }}" style="margin-bottom: 2px">充值</a>
                                    <a class='btn btn-default' href="{{ yzWebUrl('plugin.love.Backend.Modules.Love.Controllers.timing-recharge.index', array('member_id' => $list->uid)) }}" style="margin-bottom: 2px">定时充值</a>
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