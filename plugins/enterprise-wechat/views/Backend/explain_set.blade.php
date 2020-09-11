@extends('layouts.base')
@section('title', trans('Yunshop\Sign::sign.explain_set_title'))

@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">

            @include('Yunshop\Sign::Backend.tabs')


            <form action="{{ yzWebUrl('plugin.sign.Backend.Controllers.explain-set.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">

                    <div class='panel-heading'></div>
                    <div class='panel-body'>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label" style="width: 10%">{{ trans('Yunshop\Sign::sign.explain_content') }}:</label>
                            <div class="col-sm-9 col-xs-12">
                                {!! yz_tpl_ueditor('sign[explain_content]', $sign['explain_content']) !!}
                                {{--{!! tpl_ueditor('sign[explain_content]', $sign['explain_content']) !!}--}}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label" style="width: 10%"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="{{ trans('Yunshop\Sign::sign.button_submit') }}" class="btn btn-success"/>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>


    <script type="text/javascript">
        require(['bootstrap'], function ($) {
            $(document).scroll(function () {
                var toptype = $("#edui1_toolbarbox").css('position');
                if (toptype == "fixed") {
                    $("#edui1_toolbarbox").addClass('top_menu');
                }
                else {
                    $("#edui1_toolbarbox").removeClass('top_menu');
                }
            });
        });
    </script>

@endsection
