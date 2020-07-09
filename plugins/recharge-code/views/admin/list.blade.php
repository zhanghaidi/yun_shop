@extends('layouts.base')

@section('content')
@section('title', '充值码列表')
<link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>

<div class="rightlist">
    <div class="panel panel-info">
        <div class="panel-body">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="rose">
                    <i class="fa fa-bars" style="font-size: 24px;" aria-hidden="true"></i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">充值码列表</h4>
                    <form action="" method="get" class="form-horizontal" role="form" id="form1">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="recharge" id="form_do"/>
                <input type="hidden" name="route" value="plugin.recharge-code.admin.list.index" id="route" />

                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="">
                        <label class="sr-only"></label>
                        <input class="form-control" placeholder="按微信昵称搜索" name="search[name]" value="{{$search['name']}}">
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="">
                        <select name='search[status]' class='form-control'>
                            <option value=''>过期状态</option>
                            <option value='0' @if($search['status'] == 0 && $search['status'] != '')  selected="selected"@endif>未过期</option>
                            <option value='1' @if($search['status'] == 1)  selected="selected"@endif>已过期</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="">
                        <select name='search[is_bind]' class='form-control'>
                            <option value=''>充值状态</option>
                            <option value='0' @if($search['is_bind'] == 0 && $search['is_bind'] != '')  selected="selected"@endif>未充值</option>
                            <option value='1' @if($search['is_bind'] == 1)  selected="selected"@endif>已充值</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <div class="">
                        <select name='search[type]' class='form-control'>
                            <option value=''>充值类型</option>
                            <option value='1' @if($search['type'] == 1)  selected="selected"@endif>充值积分</option>
                            <option value='2' @if($search['type'] == 2)  selected="selected"@endif>充值余额</option>
                        @if (app('plugins')->isEnabled('love'))
                            <option value='3' @if($search['type'] == 3)  selected="selected"@endif>可用{{ LOVE_NAME }}类型</option>
                            <option value='4' @if($search['type'] == 4)  selected="selected"@endif>冻结{{ LOVE_NAME }}类型</option>
                        @endif
                        </select>
                    </div>
                </div>
                <div class='form-group col-xs-12 col-sm-4 col-md-4 col-lg-4'>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input type="checkbox" name="search[is_time]" value="1" @if($search['is_time'] == '1')checked="checked"@endif>
                            &nbsp;&nbsp;生成时间
                        </span>
                        {!!app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                                                                'starttime'=>$search['time']['start']?$search['time']['start']:date('Y-m-d H:i:s',strtotime('-7 day')),
                                                                'endtime'=>$search['time']['end']?$search['time']['end']:date('Y-m-d H:i:s'),
                                                                'start'=>0,
                                                                'end'=>0
                                                                ], true)!!}
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 pull-right">
                    <div class="text-right">
                        <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>
                        <button type="submit" name="export" value="1" class="btn btn-default excel back ">导出 Excel</button>
                        <button type="submit" name="download" value="1" class="btn btn-default excel back ">下载二维码包</button>
                    </div>
                </div>

            </form>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <table class='table' style='float:left;margin-bottom:0;table-layout: fixed;line-height: 40px;height: 40px'>

            <label class="btn btn-success checkall">全选</label>
            <label class="btn btn-danger batchdel">批量删除</label>

            <tr class='trhead'>
                <td colspan='8' style="text-align: left;">
                    激活码数: <span id="total">{{$list->total()}}</span>
                </td>
            </tr>
        </table>
    @if ($list->total() > 0)
            <div class=" order-info">
                <table class="table table-responsive table-hover">
                    <thead>
                    <tr>
                        <th style="width:16%;text-align: center;">选择</th>
                        <th style="width:16%;text-align: center;">CODE(点击复制)</th>
                        <th style="width:16%;text-align: center;">微信角色</th>
                        <th style="width:16%;text-align: center;">充值类型</th>
                        <th style="width:16%;text-align: center;">充值数量</th>
                        <th style="width:16%;text-align: center;">有效期</th>
                        <th style="width:10%;text-align: center;">充值状态</th>
                        <th style="width:10%;text-align: center;">过期状态</th>
                        <th style="width:12%;text-align: center;">下载</th>
                        <th style="width:12%;text-align: center;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $row)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="check1" value="{{$row->id}}">
                            </td>

                            <td style="text-align: center;">
                                <a href="javascript:;" data-clipboard-text="{{$row->code_key}}" data-url="{{$row->code_key}}" class="js-clip label label-success" title="{{$row->code_key}}">{{$row->code_key}}</a>
                            </td>
                            <td style="text-align: center;">
                                <img src='{{yz_tomedia($row->hasOneMember->avatar)}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                                <a href="{!! yzWebUrl('member.member.detail',['id' => $row->uid])!!}">{{$row->hasOneMember->nickname}}</a>
                            </td>
                            <td style="text-align: center;">
                                {{$row->type_name}}
                            </td>
                            <td style="text-align: center;">
                                {{$row->price}}
                            </td>
                            <td style="text-align: center;">
                                {{$row->time}}
                            </td>
                            <td style="text-align: center;">
                                {{$row->bind_name}}
                            </td>
                            <td style="text-align: center;">
                                {{$row->status_name}}
                            </td>
                            <td style="text-align: center;">
                                <a download="{{$row->qr_code}}" href="{{$row->qr_code}}" title="下载二维码" class="btn btn-default btn-sm js-clip"><i class="fa  fa-file-image-o"></i></a>
                            </td>
                            <td style="text-align: center;">
                                <a class="btn btn-danger" href="{!! yzWebUrl('plugin.recharge-code.admin.list.delete',['id' => $row->id]) !!}">删除</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {!!$pager!!}
            </div>
    @else
        <div class='panel panel-default'>
            <div class='panel-body' style='text-align: center;padding:30px;'>
                暂时没有充值码!
            </div>
        </div>
    @endif
</div>
</div>
<script type="text/javascript">
    $(".checkall").click(function(){
        //全选
        if($(this).html() == '全选') {
            $(this).html('全不选');
            $('[name=check1]:checkbox').prop('checked',true);
        } else {
            $(this).html('全选');
            $('[name=check1]:checkbox').prop('checked',false);
        }
    });
    
    var arr = new Array;

    $(".batchdel").click(function () {
        $(this).html('删除中...');
        $("input[type='checkbox']:checked").each(function(i){
            arr[i] = $(this).val();
        });
        $.post("{!! yzWebUrl('plugin.recharge-code.admin.list.delete') !!}", {id: arr}
            , function (d) {
                if (d.result) {
                    $(".batchdel").html('删除成功');
                    setTimeout(location.reload(), 3000);
                }
            } , "json"
        );
    });
</script>
@endsection