@extends('layouts.base')
@section('title', '记录')
@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-body">
                <form action="" method="post" class="form-horizontal" role="form" id="form1">
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <div class="">
                            <input type="text" placeholder="会员uid" class="form-control"  name="search[uid]" value="{{$search['uid']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <div class="">
                            <input type="text" placeholder="会员信息" class="form-control"  name="search[member]" value="{{$search['member']}}"/>
                        </div>
                    </div>

                    <div class="form-group  col-xs-12 col-sm-5 col-lg-4">
                        <div class="">
                            {{--<button type="submit" name="export" value="1" id="export" class="btn btn-default excel back ">导出Excel</button>
                            <input type="hidden" name="token" value="{{$var['token']}}" />--}}
                            <button class="btn btn-success "><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">
                    总数：{{ $list->total() }}
                </div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:10%;text-align: center;'>ID</th>
                            <th style='width:16%;text-align: center;'>时间</th>
                            <th style='width:20%;text-align: center'>会员信息</th>
                            <th style='width:54%;text-align: center'>备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $item)
                            <tr style="text-align: center">
                                <td style="text-align: center;">
                                    {{ $item->id }}
                                </td>
                                <td style="text-align: center;">
                                    {{ $item->created_at }}
                                </td>
                                <td style="text-align: center;">
                                    <img src='{{ yz_tomedia($item->hasOneMember->avatar) }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    <a href="{!! yzWebUrl('member.member.detail',['id' => $item->uid])!!}">
                                        {{ $item->hasOneMember->nickname }}
                                    </a>
                                    <br>
                                    {{ $item->hasOneMember->mobile }}
                                </td>
                                <td style="text-align: center;">
                                    @if($item->type == 1)
                                        <a href="{!! yzWebUrl('order.detail',['id' => $item->source_id])!!}">
                                            {{ $item->remark }}
                                        </a>
                                    @else
                                        <a href="{!! yzWebUrl('member.member.detail',['id' => $item->source_id])!!}">
                                            {{ $item->remark }}
                                        </a>
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
    <script language='javascript'>
        $(function () {
            $('#export').click(function(){
                $('#form1').submit();
            });
        });
    </script>
@endsection