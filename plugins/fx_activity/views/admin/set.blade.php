@extends('layouts.base')

@section('content')
@section('title', trans('活动报名设置'))
<script type="text/javascript">
    window.optionchanged = false;
    require(['bootstrap'], function () {
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
    });
</script>

<div class="w1200 m0a">
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">活动报名设置</a></li>
            </ul>
        </div>
        <div  class="panel panel-info">
            <ul class="add-shopnav" id="myTab">
                <li class="active" ><a href="#tab_marketing">营销设置</a></li>
                <li><a href="#tab_fee_splitting">分润设置</a></li>
                <li><a href="#tab_cash_back">返现设置</a></li>
            </ul>
        </div>
        <section>
            <form id="setform" action="" method="post" class="form-horizontal form">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane  active" id="tab_marketing">@include('Yunshop\FxActivity::admin.tpl.marketing')</div>
                            <div class="tab-pane" id="tab_fee_splitting">@include('Yunshop\FxActivity::admin.tpl.fee_splitting')</div>
                            <div class="tab-pane" id="tab_cash_back">@include('Yunshop\FxActivity::admin.tpl.cash_back')</div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-xs-12 col-sm-9 col-md-10">
                                <input type="submit" name="submit" value="提交" class="btn btn-success"
                                       onclick="return formcheck()"/>
                            </div>
                        </div>

                    </div>
                </div>

            </form>
        </section>


    </div>
</div>
<script language='javascript'>

    require(['select2'], function () {
        $('.diy-notice').select2();
    })
</script>
@endsection

