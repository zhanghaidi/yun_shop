@extends('layouts.base')

@section('content')
<div class="w1200 m0a">
    <div class="rightlist">


        @include('layouts.tabs')
        <form action="" method="post" class="form-horizontal form">
            <div class='panel panel-default'>
                <div class="'panel-body">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位一</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('adv[1][img]',
                            $adv->advs['1']['img'])!!}
                            <span class="help-block">建议尺寸:173 * 86</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位一链接</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="adv[1][link]" class="form-control" value="{{$adv->advs['1']['link']}}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位二</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('adv[2][img]',
                            $adv->advs['2']['img'])!!}
                            <span class="help-block">建议尺寸:173 * 86</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位二链接</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="adv[2][link]" class="form-control" value="{{$adv->advs['2']['link']}}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9">
                            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg"
                                   onclick='return formcheck()'/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
