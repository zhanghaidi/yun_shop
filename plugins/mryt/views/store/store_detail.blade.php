@extends('layouts.base')

@section('content')
@section('title', trans('Yunshop\StoreCashier::pack.store_detail'))
<script type="text/javascript">
    require(['bootstrap'], function () {
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
    });
</script>
<div class="w1200 ">
    <div class=" rightlist ">
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">门店信息</a></li>
            </ul>
        </div>

        <div class="right-addbox"><!-- 此处是右侧内容新包一层div -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <form id="store_form" name="store_form" action="" method="post" class="form-horizontal form">
                        <input type="hidden" name="store_id" class="form-control"
                               value="{{$store_id}}"/>
                        <div class="top">
                            <ul class="add-shopnav" id="myTab">
                                <li class="active"><a href="#tab_store_basic">门店信息</a></li>
                            </ul>
                        </div>

                        <div class="info">
                            <div class="panel-body">

                                <div class="tab-content">
                                    <div class="tab-pane  active" id="tab_store_basic">@include(\Yunshop\Mryt\store\admin\StoreController::STORE_BASE_VIEW)</div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-xs-6">
                                        <input type="button" name="submit" value="提交" class="btn btn-success"
                                               onclick="sub()"/>
                                        <input type="button" name="back" onclick='history.back()' style=''
                                               value="返回列表"
                                               class="btn btn-default back"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
<script language='javascript'>
    var province_id = $('#province_id').val();
    var city_id = $('#city_id').val();
    var district_id = $('#district_id').val();
    var street_id = $('#street_id').val();
    cascdeInit(province_id, city_id, district_id, street_id);
    function search_members() {
        if ($('#search-kwd-notice').val() == '') {
            Tip.focus('#search-kwd-notice', '请输入关键词');
            return;
        }
        $("#module-menus-notice").html("正在搜索....");
        $.get("{!! yzWebUrl('member.member.get-search-member') !!}", {
            keyword: $.trim($('#search-kwd-notice').val())
        }, function (dat) {
            $('#module-menus-notice').html(dat);
        });
    }

    function store_search_members() {
        if ($.trim($('#search-kwd').val()) == '') {
            Tip.focus('#search-kwd', '请输入关键词');
            return;
        }
        $("#module-menus").html("正在搜索....");
        $.get("{!! yzWebUrl('plugin.mryt.store.admin.query') !!}", {
            keyword: $.trim($('#search-kwd').val())
        }, function (dat) {
            $('#module-menus').html(dat);
        });
    }

    function select_member(o) {
        $("#noticeopenid").val(o.uid);
        $("#saleravatar").show();
        $("#saleravatar").find('img').attr('src', o.avatar);
        $("#saler").val(o.nickname + "/" + o.realname + "/" + o.mobile);
        $("#modal-module-menus-notice .close").click();
    }

    function store_select_member(o) {
        console.log(o);
        if ($('.multi-item[openid="' + o.has_one_fans.openid + '"]').length > 0) {
            return;
        }
        var html = '<div class="multi-item" openid="' + o.has_one_fans.openid + '">';
        html += '<img class="img-responsive img-thumbnail" src="' + o.avatar + '" onerror="this.src=\'{{static_url('resource/images/nopic.jpg')}}\'; this.title=\'图片未找到.\'">';
        html += '<div class="img-nickname-store">' + o.nickname + '</div>';
        html += '<input type="hidden" value="' + o.has_one_fans.openid + '" name="store[salers][' + o.uid + '][openid]">';
        html += '<input type="hidden" value="' + o.nickname + '" name="store[salers][' + o.uid + '][nickname]">';
        html += '<input type="hidden" value="' + o.avatar + '" name="store[salers][' + o.uid + '][avatar]">';
        html += '<input type="hidden" value="' + o.uid + '" name="store[salers][' + o.uid + '][uid]">';
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
            var ret = $(this).find('.img-nickname-store').html();
            if (ret === undefined) {
                nickname += " ";
            } else {
                nickname += " " + $(this).find('.img-nickname-store').html() + "; ";
            }
        });
        $('#salers').val(nickname);
    }

    function select_category(o) {
        $(".focuscategory:last input[data-name=coupon_ids]").val(o.id);
        $(".focuscategory:last input[data-name=coupon_names]").val(o.name);
        $(".focuscategory").removeClass("focuscategory");
        $("#modal-module-menus-categorys .close").click();
    }

    function sub()
    {
        var cbk1 = document.getElementById('dispatch_type1');
        var cbk2 = document.getElementById('dispatch_type2');
        var cbk3 = document.getElementById('dispatch_type3');
        if (!cbk1.checked && !cbk2.checked && !cbk3.checked ) {
            alert('请选择至少一种配送方式');return;
        }

        document.store_form.submit.disabled=true;

        var form_data = $($('#store_form')[0]).serialize();
        var html = '';
        var url = "{!! yzWebUrl('plugin.mryt.store.admin.store.index') !!}";
        $.post
        ({
            url: '{!! yzWebUrl('plugin.mryt.store.admin.store.edit') !!}',
            dataType: "json",
            data: form_data,
            success: function (strValue) {

                if (strValue.result == "0") {
                    $.each(strValue.msg, function(index, data){
                        html += data['0'] + "\r\n";
                    });
                    confirm(html);
                    document.store_form.submit.disabled=false;
                } else if (strValue.result == "1") {
                    confirm(strValue.msg);
                    window.location.href = url;
                } else if (strValue.status == "-1") {
                    confirm(strValue.result.msg);
                    document.store_form.submit.disabled=false;
                }
            }
        })
    }

    require(['util', 'clockpicker'], function(u, $){
        $('.clockpicker :text').clockpicker({autoclose: true});

        u.editor($('.richtext')[0]);

        $('#add-time').click(function(){
            $('#time-list').append($('#time-form-html').html());
            $('.clockpicker :text').clockpicker({autoclose: true});
        });
    });

</script>
@endsection