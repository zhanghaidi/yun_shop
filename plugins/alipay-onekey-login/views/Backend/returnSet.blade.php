@extends('layouts.base')
@section('title', trans('Yunshop\Love::return_set.title'))
@section('content')

    <div class="rightlist">

        @include('Yunshop\Love::Backend.tabs')

        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>
                <div class='panel-heading'>{{ trans('Yunshop\Love::return_set.subtitle') }}</div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                            {{ trans('Yunshop\Love::return_set.return_set_title') }}
                        </label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[is_return]" value="1"
                                       @if ($set['is_return'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::return_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[is_return]" value="0"
                                       @if ($set['is_return'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::return_set.off') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                        {{ trans('Yunshop\Love::return_set.return_rate_title') }}
                    </label>
                    <div class="col-sm-4 col-xs-6">
                        <div class='input-group'>
                            <input type='text' name='setdata[return_rate]' class="form-control"
                                   value="{{ $set['return_rate'] }}"/>
                            <div class="input-group-addon">%</div>
                        </div>

                        <div class="help-block">
                            {{ trans('Yunshop\Love::return_set.return_rate_introduce') }}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                        {{ trans('Yunshop\Love::return_set.return_time_title') }}
                    </label>
                    <div class="col-sm-6 col-xs-6">
                        <div class='input-group'>
                            <label class="radio-inline">
                                <select name='setdata[return_times]' class='form-control'>
                                    @foreach($hourData as $hour)
                                        <option value='{{$hour['key']}}'
                                                @if($set['return_times'] == $hour['key']) selected @endif>
                                            每天 {{$hour['name']}}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <div class="help-block">
                            {{ trans('Yunshop\Love::return_set.return_time_introduce') }}
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="{{ trans('Yunshop\Love::return_set.submit') }}"
                               class="btn btn-primary col-lg-1"
                               onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>

@endsection

