@extends('layouts.base')
@section('title', '激活详情')
@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('member.member.index')}}">激活详情</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="" method='post' class='form-horizontal'>
                <input type="hidden" name="id" value="{{$detail['uid']}}">
                <input type="hidden" name="op" value="detail">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="yun_shop" />
                <input type="hidden" name="do" value="member" />
                <div class='panel panel-default'>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">激活ID</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->id }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">激活时间</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->created_at }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员</label>
                            <div class="col-sm-9 col-xs-12">
                                <img src='{{ $detail->member->avatar }}' style='width:50px;height:50px;padding:1px;border:1px solid #ccc' />
                                {{ $detail->member->nickname }}
                            </div>
                        </div>

                        <hr>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级订单金额</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->first_order_money }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级激活每周订单金额比例 </label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->first_proportion }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级激活值</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->first_activation_love }}</div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">二、三级订单金额</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->second_three_order_money }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">二、三级激活每周订单金额比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->second_three_proportion }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">最后一次成为代理商奖励爱心值</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->last_upgrade_team_leve_award }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">二、三级最高激活上限比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->second_three_fetter_proportion }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">二、三级激活值</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->second_three_activation_love }}</div>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">团队订单金额</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->team_order_money }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">团队激活每周订单金额比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->team_proportion }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">最后一次成为代理商奖励爱心值</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->last_upgrade_team_leve_award }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">团队最高激活上限比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->second_three_fetter_proportion }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">团队激活值</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->team_activation_love }}</div>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员持有冻结值</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->member_froze_love }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">冻结值持有总量</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->froze_total }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">周期订单利润</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->cycle_order_profit }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">利润激活比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->profit_proportion }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">利润激活值</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->profit_activation_love }}</div>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员当前冻结值</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->member_froze_love }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">固定激活比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->fixed_proportion }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">固定比例激活值</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->fixed_activation_love }}</div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">本次应激活 </label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->sum_activation_love }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">实际激活值</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->actual_activation_love }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">剩余冻结值</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{ $detail->surplus_froze_love }}</div>
                            </div>
                        </div>
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="hidden" name="token" value="{{$var['token']}}" />
                                <input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回" style='margin-left:10px;'/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection