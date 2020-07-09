@extends('layouts.base')

@section('content')
@section('title', trans('分销消息通知设置'))
<section class="content">

    <form id="setform" action="" method="post" class="form-horizontal form">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">分销设置</a></li>
            </ul>
        </div>
        @include('Yunshop\Commission::admin.tabs')

        <div class='panel panel-default'>
            <div class='panel-body'>


                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">成为分销商通知</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name='yz_notice[become_agent]' class='form-control diy-notice'>
                                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['become_agent'])) value="{{$set['become_agent']}}"
                                        selected @else value="" @endif>
                                    默认消息模版
                                </option>
                                @foreach ($temp_list as $item)
                                    <option value="{{$item['id']}}"
                                            @if($set['become_agent'] == $item['id'])
                                            selected
                                            @endif>{{$item['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2 col-xs-6">
                            <input class="mui-switch mui-switch-animbg" id="become_agent" type="checkbox"
                                   @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['become_agent']))
                                   checked
                                   @endif
                                   onclick="message_default(this.id)"/>
                        </div>
                    </div>
                </div>
                @if(YunShop::notice()->getNotSend('commission.commission_order_title'))
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">下级下单通知</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[commission_order]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['commission_order'])) value="{{$set['commission_order']}}"
                                            selected @else value="" @endif>
                                        默认消息模版
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['commission_order'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="commission_order" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['commission_order']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>
                        </div>
                    </div>
                @endif
                @if(YunShop::notice()->getNotSend('commission.commission_order_finish_title'))
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">下级确认收货通知</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[commission_order_finish]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['commission_order_finish'])) value="{{$set['commission_order_finish']}}"
                                            selected @else value="" @endif>
                                        默认消息模版
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['commission_order_finish'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="commission_order_finish" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['commission_order_finish']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>
                        </div>
                    </div>
                @endif

                @if(YunShop::notice()->getNotSend('commission.commission_upgrade_title'))
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销商等级升级通知</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[commission_upgrade]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['commission_upgrade'])) value="{{$set['commission_upgrade']}}"
                                            selected @else value="" @endif>
                                        默认消息模版
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['commission_upgrade'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="commission_upgrade" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['commission_upgrade']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>
                        </div>
                    </div>
                @endif
                @if(YunShop::notice()->getNotSend('commission.statement_title'))
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销佣金结算通知</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[statement]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['statement'])) value="{{$set['statement']}}"
                                            selected @else value="" @endif>
                                        默认消息模版
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['statement'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="statement" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['statement']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="form-group"></div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9">
                    <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"/>
                </div>
            </div>

        </div>
    </form>
</section><!-- /.content -->
<script>
    function message_default(name) {
        var id = "#" + name;
        var setting_name = "plugin.commission_notice";
        var select_name = "select[name='yz_notice[" + name + "]']"
        var url_open = "{!! yzWebUrl('setting.default-notice.index') !!}"
        var url_close = "{!! yzWebUrl('setting.default-notice.cancel') !!}"
        var postdata = {
            notice_name: name,
            setting_name: setting_name
        };
        if ($(id).is(':checked')) {
            //开
            $.post(url_open,postdata,function(data){
                if (data.result == 1) {
                    $(select_name).find("option:selected").val(data.id)
                    showPopover($(id),"开启成功")
                } else {
                    showPopover($(id),"开启失败，请检查微信模版")
                    $(id).attr("checked",false);
                }
            }, "json");
        } else {
            //关
            $.post(url_close,postdata,function(data){
                $(select_name).val('');
                showPopover($(id),"关闭成功")
            }, "json");
        }
    }
    function showPopover(target, msg) {
        target.attr("data-original-title", msg);
        $('[data-toggle="tooltip"]').tooltip();
        target.tooltip('show');
        target.focus();
        //2秒后消失提示框
        setTimeout(function () {
                target.attr("data-original-title", "");
                target.tooltip('hide');
            }, 2000
        );
    }
</script>
<script>
    $('.diy-notice').select2();
</script>
@endsection