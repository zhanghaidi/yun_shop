@extends('layouts.base')

@section('content')
@section('title', trans('分销商等级'))
    <div class="w1200 m0a">
        <section class="content">
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">分销商等级</a></li>
                </ul>
            </div>
            <form id="setform" action="" method="post" class="form-horizontal form">
                <div class='panel panel-default'>


                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">等级权重</label>
                            <div class="col-sm-9 col-xs-12">
                                <input class="form-control" type="text" value="{{$levelModel['level']}}"
                                       name="level[level]">
                                <span class="help-block">等级权重一定要设置，且不能重复，数字越大级别越高。</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                        style='color:red'>*</span>
                                等级名称</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="level[name]" class="form-control"
                                       value="{{$levelModel['name']}}"/>
                            </div>
                        </div>
                        @if($set['level']>=1)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级分销比例</label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="text" name="level[first_level]" class="form-control"
                                           value="{{$levelModel['first_level']}}"/>
                                </div>
                            </div>
                        @endif
                        @if($set['level']>=2)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">二级分销比例</label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="text" name="level[second_level]" class="form-control"
                                           value="{{$levelModel['second_level']}}"/>
                                </div>
                            </div>
                        @endif
                        @if($set['level']>=3)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">三级分销比例</label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="text" name="level[third_level]" class="form-control"
                                           value="{{$levelModel['third_level']}}"/>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">额外分红比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <input onkeyup="value=value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')" class="form-control" type="text" value="{{$levelModel['additional_ratio']}}"
                                       name="level[additional_ratio]">
                                <span class="help-block">填写额外分红比例才会进行额外分红</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">限制提现</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline">
                                    <input type="radio" name="level[no_withdraw]" value="0"
                                           @if($levelModel['no_withdraw'] == 0)
                                           checked="checked" @endif />
                                    关闭</label>
                                <label class="radio-inline">
                                    <input type="radio" name="level[no_withdraw]" value="1"
                                           @if($levelModel['no_withdraw'] == 1)
                                           checked="checked" @endif />
                                    开启</label>
                                <span class='help-block'>开启则不可提现</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">升级条件</label>
                            <div class="col-sm-9 col-xs-12">

                                <div class='input-group'>
                                    <label class="radio-inline">
                                        <input type="checkbox" name="upgrade_type[buy_and_sum]"
                                               value="1"
                                               @if($upgrade_data['buy_and_sum']) checked="checked" @endif/>
                                        一级客户消费满
                                        <input onkeyup="value=value.replace(/[^\d]/g,'')" type="text" name="upgrade_value[buy]"
                                               value="{{$upgrade_data['buy_and_sum']['buy']}}"/>
                                        <span>元 人数达到</span>
                                        <input onkeyup="value=value.replace(/[^\d]/g,'')" type="text" name="upgrade_value[sum]"
                                               value="{{$upgrade_data['buy_and_sum']['sum']}}"/>
                                        <span>个</span>
                                    </label>
                                </div>

                                @foreach($upgrade_config as $key=>$config)
                                    @if($config['key'] != 'goods' && $config['key'] != 'self_order_after' && $config['key'] != 'many_good')
                                        @if($key%2==0)
                                            <div class='input-group'>
                                                @endif
                                                <label class="radio-inline">
                                                    <input type="checkbox" name="upgrade_type[{{$config['key']}}]"
                                                           value="1"
                                                           @if($upgrade_data['type'][$config['key']]) checked="checked" @endif/>
                                                    {{$config['text']}}
                                                    <input type="text" name="upgrade_value[{{$config['key']}}]"
                                                           value="{{$upgrade_data['value'][$config['key']]}}"/>
                                                    <span>{{$config['unit']}}</span>
                                                </label>
                                                @if($key%2!=0)
                                            </div>
                                        @endif
                                    @elseif($config['key'] == 'goods' && !$set['is_with'])
                                        <div class='input-group'>
                                            <label class="radio-inline">
                                                <input type="checkbox" name="upgrade_type[{{$config['key']}}]"
                                                       value="1"
                                                       @if($upgrade_data['type'][$config['key']]) checked="checked" @endif/>
                                                {{$config['text']}}

                                                <input type='hidden' id='goodsid'
                                                       name="upgrade_value[{{$config['key']}}]"
                                                       value="{{$upgrade_data['value'][$config['key']]}}"/>
                                                <div class='input-group'>
                                                    <input type="text" name="goods" maxlength="30"
                                                           value="@if(isset($upgrade_data['goods'])) [{{$upgrade_data['goods']->id}}]{{$upgrade_data['goods']->title}} @endif"
                                                           id="goods" class="form-control" readonly/>
                                                    <div class='input-group-btn'>
                                                        <button class="btn btn-default" type="button"
                                                                onclick="popwin = $('#modal-module-menus-goods').modal();">
                                                            选择商品
                                                        </button>
                                                        <button class="btn btn-danger" type="button"
                                                                onclick="$('#goodsid').val('');$('#goods').val('');">
                                                            清除选择
                                                        </button>
                                                    </div>
                                                </div>
                                            </label>
                                            <span id="goodsthumb" class='help-block'
                                                  @if(empty($upgrade_data['goods'])) style="display:none" @endif ><img
                                                        style="width:100px;height:100px;border:1px solid #ccc;padding:1px"
                                                        src="@if(isset($upgrade_data['goods']->thumb)) {{tomedia($upgrade_data['goods']->thumb) }} @endif"/>
                                            </span>
                                        </div>
                                    @elseif($config['key'] == 'many_good' && !$set['is_with'])
                                        <div class="input-group row">
                                            <div class="input-group">
                                                <label class="radio-inline" >
                                                    <input type="checkbox"  name="upgrade_type[many_good]" value="1"
                                                           @if($upgrade_data['type'][$config['key']])
                                                           checked="checked"
                                                            @endif
                                                    />
                                                    购买指定商品之一
                                                    <div class="input-group">
                                                        <input type='text' style="width: 640px" class='form-control' id="many_good" value="@if(!empty($upgrade_data['many_good']))@foreach($upgrade_data['many_good'] as $good){{$good['title']}};@endforeach
                                                        @endif" readonly />
                                                        <div class="input-group-btn">
                                                            <button type="button" onclick="$('#modal-goods').modal()" class="btn btn-default" >选择商品</button>
                                                        </div>
                                                    </div>
                                                    <span class="help-block">可指定多件商品，只需购买其中一件就可以升級</span>
                                                    <div class="input-group multi-img-details" id='goods_id'>
                                                        @foreach ($upgrade_data['many_good'] as $goods_id => $good)
                                                            <div class="multi-item saler-item" style="height: 220px" openid="{{ $goods_id }}">
                                                                <img class="img-responsive img-thumbnail" src='{{ tomedia($good['thumb']) }}'
                                                                     onerror="this.src='{{static_url('resource/images/nopic.jpg')}}'; this.title='图片未找到.'">
                                                                <div class='img-nickname' style="max-height: 58px;overflow: hidden">{{ $good['title'] }}</div>
                                                                <input type="hidden" value="{{ $good->id }}"
                                                                       name="upgrade_value[many_good][{{ $goods_id }}]">
                                                                <em onclick="remove_member(this)" class="close">×</em>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                     @elseif($config['key'] == 'self_order_after')
                                        <div class='input-group'style="padding-left: 20px;">
                                            <label class="radio-inline">
                                                <input type='hidden' name="upgrade_type[{{$config['key']}}]" value="1"/>
                                                <input type="radio" name="upgrade_value[{{$config['key']}}]" value="1"
                                                       @if ($upgrade_data['value'][$config['key']]) checked @endif/> 付款后
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="upgrade_value[{{$config['key']}}]" value="0"
                                                       @if (empty($upgrade_data['value'][$config['key']])) checked @endif/> 完成后
                                            </label>
                                        </div>

                                    @endif
                                @endforeach
                                <br>
                                <span class='help-block'>
                                    付款后、完成后设置只对自购订单金额满、自购订单数量满@if(!$set['is_with'])、购买指定商品条件@endif生效，
                                    <br>
                                    如果选择付款后，只要用户下单付款满足升级依据，即可升级；如果选择完成后，则表示需要订单完成状态才能升级；<br>
                                    升级条件选择多个的，满足其中任何一个即可升级。
                                </span>
                            </div>
                        </div>


                    </div>
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

                    <div id="modal-goods"  class="modal fade" tabindex="-1">
                        <div class="modal-dialog" style='width: 920px;'>
                            <div class="modal-content">
                                <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择商品</h3></div>
                                <div class="modal-body" >
                                    <div class="row">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="keyword" value="" id="search-kwd-goods-commission" placeholder="请输入商品名称" />
                                            <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_goods_two();">搜索</button></span>
                                        </div>
                                    </div>
                                    <div id="module-menus-goods-two" style="padding-top:5px;"></div>
                                </div>
                                <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                            </div>
                        </div>
                    </div>

                    {{--<div class="form-group">--}}
                        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">分销管理奖</label>--}}
                        {{--<div class="col-sm-9 col-xs-12">--}}
                            {{--<label class="radio-inline">--}}
                                {{--<input type="radio" name="level[is_manage]" value="0"--}}
                                       {{--@if($levelModel['is_manage'] == 0)--}}
                                       {{--checked="checked" @endif />--}}
                                {{--关闭</label>--}}
                            {{--<label class="radio-inline">--}}
                                {{--<input type="radio" name="level[is_manage]" value="1"--}}
                                       {{--@if($levelModel['is_manage'] == 1)--}}
                                       {{--checked="checked" @endif />--}}
                                {{--开启</label>--}}
                        {{--</div>--}}
                    {{--</div>--}}
 {{--11--}}
                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"
                                   onclick='return formcheck()'/>
                        </div>
                    </div>

                </div>
            </form>

        </section><!-- /.content -->
    </div>
@endsection

<script>
    function search_goods() {
        if ($.trim($('#search-kwd-goods').val()) == '') {
            Tip.focus('#search-kwd-goods', '请输入关键词');
            return;
        }
        $("#module-menus-goods").html("正在搜索....");
        $.get('{!! yzWebUrl('goods.goods.get-search-goods-level') !!}', {
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

    function search_goods_two() {
        if( $.trim($('#search-kwd-goods-commission').val())==''){
            Tip.focus('#search-kwd-goods-commission','请输入关键词');
            return;
        }
        $("#module-goods").html("正在搜索....")
        $.get('{!! yzWebUrl('goods.goods.get-search-goods-by-dividend-level') !!}', {
            keyword: $.trim($('#search-kwd-goods-commission').val())
        }, function(dat){
            $('#module-menus-goods-two').html(dat);
        });
    }

    function select_good_two(o) {
        var html = '<div class="multi-item" style="height: 220px" openid="' + o.id + '">';
        html += '<img class="img-responsive img-thumbnail" src="' + o.thumb + '" onerror="this.src=\'{{static_url('resource/images/nopic.jpg')}}\'; this.title=\'图片未找到.\'">';
        html += '<div class="img-nickname" style="max-height: 58px;overflow: hidden">' + o.title + '</div>';
        html += '<input type="hidden" value="' + o.id + '" name="upgrade_value[many_good][' + o.id + ']">';
        html += '<em onclick="remove_member(this)"  class="close">×</em>';
        html += '</div>';
        $("#goods_id").append(html);
        refresh_members();
    }
    function remove_member(obj) {
        $(obj).parent().remove();
        refresh_members();
    }
    function refresh_members() {
        var nickname = "";
        $('.multi-item').each(function () {
            nickname += " " + $(this).find('.img-nickname').html() + "; ";
        });
        $('#many_good').val(nickname);
    }
</script>