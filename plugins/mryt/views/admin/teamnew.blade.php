@extends('layouts.base')
@section('title', trans('团队新进人员统计'))
@section('content')
<form action="{!! yzWebUrl('plugin.mryt.admin.teamnew') !!}" method="post" class="form-horizontal" id="form1">
    <div class="panel panel-info">
        <div class="panel panel-default">
            <div class="panel-heading">团队新进人员统计</div>
            <!-- list1 -->
            <div class="form-group">
                <div class="col-md-3 col-xs-12">
                    <label class="col-md-4 col-sm-12 col-xm-4 control-label">会员ID</label>
                    <div class="col-md-8 col-xs-12">
                        <input type="text" class="form-control" name="search[member]" value="{{$search['member']}}" placeholder="请输入会员ID">
                    </div>
                </div>
                <div class="col-md-4 col-xs-12">
                    <label class="col-md-4 col-sm-12 col-xm-4 control-label">会员信息</label>
                    <div class="col-md-6 col-sm-12 col-xm-4">
                        <input type="text" class="form-control" name="search[name]" value="{{$search['name']}}" placeholder="请输入会员姓名、昵称、手机号">
                    </div>
                </div>
                <div class="col-md-3 col-xs-12">
                    <div class='col-md-10'>
                        <select class="form-control" name="search[level]">
                            <option selected="selected" value="-1">会员等级</option>
                            @if (!is_null($level))
                                @foreach ($level as $item)
                            <option value="{{$item->id}}" @if ($search['level'] == $item->id) selected @endif>{{$item->level_name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <!-- <div class="col-md-4 col-sm-12 col-xm-4"> -->
                    <button class="btn btn-success" type="submit">搜索</button>
                    <!-- </div> -->
                </div>
                <!-- list2 -->

                <!-- 表格 -->
                <div class="bs-example" data-example-id="simple-responsive-table" style='margin-top:50px'>
                    <div class="table-responsive">
                        <table class="table" style=''>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>会员信息</th>
                                    <th>会员等级</th>
                                    <th style='text-align:center'>{{substr($show_month_3, 4)}}月份新进VIP人数</th>
                                    <th style='text-align:center'>{{substr($show_month_2, 4)}}月份新进VIP人数</th>
                                    <th style='text-align:center'>{{substr($show_month_1, 4)}}月份新进VIP人数</th>
                                    <th style='text-align:center'>近三月新进VIP人数</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if (!empty($list))
                                @foreach ($list as $key => $item)
                                <tr>
                                    <!-- <th scope="row">1</th> -->
                                    <td>{{$key}}</td>
                                    <td>
                                        <img src="{!! $item['avatar'] !!}"
                                            alt="" style="width:32px; display: block;">
                                        {{$item['nickname']}}
                                    </td>
                                    <td>{{$item['level']}}</td>
                                    <td style='text-align:center'>{{$item[$show_month_3]}}</td>
                                    <td style='text-align:center'>{{$item[$show_month_2]}}</td>
                                    <td style='text-align:center'>{{$item[$show_month_1]}}</td>
                                    <td style='text-align:center'>{{$item[$show_month_1] + $item[$show_month_2] + $item[$show_month_3]}}</td>
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive -->
                </div>
            </div>
            <!-- 分页 -->
        </div>
    </div>
</form>

<style>
    .num {
        width: 60px;
        height: 30px;
        background: rgb(235, 57, 19);
        color: #fff;
        text-align: center;
        line-height: 30px;
        border-radius: 10%;
    }

    .num-gray {
        width: 60px;
        height: 30px;
        background: rgb(153, 153, 153);
        color: #fff;
        text-align: center;
        line-height: 30px;
        border-radius: 10%;
    }
</style>

@endsection