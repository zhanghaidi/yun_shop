@extends('layouts.base')
@section('title', trans('Yunshop\Sign::sign.sign_records'))
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist" style="padding-bottom:100px">

            <div class="panel panel-info">
                <div class="panel-heading">{{ trans('Yunshop\Sign::sign.filter') }}</div>
                <div class="panel-body">
                    <form action="" method="get" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="c" value="site"/>
                        <input type="hidden" name="a" value="entry"/>
                        <input type="hidden" name="m" value="yun_shop"/>
                        <input type="hidden" name="do" value="sign" id="form_do"/>
                        <input type="hidden" name="route" value="plugin.sign.Backend.Modules.Sign.Controllers.sign.index" id="route"/>


                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                            <div class="">
                                <input type="text" placeholder="{{ trans('Yunshop\Sign::sign.member_id') }}" class="form-control" name="search[member_id]" value="{{$search['member_id']}}"/>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control" name="search[realname]" value="{{$search['realname']}}" placeholder="{{ trans('Yunshop\Sign::sign.member_info') }}"/>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <select name='search[member_level]' class='form-control'>
                                    <option value=''>{{ trans('Yunshop\Sign::sign.member_level') }}</option>
                                    @foreach($levels as $level)
                                        <option value='{{$level['id']}}' @if($search['member_level']==$level['id']) selected @endif>{{$level['level_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <select name='search[member_group]' class='form-control'>
                                    <option value=''>{{ trans('Yunshop\Sign::sign.member_group') }}</option>
                                    @foreach($groups as $group)
                                        <option value='{{$group['id']}}' @if($search['member_group']==$group['id']) selected @endif>{{$group['group_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg">

                            <div class="time">

                                <select name='search[search_time]' class='form-control'>
                                    <option value='0'
                                            @if($search['search_time']=='0')
                                            selected
                                            @endif>{{ trans('Yunshop\Sign::sign.search_time_off') }}
                                    </option>
                                    <option value='1'
                                            @if($search['search_time']=='1')
                                            selected
                                            @endif>{{ trans('Yunshop\Sign::sign.search_time_on') }}
                                    </option>
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

                        <div class="form-group  col-xs-12 col-md-12 col-lg-6">
                            <div class="">
                                <button class="btn btn-success "><i class="fa fa-search"></i>{{ trans('Yunshop\Sign::sign.button_search') }}</button>
                                <button type="button" name="export" value="1" id="export" class="btn btn-default">{{ trans('Yunshop\Sign::sign.button_export') }}</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>



            <div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading"></div>
                    <div class="panel-body" style="margin-bottom:200px">
                        <table class="table table-hover" style="overflow:visible">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:7%;text-align: center;'>{{ trans('Yunshop\Sign::sign.sign_column_one') }}</th>
                                <th style='width:8%;text-align: center;'>{{ trans('Yunshop\Sign::sign.sign_column_two') }}</th>
                                <th style='width:10%;text-align: center;'>{{ trans('Yunshop\Sign::sign.sign_column_three') }}</th>
                                <th style='width:10%;text-align: center;'>{{ trans('Yunshop\Sign::sign.sign_column_four') }}</th>
                                <th style='width:10%;text-align: center;'>{{ trans('Yunshop\Sign::sign.sign_column_five') }}</th>
                                <th style='width:10%;text-align: center;'>{{ trans('Yunshop\Sign::sign.sign_column_six') }}</th>
                                <th style='width:12%;text-align: center;'>{{ trans('Yunshop\Sign::sign.sign_column_seven') }}</th>
                                <th style='width:10%;text-align: center;'>{{ trans('Yunshop\Sign::sign.sign_column_eight') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($page_list as $key => $item)
                                <tr style="text-align: center">
                                    <td style="text-align: center;">{{ $item->member->uid }}</td>

                                    <td style="text-align: center;">
                                        @if(!empty($item->member->avatar))
                                            <img src='{{ $item->member->avatar }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/><br/>
                                        @endif
                                        @if(empty($item->member->nickname))
                                            未更新
                                        @else
                                            {{ $item->member->nickname }}
                                        @endif
                                    </td>

                                    <td>{{ $item->member->realname }}<br/>{{ $item->member->mobile }}</td>

                                    <td>{{ $item->updated_at }}</td>

                                    <td>
                                        @if($item->sign_status)
                                            <label class="label label-info">已{{trans('Yunshop\Sign::sign.plugin_name')}}</label>
                                        @else
                                            <label class="label label-danger">未{{trans('Yunshop\Sign::sign.plugin_name')}}</label>
                                        @endif
                                    </td>

                                    <td>{{ $item->cumulative_name }}</td>
                                    <td>
                                        积分：{{ $item->cumulative_point }}<br>
                                        优惠券：{{ $item->cumulative_coupon }}张<br>
                                        爱心值：{{ $item->cumulative_love }}
                                    </td>
                                    <td  style="overflow:visible;">
                                        <a class='btn btn-default' href="{{ yzWebUrl('plugin.sign.Backend.Modules.Sign.Controllers.sign-log.index', array('member_id' => $item->member->uid)) }}" style="margin-bottom: 2px">{{ trans('Yunshop\Sign::sign.plugin_name') }}详情</a>
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
            $('#export').click(function () {
                $('#route').val("plugin.sign.Backend.Modules.Sign.Controllers.sign.export");
                $('#form1').submit();
                $('#route').val("plugin.sign.Backend.Modules.Sign.Controllers.sign.index");
            });
        });
    </script>
@endsection