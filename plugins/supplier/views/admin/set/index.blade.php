@extends('layouts.base')

@section('content')
@section('title', trans('基础设置'))
<script type="text/javascript">
    window.optionchanged = false;
    require(['bootstrap'], function () {
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
    });
    $(document).ready(function() {
        $('.diy-notice').select2();
    })
</script>
<section class="content">
    <form id="setform" action="" method="post" class="form-horizontal form">

        <div  class="top">
            <ul class="add-shopnav" id="myTab">
                <li class="active" ><a href="#tab_set">基础设置</a></li>
                <li><a href="#tab_permission">权限设置</a></li>
                <li><a href="#tab_notice">消息通知</a></li>
                <li><a href="#tab_info">信息设置</a></li>
                <li><a href="#tab_banner">背景图</a></li>
                <li><a href="#tab_ins_set">保单设置</a></li>
            </ul>
        </div>

        <div class="info">
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane  active" id="tab_set">
                        @include('Yunshop\Supplier::admin.set.base')
                    </div>
                    <div class="tab-pane" id="tab_permission">
                        @include('Yunshop\Supplier::admin.set.permission')
                    </div>
                    <div class="tab-pane" id="tab_notice">
                        @include('Yunshop\Supplier::admin.set.notice')
                    </div>
                    <div class="tab-pane" id="tab_info">
                        @include('Yunshop\Supplier::admin.set.info')
                    </div>
                    <div class="tab-pane" id="tab_banner">
                        @include('Yunshop\Supplier::admin.set.banner')
                    </div>
                    <div class="tab-pane" id="tab_ins_set">
                        @include('Yunshop\Supplier::admin.set.ins_set')
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