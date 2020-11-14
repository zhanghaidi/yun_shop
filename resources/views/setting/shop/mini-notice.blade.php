@extends('layouts.base')

@section('content')

    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->

            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

                <div class='alert alert-info alert-important'>
                    请将公众平台模板消息所在行业选择为： IT科技/互联网|电子商务<br>
                    提示：点击模版消息后方开关按钮<input class="mui-switch" type="checkbox" disabled/>即可开启默认模版消息，无需进行额外设置。<br>
                    如需进行消息推送个性化消息，点击进入自定义模版管理。
                </div>
                <div class="page-toolbar">
                <span class=''>
                     <a class='btn btn-success btn-sm' href="{!! yzWebUrl('setting.shop.notice') !!}"><i
                                 class="fa fa-plus-square"></i>公众号消息提醒</a>
                 </span>
                </div>
                <div class="panel panel-default">
                    <style type='text/css'>
                        .multi-item {
                            height: 110px;
                        }

                        .img-thumbnail {
                            width: 100px;
                            height: 100px
                        }

                        .img-nickname {
                            position: absolute;
                            bottom: 0px;
                            line-height: 25px;
                            height: 25px;
                            color: #fff;
                            text-align: center;
                            width: 90px;
                            bottom: 55px;
                            background: rgba(0, 0, 0, 0.8);
                            left: 5px;
                        }

                        .multi-img-details {
                            padding: 5px;
                        }
                    </style>

                    <div class='panel-heading'>
                        商城消息提醒
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城消息提醒</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'>
                                    <input type='radio' name='yz_notice[toggle]' value='1'
                                           @if ($set['toggle'] == 1) checked @endif/>
                                    开启
                                </label>
                                <label class='radio-inline'>
                                    <input type='radio' name='yz_notice[toggle]' value='0'
                                           @if (empty($set['toggle'])) checked @endif />
                                    关闭
                                </label>
                                <div class="help-block">
                                    消息通知开关：控制商城全部消息（包含插件消息）
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class='panel-heading'>
                        签到通知
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">未签到通知</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[sign_reminder]' class='form-control diy-notice'>

                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['sign_reminder'] == $item['id'])
                                                selected
                                                @endif>
                                            {{$item['title']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="sign_reminder" type="checkbox"
                                       @if($set['sign_reminder'])
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                        </div>
                    </div>
                </div>
                <script>
                    function message_default(name) {
                        var id = "#" + name;
                        var setting_name = "shop.miniNotice";
                        var select_name = "select[name='yz_notice[" + name + "]']"
                        var url_open = "{!! yzWebUrl('setting.default-notice.index') !!}"
                        var url_close = "{!! yzWebUrl('setting.default-notice.cancel') !!}"
                        var postdata = {
                            notice_name: name,
                            notice_id: $(select_name).val(),
                            setting_name: setting_name,
                        };
                        if ($(id).is(':checked')) {
                            //开
                            $.post(url_open, postdata, function (data) {
                                if (data.result == 1) {
                                    $(select_name).find("option:selected").val(data.id)
                                    showPopover($(id), "开启成功")
                                } else {
                                    showPopover($(id), "开启失败，请检查微信模版")
                                    $(id).attr("checked", false);
                                }
                            }, "json");
                        } else {
                            //关
                            $.post(url_close, postdata, function (data) {
                                $(select_name).find("option").eq(0).val('')
                                select2_obj.each(function (key, item) {
                                    if ($(this).attr('name') == ('yz_notice[' + name + ']') && $(this).val() != '') {
                                        select2_obj.eq(key).val('').trigger("change");
                                    }
                                })
                                //$(select_name).val('');
                                showPopover($(id), "关闭成功")
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
                <script language='javascript'>
                    function search_members() {
                        if ($.trim($('#search-kwd').val()) == '') {
                            Tip.focus('#search-kwd', '请输入关键词');
                            return;
                        }
                        $("#module-menus").html("正在搜索....");
                        $.get("{!! yzWebUrl('member.member.get-search-member') !!}", {
                            keyword: $.trim($('#search-kwd').val())
                        }, function (dat) {
                            $('#module-menus').html(dat);
                        });
                    }

                    function noticeType(tpye) {
                        $("#type_id").val(tpye);
                    }

                    function select_member(o) {
                        if ($('.multi-item[openid="' + o.has_one_fans.openid + '"]').length > 0) {
                            return;
                        }
                        var html = '<div class="multi-item" openid="' + o.has_one_fans.openid + '">';
                        html += '<img class="img-responsive img-thumbnail" src="' + o.avatar + '" onerror="this.src=\'{{static_url('resource/images/nopic.jpg')}}\'; this.title=\'图片未找到.\'">';
                        html += '<div class="img-nickname">' + o.nickname + '</div>';
                        html += '<input type="hidden" value="' + o.has_one_fans.openid + '" name="yz_notice[salers][' + o.uid + '][openid]">';
                        html += '<input type="hidden" value="' + o.nickname + '" name="yz_notice[salers][' + o.uid + '][nickname]">';
                        html += '<input type="hidden" value="' + o.avatar + '" name="yz_notice[salers][' + o.uid + '][avatar]">';
                        html += '<input type="hidden" value="' + o.uid + '" name="yz_notice[salers][' + o.uid + '][uid]">';
                        html += '<em onclick="remove_member(this)"  class="close">×</em>';
                        html += '</div>';
                        $("#saler_container").append(html);
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
                        $('#salers').val(nickname);
                    }
                </script>
                <script type="text/javascript">
                    var select2_obj = $('.diy-notice').select2();
                </script>
        </div>
        </form>
    </div>
    </div>
@endsection
