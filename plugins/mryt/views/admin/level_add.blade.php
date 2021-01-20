@extends('layouts.base')
@section('title', trans('等级管理'))
@section('content')
    <style>
        .radio-inline + .radio-inline {
            margin: 0;
        }

        .row {
            width: 100%;
        }

        #goodsthumb {
            margin-left: 20px;
        }
    </style>
    <form action="" method="post" class="form-horizontal"  id="form1" >
        <div class="panel panel-info">
            <div class="panel panel-default">
                <div class="panel-heading">等级管理</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">等级权重</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="set[level_weight]" class="form-control" value="{{$level['level_weight']?:1}}" autocomplete="off" >
                            <span class="help-block">等级权重，数字越大级别越高。</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">等级名称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="set[level_name]" class="form-control" value="{{$level['level_name']}}" autocomplete="off">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$set['teammanage_name']}}比例%</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="set[team_manage_ratio]" class="form-control" value="{{$level['team_manage_ratio']}}" autocomplete="off">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$set['team_name']}}</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="set[team]" class="form-control" value="{{$level['team']}}" autocomplete="off">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$set['thanksgiving_name']}}</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="set[thankful]" class="form-control" value="{{$level['thankful']}}" autocomplete="off">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$set['parenting_name']}}比例%</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="set[train_ratio]" class="form-control" value="{{$level['train_ratio']}}" autocomplete="off">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ $set['referral_name'] }}</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="set[direct]" class="form-control" value="{{$level['direct']}}" autocomplete="off">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$set['tier_name']}}层级</label>
                        <div class="col-sm-9 col-xs-12">
                            <input onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" type="text" name="set[tier]" class="form-control" value="{{$level['tier']}}" autocomplete="off">
                            <span class="help-block">设置0或不设置,不限制层级</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$set['tier_name']}}金额</label>
                        <div class="col-sm-9 col-xs-12">
                            <input onkeyup="this.value= this.value.match(/\d+(\.\d{0,2})?/) ? this.value.match(/\d+(\.\d{0,2})?/)[0] : ''" type="text" name="set[tier_amount]" class="form-control" value="{{$level['tier_amount']}}" autocomplete="off">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">签署合同</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="set[contract]" value="1"
                                       @if ($level['contract'] == 1)
                                       checked
                                        @endif> 是
                            </label>

                            <label class="radio-inline">
                                <input type="radio" name="set[contract]" value="0"
                                       @if (empty($level['contract']) || $level['contract'] == 0)
                                       checked
                                        @endif> 否
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">直推商家管理权限</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="set[is_username]" value="1"
                                       @if ($level['is_username'] == 1)
                                       checked
                                        @endif> 是
                            </label>

                            <label class="radio-inline">
                                <input type="radio" name="set[is_username]" value="0"
                                       @if (empty($level['is_username']) || $level['is_username'] == 0)
                                       checked
                                        @endif> 否
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">管理账号</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="set[username]" class="form-control" value="{{$level['username']}}">
                            <span class="help-block">账号为：[管理账号] + [会员ID] 。例如：合伙人1002。为空统一为：合伙人+[会员ID]</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">默认密码</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="set[password]" class="form-control" value="{{$level['password']}}">
                            <span class="help-block">为空统一为：ch123456</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">自动提现</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="set[auto_withdraw]" value="1"
                                       @if ($level['auto_withdraw'] == 1)
                                       checked
                                        @endif> 是
                            </label>

                            <label class="radio-inline">
                                <input type="radio" name="set[auto_withdraw]" value="0"
                                       @if (empty($level['auto_withdraw']) || $level['auto_withdraw'] == 0)
                                       checked
                                        @endif> 否
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">自动提现日期</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="set[withdraw_time]" class="form-control" value="{{$level['withdraw_time'] ?: 0}}">
                            <span class="help-block">例如：25号，则在每月25号自动完成手动提现流程。为0则不自动提现</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">升级条件</label>
                        <div class="col-xs-12 col-sm-9 col-md-10">
                            <div class="input-group row">
                                <label class="radio-inline col-xs-12 col-sm-6">
                                    <input type="checkbox" name="upgrade_type[direct_vip]" value="1"
                                           @if ($upgrade_type['direct_vip'])
                                           checked
                                            @endif
                                    >
                                    直推{{$set['default_level']}}人数
                                    <input type="text" name="upgrade_value[direct_vip]"
                                           value="{{$upgrade_value['direct_vip']}}">
                                    <span>人</span>
                                </label>

                                <label class="radio-inline  col-xs-12 col-sm-6">
                                    <input type="checkbox" name="upgrade_type[team_vip]" value="1"
                                           @if ($upgrade_type['team_vip'])
                                           checked
                                            @endif
                                    >
                                    团队{{$set['default_level']}}人数
                                    <input type="text" name="upgrade_value[team_vip]"
                                           value="{{$upgrade_value['team_vip']}}">
                                    <span>人</span>
                                </label>

                            </div>

                            <div class="input-group row">
                                <label class="radio-inline col-xs-12 col-sm-6">
                                    <input type="checkbox" name="upgrade_type[settle_money]" value="1"
                                           @if ($upgrade_type['settle_money'])
                                           checked
                                            @endif
                                    >
                                    个人销售佣金
                                    <input type="text" name="upgrade_value[settle_money]"
                                           value="{{$upgrade_value['settle_money']}}">
                                    <span>元</span>
                                </label>
                                <label class="radio-inline col-xs-12 col-sm-6">
                                    <input type="checkbox" name="upgrade_type[team_cost_count]" value="1"
                                           @if ($upgrade_type['team_cost_count'])
                                           checked
                                            @endif
                                    >
                                    团队中个人销售佣金 达到
                                    <input type="text" name="upgrade_value[team_cost_count]" style="width: 50px;"
                                           value="{{$upgrade_value['team_cost_count']}}">
                                    <span>元</span>
                                    的VIP人数
                                    <input type="text" name="upgrade_value[team_cost_num]" style="width: 50px;"
                                           value="{{$upgrade_value['team_cost_num']}}">
                                    <span>人</span>
                                </label>
                            </div>

                            @if ($level_set)
                                @foreach ($level_list as $key => $item)
                                    @if ($key%2 == 0)
                                        <div class="input-group row">
                                    @endif
                                        <label class="radio-inline col-xs-12 col-sm-6">
                                            <input type="checkbox" name="upgrade_type[level][{{$item->id}}]"
                                                   value="1"
                                                   @if ($upgrade_type['level'][$item->id])
                                                   checked
                                                    @endif
                                            >
                                            直推{{$item->level_name}}人数满
                                            <input type="text" name="upgrade_value[level][{{$item->id}}]"
                                                   value="{{$upgrade_value['level'][$item->id]}}">
                                            <span>人</span>
                                        </label>
                                        <label class="radio-inline col-xs-12 col-sm-6">
                                            <input type="checkbox" name="upgrade_type[team][{{$item->id}}]"
                                                   value="1"
                                                   @if ($upgrade_type['team'][$item->id])
                                                   checked
                                                   @endif
                                            >
                                            团队{{$item->level_name}}人数满
                                            <input type="text" name="upgrade_value[team][{{$item->id}}]"
                                                   value="{{$upgrade_value['team'][$item->id]}}">
                                            <span>人</span>
                                        </label>
                                    @if ($key%2 != 0)
                                        </div>
                                    @endif
                                @endforeach
                            @endif


                            {{--<div class="input-group row">--}}
                                {{--<div class="input-group">--}}
                                    {{--<label class="radio-inline">--}}
                                        {{--<input type="checkbox" name="upgrade_type[goods]" value="1"--}}
                                               {{--@if ($upgrade_type['goods'])--}}
                                               {{--checked--}}
                                                {{--@endif--}}
                                        {{-->--}}
                                        {{--购买指定商品--}}

                                        {{--<input type="hidden" id="goodsid" name="upgrade_value[goods]"--}}
                                               {{--value="{{$upgrade_data->id}}">--}}
                                        {{--<div class="input-group">--}}
                                            {{--<input type="text" name="goods" maxlength="30"--}}
                                                   {{--value="@if(isset($upgrade_data)) [{{$upgrade_data->id}}]{{$upgrade_data->title}} @endif"--}}
                                                   {{--id="goods" class="form-control" readonly="">--}}
                                            {{--<div class="input-group-btn">--}}
                                                {{--<button class="btn btn-default" type="button"--}}
                                                        {{--onclick="popwin = $('#modal-module-menus-goods').modal();">--}}
                                                    {{--选择商品--}}
                                                {{--</button>--}}
                                                {{--<button class="btn btn-danger" type="button"--}}
                                                        {{--onclick="$('#goodsid').val('');$('#goods').val('');">--}}
                                                    {{--清除选择--}}
                                                {{--</button>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</label>--}}

                                    {{--<span id="goodsthumb" class='help-block'--}}
                                          {{--@if(empty($upgrade_data)) style="display:none" @endif ><img--}}
                                                {{--style="width:100px;height:100px;border:1px solid #ccc;padding:1px"--}}
                                                {{--src="@if(isset($upgrade_data->thumb)) {{tomedia($upgrade_data->thumb) }} @endif"/></span>--}}
                                {{--</div>--}}

                                {{--<div class="input-group">
                                    <label class="radio-inline"><input type="radio" name="upgrade_type[become]" value="0" @if (empty($upgrade_type['become'])) checked @endif style="margin: 4px 0 0; position:inherit"> 付款后</label>
                                    <label class="radio-inline"><input type="radio" name="upgrade_type[become]" value="1" @if (1 == $upgrade_type['become']) checked @endif style="margin: 4px 0 0; position:inherit"> 完成后</label>
                                </div>
                                <span class="help-block">获取推广下线权利的会员达到条件，自动升级为对应等级的代理商；指定某团队等级人数升级需添加完成后编辑</span>--}}
                            </div>
                        </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="hidden" name="token" value="{$_W['token']}">
                            <input type="submit" id="submit" value="保存" class="btn btn-primary" onclick="return formValidator()">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <div class="form-group">
        <div class="col-sm-9">
            <div id="modal-module-menus-goods" class="modal fade" tabindex="-1">
                <div class="modal-dialog" style='width: 920px;'>
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-hidden="true" data-dismiss="modal"
                                    class="close" type="button">
                                ×
                            </button>
                            <h3>选择商品</h3></div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                           name="keyword" value=""
                                           id="search-kwd-goods"
                                           placeholder="请输入商品名称"/>
                                    <span class='input-group-btn'>
                                                            <button type="button" class="btn btn-default"
                                                                    onclick="search_goods();">搜索
                                                            </button></span>
                                </div>
                            </div>
                            <div id="module-menus-goods"
                                 style="padding-top:5px;"></div>
                        </div>
                        <div class="modal-footer"><a href="#"
                                                     class="btn btn-default"
                                                     data-dismiss="modal"
                                                     aria-hidden="true">关闭</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group"></div>

    <script  language='javascript'>
        function formValidator(){
            // 判断表单内所有元素验证全部通过
            if($(":input[name='set[level_weight]']").val() <= 0){
                $(":input[name='set[level_weight]']").focus();
                alert("等级权重必须大于0");
                return false;
            }
            var level_weight = $(":input[name='set[level_weight]']").val();
            var id = '{!! $level['id'] ?: 0 !!}';
            var exit = false;
            $.ajax({
                type : "post",
                url : "{!! yzWebUrl('plugin.mryt.admin.level.validator') !!}",
                data : {
                    data:level_weight,
                    id:id
                },
                async : false,
                dataType: 'json',
                success : function(data){
                    exit = data.data;
                }
            });
            if (exit) {
                $(":input[name='set[level_weight]']").focus();
                alert("该等级权重已存在");
                return false;
            } else {
                return true;
            }
        }
        function search_goods() {
            if ($.trim($('#search-kwd-goods').val()) == '') {
                Tip.focus('#search-kwd-goods', '请输入关键词');
                return;
            }
            $("#module-menus-goods").html("正在搜索....");
            $.get('{!! yzWebUrl('goods.goods.get-search-goods') !!}', {
                    keyword: $.trim($('#search-kwd-goods').val())
                }, function (dat) {
                    $('#module-menus-goods').html(dat);
                }
            )
            ;
        }
        function select_good(o) {
            $("#goodsid").val(o.id);
            $("#goodsthumb").show();
            $("#goodsthumb").find('img').attr('src', o.thumb);
            $("#goods").val("[" + o.id + "]" + o.title);
            $("#modal-module-menus-goods .close").click();
        }
    </script>
@endsection
