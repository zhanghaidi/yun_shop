@extends('layouts.base')
@section('title', trans('Yunshop\Sign::sign.notice_set_title'))

@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">

            @include('Yunshop\Sign::Backend.tabs')


            <form action="{{ yzWebUrl('plugin.sign.Backend.Controllers.notice-set.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">

                    <div class='panel-heading'></div>
                    <div class='panel-body'>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label" style="width: 10%">{{ trans('Yunshop\Sign::sign.sign_notice') }}</label>
                            <div class="col-sm-9 col-xs-12">
                                <select name='sign[sign_notice]' class='form-control diy-notice select2'>
                                    <option value="" @if(!$sign['sign_notice']) selected @endif;>
                                        {{ trans('Yunshop\Sign::sign.choose_notice_template') }}
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}" @if($sign['sign_notice'] == $item['id']) selected @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
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
        require(['select2'], function () {
            $('.diy-notice').select2();
        })
    </script>

@endsection
