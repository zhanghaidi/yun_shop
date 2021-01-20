@extends('layouts.base')

@section('content')
@section('title', trans('视频点播设置'))
<script type="text/javascript">
    window.optionchanged = false;
    require(['bootstrap'], function () {
        $('#myTab .tab').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
    });
</script>

<section class="content">
    <form id="setform" action="" method="post" class="form-horizontal form">
        @include('Yunshop\VideoDemand::admin.tabs')

        <div class="info">
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane  active"
                         id="tab_video_demand">@include('Yunshop\VideoDemand::admin.tpl.video-demand')</div>
                    <div class="tab-pane" id="tab_notice">@include('Yunshop\VideoDemand::admin.tpl.notice')</div>
                </div>

                <div class="form-group col-sm-12 mrleft40 border-t">
                    <input type="submit" name="submit" value="提交" class="btn btn-success"
                           onclick="return formcheck()"/>
                </div>
            </div>
        </div>

    </form>
</section><!-- /.content -->
<script>
    require(['select2'], function () {
        $('.diy-notice').select2();
    })

    $('.js-clip').each(function () {
        util.clip(this, $(this).attr('data-url'));
    });
</script>
@endsection

