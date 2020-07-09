@extends('layouts.base')
@section('title', trans('Yunshop\Love::change_records.title'))
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="">{{ trans('Yunshop\Love::change_records.subtitle') }}</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info">
                <div class="panel-body">
                    <form action="" method="get" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="c" value="site" />
                        <input type="hidden" name="a" value="entry" />
                        <input type="hidden" name="m" value="yun_shop" />
                        <input type="hidden" name="do" value="5201" />
                        <input type="hidden" name="route" value="plugin.love.Backend.Modules.Love.Controllers.change-records.index" id="route" />
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                            <div class="">
                                <input type="text" placeholder="{{ trans('Yunshop\Love::change_records.search_member_id') }}" class="form-control"  name="search[member_id]" value="{{$search['member_id']}}"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control"  name="search[realname]" value="{{$search['realname']}}" placeholder="{{ trans('Yunshop\Love::change_records.search_member') }}"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <select name='search[member_level]' class='form-control'>
                                    <option value=''>{{ trans('Yunshop\Love::change_records.search_member_level') }}</option>

                                    @foreach($memberLevels as $list)
                                        <option value='{{ $list['id'] }}' @if($search['member_level'] == $list['id']) selected @endif>{{ $list['level_name'] }}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员等级</label>-->
                            <div class="">
                                <select name='search[member_group]' class='form-control'>
                                    <option value=''>{{ trans('Yunshop\Love::change_records.search_member_group') }}</option>
                                    @foreach($memberGroups as $list)
                                        <option value='{{ $list['id'] }}' @if($search['member_group'] == $list['id']) selected @endif>{{ $list['group_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!--  <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员分组</label>-->
                            <div class="">
                                <select name='search[source]' class='form-control'>
                                    <option value=''>{{ trans('Yunshop\Love::change_records.search_source') }}</option>
                                    @foreach($sourceName as $key => $value)
                                        <option value='{{ $key }}' @if($search['source'] == $key) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!--        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">黑名单</label>-->
                            <div class="">
                                <select name='search[type]' class='form-control'>
                                    <option value=''>{{ trans('Yunshop\Love::change_records.search_type') }}</option>
                                    <option value='1' @if($search['type']=='1') selected @endif>{{ trans('Yunshop\Love::change_records.search_type_income') }}</option>
                                    <option value='2' @if($search['type']=='2') selected @endif>{{ trans('Yunshop\Love::change_records.search_type_expend') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" placeholder="订单号" class="form-control"  name="search[order_sn]" value="{{$search['order_sn']}}"/>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-6">

                            <div class="time">

                                <select name='search[search_time]' class='form-control'>
                                    <option value='0' @if($search['search_time']=='0') selected @endif>{{ trans('Yunshop\Love::change_records.search_time_off') }}</option>
                                    <option value='1' @if($search['search_time']=='1') selected @endif>{{ trans('Yunshop\Love::change_records.search_time_on') }}</option>
                                </select>
                            </div>
                            <div class="search-select">
                                {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                                'starttime'=>date('Y-m-d H:i', strtotime($search['time']['start']) ?: strtotime('-1 month')),
                                'endtime'=>date('Y-m-d H:i',strtotime($search['time']['end']) ?: time()),
                                'start'=>0,
                                'end'=>0
                                ], true) !!}
                            </div>
                        </div>

                        <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                            <div class="">
                                <button type="button" name="export" value="1" id="export" class="btn btn-default excel back ">{{ trans('Yunshop\Love::change_records.button.export') }}</button>
                                <input type="hidden" name="token" value="{{$var['token']}}" />
                                <button class="btn btn-success "><i class="fa fa-search"></i>{{ trans('Yunshop\Love::change_records.button.search') }}</button>

                            </div>
                        </div>

                    </form>
                </div>
            </div><div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('Yunshop\Love::change_records.total') }}：{{$pageList->total()}}   </div>
                    <div class="panel-body" style="margin-bottom:200px">
                        <table class="table table-hover" style="overflow:visible">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:10%;text-align: center;'>{{ trans('Yunshop\Love::change_records.menu.menu_one') }}</th>
                                <th style='width:10%;text-align: center;'>{{ trans('Yunshop\Love::change_records.menu.menu_two') }}</th>
                                <th style='width:10%;text-align: center;'>{{ trans('Yunshop\Love::change_records.menu.menu_three') }}</th>
                                <th style='width:8%;text-align: center;'>{{ trans('Yunshop\Love::change_records.menu.menu_four') }}</th>
                                <th style='width:8%;text-align: center;'>{{ trans('Yunshop\Love::change_records.menu.menu_five') }}</th>
                                <th style='width:8%;text-align: center;'>{{ trans('Yunshop\Love::change_records.menu.menu_six') }}</th>
                                <th style='width:8%;text-align: center;'>{{ trans('Yunshop\Love::change_records.menu.menu_seven') }}</th>
                                <th style='width:8%;text-align: center;'>{{ trans('Yunshop\Love::change_records.menu.menu_eight') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pageList as $key => $list)
                                <tr style="text-align: center;">
                                    <td>
                                        {{ $list->created_at }}
                                    </td>
                                    <td>
                                        {{ $list->relation }}
                                    </td>
                                    <td>
                                        <a href="{{ yzWebUrl('member.member.detail',['id' => $list->member_id]) }}">
                                            @if($list->member->avatar || $shopSet['headimg'])
                                                <img src='{{ $list->member->avatar ? tomedia($list->member->avatar) : tomedia($shopSet['headimg'])}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/>
                                                <br/>
                                            @endif
                                                {{ $list->member->realname ?: ($list->member->nickname ? $list->member->nickname : '未更新') }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $list->member->yzMember->level->level_name ?: $shopSet['level_name'] ?: '普通等级'}}<br>
                                        {{ $list->member->yzMember->group->group_name ?: '无分组' }}
                                    </td>
                                    <td>
                                        {{ $list->source_name }}
                                    </td>
                                    <td>{{ $list->change_value or "0.00" }}</td>
                                    <td>
                                        @if($list->value_type == \Yunshop\Love\Common\Services\ConstService::VALUE_TYPE_USABLE)
                                            <label class="label label-danger">可用：{{ $list->new_value }}</label>
                                        @else
                                            <label class="label label-info">冻结：{{ $list->new_value }}</label>
                                        @endif
                                    </td>
                                    <td  style="overflow:visible;">
                                        {{ $list->value_type_name }}
                                       {{--<a class='btn btn-default' href="{{ yzWebUrl('plugin.love.Backend.Modules.Love.Controllers.change-record-detail.index', array('record_id' => $list->id)) }}" style="margin-bottom: 2px">查看明细</a>--}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!!$page!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script language='javascript'>
        $(function () {
            $('#export').click(function(){
                $('#route').val("plugin.love.Backend.Modules.Love.Controllers.change-records.export");
                $('#form1').submit();
                $('#route').val("plugin.love.Backend.Modules.Love.Controllers.change-records.index");
            });
        });
    </script>
@endsection