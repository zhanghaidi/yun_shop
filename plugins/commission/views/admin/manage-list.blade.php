@extends('layouts.base')

@section('content')
@section('title', trans('分销管理奖'))
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">分销管理奖</a></li>
        </ul>
    </div>


    <div class='panel panel-default'>
        <form action="" method="post" class="form-horizontal">
            <div class="panel panel-info">
                <div class="panel-body">


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">获奖者信息</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="search[member]" type="text"
                                   value="{{$search['member']}}" placeholder="会员ID/昵称/手机">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">管理奖状态</label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <select name='search[status]' class='form-control'>
                                <option value=''>所有状态</option>
                                <option value='0' @if($search['status'] == '0') selected @endif>未提现</option>
                                <option value='1' @if($search['status'] == '1') selected @endif>已提现</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">管理奖层级</label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <select name='search[hierarchy]' class='form-control'>
                                <option value=''>所有层级</option>
                                <option value='1' @if($search['hierarchy'] == '1') selected @endif>一级</option>
                                <option value='2' @if($search['hierarchy'] == '2') selected @endif>二级</option>
                                {{--<option value='3' @if($search['hierarchy'] == '3') selected @endif>三级</option>--}}
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"> </label>
                        <div class="col-xs-12 col-sm-2 col-lg-2">
                            <input type="submit" class="btn btn-success" value="搜索">
                        </div>
                    </div>

                </div>
            </div>
        </form>


        <div class='panel-heading'>
            管理 (数量: {{$total}} 条)
        </div>
        <div class='panel-body'>

            <table class="table table-hover" style="overflow:visible;">
                <thead class='panel panel-default'>
                <tr class='panel-heading'>
                    <th style='width:5%;'>ID</th>
                    <th style='width:15%;'>时间</th>
                    <th style='width:15%;'>获得者信息</th>
                    <th style='width:10%;'>粉丝信息</th>
                    <th style='width:15%;'>粉丝佣金金额(元)</th>
                    <th style='width:15%;'>粉丝层级</th>
                    <th style='width:15%;'>管理奖比例（%）</th>
                    <th style='width:10%;'>管理奖金额（元）</th>
                    <th style='width:10%;'>管理奖状态</th>

                </tr>
                </thead>
                <tbody>
                @foreach($list as $row)
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{$row->created_at}}</td>
                        <td>
                            <img src="{{$row->hasOneMember->avatar}}"
                                 style="width: 40px; height: 40px;border:1px solid #ccc;padding:1px;">
                            </br>{{$row->hasOneMember->nickname}}</td>
                        <td>
                            <img src="{{$row->hasOneSubordinate->avatar}}"
                                 style="width: 40px; height: 40px;border:1px solid #ccc;padding:1px;">
                            </br>{{$row->hasOneSubordinate->nickname}}</td>

                        <td>{{$row->subordinate_commission}}</td>
                        <td>{{$row->hierarchy}}（级）</td>
                        <td>{{$row->manage_rage}} %</td>
                        <td>{{$row->manage_amount}}</td>
                        <td>{{$row->status_name}}</td>

                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $pager !!}
        </div>
    </div>

@endsection
