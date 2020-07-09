@extends('layouts.base')

@section('content')
@section('title', trans('分销管理奖设置'))
    <section class="content">

        <form id="setform" action="" method="post" class="form-horizontal form">

            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">分销管理奖设置</a></li>
                </ul>
            </div>
            @include('Yunshop\Commission::admin.tabs')

            <div class='panel panel-default'>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启管理奖</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="manage[is_manage]" value="0"
                                       @if($set['is_manage'] == 0)
                                checked="checked" @endif />
                                关闭</label>
                            <label class="radio-inline">
                                <input type="radio" name="manage[is_manage]" value="1"
                                       @if($set['is_manage'] == 1)
                                checked="checked" @endif />
                                开启</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">默认等级开启管理奖</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="manage[is_default_level]" value="0"
                                       @if($set['is_default_level'] == 0)
                                       checked="checked" @endif />
                                关闭</label>
                            <label class="radio-inline">
                                <input type="radio" name="manage[is_default_level]" value="1"
                                       @if($set['is_default_level'] == 1)
                                       checked="checked" @endif />
                                开启</label>
                        </div>
                    </div>

                    @if($commissionSet['level']>=1)
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级分销比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="manage[first_level]" class="form-control"
                                       value="@if(isset($set['first_level'])){{$set['first_level']}}@endif"/>
                            </div>
                        </div>
                    @endif
                    @if($commissionSet['level']>=2)
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">二级分销比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="manage[second_level]" class="form-control"
                                       value="@if(isset($set['second_level'])){{$set['second_level']}}@endif"/>
                            </div>
                        </div>
                    @endif
                    @if($commissionSet['level']>=3)
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">三级分销比例</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="manage[third_level]" class="form-control"
                                       value="@if(isset($set['third_level'])){{$set['third_level']}}@endif"/>
                            </div>
                        </div>
                    @endif


                <div class="form-group"></div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"
                               onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </section><!-- /.content -->
@endsection
