@extends('layouts.base')

@section('content')
@section('title', trans('推荐奖励'))
<script type="text/javascript">
    window.optionchanged = false;
    require(['bootstrap'], function () {
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
    });
    require(['select2'], function () {
        $('.diy-notice').select2();
    })
    function message_default(name) {
        var id = "#" + name;
        var setting_name = "plugin.nominate_notice";
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
<section class="content">
    <form id="setform" action="" method="post" class="form-horizontal form">

        <div  class="top">
            <ul class="add-shopnav" id="myTab">
                <li class="active" >
                    <a href="#tab_set">
                        基础设置
                    </a>
                </li>
                <li>
                    <a href="#tab_notice">
                        消息通知
                    </a>
                </li>
            </ul>
        </div>

        <div class="info">
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane  active" id="tab_set">
                        @include('Yunshop\Nominate::set.base')
                    </div>
                    <div class="tab-pane" id="tab_notice">
                        @include('Yunshop\Nominate::set.notice')
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <input type="submit" name="submit" value="保存设置" class="btn btn-success"
                               onclick="return formcheck()"/>
                    </div>
                </div>
            </div>
        </div>

    </form>
</section>
@endsection