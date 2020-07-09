@extends('layouts.base')
@section('title', trans('Yunshop\Love::notice_set.title'))
@section('content')

    <div class="rightlist">

        @include('Yunshop\Love::Backend.tabs')

        <form action="{{ yzWebUrl('plugin.love.Backend.Controllers.notice-set.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>

                <div class='panel-heading'>消息通知</div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::notice_set.change_title') }}</label>
                    <div class="col-sm-8 col-xs-12">
                        <select name='love[change_temp_id]' class='form-control diy-notice'>
                            <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['change_temp_id'])) value="{{$set['change_temp_id']}}"
                                    selected @else value="" @endif>
                                默认消息模板
                            </option>
                            @foreach ($temp_list as $item)
                                <option value="{{$item['id']}}" @if($set['change_temp_id'] == $item['id']) selected @endif>{{$item['title']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 col-xs-6">
                        <input class="mui-switch mui-switch-animbg" id="change_temp_id" type="checkbox"
                               @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['change_temp_id']))
                               checked
                               @endif
                               onclick="message_default(this.id)"/>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Love::notice_set.activation_title') }}</label>
                    <div class="col-sm-8 col-xs-12">
                        <select name='love[activation_temp_id]' class='form-control diy-notice'>
                            <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['activation_temp_id'])) value="{{$set['activation_temp_id']}}"
                                    selected @else value="" @endif>
                                默认消息模板
                            </option>
                            @foreach ($temp_list as $item)
                                <option value="{{$item['id']}}" @if($set['activation_temp_id'] == $item['id']) selected @endif>{{$item['title']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 col-xs-6">
                        <input class="mui-switch mui-switch-animbg" id="activation_temp_id" type="checkbox"
                               @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['activation_temp_id']))
                               checked
                               @endif
                               onclick="message_default(this.id)"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="{{ trans('Yunshop\Love::notice_set.submit') }}" class="btn btn-primary col-lg-1"
                               onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>
    <script>
        function message_default(name) {
            var id = "#" + name;
            var setting_name = "love." + name;
            var select_name = "select[name='love[" + name + "]']"
            var url_open = "{!! yzWebUrl('setting.default-notice.store') !!}"
            var url_close = "{!! yzWebUrl('setting.default-notice.storeCancel') !!}"
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
    <script type="text/javascript">
        $('.diy-notice').select2();
    </script>
@endsection

