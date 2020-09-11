@extends('layouts.base')
@section('title', trans('Yunshop\Sign::sign.share_set_title'))

@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">

            @include('Yunshop\Sign::Backend.tabs')


            <form action="{{ yzWebUrl('plugin.sign.Backend.Controllers.share-set.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">

                    <div class='panel-heading'></div>
                    <div class='panel-body'>

                        {{--分享标题--}}
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Sign::sign.share_title') }}</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="sign[share_title]" class="form-control"
                                       value="{{ $sign['share_title'] }}"/>
                                <span class="help-block">{{ trans('Yunshop\Sign::sign.share_title_introduce') }}</span>
                            </div>
                        </div>

                        {{--分享图片--}}
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Sign::sign.share_img') }}</label>
                            <div class="col-sm-9 col-xs-12">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('sign[share_icon]', $sign['share_icon'])!!}
                            </div>
                        </div>

                        {{--分享描述--}}
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Sign::sign.share_describe') }}</label>
                            <div class="col-sm-9 col-xs-12">
                                <textarea style="height:100px;" name="sign[share_desc]" class="form-control" cols="60">{{ $sign['share_desc'] }}</textarea>
                            </div>
                        </div>

                        <div class="form-group"></div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="{{ trans('Yunshop\Sign::sign.button_submit') }}" class="btn btn-success"/>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
