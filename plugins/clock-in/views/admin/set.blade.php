@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))
    <script type="text/javascript">
        window.optionchanged = false;
        require(['bootstrap'], function () {
            $('#myTab a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
            })
        });
    </script>

    <section class="content">
        <form id="setform" action="" method="post" class="form-horizontal form">

            @include('Yunshop\ClockIn::admin.tabs')

            <div class="info">
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane  active"
                             id="tab_reward">@include('Yunshop\ClockIn::admin.tpl.clock-in')</div>
                        <div class="tab-pane"
                             id="tab_rule">@include('Yunshop\ClockIn::admin.tpl.rule')</div>
                        <div class="tab-pane"
                             id="tab_share">@include('Yunshop\ClockIn::admin.tpl.share')</div>
                        <div class="tab-pane"
                             id="tab_pay_method">@include('Yunshop\ClockIn::admin.tpl.pay-method')</div>
                    </div>

                    <div class="form-group col-sm-12 mrleft40 border-t">
                        <input type="submit" name="submit" value="提交" class="btn btn-success"
                               onclick="return formcheck()"/>
                    </div>
                </div>
            </div>

        </form>
    </section><!-- /.content -->
@endsection

