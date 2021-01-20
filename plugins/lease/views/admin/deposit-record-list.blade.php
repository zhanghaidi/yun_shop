@extends('layouts.base')
@section('title', '记录列表')
@section('content')

    
    <div class="w1200 m0a">
        <div class="rightlist" style="padding-bottom:100px">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{!! yzWebUrl('plugin.lease-toy.admin.deposit-record.index') !!}">记录列表</a></li>
                    <li><a  style="padding-left:0" href="javascript:void"><i class="fa fa-angle-double-right"></i>全部记录</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info"><!--
                <div class="panel-heading">筛选</div>-->
                <div class="panel-body">
                    <form action="" method="post" class="form-horizontal" role="form" id="form1">
                    <!--     <input type="hidden" name="c" value="site"/>
                        <input type="hidden" name="a" value="entry"/>
                        <input type="hidden" name="m" value="yun_shop"/>
                        <input type="hidden" name="do" value="member" id="form_do"/>
                        <input type="hidden" name="route" value="" id="route"/> -->
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control" name="search[realname]"
                                       value="{{$search['realname']}}" placeholder="可搜索昵称/姓名/手机号"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <select name="search[level]" class='form-control'>
                                    <option value=''>会员等级不限</option>
                                    @foreach($levels as $level)
                                        <option value="{{$level['id']}}"
                                                @if($search['level']==$level['id'])
                                                selected
                                                @endif
                                        >{{$level['level_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-xs-1 col-sm-1 col-md-1 col-lg-1 control-label">押金区间</label>
                            <div class="col-xs-3 col-sm-2 col-lg-2">
                                <input class="form-control" name="search[min_deposit]" id="minprice" type="text" value="{{$search['min_deposit']}}" onclick="value='';" ／>
                            </div>
                            <div style="float: left;margin: 0 -20px;padding-top: 5px;">——</div>
                            <div class="col-xs-3 col-sm-2 col-lg-2">
                                <input class="form-control" name="search[max_deposit]" id="max_price" type="text" value="{{$search['max_deposit']}}" onclick="value='';" ／>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <button class="btn btn-success" id="search"><i class="fa fa-search"></i> 搜索</button>
                                <button type="button" name="export" value="1" id="export" class="btn btn-default">导出
                                    Excel
                                </button>


                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">总数：{{$total}}  </div>
                    <div class="panel-body" style="margin-bottom:200px">
                        <table class="table table-hover" style="overflow:visible">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:7%;text-align: center;'>会员ID</th>
                                <th style='width:12%;text-align: center;'>粉丝</th>
                                <th style='width:13%;'>姓名<br/>手机号码</th>
                                <th style='width:10%;'>会员等级</th>
                                <th style='width:15%;'>押金(元)</th>
                                <th style='width:13%'>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list['data'] as $row)
                                <tr>
                                    <td style="text-align: center;">   {{$row['member_id']}}</td>
                                    <td style="text-align: center;">
                                        @if(!empty($row['belongs_to_member']['avatar']))
                                            <img src="{{$row['belongs_to_member']['avatar']}}"
                                                 style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/><br/>
                                        @endif
                                        @if(empty($row['belongs_to_member']['nickname']))
                                            未更新
                                        @else
                                            {{$row['belongs_to_member']['nickname']}}
                                        @endif
                                    </td>
                                    <td>{{$row['belongs_to_member']['realname']}}<br/>{{$row['belongs_to_member']['mobile']}}</td>
                                    <td>
                                        {{$row['levelname']}}
                                    </td>
                                    <td>{{$row['total_deposit']}}</td>
                                    <td  style="overflow:visible;">
                                        <a class="btn btn-info" href="{{yzWebUrl('member.member.detail', ['id' => $row['member_id']])}}" title="会员详情">会员详情</a>

                                        <a class="btn btn-info" href="{{yzWebUrl('plugin.lease-toy.admin.deposit-record.detail', ['lease_id' => $row['member_id']])}}">押金记录</span></a>

                                    </td>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!!$pager!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script language='javascript'>
        $(function () {
            $('#export').click(function () {
                $('#form1').attr('action', '{!! yzWebUrl('plugin.lease-toy.admin.deposit-record.export') !!}');
                $('#form1').submit();
            });
            $('#search').click(function () {
                $('#form1').attr('action', '{!! yzWebUrl('plugin.lease-toy.admin.deposit-record.index') !!}');
                $('#form1').submit();
            });
        });
    </script>
@endsection