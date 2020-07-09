@extends('layouts.base')
@section('title', trans('Yunshop\Love::dividend_set.title'))
@section('content')

    <div class="rightlist">

        @include('Yunshop\Love::Backend.tabs')

        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>
                <div class='panel-heading'>{{ trans('Yunshop\Love::dividend_set.subtitle') }}</div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                            {{ trans('Yunshop\Love::dividend_set.dividend_set_title') }}
                        </label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[is_dividend]" value="1"
                                       @if ($set['is_dividend'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::dividend_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[is_dividend]" value="0"
                                       @if ($set['is_dividend'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::dividend_set.off') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                        {{ trans('Yunshop\Love::dividend_set.dividend_rate_title') }}
                    </label>
                    <div class="col-sm-4 col-xs-6">
                        <div class='input-group'>
                            <input type='text' name='setdata[dividend_rate]' class="form-control"
                                   value="{{ $set['dividend_rate'] }}"/>
                            <div class="input-group-addon">%</div>
                        </div>

                        <div class="help-block">
                            {{ trans('Yunshop\Love::dividend_set.dividend_rate_introduce') }}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                        {{ trans('Yunshop\Love::dividend_set.dividend_time_title') }}
                    </label>
                    <div class="col-sm-6 col-xs-6">
                        <div class='input-group'>
                            <div class='input-group'>
                                <input type='text' name='setdata[dividend_day]' class="form-control"
                                       value="{{ $set['dividend_day'] }}"/>
                                <div class="input-group-addon">天</div>
                            </div>
                        </div>
                        <div class="help-block">
                            {{ trans('Yunshop\Love::dividend_set.dividend_time_introduce') }}
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="{{ trans('Yunshop\Love::dividend_set.submit') }}"
                               class="btn btn-primary col-lg-1"
                               onclick='dividend formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>

@endsection

