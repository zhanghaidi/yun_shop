@extends('layouts.base')
@section('title', trans('Yunshop\Love::trading_set.title'))
@section('content')

    <div class="rightlist">

        @include('Yunshop\Love::Backend.tabs')

        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>
                <div class='panel-heading'>{{ trans('Yunshop\Love::trading_set.subtitle') }}</div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                            {{ trans('Yunshop\Love::trading_set.trading_set_title') }}
                        </label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="setdata[trading]" value="1"
                                       @if ($set['trading'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Love::trading_set.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="setdata[trading]" value="0"
                                       @if ($set['trading'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Love::trading_set.off') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                        {{ trans('Yunshop\Love::trading_set.trading_limit_title') }}
                    </label>
                    <div class="col-sm-4 col-xs-6">
                        <div class='input-group'>
                            <input type='text' name='setdata[trading_limit]' class="form-control"
                                   value="{{ $set['trading_limit'] }}"/>
                            <div class="input-group-addon">{{\Yunshop\Love\Common\Services\SetService::getLoveName()}}</div>
                        </div>

                        <div class="help-block">
                            {{ trans('Yunshop\Love::trading_set.trading_limit_introduce') }}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                        {{ trans('Yunshop\Love::trading_set.trading_fold_title') }}
                    </label>
                    <div class="col-sm-4 col-xs-6">
                        <div class='input-group'>
                            <input type='text' name='setdata[trading_fold]' class="form-control"
                                   value="{{ $set['trading_fold'] }}"/>
                            <div class="input-group-addon">{{ trans('Yunshop\Love::trading_set.trading_fold_unit') }}</div>
                        </div>

                        <div class="help-block">
                            {{ trans('Yunshop\Love::trading_set.trading_fold_introduce') }}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                        {{ trans('Yunshop\Love::trading_set.poundage_title') }}
                    </label>
                    <div class="col-sm-4 col-xs-6">
                        <div class='input-group'>
                            <input type='text' name='setdata[poundage]' class="form-control"
                                   value="{{ $set['poundage'] }}"/>
                            <div class="input-group-addon">{{ trans('Yunshop\Love::trading_set.poundage_unit') }}</div>
                        </div>

                        <div class="help-block">
                            {{ trans('Yunshop\Love::trading_set.poundage_introduce') }}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                        {{ trans('Yunshop\Love::trading_set.trading_money_title') }}
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <div class="input-group">
                                <div class="input-group-addon">{{ trans('Yunshop\Love::trading_set.trading_money_hint') }}</div>
                                <input type="text" name="setdata[trading_money]" class="form-control" value="{{ $set['trading_money'] }}" placeholder=""/>
                                <div class="input-group-addon">{{ trans('Yunshop\Love::trading_set.trading_money_unit') }}</div>
                            </div>
                        </div>
                        <div class="help-block">
                            {{ trans('Yunshop\Love::trading_set.trading_money_introduce') }}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                        {{ trans('Yunshop\Love::trading_set.recycl_title') }}
                    </label>
                    <div class="col-sm-4 col-xs-6">
                        <div class='input-group'>
                            <input type='text' name='setdata[recycl]' class="form-control"
                                   value="{{ $set['recycl'] }}"/>
                            <div class="input-group-addon">{{ trans('Yunshop\Love::trading_set.recycl_unit') }}</div>
                        </div>

                        <div class="help-block">
                            {{ trans('Yunshop\Love::trading_set.recycl_introduce') }}
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="{{ trans('Yunshop\Love::trading_set.submit') }}"
                               class="btn btn-primary col-lg-1"
                               onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>

@endsection

